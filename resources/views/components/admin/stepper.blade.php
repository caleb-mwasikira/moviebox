{{--
  Progress stepper header. Must sit inside an element using x-data="stepper(count, start)".
  $steps: list of labels. $done (optional): booleans marking steps that are already complete.
--}}
@props(['steps', 'done' => []])

<nav aria-label="Progress" class="card card-pad lg:sticky lg:top-24">
  <div class="mb-4 flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-muted">
    <span>Step <span class="text-fg" x-text="step + 1"></span> of {{ count($steps) }}</span>
    <span class="text-brand" x-text="Math.round(((step + 1) / {{ count($steps) }}) * 100) + '%'"></span>
  </div>
  <div class="mb-5 hidden h-1.5 overflow-hidden rounded-full bg-surface-3 lg:block"><div class="h-full rounded-full bg-brand transition-all duration-300" :style="'width:' + Math.round(((step + 1) / {{ count($steps) }}) * 100) + '%'"></div></div>

  {{-- Horizontal on small screens, vertical (left rail) from lg up --}}
  <ol class="flex items-start lg:flex-col lg:items-stretch">
    @foreach ($steps as $i => $label)
      @php $doneExpr = !empty($done[$i]) ? 'true' : 'false'; @endphp
      <li class="relative flex-1 text-center lg:flex-none lg:pb-7 lg:text-left {{ $loop->last ? 'lg:pb-0' : '' }}">
        @unless ($loop->last)
          <span class="absolute left-1/2 top-[1.125rem] h-0.5 w-full bg-line lg:left-[1.0625rem] lg:top-9 lg:h-[calc(100%-2.25rem)] lg:w-0.5">
            <span class="block bg-brand transition-all duration-300 lg:w-full"
              :class="'h-full'" :style="window.innerWidth >= 1024 ? 'height:' + (step > {{ $i }} ? 100 : 0) + '%' : 'width:' + (step > {{ $i }} ? 100 : 0) + '%'"></span>
          </span>
        @endunless
        <button type="button" @click="go({{ $i }})" class="group relative z-10 inline-flex flex-col items-center gap-2 lg:flex-row lg:gap-3" :aria-current="step === {{ $i }} ? 'step' : null">
          <span class="grid size-9 shrink-0 place-items-center rounded-full border-2 text-sm font-bold transition"
            :class="step === {{ $i }} ? 'border-brand bg-brand text-white shadow-[0_0_0_4px_var(--c-brand-soft)]' : ((step > {{ $i }} || {{ $doneExpr }}) ? 'border-brand bg-brand-soft text-brand' : 'border-line-strong bg-surface text-muted group-hover:border-brand')">
            <template x-if="step !== {{ $i }} && (step > {{ $i }} || {{ $doneExpr }})"><x-ui.icon name="check" class="size-4" /></template>
            <template x-if="!(step !== {{ $i }} && (step > {{ $i }} || {{ $doneExpr }}))"><span>{{ $i + 1 }}</span></template>
          </span>
          <span class="max-w-[5.5rem] text-[11px] font-semibold leading-tight sm:max-w-none sm:text-xs lg:text-sm" :class="step === {{ $i }} ? 'text-fg' : 'text-muted'">{{ $label }}</span>
        </button>
      </li>
    @endforeach
  </ol>
</nav>

@once
  @push('scripts')
  <script>
  // start: explicit step (e.g. the step holding a validation error) or null to resume the last one.
  function stepper(count, start = null, persist = true) {
    const key = 'stepper:' + location.pathname;
    let initial = start;
    if (initial === null && persist) {
      try { initial = parseInt(sessionStorage.getItem(key) || '0', 10) || 0; } catch (e) { initial = 0; }
    }
    return {
      step: Math.min(Math.max(initial || 0, 0), count - 1), count,
      go(i) {
        this.step = i;
        if (persist) { try { sessionStorage.setItem(key, String(i)); } catch (e) {} }
        this.$nextTick(() => this.$root.scrollIntoView({ behavior: 'smooth', block: 'start' }));
      },
      next() { if (this.step < this.count - 1) this.go(this.step + 1); },
      prev() { if (this.step > 0) this.go(this.step - 1); },
      get last() { return this.step === this.count - 1; },
    };
  }
  </script>
  @endpush
@endonce
