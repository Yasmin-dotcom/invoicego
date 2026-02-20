<?php

use Illuminate\Support\Facades\Route;
use App\Services\GstCalculator;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SettingsController;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-gst', function () {
    $result = GstCalculator::calculate(
        itemPrice: 100,
        quantity: 2,
        gstRate: 18,
        sellerStateCode: '29',
        clientStateCode: '29'
    );
    return response()->json($result);
});

Route::get('/pay/{invoice}', [PaymentController::class, 'publicPay'])
    ->name('invoice.public.pay');

Route::get('/pay/{invoice}/{token}', [PaymentController::class, 'publicPaySummary'])
    ->name('invoice_public_pay');

Route::get('/payment/success/{invoice}', [PaymentController::class, 'paymentSuccessPage'])
    ->name('payment.success');


/*
|--------------------------------------------------------------------------
| Razorpay Webhook (NO AUTH + NO CSRF)
|--------------------------------------------------------------------------
*/

Route::post('/razorpay/webhook', [PaymentController::class, 'webhook'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'onboarded'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/onboarding', [OnboardingController::class, 'index'])
        ->name('onboarding');

    Route::post('/onboarding/business', [OnboardingController::class, 'storeBusiness'])
        ->name('onboarding.business');

    Route::get('/onboarding/client', [OnboardingController::class, 'client'])
        ->name('onboarding.client');

    Route::post('/onboarding/client', [OnboardingController::class, 'storeClient'])
        ->name('onboarding.client.store');

    Route::post('/onboarding/complete', [OnboardingController::class, 'storeClient'])
        ->name('onboarding.complete');

    Route::post('/onboarding/skip', [OnboardingController::class, 'skip'])
        ->name('onboarding.skip');

    Route::resource('clients', ClientController::class);

    Route::get('/dashboard/search-invoices', [DashboardController::class, 'searchInvoices']);

    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportAllInvoices'])
        ->name('dashboard.export.all');

    Route::get('/dashboard/export/csv/month', [DashboardController::class, 'exportMonthInvoices'])
        ->name('dashboard.export.month');

    Route::get('/settings/reminders', [SettingsController::class, 'reminders'])
        ->name('settings.reminders');

    Route::post('/settings/reminders', [SettingsController::class, 'saveReminders'])
        ->name('settings.reminders.save');


    /*
    |--------------------------------------------------------------------------
    | Invoices
    |--------------------------------------------------------------------------
    */

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');

    Route::get('/invoices/search', [InvoiceController::class, 'search'])->name('invoices.search');


    /*
    |--------------------------------------------------------------------------
    | 🔥 BULK ACTIONS
    |--------------------------------------------------------------------------
    */

    Route::post('/invoices/bulk/send', [InvoiceController::class, 'bulkSend'])
        ->name('invoices.bulk.send');

    Route::post('/invoices/bulk/mark-paid', [InvoiceController::class, 'bulkMarkPaid'])
        ->name('invoices.bulk.mark-paid');

    Route::post('/invoices/bulk/delete', [InvoiceController::class, 'bulkDelete'])
        ->name('invoices.bulk.delete');

    Route::get('/invoices/export/csv', [InvoiceController::class, 'exportCsv'])
        ->name('invoices.export.csv');


    /*
    |--------------------------------------------------------------------------
    | Invoice Actions (Single)
    |--------------------------------------------------------------------------
    */

    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'markAsSent'])
        ->name('invoices.send');

    Route::get('/invoices/{invoice}/send-preview', [InvoiceController::class, 'sendPreview'])
        ->name('invoices.send.preview');

    Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])
        ->name('invoices.send.email');

    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])
        ->name('invoices.mark-paid');

    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'markAsPaid'])
        ->name('invoices.pay');

    Route::post('/invoices/{invoice}/reminder', [InvoiceController::class, 'sendPaymentReminder'])
        ->name('invoices.reminder');


    /*
    |--------------------------------------------------------------------------
    | 🔥 PAYMENT SUCCESS (FIXED SAFELY)
    |--------------------------------------------------------------------------
    */

    // POST → update DB status after payment
    Route::post('/payment/success', [PaymentController::class, 'success'])
        ->name('payment.success.post');


    /*
    |--------------------------------------------------------------------------
    | Invoice CRUD (KEEP LAST)
    |--------------------------------------------------------------------------
    */

    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');

    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
        ->name('invoices.destroy');

    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');


    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
    */

    Route::post('/invoices/{invoice}/items', [InvoiceItemController::class, 'store'])->name('invoices.items.store');
    Route::put('/invoices/items/{item}', [InvoiceItemController::class, 'update'])->name('invoices.items.update');
    Route::delete('/invoices/items/{item}', [InvoiceItemController::class, 'destroy'])->name('invoices.items.destroy');


    /*
    |--------------------------------------------------------------------------
    | Clients
    |--------------------------------------------------------------------------
    */

    Route::resource('clients', ClientController::class)
        ->only(['index','create','store']);


    /*
    |--------------------------------------------------------------------------
    | Razorpay
    |--------------------------------------------------------------------------
    */

    Route::post('/payments/order', [PaymentController::class, 'createOrder'])
    ->middleware('throttle:10,1')
    ->name('payments.order');

Route::post('/payments/verify', [PaymentController::class, 'verify'])
    ->middleware('throttle:10,1')
    ->name('payments.verify');


    /*
    |--------------------------------------------------------------------------
    | Upgrade
    |--------------------------------------------------------------------------
    */

    Route::view('/upgrade', 'upgrade')->name('upgrade');

});

/*
|--------------------------------------------------------------------------
| Client (User) Routes   👈 👈 YAHAN PASTE KARO
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'client'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {

        Route::get('/dashboard', [ClientDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/invoices', [ClientInvoiceController::class, 'index'])
            ->name('invoices.index');

    });


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::post('/users/{user}/plan', [UserManagementController::class, 'updatePlan'])
            ->name('users.plan.update');

        Route::post('/users/bulk', [UserManagementController::class, 'bulk'])
            ->name('users.bulk');
    });


require __DIR__.'/auth.php';
