<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name','Laravel') }}</title>

  {{-- Google Icons + Materialize CSS --}}
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

  <style>
    body { background: #f5f5f5; }
    .container-narrow { max-width: 960px; }
    main { min-height: 70vh; }
  </style>
  @stack('styles')
</head>
<body>

  <nav class="blue">
    <div class="nav-wrapper container container-narrow">
      <a href="{{ url('/') }}" class="brand-logo">{{ config('app.name','Laravel') }}</a>
      <ul class="right hide-on-med-and-down">
        <li><a href="{{ route('materials.index') }}">Materials</a></li>
      </ul>
    </div>
  </nav>

  <main class="container container-narrow">
    @yield('content')
  </main>

  <footer class="page-footer blue">
    <div class="container container-narrow">
      <p class="grey-text text-lighten-4">Simple Materialize layout.</p>
    </div>
  </footer>

  {{-- Materialize JS --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tooltips = document.querySelectorAll('.tooltipped'); M.Tooltip.init(tooltips, {});
      M.AutoInit();
    });
  </script>
  @stack('scripts')
</body>
</html>
