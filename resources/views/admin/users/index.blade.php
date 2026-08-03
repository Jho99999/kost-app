@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')

{{-- Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div class="card">
        <div class="card-body flex items-center gap-3">

            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.964 0a9 9 0 1 0-11.964 0m11.964 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </div>

            <div>
                <p class="text-2xl font-bold">{{ $summary['users'] }}</p>
                <p class="text-xs text-gray-500">Total Pengguna</p>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body flex items-center gap-3">

            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">

                <svg class="w-5 h-5 text-green-500"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18"/>

                </svg>

            </div>

            <div>
                <p class="text-2xl font-bold text-green-700">
                    {{ $summary['active_booking'] }}
                </p>

                <p class="text-xs text-gray-500">
                    Sedang Menyewa
                </p>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body flex items-center gap-3">

            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">

                <svg class="w-5 h-5 text-red-500"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v3.75m0 3.75h.008"/>

                </svg>

            </div>

            <div>
                <p class="text-2xl font-bold text-red-700">
                    {{ $summary['ktp_missing'] }}
                </p>

                <p class="text-xs text-gray-500">
                    Belum Upload KTP
                </p>
            </div>

        </div>
    </div>

</div>


{{-- Filter --}}

<div class="card mb-5">

    <div class="card-body py-3">

        <form
            method="GET"
            action="{{ route('admin.users.index') }}"
            class="flex flex-wrap gap-2 items-center">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, email, atau nomor HP..."
                class="form-input flex-1 min-w-52">

            <select
                name="booking"
                class="form-select w-44">

                <option value="">Semua Booking</option>

                <option value="active"
                    @selected(request('booking')=='active')>

                    Sedang Menyewa

                </option>

                <option value="inactive"
                    @selected(request('booking')=='inactive')>

                    Tidak Menyewa

                </option>

            </select>

            <select
                name="ktp"
                class="form-select w-44">

                <option value="">Semua KTP</option>

                <option value="uploaded"
                    @selected(request('ktp')=='uploaded')>

                    Sudah Upload

                </option>

                <option value="empty"
                    @selected(request('ktp')=='empty')>

                    Belum Upload

                </option>

            </select>

            <button
                class="btn btn-primary btn-sm">

                Cari

            </button>

            @if(request()->hasAny(['search','booking','ktp']))

                <a
                    href="{{ route('admin.users.index') }}"
                    class="btn btn-secondary btn-sm">

                    Reset

                </a>

            @endif

        </form>

    </div>

</div>


{{-- Table --}}

<div class="card">

<div class="overflow-x-auto">

<table class="table-base">

<thead class="table-thead">

<tr>

<th class="table-th">Pengguna</th>

<th class="table-th">Kontak</th>

<th class="table-th">KTP</th>

<th class="table-th">Status</th>

<th class="table-th"></th>

</tr>

</thead>

<tbody class="bg-white divide-y divide-gray-100">

@forelse($users as $user)

<tr class="table-tr">

<td class="table-td">

<div class="flex items-center gap-3">

<img
    src="{{ $user->avatar_url }}"
    class="w-10 h-10 rounded-full object-cover">

<div>

<p class="font-medium text-gray-900">

{{ $user->name }}

</p>

<p class="text-xs text-gray-500">

{{ $user->email }}

</p>

</div>

</div>

</td>

<td class="table-td">

{{ $user->phone ?: '-' }}

</td>

<td class="table-td">

@if($user->ktp_image)

<span class="badge badge-green">

Sudah Upload

</span>

@else

<span class="badge badge-red">

Belum Upload

</span>

@endif

</td>

<td class="table-td">

@if($user->activeBooking)

<span class="badge badge-blue">

{{ $user->activeBooking->room->name }}

</span>

@else

<span class="badge badge-gray">

Tidak Menyewa

</span>

@endif

</td>

<td class="table-td">

<a
    href="{{ route('admin.users.show',$user) }}"
    class="text-blue-600 hover:text-blue-800 font-medium">

    Lihat

</a>

</td>

</tr>

@empty

<tr>

<td colspan="5"
    class="text-center py-12 text-gray-400">

Belum ada pengguna.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

@if($users->hasPages())

<div class="card-footer">

{{ $users->links() }}

</div>

@endif

</div>

@endsection