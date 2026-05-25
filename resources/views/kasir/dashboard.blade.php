@extends('kasir.layouts.app')

@section('title', 'POS - Pahlawan Kesorean')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kasir-dashboard.css') }}">
@endpush

@section('kasir-content')

<!-- Main POS View -->
<div id="posView" class="flex flex-col md:flex-row gap-2 md:gap-4 h-full">

    @include('kasir.partials.menu-grid')

    @include('kasir.partials.cart-sidebar')

</div>

<!-- Payment Method View - Full Page -->
@include('kasir.partials.payment-method')

<!-- Cash Payment View -->
@include('kasir.partials.cash-payment')

<!-- QRIS Payment View -->
@include('kasir.partials.qris-payment')


<!-- Payment Success View -->
@include('kasir.partials.payment-success')

@push('scripts')
    <script src="{{ asset('js/kasir-dashboard.js') }}"></script>
@endpush

@endsection