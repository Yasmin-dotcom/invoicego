<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-6 text-xl font-bold text-red-500">
            Invoice SaaS 🚀
        </div>

        <nav class="px-4 space-y-2">
            <a href="/dashboard" class="block px-4 py-2 rounded hover:bg-red-50">
                Dashboard
            </a>
            <a href="/invoices" class="block px-4 py-2 rounded hover:bg-red-50">
                Invoices
            </a>
            <a href="/clients" class="block px-4 py-2 rounded hover:bg-red-50">
                Clients
            </a>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>
</html>
