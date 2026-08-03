@extends('layouts.admin')

@section('title','Metode Pembayaran')
@section('page-title','Metode Pembayaran')

@section('content')

<div class="flex items-center justify-between mb-6">

    <div>

        <h2 class="text-lg font-semibold text-gray-800">
            Daftar Metode Pembayaran
        </h2>

        <p class="text-sm text-gray-500">
            Kelola seluruh metode pembayaran yang tersedia.
        </p>

    </div>

    <a
        href="{{ route('admin.payment-methods.create') }}"
        class="btn btn-primary">

        Tambah Metode

    </a>

</div>

<div class="card">

    <div class="overflow-x-auto">

        <table class="table-base">

            <thead class="table-thead">

            <tr>

                <th class="table-th">
                    Nama
                </th>

                <th class="table-th">
                    Jenis
                </th>

                <th class="table-th">
                    Informasi
                </th>

                <th class="table-th text-center">
                    Status
                </th>

                <th class="table-th text-right">
                    Aksi
                </th>

            </tr>

            </thead>

            <tbody class="bg-white divide-y divide-gray-100">

            @forelse($methods as $method)

                <tr class="table-tr">

                    <td class="table-td">

                        <div class="font-medium text-gray-800">
                            {{ $method->name }}
                        </div>

                    </td>

                    <td class="table-td">

                        @switch($method->type)

                            @case('bank')

                                <span class="badge badge-green">
                                    Transfer Bank
                                </span>

                            @break

                            @case('qris')

                                <span class="badge badge-blue">
                                    QRIS
                                </span>

                            @break

                            @case('ewallet')

                                <span class="badge badge-yellow">
                                    E-Wallet
                                </span>

                            @break

                        @endswitch

                    </td>

                    <td class="table-td">

                        @if($method->type=='qris')

                            @if($method->image)

                                <img
                                    src="{{ asset('storage/'.$method->image) }}"
                                    class="w-20 rounded border">

                            @else

                                <span class="text-gray-400 text-sm">
                                    Belum ada QR
                                </span>

                            @endif

                        @else

                            <div>

                                <div class="text-sm">

                                    {{ $method->account_number }}

                                </div>

                                <div class="text-xs text-gray-500">

                                    {{ $method->account_name }}

                                </div>

                            </div>

                        @endif

                    </td>

                    <td class="table-td text-center">

                        @if($method->is_active)

                            <span class="badge badge-green">
                                Aktif
                            </span>

                        @else

                            <span class="badge badge-red">
                                Nonaktif
                            </span>

                        @endif

                    </td>

                    <td class="table-td text-right">

                        <div class="flex justify-end gap-2">

                            <a
                                href="{{ route('admin.payment-methods.edit',$method) }}"
                                class="btn btn-secondary btn-sm">

                                Edit

                            </a>

                            <form
                                action="{{ route('admin.payment-methods.destroy',$method) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus metode pembayaran ini?')">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm">

                                    Hapus

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5"
                        class="py-10 text-center text-gray-400">

                        Belum ada metode pembayaran.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    @if($methods->hasPages())

        <div class="card-footer">

            {{ $methods->links() }}

        </div>

    @endif

</div>

@endsection