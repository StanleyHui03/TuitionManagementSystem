@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="materialsPage()">

  {{-- Header + Upload --}}
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-bold tracking-tight">Teaching Materials</h1>
      <p class="text-gray-500 text-sm mt-1">Preview, download, and manage uploaded PDFs.</p>
    </div>

    @auth
    <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data"
          class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-3 shadow-sm">
      @csrf

      <input type="text" name="title" placeholder="Title" value="{{ old('title') }}"
             class="w-full sm:w-48 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
             required>

      {{-- Subject dropdown --}}
      <select name="subject_id"
              class="w-full sm:w-56 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
              required>
        <option value="" disabled {{ old('subject_id') ? '' : 'selected' }}>Choose Subject</option>
        @foreach($subjects as $s)
          <option value="{{ $s->subject_id }}" {{ old('subject_id') == $s->subject_id ? 'selected' : '' }}>
            {{ $s->subject_id }} — {{ $s->subject_Name }}
          </option>
        @endforeach
      </select>

      <input type="file" name="file" accept="application/pdf"
             class="w-full sm:w-auto file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white
                    hover:file:bg-blue-700 cursor-pointer text-sm"
             required>

      <button type="submit"
              class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white px-4 py-2 font-medium hover:bg-blue-700 shadow">
        Upload
      </button>
    </form>
    @endauth
  </div>

  {{-- Validation / Flash --}}
  @if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
      {{ session('success') }}
    </div>
  @endif

  {{-- TABLE --}}
  <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800/60">
          <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">Subject</th>
            <th class="px-4 py-3 w-56">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($materials as $m)
            @php
              $previewUrl = route('materials.preview', $m);
            @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 cursor-pointer"
                @click="select('{{ $previewUrl }}', '{{ e($m->title) }}')">
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $m->title }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $m->material_id }}</div>
              </td>
              <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $m->subject_id }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <a href="{{ route('materials.show', $m) }}"
                     class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                    Preview Page
                  </a>
                  <a href="{{ route('materials.download', $m) }}"
                     class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 text-white px-3 py-1.5 text-sm hover:bg-black">
                    Download
                  </a>
                  @auth
                  <form action="{{ route('materials.destroy', $m) }}" method="POST"
                        onsubmit="return confirm('Delete this material?');">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 dark:border-red-800 px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10">
                      Delete
                    </button>
                  </form>
                  @endauth
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                No materials yet. @auth Upload your first PDF above. @endauth
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($materials->hasPages())
      <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
        {{ $materials->links() }}
      </div>
    @endif
  </div>

  {{-- PREVIEW PANEL (inline iframe via secure route) --}}
  <div class="mt-6">
    <div class="mb-2 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        <span x-text="selectedTitle || 'Preview'"></span>
      </h2>
      <template x-if="selectedSrc">
        <a :href="selectedSrc" class="text-sm underline text-blue-600 hover:text-blue-700" target="_blank" rel="noreferrer">
          Open in new tab
        </a>
      </template>
    </div>

    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
      <template x-if="selectedSrc">
        <iframe :src="selectedSrc" class="w-full h-[70vh]" title="PDF preview"></iframe>
      </template>
      <template x-if="!selectedSrc">
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
          Select a row above to preview the file here.
        </div>
      </template>
    </div>
  </div>
</div>

{{-- Alpine.js --}}
<script>
  function materialsPage() {
    return {
      selectedSrc: null,
      selectedTitle: null,
      select(src, title) {
        this.selectedSrc = src;
        this.selectedTitle = title;
      }
    }
  }
</script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection