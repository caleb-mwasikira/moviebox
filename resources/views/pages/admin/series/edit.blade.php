@extends('layouts.admin')

@section('admin-title', 'Manage Series')

@section('admin-content')
@php
  $uid      = auth()->id() ?? 0;
  $exp      = time() + 7200;
  $sig      = hash_hmac('sha256', $uid . ':' . $exp, config('services.streaming.secret'));
  $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
  $genres   = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western'];
  $videoAccept = 'video/mp4,video/webm,video/ogg,video/x-matroska,.mkv';
  $tabBtn = 'border-b-2 px-1 pb-2 text-sm font-semibold transition';
  $uploadCfg = fn ($target, string $type, string $kind, string $route, string $label) => [
      'targetType' => $type, 'targetId' => $target->id, 'kind' => $kind, 'url' => route($route, $target), 'label' => $label,
      'upload' => ($u = $target->activeUpload($kind)) ? ['id' => $u->id, 'name' => $u->file_name, 'size' => $u->file_size] : null,
  ];
@endphp

<x-ui.page-header :title="$series->title" subtitle="Manage metadata, seasons, and episodes.">
  <x-slot:actions>
    <a href="{{ route('series.show', $series) }}" target="_blank" class="btn btn-secondary btn-sm"><x-ui.icon name="external" class="size-3.5" /> View on site</a>
    <a href="{{ route('admin.series.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back to series</a>
  </x-slot:actions>
</x-ui.page-header>

<div x-data="stepper(3, {{ $errors->has('trailer') ? 1 : ($errors->hasAny(['title','genre','year','status','description','thumbnail','posters','posters.*']) ? 0 : 'null') }})" class="space-y-6 lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:items-start lg:gap-8 lg:space-y-0">
<x-admin.stepper :steps="['Details', 'Trailer', 'Episodes']" :done="[true, $series->trailerIsReady(), $series->seasons->flatMap->episodes->isNotEmpty()]" />

<div class="min-w-0 space-y-6">

<div x-show="step === 0" class="space-y-6">
{{-- Metadata --}}
<form method="POST" action="{{ route('admin.series.update', $series) }}" enctype="multipart/form-data" id="series-meta-form" class="card">
  @csrf @method('PUT')
  <div class="card-header"><h2 class="card-title">Series metadata</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title', $series->title)" /></div>
      <x-admin.genre-select :selected="old('genre', $series->genre)" />
      <x-ui.field name="year" type="number" label="Year" :value="old('year', $series->year)" />
      <x-ui.select name="status" label="Status" :selected="old('status', $series->status)" :options="['hidden' => 'Hidden', 'published' => 'Published']" />
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description', $series->description)" rows="5" />

    <x-admin.poster-picker :posters="$series->posters" />

    <button type="submit" class="btn btn-primary">Save metadata</button>
  </div>
</form>

</div>

<div x-show="step === 1" x-cloak class="space-y-6">
{{-- Trailer --}}
<section class="card">
  <div class="card-header"><h2 class="card-title">Trailer</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    @if ($series->trailerIsReady())
      <div>
        <video id="admin-trailer-video" controls preload="metadata" class="aspect-video w-full max-w-xl rounded-xl bg-black"></video>
        <p class="mt-2 text-xs text-muted">{{ basename($series->trailer_path) }}</p>
      </div>
    @elseif ($series->trailer_path)
      <span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span> Trailer processing…</span>
    @endif

    <form x-data="trailerUpload({{ \Illuminate\Support\Js::from($uploadCfg($series, 'series', 'trailer', 'admin.series.trailer', $series->title . ' — trailer')) }})" x-init="initUploads()" method="POST" action="{{ route('admin.series.trailer', $series) }}" enctype="multipart/form-data" @submit.prevent="upload($el)" class="max-w-xl space-y-4">
      @csrf
      <x-admin.upload-status />
      <x-ui.file name="trailer" id="series-trailer" :label="$series->trailer_path ? 'Replace trailer' : 'Upload trailer'" hint="MP4, WebM, OGG or MKV — will be transcoded to MP4" :accept="$videoAccept" />
      <p x-show="pickError" x-cloak x-text="pickError" class="field-error"></p>
      <button type="submit" class="btn btn-primary">{{ $series->trailer_path ? 'Replace trailer' : 'Upload trailer' }}</button>
    </form>
  </div>
