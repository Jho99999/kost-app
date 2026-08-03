@extends('layouts.admin')

@section('title','Edit Metode Pembayaran')
@section('page-title','Edit Metode Pembayaran')

@section('content')

<form
    action="{{ route('admin.payment-methods.update',$paymentMethod) }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @method('PUT')

    @include('admin.payment-methods._form')

</form>

@endsection