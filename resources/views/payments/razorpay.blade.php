<!DOCTYPE html>
<html>
<head>
    <title>Pay Invoice</title>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="font-family: Arial; text-align:center; padding-top:60px;">

<h2>Redirecting to secure payment...</h2>
<p>Please wait...</p>

<script>
window.onload = function () {

    let options = {
        key: "{{ config('services.razorpay.key') }}",

        amount: {{ $invoice->total * 100 }},
        currency: "INR",

        name: "Invoice SaaS",
        description: "Invoice #{{ $invoice->invoice_number }}",

        handler: function (response) {

            // ✅ call backend
            fetch("{{ route('payment.success.post') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                },
                body: JSON.stringify({
                    invoice_id: "{{ $invoice->id }}",
                    razorpay_payment_id: response.razorpay_payment_id
                })
            })
            // ✅ NO res.json() (VERY IMPORTANT)
            .then(() => {
                window.location.href = "{{ route('invoices.index') }}";
            });

        },

        prefill: {
            name: "{{ auth()->user()->name ?? 'Customer' }}",
            email: "{{ auth()->user()->email ?? '' }}"
        },

        theme: {
            color: "#16a34a"
        }
    };

    new Razorpay(options).open();
};
</script>

</body>
</html>
