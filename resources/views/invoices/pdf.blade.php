@php
    $template = $invoice->template_name ?? 'classic';
@endphp

@include("invoices.templates.$template")
