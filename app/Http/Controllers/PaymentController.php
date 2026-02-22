<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    protected $api = null;

    public function __construct()
    {
        $this->middleware('auth')->except([
            'publicPay',
            'publicPaySummary',
            'success',
            'paymentSuccessPage'
        ]);
    
        $key = config('services.razorpay.key');    
        $secret = config('services.razorpay.secret');


        if ($key && $secret) {
            $this->api = new Api($key, $secret);
        }
    }


    /*
    ==============================
    CREATE ORDER
    ==============================
    */
    public function createOrder(Request $request): JsonResponse
    {
        \Log::info('🔥 createOrder route HIT');

        try {



            if (!$this->api) {
                \Log::error('Razorpay API NOT initialized');
                return response()->json([
                    'error' => 'Razorpay not configured'
                ], 500);
            }

            $order = $this->api->order->create([
                'receipt' => 'rcpt_' . time(),
                'amount' => 49900, // ₹499
                'currency' => 'INR',
            ]);

            return response()->json([
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => 'INR'
            ]);

        } catch (\Throwable $e) {

            \Log::error('RAZORPAY ORDER ERROR: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /*
    ==============================
    VERIFY PAYMENT
    ==============================
    */
    public function verify(Request $request): JsonResponse
    {
        try {

            if (!$this->api) {
                \Log::error('Verify hit but API not initialized');
                return response()->json(['status' => 'failed'], 500);
            }

            $data = $request->validate([
                'razorpay_order_id' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature' => 'required',
            ]);

            \Log::info('Verify request data:', $request->all());

            $this->api->utility->verifyPaymentSignature($data);

            $user = auth()->user();
            $user->plan = 'pro';
            $user->save();

            return response()->json(['status' => 'success']);

        } catch (SignatureVerificationError $e) {

            \Log::error('Signature failed: ' . $e->getMessage());
            return response()->json(['status' => 'failed'], 400);

        } catch (\Throwable $e) {

            \Log::error('VERIFY ERROR: ' . $e->getMessage());
            return response()->json(['status' => 'failed'], 500);
        }
    }

    /*
=================================
OPEN RAZORPAY CHECKOUT PAGE
=================================
*/
public function pay(Invoice $invoice)
{
    return view('payments.razorpay', compact('invoice'));
}


/*
=================================
PAYMENT SUCCESS HANDLER
=================================
*/
public function success(Request $request)
{
    $invoice = Invoice::findOrFail($request->invoice_id);

    $invoice->status = 'paid';
    $invoice->paid_at = now();
    $invoice->save();

    $mail = new \App\Mail\InvoicePaidMail($invoice);

    try {
        $downloadResponse = app()->handle(
            \Illuminate\Http\Request::create(route('invoices.download', $invoice->id), 'GET')
        );
        $file = method_exists($downloadResponse, 'getFile') ? $downloadResponse->getFile() : null;
        if ($file) {
            $mail->attachData(
                file_get_contents($file->getPathname()),
                'invoice-' . $invoice->invoice_number . '.pdf',
                ['mime' => 'application/pdf']
            );
        }
    } catch (\Throwable $e) {
        \Log::error('Failed to attach invoice PDF', [
            'invoice_id' => $invoice->id,
            'error' => $e->getMessage(),
        ]);
    }

    Mail::to($invoice->client->email)
        ->send($mail);

    return redirect()
        ->route('payment.success', $invoice->id)
        ->with('success', 'Payment successful ✅');
}

public function paymentSuccessPage($invoiceId)
{
    $invoice = Invoice::findOrFail($invoiceId);

    return view('payments.success', compact('invoice'));
}

public function publicPay(Request $request, Invoice $invoice)
{
    \Log::info('PUBLIC PAY HIT');

    \Log::info(['URL_TOKEN' => $request->token, 'DB_TOKEN' => $invoice->payment_token]);

    if ($request->token !== $invoice->payment_token) {
        \Log::warning('TOKEN MISMATCH → 403');
        abort(403);
    }

    if ($invoice->normalizedStatus() === Invoice::STATUS_PAID) {
        return redirect()
            ->back()
            ->with('info', 'Already paid');
    }

    \Log::info('TOKEN OK → showing razorpay page');
    return view('payments.razorpay', compact('invoice'));
}

/**
 * Public invoice payment summary page (token in path).
 * Shows invoice details before Pay Now → Razorpay checkout.
 */
public function publicPaySummary(Invoice $invoice, string $token)
{
    if ($token !== $invoice->payment_token) {
        abort(403);
    }

    if ($invoice->normalizedStatus() === Invoice::STATUS_PAID) {
        return redirect()
            ->route('invoice_public_pay', ['invoice' => $invoice, 'token' => $token])
            ->with('info', 'This invoice has already been paid.');
    }

    $invoice->load(['client', 'items', 'user']);

    return view('payments.invoice-summary', compact('invoice'));
}

/*
=================================
RAZORPAY WEBHOOK (SECURE)
=================================
*/
public function webhook(Request $request)
{
    Log::info('RAZORPAY WEBHOOK HIT', request()->all());

    // 🔐 Verify signature first (MOST IMPORTANT)
    $signature = $request->header('X-Razorpay-Signature');

    $expected = hash_hmac(
        'sha256',
        $request->getContent(),
        config('services.razorpay.webhook_secret')
    );

    if (!hash_equals($expected, $signature)) {
        abort(403, 'Invalid signature');
    }

    $payload = $request->all();

    File::append(
        storage_path('logs/razorpay.log'),
        '[' . now()->toDateTimeString() . '] ' . json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL
    );

    \Log::info('Webhook received', $payload);

    // Example: handle payment success
    if (($payload['event'] ?? '') === 'payment.captured') {
        $orderId = $payload['payload']['payment']['entity']['order_id'] ?? null;
        if (! $orderId) {
            return response()->json(['status' => 'ok']);
        }

        $invoice = Invoice::query()
            ->where('razorpay_order_id', $orderId)
            ->first();

        if (! $invoice) {
            return response()->json(['status' => 'ok']);
        }

        if ($invoice->paid_at || strtolower((string) $invoice->status) === 'paid') {
            return response()->json(['status' => 'ok']);
        }

        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();

        Mail::to($invoice->client->email)
    ->send(new \App\Mail\InvoicePaidMail($invoice));
    }

    return response()->json(['status' => 'ok']);
}

}
