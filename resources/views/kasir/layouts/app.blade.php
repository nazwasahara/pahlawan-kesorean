@extends('layouts.app')

@section('content')
    @include('kasir.partials.navbar')
    <div class="flex h-[calc(100vh-7rem)] md:h-[calc(100vh-6rem)]">
        @include('kasir.partials.sidebar')

        <div class="flex-1 overflow-y-auto p-2 md:p-3 lg:p-6 bg-[#FBF9F8] w-full">
            @yield('kasir-content')
        </div>
    </div>
@endsection
