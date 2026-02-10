@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500">Total Invoices</p>
            <p class="text-2xl font-bold">0</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500">Total Clients</p>
            <p class="text-2xl font-bold">0</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500">Revenue</p>
            <p class="text-2xl font-bold">₹0</p>
        </div>
    </div>
@endsection
