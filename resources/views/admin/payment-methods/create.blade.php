@extends('layouts.admin')

@section('title','Tambah Metode Pembayaran')
@section('page-title','Tambah Metode Pembayaran')

@section('content')

<form
    action="{{ route('admin.payment-methods.store') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf

    @include('admin.payment-methods._form')

</form>

@endsection