</section>

</div>

<div x-show="step === 2" x-cloak class="space-y-6">
{{-- Seasons & episodes --}}
<section class="space-y-4">
  <h2 class="section-title">Seasons &amp; episodes</h2>

  @forelse ($series->seasons as $season)
    <div class="card overflow-hidden">
      <div class="card-header">
        <h3 class="card-title">{{ $season->label() }}@if ($season->title) <span class="font-normal text-muted">— {{ $season->title }}</span>@endif</h3>
        <x-admin.delete :action="route('admin.seasons.destroy', $season)" :confirm="'Delete ' . $season->label() . '? All its episodes will be removed.'" label="Delete season" />
      </div>

      @if ($season->episodes->isNotEmpty())
        <div class="overflow-x-auto">
          <table class="table">
            <thead><tr><th class="w-14">#</th><th>Title</th><th>Duration</th><th>Video</th><th class="text-right">Actions</th></tr></thead>
            @foreach ($season->episodes as $episode)
              {{-- Each episode pair gets its own tbody for Alpine scoping --}}
              <tbody x-data="seriesVideoReplace({{ $episode->is_free ? 'true' : 'false' }}, {{ \Illuminate\Support\Js::from(route('admin.episodes.toggle-free', $episode)) }}, {{ \Illuminate\Support\Js::from($uploadCfg($episode, 'episode', 'video', 'admin.episodes.video', $series->title . ' — ' . $episode->label())) }})" x-init="initUploads()">
                <tr>
                  <td class="font-semibold tabular-nums text-fg">{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}</td>
                  <td class="font-semibold text-fg">{{ $episode->title }}</td>
                  <td>{{ $episode->duration ? $episode->duration . ' min' : '—' }}</td>
                  <td>
                    <div x-show="up()" x-cloak class="min-w-32 space-y-1.5">
                      <span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span><span x-text="up() && up().status === 'stalled' ? 'Processing — upload interrupted' : 'Processing — uploading ' + (up() ? up().percent : 0) + '%'"></span></span>
                      <div class="progress"><span class="transition-all" :style="'width:' + (up() ? up().percent : 0) + '%'"></span></div>
                    </div>
                    <div x-show="!up()">
                    @if ($episode->streaming_path)<span class="badge badge-success"><span class="size-1.5 rounded-full bg-emerald-500"></span>Ready</span>
                    @elseif ($episode->video_path)<span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span>Processing</span>
                    @else<span class="text-muted">—</span>@endif
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center justify-end gap-1.5">
                      <x-admin.video-preview :title="$episode->label() . ' — ' . $series->title" :src="$episode->streaming_path ? $episode->streamingUrl() : ($episode->video_path ? $episode->videoUrl() : null)" :hls="(bool) $episode->streaming_path" label="Watch"
                        :report-url="route('admin.episodes.corrupted', $episode)" :clear-url="route('admin.episodes.corrupted.clear', $episode)" :corrupted="$episode->is_corrupted" :token="$hlsToken" />
                      <button type="button" @click="toggleFree" class="badge px-3 py-1.5 transition hover:opacity-80" :class="isFree ? 'bg-emerald-500 text-white' : 'badge-neutral'" :title="isFree ? 'Click to remove free access' : 'Click to make free'">
                        <span x-text="isFree ? 'Free' : 'Set free'">{{ $episode->is_free ? 'Free' : 'Set free' }}</span>
                      </button>
                      <button type="button" @click="open = !open" class="btn btn-secondary btn-sm">Edit episode</button>
                      <x-admin.delete :action="route('admin.episodes.destroy', $episode)" :confirm="'Delete episode ' . $episode->episode_number . '?'" />
                    </div>
                  </td>
                </tr>

                {{-- Collapsible edit-episode row --}}
                <tr x-show="open" x-cloak>
                  <td colspan="5" class="!bg-surface-2/50 !p-0">
                    <div class="space-y-5 p-5" x-data="{ etab: 'details' }">
                      <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-fg">Edit episode — {{ $episode->label() }}</p>
                        <button type="button" @click="open = false" class="btn btn-ghost btn-icon" title="Hide" aria-label="Hide episode editor"><x-ui.icon name="chevron-up" class="size-4" /></button>
                      </div>

                      {{-- Tabs --}}
                      <div class="flex gap-5 border-b border-line" role="tablist">
                        <button type="button" role="tab" @click="etab = 'details'" :class="etab === 'details' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Details</button>
                        <button type="button" role="tab" @click="etab = 'video'" :class="etab === 'video' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Video</button>
                        @if ($episode->video_path)
                          <button type="button" role="tab" @click="etab = 'encoding'" :class="etab === 'encoding' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Encoding</button>
                        @endif
                      </div>

                      <div x-show="etab === 'details'">
                      <form method="POST" action="{{ route('admin.episodes.update', $episode) }}" class="grid items-end gap-4 sm:grid-cols-[1fr_auto_auto]">
                        @csrf @method('PUT')
                        <div><label class="label">Title</label><input type="text" name="title" value="{{ $episode->title }}" class="input"></div>
                        <div class="pb-2.5"><x-ui.check name="is_free" :id="'is_free_' . $episode->id" label="Free to watch" hint="(no login required)" x-model="isFree" /></div>
                        <button type="submit" class="btn btn-primary">Save</button>
                      </form>
                      </div>

                      {{-- Video --}}
                      <div class="space-y-3" x-show="etab === 'video'" x-cloak @dragover.prevent @drop.prevent="drop($event)">
                        <div class="flex flex-wrap items-center gap-3">
                          <p class="text-xs font-semibold uppercase tracking-wider text-muted">Video</p>
                          @if ($episode->streaming_path)
                            <span class="badge badge-success"><span class="size-1.5 rounded-full bg-emerald-500"></span>Streaming ready</span>
                            <code class="rounded-md bg-surface-3 px-2 py-0.5 text-xs text-muted">{{ $episode->streaming_path }}</code>
                          @elseif ($episode->video_path)
                            <span x-show="!up()" class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span>Processing</span>
                          @else
                            <span x-show="!up()" class="badge badge-neutral">No video uploaded</span>
                          @endif
                          <button type="button" @click="videoOpen = !videoOpen" class="btn btn-secondary btn-sm ml-auto">Replace / set URL</button>
                        </div>

                        <x-admin.upload-status />

                        <div x-show="videoOpen" x-cloak x-transition>
                          <form method="POST" action="{{ route('admin.episodes.video', $episode) }}" enctype="multipart/form-data" x-ref="form" @submit.prevent="upload($refs.form)" class="panel space-y-4 p-4">
                            @csrf
                            <div class="flex gap-5 border-b border-line">
                              <button type="button" @click="tab = 'upload'" :class="tab === 'upload' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Upload file</button>
                              <button type="button" @click="tab = 'url'" :class="tab === 'url' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Streaming URL</button>
                            </div>

                            <div x-show="tab === 'upload'" class="space-y-3">
                              <label @dragover.prevent="dragging = true" @dragleave="dragging = false" @drop.prevent="drop($event)"
                                :class="dragging ? 'border-brand bg-brand-soft' : 'border-line-strong'"
                                class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed px-6 py-8 text-center transition">
                                <span class="text-sm font-semibold text-fg">Click to browse</span>
                                <span class="text-xs text-muted" x-text="fileName">MP4, WebM, OGG, MKV</span>
                                <input x-ref="videoInput" type="file" name="video" accept="{{ $videoAccept }}" :disabled="tab !== 'upload'" class="sr-only"
                                  @change="fileName = $event.target.files[0] ? $event.target.files[0].name : 'MP4, WebM, OGG, MKV'">
                              </label>
                              <p x-show="pickError" x-cloak x-text="pickError" class="field-error"></p>
                            </div>

                            <div x-show="tab === 'url'" x-cloak>
                              <input type="text" name="streaming_url" placeholder="/stream/series/title/ep01/master.m3u8" autocomplete="off" class="input">
                              <p class="hint">Relative path or full URL to the HLS master playlist.</p>
                            </div>

                            <div class="flex gap-2">
                              <button type="submit" x-text="submitLabel" class="btn btn-primary btn-sm">Upload</button>
                              <button type="button" @click="videoOpen = false" class="btn btn-secondary btn-sm">Cancel</button>
                            </div>
                          </form>
                        </div>
                      </div>

                      {{-- Encoding quality --}}
                      @if ($episode->video_path)
                        <div class="space-y-3" x-show="etab === 'encoding'" x-cloak x-data='encodingQuality(@json($episode->streamingUrl()), @json($hlsToken))'>
                          <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Encoding quality</p>
                            <form method="POST" action="{{ route('admin.episodes.encode-all', $episode) }}">
                              @csrf
                              <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Re-encode {{ addslashes($episode->label()) }} at all qualities?')"><x-ui.icon name="refresh" class="size-3.5" /> Re-encode</button>
                            </form>
                          </div>
                          @if ($episode->streaming_path)
                            <div class="flex flex-wrap gap-2">
                              <template x-for="q in allQualities" :key="q">
                                <span class="badge px-3 py-1.5" :class="encoded.includes(q) ? 'badge-success' : 'badge-neutral'">
                                  <span class="size-1.5 rounded-full" :class="encoded.includes(q) ? 'bg-emerald-500' : 'bg-muted/50'"></span><span x-text="q"></span>
                                </span>
                              </template>
                              <span x-show="loading" x-cloak class="text-xs text-muted">Checking…</span>
                            </div>
                          @else
                            <p class="text-sm text-muted">Processing — not yet encoded</p>
                          @endif
                          <div class="flex flex-wrap gap-2">
                            @foreach (['360p', '480p', '720p', '1080p'] as $quality)
                              <form method="POST" action="{{ route('admin.episodes.encode-quality', $episode) }}">
                                @csrf
                                <input type="hidden" name="resolution" value="{{ $quality }}">
                                <button type="submit" class="btn btn-secondary btn-sm">{{ $quality }}</button>
                              </form>
                            @endforeach
                          </div>
                        </div>
                      @endif
                    </div>
                  </td>
                </tr>
              </tbody>
            @endforeach
          </table>
        </div>
      @endif

      <div class="space-y-4 border-t border-line p-5 sm:p-6">
        {{-- Add episode --}}
        <details class="panel group">
          <summary class="flex cursor-pointer list-none items-center gap-2 px-5 py-3.5 text-sm font-semibold text-brand"><x-ui.icon name="plus" class="size-4" /> Add episode</summary>
          <form x-data="episodeUpload" method="POST" action="{{ route('admin.episodes.store', $season) }}" enctype="multipart/form-data" @submit.prevent="upload($el)" class="space-y-4 border-t border-line p-5">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
              <div><label class="label">Episode #</label><input type="number" name="episode_number" min="1" value="{{ $season->episodes->max('episode_number') + 1 }}" class="input"></div>
              <div><label class="label">Episode title</label><input type="text" name="title" class="input"></div>
              <div class="sm:col-span-2"><x-ui.file name="video" :id="'ep-video-' . $season->id" label="Video file" required :accept="$videoAccept" /></div>
              <div class="sm:col-span-2"><x-ui.check name="is_free" :id="'ep_is_free_' . $season->id" label="Free to watch" hint="(no login required)" /></div>
            </div>
            <p x-show="error" x-cloak x-text="error" class="field-error"></p>
            <div x-show="saved" x-cloak class="space-y-2">
              <p class="alert alert-success">Episode saved. Its video is uploading in the background — the list refreshes when it finishes. Keep this tab open.</p>
              <template x-for="item in $store.uploads.items.filter(function (i) { return ids.indexOf(i.id) !== -1; })" :key="item.id">
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-wider text-muted">
                    <span class="truncate normal-case tracking-normal" x-text="item.label"></span>
                    <span x-text="item.status === 'error' ? 'Failed' : (item.status === 'finalizing' ? 'Saving…' : item.percent + '%')"></span>
                  </div>
                  <div class="progress"><span class="transition-all" :style="'width:' + item.percent + '%'"></span></div>
                  <div x-show="item.status === 'error'" x-cloak class="flex flex-wrap items-center gap-3">
                    <span class="text-xs text-red-500" x-text="item.error"></span>
                    <button type="button" @click="$store.uploads.retry(item)" class="btn btn-secondary btn-sm">Retry upload</button>
                  </div>
                </div>
              </template>
            </div>
            <button type="submit" :disabled="saving" class="btn btn-primary" x-text="saving ? 'Saving…' : 'Add episode'">Add episode</button>
          </form>
        </details>

        {{-- Bulk upload --}}
        <div x-data="bulkUpload({{ $season->id }}, '{{ route('admin.episodes.store', $season) }}', {{ ($season->episodes->max('episode_number') ?? 0) + 1 }})">
          <button type="button" @click="show = !show" class="btn btn-secondary btn-sm"><span x-text="show ? '− Bulk upload' : '↑ Bulk upload'">↑ Bulk upload</span></button>
          <div x-show="show" x-cloak x-transition @dragover.prevent="dragging = true" @dragleave.self="dragging = false" @drop.prevent="dragging = false; addFiles($event.dataTransfer.files)"
            :class="dragging ? 'border-brand bg-brand-soft' : ''" class="panel mt-4 space-y-4 border-2 border-dashed p-5 transition">
            <div class="flex flex-wrap items-center justify-between gap-3">
              <p class="text-sm font-semibold text-fg">Bulk upload episodes</p>
              <label class="btn btn-primary btn-sm cursor-pointer">Choose files
                <input type="file" accept="{{ $videoAccept }}" multiple @change="pick($event)" class="sr-only">
              </label>
            </div>
            <template x-if="!queue.length"><p class="text-sm text-muted">Drag &amp; drop video files here, or choose files, to queue them. Titles are extracted from filenames and can be edited before uploading.</p></template>

            <template x-if="queue.length > 0">
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-muted" x-text="queue.length + ' file' + (queue.length !== 1 ? 's' : '') + ' queued'"></span>
                  <button type="button" @click="uploadAll()" :disabled="queue.every(function(i){ return i.status !== 'pending'; })" class="btn btn-primary btn-sm">Upload all</button>
                </div>
                <template x-for="item in queue" :key="item.id">
                  <div class="rounded-xl border border-line bg-surface p-3">
                    <div class="flex flex-wrap items-center gap-2">
                      <input type="number" x-model.number="item.epNum" min="1" :disabled="item.status !== 'pending'" class="input !w-20 !py-2">
                      <input type="text" x-model="item.title" placeholder="Episode title" required :disabled="item.status !== 'pending'" class="input !w-auto min-w-40 flex-1 !py-2">
                      <div class="flex items-center gap-2">
                        <template x-if="item.status === 'pending'">
                          <div class="flex gap-1.5">
                            <button type="button" @click="upload(item)" class="btn btn-secondary btn-sm">Upload</button>
                            <button type="button" @click="remove(item.id)" class="btn btn-ghost btn-sm">✕</button>
                          </div>
                        </template>
                        <template x-if="item.status === 'uploading'"><span class="text-xs font-semibold text-brand">Saving…</span></template>
                        <template x-if="item.status === 'done'"><span class="badge badge-success" x-text="$store.uploads.find(item.uploadId) && $store.uploads.find(item.uploadId).status === 'done' ? '✓ Uploaded' : '✓ Saved'"></span></template>
                        <template x-if="item.status === 'error'">
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-red-500" x-text="item.error"></span>
                            <button type="button" @click="upload(item)" class="btn btn-secondary btn-sm">Retry</button>
                          </div>
                        </template>
                      </div>
                    </div>
                    <template x-if="item.status === 'done' && $store.uploads.find(item.uploadId)">
                      <div class="mt-2 space-y-1">
                        <div class="progress"><span class="transition-all" :style="'width:' + $store.uploads.find(item.uploadId).percent + '%'"></span></div>
                        <p class="text-[11px] text-muted" x-text="$store.uploads.find(item.uploadId).status === 'error' ? $store.uploads.find(item.uploadId).error : 'Uploading in the background · ' + $store.uploads.find(item.uploadId).percent + '%'"></p>
                        <button type="button" x-show="$store.uploads.find(item.uploadId).status === 'error'" @click="$store.uploads.retry($store.uploads.find(item.uploadId))" class="btn btn-secondary btn-sm">Retry upload</button>
                      </div>
                    </template>
                    <p class="mt-1.5 truncate text-[11px] text-muted" x-text="item.file.name"></p>
                  </div>
                </template>
                <template x-if="queue.some(function(i){ return i.status === 'done'; })"><p class="text-sm text-muted">Episodes saved. Their videos are uploading in the background — keep this tab open; the page refreshes when they finish.</p></template>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  @empty
    <x-ui.empty icon="tv" title="No seasons yet" text="Add the first season below." />
  @endforelse

  {{-- Add season --}}
  <form method="POST" action="{{ route('admin.seasons.store', $series) }}" class="card card-pad grid items-end gap-4 sm:grid-cols-[10rem_1fr_auto]">
    @csrf
    <div><label class="label">Season number</label><input type="number" name="season_number" min="1" value="{{ $nextSeasonNumber }}" class="input"></div>
    <div><label class="label">Season title <span class="font-normal normal-case tracking-normal">(optional)</span></label><input type="text" name="title" placeholder="e.g. The Beginning" class="input"></div>
    <button type="submit" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> Add season</button>
  </form>
