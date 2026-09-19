<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Stream movies and series anywhere, anytime.">
  <title>{{ config('app.name') }} — @yield('title')</title>

  <link rel="shortcut icon" href="{{ asset('assets/img/str1/favicons/favicon.ico') }}">
  <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/str1/favicons/favicon-152.png') }}">
  <meta name="theme-color" content="#0b0b12">

  {{-- Apply the saved theme before first paint --}}
  <script>
    (function () {
      try {
        var t = localStorage.getItem('str1-color-theme');
        document.documentElement.classList.toggle('dark', t !== 'light');
      } catch (e) {}
    })();
  </script>

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
</head>
