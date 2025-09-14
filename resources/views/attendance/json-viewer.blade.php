@extends('layouts.app')

@section('title', 'Lesson Attendance (JSON Viewer)')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">
        Lesson Attendance — {{ $lesson->lesson_id }}
    </h1>

    {{-- Controls --}}
    <div class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label for="sessionDate" class="block text-sm font-medium mb-1">Session Date</label>
            <input id="sessionDate" type="date" value="{{ $defaultDate }}"
                   class="border rounded px-3 py-2">
        </div>

        <button id="loadBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Load Attendance
        </button>

        <span id="statusBadge" class="text-sm text-gray-500"></span>
    </div>

    {{-- Summary --}}
    <div id="summary" class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
        <div class="border rounded p-3"><div class="text-xs text-gray-500">Present</div><div id="sumPresent" class="text-xl font-semibold">—</div></div>
        <div class="border rounded p-3"><div class="text-xs text-gray-500">Absent</div><div id="sumAbsent" class="text-xl font-semibold">—</div></div>
        <div class="border rounded p-3"><div class="text-xs text-gray-500">Late</div><div id="sumLate" class="text-xl font-semibold">—</div></div>
        <div class="border rounded p-3"><div class="text-xs text-gray-500">Excused</div><div id="sumExcused" class="text-xl font-semibold">—</div></div>
        <div class="border rounded p-3"><div class="text-xs text-gray-500">Pending</div><div id="sumPending" class="text-xl font-semibold">—</div></div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left px-4 py-2">Student ID</th>
                    <th class="text-left px-4 py-2">Status</th>
                    <th class="text-left px-4 py-2">Note</th>
                    <th class="text-left px-4 py-2">Session Date</th>
                    <th class="text-left px-4 py-2">Marked By</th>
                    <th class="text-left px-4 py-2">Updated</th>
                </tr>
            </thead>
            <tbody id="rows">
                <tr><td colspan="6" class="px-4 py-4 text-gray-500">No data loaded yet.</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
  const lessonId   = @json($lesson->lesson_id);
  const loadBtn    = document.getElementById('loadBtn');
  const dateInput  = document.getElementById('sessionDate');
  const statusText = document.getElementById('statusBadge');
  const rowsEl     = document.getElementById('rows');

  const sumEls = {
    present:  document.getElementById('sumPresent'),
    absent:   document.getElementById('sumAbsent'),
    late:     document.getElementById('sumLate'),
    excused:  document.getElementById('sumExcused'),
    pending:  document.getElementById('sumPending'),
  };

  function setStatus(msg){ statusText.textContent = msg || ''; }
  function escapeHtml(s){ const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

  function renderSummary(records){
    const c = {present:0,absent:0,late:0,excused:0,pending:0};
    for(const r of records){ const s=(r.status||'').toLowerCase(); if(c[s]!=null) c[s]++; }
    sumEls.present.textContent=c.present; sumEls.absent.textContent=c.absent;
    sumEls.late.textContent=c.late; sumEls.excused.textContent=c.excused; sumEls.pending.textContent=c.pending;
  }

  function renderTable(records){
    if(!records.length){
      rowsEl.innerHTML = `<tr><td colspan="6" class="px-4 py-4 text-gray-500">No records.</td></tr>`;
      return;
    }
    rowsEl.innerHTML = records.map(r => `
      <tr class="border-t">
        <td class="px-4 py-2">${r.student_id ?? '-'}</td>
        <td class="px-4 py-2 capitalize">${r.status ?? '-'}</td>
        <td class="px-4 py-2">${r.note ? escapeHtml(r.note) : '-'}</td>
        <td class="px-4 py-2">${r.session_date ?? '-'}</td>
        <td class="px-4 py-2">${r.marked_by_tutor_id ?? '-'}</td>
        <td class="px-4 py-2">${r.updated_at ?? r.marked_at ?? '-'}</td>
      </tr>
    `).join('');
  }

  // Debug box to show raw server response if not JSON
  const debugEl = document.createElement('pre');
  debugEl.style.cssText = 'margin-top:12px;color:#b91c1c;background:#fee2e2;padding:8px;border-radius:8px;display:none;';
  rowsEl.parentElement.after(debugEl);

  async function loadData() {
    const date = dateInput.value;
    const base = @json(url('/api/v1')); // robust base
    const url  = `${base}/lessons/${encodeURIComponent(lessonId)}/attendance${date ? `?session_date=${encodeURIComponent(date)}` : ''}`;

    setStatus('Loading…');
    debugEl.style.display = 'none';
    debugEl.textContent = '';

    try {
      const res = await fetch(url, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      });

      const text = await res.text(); // read once
      const isJson = (res.headers.get('content-type') || '').includes('application/json');
      const data = isJson ? JSON.parse(text) : null;

      if (!res.ok || !data || data.status !== 'success') {
        // show raw response to help debug (403/404/login HTML)
        debugEl.style.display = 'block';
        debugEl.textContent = text || 'No response text.';
        throw new Error((data && data.message) || `HTTP ${res.status}`);
      }

      renderSummary(data.attendance || []);
      renderTable(data.attendance || []);
      setStatus(`Loaded (${data.count ?? (data.attendance||[]).length})`);
    } catch (err) {
      console.error(err);
      setStatus('Error loading data');
      rowsEl.innerHTML = `<tr><td colspan="6" class="px-4 py-4 text-red-600">${(err && err.message) || 'Error'}</td></tr>`;
    }
  }

  loadBtn.addEventListener('click', loadData);
  loadData(); // auto-load
})();
</script>


@endsection