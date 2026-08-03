@extends('layouts.admin')

@section('title','Detail Pengguna')
@section('page-title','Detail Pengguna')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="card">

        <div class="card-body flex items-center gap-5">

            <img
                src="{{ $user->avatar_url }}"
                class="w-24 h-24 rounded-full object-cover border">

            <div class="flex-1">

                <h2 class="text-xl font-semibold">

                    {{ $user->name }}

                </h2>

                <p class="text-gray-500">

                    {{ $user->email }}

                </p>

                <p class="text-gray-500">

                    {{ $user->phone ?: '-' }}

                </p>

                <div class="mt-3 flex gap-2">

                    @if($user->activeBooking)

                        <span class="badge badge-blue">

                            Sedang Menyewa

                        </span>

                    @else

                        <span class="badge badge-gray">

                            Tidak Menyewa

                        </span>

                    @endif

                    @if($user->ktp_image)

                        <span class="badge badge-green">

                            KTP Lengkap

                        </span>

                    @else

                        <span class="badge badge-red">

                            KTP Belum Upload

                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>
    <div class="grid lg:grid-cols-2 gap-6">

    <div class="card">

        <div class="card-header">

            <h3 class="font-semibold">

                Biodata

            </h3>

        </div>

        <div class="card-body space-y-3">

            <div class="flex justify-between">

                <span>Jenis Kelamin</span>

                <span>{{ $user->gender ?: '-' }}</span>

            </div>

            <div class="flex justify-between">

                <span>Agama</span>

                <span>{{ $user->religion ?: '-' }}</span>

            </div>

            <div class="flex justify-between">

                <span>Pekerjaan</span>

                <span>{{ $user->occupation ?: '-' }}</span>

            </div>

            <div class="flex justify-between">

                <span>No KTP</span>

                <span>{{ $user->id_card ?: '-' }}</span>

            </div>

            <div class="flex justify-between">

                <span>Bergabung</span>

                <span>{{ $user->created_at->format('d M Y') }}</span>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="card-header">

            <h3 class="font-semibold">

                Foto KTP

            </h3>

        </div>

        <div class="card-body">

            @if($user->ktp_image)

                <a href="{{ asset('storage/'.$user->ktp_image) }}"
                   target="_blank">

                    <img
                        src="{{ asset('storage/'.$user->ktp_image) }}"
                        class="rounded-lg border">

                </a>

            @else

                <div class="text-center py-12 text-gray-400">

                    Belum upload KTP

                </div>

            @endif

        </div>

    </div>

</div>

<div class="card">

<div class="card-header">

<h3 class="font-semibold">

Booking Aktif

</h3>

</div>

<div class="card-body">

@if($user->activeBooking)

<div class="grid md:grid-cols-4 gap-4">

<div>

<p class="text-xs text-gray-500">

Kamar

</p>

<p>

{{ $user->activeBooking->room->name }}

</p>

</div>

<div>

<p class="text-xs text-gray-500">

Tanggal Masuk

</p>

<p>

{{ $user->activeBooking->check_in_date }}

</p>

</div>

<div>

<p class="text-xs text-gray-500">

Tanggal Keluar

</p>

<p>

{{ $user->activeBooking->check_out_date }}

</p>

</div>

<div>

<p class="text-xs text-gray-500">

Status

</p>

<span class="badge badge-green">

{{ ucfirst($user->activeBooking->status) }}

</span>

</div>

</div>

@else

<div class="text-center py-10 text-gray-400">

Pengguna belum memiliki booking aktif.

</div>

@endif

</div>

</div>
<div class="card">

<div class="card-header">

<h3 class="font-semibold">

5 Pembayaran Terakhir

</h3>

</div>

<div class="overflow-x-auto">

<table class="table-base">

<thead class="table-thead">

<tr>

<th class="table-th">

Kode

</th>

<th class="table-th">

Nominal

</th>

<th class="table-th">

Jatuh Tempo

</th>

<th class="table-th">

Status

</th>

</tr>

</thead>

<tbody>

@forelse($payments as $payment)

<tr>

<td class="table-td">

{{ $payment->payment_code }}

</td>

<td class="table-td">

Rp {{ number_format($payment->amount,0,',','.') }}

</td>

<td class="table-td">

{{ $payment->due_date }}

</td>

<td class="table-td">

{{ ucfirst($payment->status) }}

</td>

</tr>

@empty

<tr>

<td colspan="4"
class="text-center py-8 text-gray-400">

Belum ada pembayaran.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

<div class="card">

<div class="card-header">

<h3 class="font-semibold">

Riwayat Aduan

</h3>

</div>

<div class="overflow-x-auto">

<table class="table-base">

<thead class="table-thead">

<tr>

<th class="table-th">

Tanggal

</th>

<th class="table-th">

Judul

</th>

<th class="table-th">

Status

</th>

</tr>

</thead>

<tbody>

@forelse($complaints as $complaint)

<tr>

<td class="table-td">

{{ $complaint->created_at->format('d M Y') }}

</td>

<td class="table-td">

{{ $complaint->title }}

</td>

<td class="table-td">

{{ ucfirst($complaint->status) }}

</td>

</tr>

@empty

<tr>

<td colspan="3"
class="text-center py-8 text-gray-400">

Belum ada aduan.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

<div class="flex justify-end">

<a
href="{{ route('admin.users.index') }}"
class="btn btn-secondary">

Kembali

</a>

</div>

</div>

@endsection