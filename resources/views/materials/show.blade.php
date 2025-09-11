@extends('layouts.app')

@section('content')
@php
  $previewSrc = route('materials.preview', $material); // secure inline endpoint
  $displayName = $material->original_file_name ?: ($material->title . '.pdf');
@endphp

<section class="max-w-5xl mx-auto px-4 py-8">
  <header class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <h1 class="text-3xl font-extrabold leading-tight">{{ $material->title }}</h1>
      <dl class="mt-1 text-sm text-gray-500">
        <div class="flex flex-wrap gap-x-3 gap-y-1">
          <dt class="sr-only">File</dt>
          <dd title="Original file name">{{ $displayName }}</dd>
          <span aria-hidden="true">•</span>
          <dt class="sr-only">Subject</dt>
          <dd>Subject: <span class="font-medium text-gray-700">{{ $material->subject_id }}</span></dd>
        </div>
      </dl>
    </div>

    <nav class="flex items-center gap-2">
      <a href="{{ route('materials.download', $material) }}"
         class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-4 py-2 text-white shadow hover:bg-black">
        Download
      </a>
      <a href="{{ route('materials.index') }}"
         class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-50">
        Back to list
      </a>
    </nav>
  </header>

  <article class="rounded-2xl border border-gray-200 bg-white shadow-sm">
    <iframe
      src="{{ $previewSrc }}"
      title="Preview of {{ $displayName }}"
      class="w-full h-[75vh]"
      loading="lazy"
      referrerpolicy="no-referrer"
    ></iframe>
  </article>
</section>
@endsection
