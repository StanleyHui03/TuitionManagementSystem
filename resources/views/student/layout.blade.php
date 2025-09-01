<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student · @yield('title','')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <style>
    :root { --bg:#0f172a; --card:#111827; --muted:#94a3b8; --text:#e5e7eb; --brand:#60a5fa; --ok:#22c55e; --warn:#ef4444; }
    *{box-sizing:border-box} body{margin:0;font:14px/1.5 system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial; background:var(--bg); color:var(--text);}
    a{color:var(--brand);text-decoration:none} a:hover{text-decoration:underline}
    .container{max-width:980px;margin:24px auto;padding:0 16px}
    .navbar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:16px}
    .navlink{padding:8px 12px;border-radius:8px;background:#0b1220;border:1px solid #1f2937}
    .navlink.active{border-color:var(--brand); box-shadow:0 0 0 1px color-mix(in srgb, var(--brand) 60%, transparent)}
    .card{background:var(--card);border:1px solid #1f2937;border-radius:12px;padding:16px}
    .h1{font-size:22px;margin:0 0 8px}
    .muted{color:var(--muted)}
    .grid{display:grid;gap:12px}
    .table{width:100%;border-collapse:collapse}
    .table th,.table td{padding:10px;border-bottom:1px solid #1f2937;text-align:left}
    .btn{display:inline-block;padding:8px 12px;border-radius:8px;border:1px solid #1f2937;background:#0b1220;color:var(--text)}
    .btn.primary{background:var(--brand);border-color:var(--brand);color:#0b1020}
    .btn.danger{background:#1b0b0b;border-color:#311010;color:#fecaca}
    .flex{display:flex;gap:8px;align-items:center}
    .right{margin-left:auto}
    .flash{padding:10px 12px;border-radius:8px;margin-bottom:12px}
    .flash.ok{background:#052e1a;border:1px solid #14532d;color:#bbf7d0}
    .flash.err{background:#2b0b0b;border:1px solid #7f1d1d;color:#fecaca}
    .input{width:100%;padding:10px;border-radius:8px;border:1px solid #1f2937;background:#0b1220;color:var(--text)}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .hint{font-size:12px;color:var(--muted)}
    .empty{padding:18px;border:1px dashed #334155;border-radius:10px;text-align:center;color:#9ca3af}
  </style>
  @stack('head')
</head>
<body>
  <div class="container">
    {{-- NAV --}}
    <div class="navbar card">
      <div class="flex">
        <strong>Student:</strong>
        <span class="muted">{{ $student->studentName }} <span class="muted">({{ $student->student_id }})</span></span>
      </div>

      @php
        $base = '/student/'.$student->student_id;
        $is = fn($path) => request()->is(ltrim($path,'/'));
      @endphp

      <a class="navlink {{ $is("$base/dashboard") ? 'active' : '' }}" href="{{ $base }}/dashboard">Dashboard</a>
      <a class="navlink {{ $is("$base/profile") ? 'active' : '' }}" href="{{ $base }}/profile">Profile</a>
      <a class="navlink {{ $is("$base/classes") ? 'active' : '' }}" href="{{ $base }}/classes">Classes</a>
      <a class="navlink {{ $is("$base/payments") ? 'active' : '' }}" href="{{ $base }}/payments">Payments</a>
      <a class="navlink {{ $is("$base/receipts") ? 'active' : '' }}" href="{{ $base }}/receipts">Receipts</a>
      <a class="navlink right" href="{{ url('/') }}">Home</a>
    </div>

    {{-- FLASH --}}
    @if (session('status'))
      <div class="flash ok">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="flash err">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="flash err">
        <strong>Fix the following:</strong>
        <ul style="margin:6px 0 0 18px">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    {{-- PAGE CONTENT --}}
    <div class="card grid">
      <h1 class="h1">@yield('title')</h1>
      @yield('content')
    </div>
  </div>
  @stack('scripts')
</body>
</html>
