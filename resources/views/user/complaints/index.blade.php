@extends('layouts.app')
@section('title', 'Aduan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold text-gray-900">Aduan Saya</h1>
      <p class="text-sm text-gray-500 mt-0.5">Laporan kerusakan atau keluhan kamar</p>
    </div>
    <a href="{{ route('complaints.create') }}"
       class="btn-primary">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
      </svg>
      Buat Aduan
    </a>
  </div>

  @if($complaints->isEmpty())
    <div class="text-center py-20 text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
      </svg>
      <p>Belum ada aduan. Jika ada kerusakan, silakan buat aduan.</p>
    </div>
  @else
    <div class="space-y-3">
      @foreach($complaints as $c)
      <a href="{{ route('complaints.show', $c) }}"
         class="block bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-100 transition-all p-4">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <span class="{{ $c->status_color }}">{{ $c->status_label }}</span>
              <span class="text-xs text-gray-400">{{ $c->created_at->locale('id')->isoFormat('D MMM YYYY') }}</span>
            </div>
            <p class="font-semibold text-gray-900">{{ $c->title }}</p>
            <p class="text-sm text-gray-500 mt-0.5 line-clamp-2">{{ $c->description }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $c->room->name }}</p>
          </div>
          @if($c->image)
            <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
              <img src="{{ $c->image_url }}" alt="" class="w-full h-full object-cover">
            </div>
          @endif
        </div>
      </a>
      @endforeach
    </div>

    @if($complaints->hasPages())
      <div class="mt-6">{{ $complaints->links() }}</div>
    @endif
  @endif

</div>
@endsection
