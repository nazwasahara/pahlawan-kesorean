@extends('admin-owner.layouts.app')

@section('title', 'Log Aktivitas - Pahlawan Kesorean')

@section('navbar-title', 'LOG AKTIVITAS')

@section('admin-owner-content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Log Aktivitas</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh log aktivitas dalam sistem.</p>
        </div>
        
        <!-- Filter Section -->
        <div>
            <form action="{{ route('admin-owner.logs.index') }}" method="GET" class="flex items-center gap-3">
                <div class="relative">
                    <input type="date" 
                           name="date" 
                           value="{{ $date }}"
                           onchange="this.form.submit()"
                           class="bg-white border border-gray-350 rounded-full py-2 px-5 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] cursor-pointer">
                </div>
                @if($date)
                    <a href="{{ route('admin-owner.logs.index') }}" 
                       class="text-xs font-black text-red-600 hover:text-red-800 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Data Table Card Container -->
    <div class="bg-white border border-gray-200/80 rounded-[2.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-[#F2F2F2] border-b border-gray-200 text-xs font-extrabold text-gray-900 tracking-wider">
                        <th class="py-3 px-4 border border-gray-200 w-32">ID User</th>
                        <th class="py-3 px-4 border border-gray-200 w-48">Aksi</th>
                        <th class="py-3 px-4 border border-gray-200">Keterangan</th>
                        <th class="py-3 px-4 border border-gray-200 w-52 text-center">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-xs font-bold text-gray-800 bg-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- ID User (Role / Identity) -->
                            <td class="py-3 px-4 border border-gray-200 font-extrabold text-gray-900">
                                {{ $log->user_identity }}
                            </td>
                            
                            <!-- Aksi -->
                            <td class="py-3 px-4 border border-gray-200 text-gray-800 font-extrabold">
                                {{ $log->action }}
                            </td>
                            
                            <!-- Keterangan -->
                            <td class="py-3 px-4 border border-gray-200 text-gray-700 font-bold">
                                {{ $log->description }}
                            </td>
                            
                            <!-- Waktu -->
                            <td class="py-3 px-4 border border-gray-200 text-center text-gray-500 font-bold">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold border border-gray-200">
                                Tidak ada data log aktivitas ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($logs->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4 rounded-b-[2rem]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
