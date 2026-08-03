@extends('layouts.app')
@section('title', $complaint->title)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('complaints.index') }}" class="hover:text-gray-600">Aduan</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Detail</span>
  </nav>

  {{-- Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-xl font-bold text-gray-900">{{ $complaint->title }}</h1>
    <span class="{{ $complaint->status_color }} text-sm px-3 py-1">{{ $complaint->status_label }}</span>
  </div>

  {{-- Info --}}
  <div class="card mb-5">
    <div class="card-body space-y-3 text-sm">
      <div class="flex justify-between">
        <span class="text-gray-500">Kamar</span>
        <span class="font-medium text-gray-800">{{ $complaint->room->name }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Tanggal</span>
        <span class="text-gray-800">{{ $complaint->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Status</span>
        <span class="{{ $complaint->status_color }}">{{ $complaint->status_label }}</span>
      </div>
      @if($complaint->resolved_at)
      <div class="flex justify-between">
        <span class="text-gray-500">Selesai</span>
        <span class="text-green-700">{{ $complaint->resolved_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
      </div>
      @endif
    </div>
  </div>

  {{-- Deskripsi --}}
  <div class="card mb-5">
    <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Deskripsi Aduan</h3></div>
    <div class="card-body">
      <p class="text-sm text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
    </div>
  </div>

  {{-- Foto --}}
  @if($complaint->image)
  <div class="card mb-5">
    <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Foto Bukti</h3></div>
    <div class="card-body">
      <a href="{{ $complaint->image_url }}" target="_blank"
         class="block rounded-xl overflow-hidden border border-gray-100 hover:opacity-90 transition">
        <img src="{{ $complaint->image_url }}" alt="Foto aduan" class="w-full max-h-80 object-contain bg-gray-50">
      </a>
    </div>
  </div>
  @endif

  {{-- Respon Admin --}}
  @if($complaint->admin_notes)
  <div class="card border-blue-200 bg-blue-50">
    <div class="card-header">
      <h3 class="text-sm font-semibold text-blue-800">Respon Admin</h3>
    </div>
    <div class="card-body">
      <p class="text-sm text-blue-900 whitespace-pre-line">{{ $complaint->admin_notes }}</p>
    </div>
  </div>
  @endif

  <a href="{{ route('complaints.index') }}"
     class="back-link mt-5">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
    </svg>
    Kembali ke daftar aduan
  </a>

</div>
@endsection