</section>
</div>

<div class="flex items-center justify-between">
  <button type="button" @click="prev()" x-show="step > 0" class="btn btn-secondary"><x-ui.icon name="arrow-left" class="size-4" /> Back</button>
  <span x-show="step === 0"></span>
  <button type="button" @click="next()" x-show="!last" class="btn btn-primary">Next <x-ui.icon name="arrow-right" class="size-4" /></button>
  <a href="{{ route('admin.series.index') }}" x-show="last" x-cloak class="btn btn-primary">Done</a>
</div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
// ── Admin trailer preview ─────────────────────────────────────
(function () {
  var video = document.getElementById('admin-trailer-video');
  if (!video) return;
  var src = @json($series->trailerIsReady() ? $series->trailerUrl() : '');
  if (!src) return;
  var hlsToken = @json($hlsToken);
  if (Hls.isSupported()) {
    var hls = new Hls({ xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken); } });
    hls.loadSource(src);
    hls.attachMedia(video);
  } else {
    video.src = src;
  }
})();

// ── Alpine components ─────────────────────────────────────────
document.addEventListener('alpine:init', function () {
  Alpine.data('encodingQuality', function (streamingUrl, token) {
    return {
      allQualities: ['360p', '480p', '720p', '1080p'],
      encoded: [],
      loading: false,
      init: function () {
        if (!streamingUrl) return;
        var self = this;
        self.loading = true;
        fetch(streamingUrl, { headers: { 'Authorization': 'Bearer ' + token } })
          .then(function (r) { return r.ok ? r.text() : ''; })
          .then(function (text) {
            self.encoded = [];
            text.split('\n').forEach(function (line) {
              line = line.trim();
              if (line === '360p/index.m3u8') self.encoded.push('360p');
              else if (line === '480p/index.m3u8') self.encoded.push('480p');
              else if (line === '720p/index.m3u8') self.encoded.push('720p');
              else if (line === '1080p/index.m3u8') self.encoded.push('1080p');
            });
            self.loading = false;
          })
          .catch(function () { self.loading = false; });
      }
    };
  });

  Alpine.data('seriesVideoReplace', function (isFree, toggleFreeUrl, cfg) {
    return Object.assign(window.uploadMixin(cfg), {
      open: false,
      videoOpen: false,
      tab: 'upload',
      fileName: 'MP4, WebM, OGG, MKV',
      dragging: false,
      isFree: isFree,
      toggleFreeUrl: toggleFreeUrl,
      toggleFree: function () {
        var self = this;
        fetch(self.toggleFreeUrl, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { self.isFree = data.is_free; })
        .catch(function () {});
      },
      get submitLabel() { return this.tab === 'url' ? 'Save URL' : 'Upload'; },
      upload: function (form) {
        this.beginUpload(form, { optional: this.tab === 'url' });
        if (this.uploadId) this.videoOpen = false;
      },
      drop: function (e) {
        this.dragging = false;
        var files = e.dataTransfer.files;
        if (!files.length) return;
        if (this.$refs.videoInput) { this.$refs.videoInput.files = files; }
        this.fileName = files[0].name;
        // Dropping anywhere on the Video tab opens the upload panel with the file ready to send.
        this.videoOpen = true;
        this.tab = 'upload';
      }
    });
  });

  Alpine.data('episodeUpload', function () {
    return {
      saving: false,
      saved: false,
      ids: [],
      error: '',
      upload: function (form) {
        var self = this;
        var input = form.querySelector('[name="video"]');
        if (!input || !input.files.length) { form.submit(); return; }
        self.saving = true; self.error = ''; self.saved = false;
        window.__mbReloadOnDone = true;
        window.createWithBackgroundUploads(form, ['video']).then(function (data) {
          self.ids = data.uploads.map(function (u) { return u.id; });
          self.saved = true;
          form.reset();
        }).catch(function (e) { self.error = e.message; }).finally(function () { self.saving = false; });
      }
    };
  });

  Alpine.data('bulkUpload', function (seasonId, storeUrl, startEpNum) {
    return {
      show: false,
      queue: [],
      nextNum: startEpNum,
      titleFromFile: function (filename) {
        return filename
          .replace(/\.[^.]+$/, '')
          .replace(/[_\-\.]+/g, ' ')
          .replace(/\b\w/g, function (c) { return c.toUpperCase(); })
          .trim();
      },
      dragging: false,
      pick: function (event) {
        this.addFiles(event.target.files);
        event.target.value = '';
      },
      addFiles: function (files) {
        var self = this;
        Array.from(files).forEach(function (file) {
          if (!/^video\//.test(file.type) && !/\.mkv$/i.test(file.name)) return;
          self.queue.push({
            id: Math.random().toString(36).slice(2),
            file: file,
            title: self.titleFromFile(file.name),
            epNum: self.nextNum++,
            status: 'pending',
            progress: 0,
            error: '',
          });
        });
      },
      remove: function (id) {
        this.queue = this.queue.filter(function (item) { return item.id !== id; });
      },
      upload: function (item) {
        item.status = 'uploading';
        item.progress = 0;
        item.error = '';
        var fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fd.append('episode_number', item.epNum);
        fd.append('title', item.title);
        fd.append('video', item.file);
        window.__mbReloadOnDone = true;
        window.createWithBackgroundUploads(fd, ['video'], storeUrl).then(function (data) {
          item.uploadId = data.uploads.length ? data.uploads[0].id : null;
          item.status = 'done';
        }).catch(function (e) {
          item.status = 'error';
          item.error = e.message;
        });
      },
      uploadAll: function () {
        var self = this;
        this.queue
          .filter(function (i) { return i.status === 'pending'; })
          .forEach(function (item) { self.upload(item); });
      }
    };
  });

  Alpine.data('trailerUpload', function (cfg) {
    return Object.assign(window.uploadMixin(cfg), {
      upload: function (form) { this.beginUpload(form); }
    });
  });
});
</script>
@endpush
