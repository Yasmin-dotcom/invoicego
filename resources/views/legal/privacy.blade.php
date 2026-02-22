<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - InvoiceGo</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800">
    <main class="max-w-3xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Privacy Policy</h1>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
            <ul class="space-y-3 text-slate-700 leading-relaxed">
                <li>We collect name, email, and phone only for invoicing.</li>
                <li>Payments are processed by Razorpay.</li>
                <li>We do not sell user data.</li>
                <li>Contact: <a class="text-indigo-600 hover:text-indigo-700" href="mailto:support@invoicego.in">support@invoicego.in</a></li>
                <li>We store billing data securely.</li>
                <li>Users can request account deletion anytime.</li>
                <li>Data is used only for invoicing purposes.</li>
            </ul>
        </div>
    </main>
</body>
</html>
