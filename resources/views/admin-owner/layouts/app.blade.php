@extends('layouts.app')

@section('content')
<div class="h-screen flex flex-col overflow-hidden">
    @include('admin-owner.partials.navbar')

    <div class="flex flex-1 min-h-0">
        @include('admin-owner.partials.sidebar')

        <main class="flex-1 overflow-y-auto bg-[#FDFBF7] p-6 md:p-8">
            @yield('admin-owner-content')
        </main>
    </div>
</div>
@endsection
