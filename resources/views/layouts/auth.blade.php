<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
@include('layouts.partials.head')

<body class="min-h-screen">
  <div class="grid min-h-screen lg:grid-cols-2">

    {{-- Brand panel --}}
    <aside class="relative hidden overflow-hidden lg:block">
      @if ($banner ?? null)
        <img src="{{ $banner }}" alt="" class="absolute inset-0 size-full object-cover">
      @endif
      <div class="absolute inset-0 bg-neutral-950"></div>
      @if ($banner ?? null)
        <img src="{{ $banner }}" alt="" class="absolute inset-0 size-full object-cover opacity-50">
      @endif
      <div class="absolute inset-0 bg-gradient-to-br from-brand/50 via-neutral-950/70 to-neutral-950"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 via-transparent to-neutral-950/40"></div>

      <div class="relative z-10 flex h-full flex-col justify-between p-12 xl:p-16">
        <a href="{{ route('home') }}" class="text-white"><x-ui.logo class="!text-white" /></a>
        <div class="max-w-md">
          <h2 class="text-4xl font-bold leading-tight text-white xl:text-5xl">Watch anywhere.<br><span class="text-gradient">Stream everything.</span></h2>
          <p class="mt-4 text-base leading-relaxed text-white/70">Movies, series and trailers — all in one place, on every screen you own.</p>
          <ul class="mt-8 space-y-3 text-sm text-white/80">
            <li class="flex items-center gap-3"><x-ui.icon name="check-circle" class="size-5 text-brand" /> Adaptive HD streaming</li>
            <li class="flex items-center gap-3"><x-ui.icon name="check-circle" class="size-5 text-brand" /> Watch parties with live chat</li>
            <li class="flex items-center gap-3"><x-ui.icon name="check-circle" class="size-5 text-brand" /> Profiles for the whole family</li>
          </ul>
        </div>
      </div>
    </aside>

    {{-- Form panel --}}
    <section class="relative flex flex-col">
      <div class="flex items-center justify-between p-5 sm:p-8">
        <a href="{{ route('home') }}" class="lg:invisible"><x-ui.logo /></a>
        <button type="button" onclick="toggleTheme()" class="btn btn-ghost btn-icon" aria-label="Toggle theme">
          <x-ui.icon name="sun" class="hidden dark:block" /><x-ui.icon name="moon" class="dark:hidden" />
        </button>
      </div>

      <div class="flex flex-1 items-center justify-center px-5 pb-12 sm:px-8">
        <div class="w-full max-w-md animate-fade-up">
          @yield('content')
        </div>
      </div>
    </section>
  </div>

  <script>
    function toggleTheme() {
      var dark = !document.documentElement.classList.contains('dark');
      document.documentElement.classList.toggle('dark', dark);
      try { localStorage.setItem('str1-color-theme', dark ? 'dark' : 'light'); } catch (e) {}
    }
  </script>
  @stack('scripts')
</body>
</html>
