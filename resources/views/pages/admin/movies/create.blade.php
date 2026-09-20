@extends('layouts.admin')

@section('admin-title', 'Upload Movie')

@section('admin-content')
<x-ui.page-header title="Upload movie" subtitle="Add a new title to the catalogue." />

<form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data" id="upload-form" class="space-y-6 lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:items-start lg:gap-8 lg:space-y-0" x-data="stepper(3, {{ $errors->hasAny(['video', 'trailer']) ? 2 : ($errors->hasAny(['thumbnail', 'posters', 'posters.*']) ? 1 : 0) }}, false)" @keydown.enter="if (!last && $event.target.tagName === 'INPUT') { $event.preventDefault(); next(); }">
  @csrf

  <x-admin.stepper :steps="['Details', 'Artwork', 'Media']" />

  <div class="min-w-0 space-y-6">

  <div class="card card-pad space-y-5" x-show="step === 0">
    <h2 class="card-title">Details</h2>
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title')" /></div>
      <x-admin.genre-select :selected="old('genre')" />
      <x-ui.field name="year" type="number" label="Year" :value="old('year')" placeholder="e.g. 2024" />
      <x-ui.select name="status" label="Status" :selected="old('status', 'hidden')" :options="['hidden' => 'Hidden', 'published' => 'Published']" />
      <div class="flex items-end pb-2.5"><x-ui.check name="is_free" label="Free to watch" hint="(no login required)" :checked="old('is_free')" /></div>
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description')" rows="5" />
  </div>

  <div class="card card-pad space-y-5" x-show="step === 1" x-cloak>
    <h2 class="card-title">Artwork</h2>
    <x-admin.poster-picker />
  </div>

  <div class="card card-pad space-y-5" x-show="step === 2" x-cloak>
    <h2 class="card-title">Media</h2>
    <x-ui.file name="video" label="Video file" optional hint="MP4, WebM, OGG or MKV — or add it later from the edit page" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
    <x-ui.file name="trailer" label="Trailer" optional hint="Short preview clip shown on the movie page" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
    <x-admin.create-upload :fields="['video', 'trailer']" label="Upload movie" />
  </div>

  <div class="flex flex-wrap items-center gap-3">
    <button type="button" @click="prev()" x-show="step > 0" x-cloak class="btn btn-secondary btn-lg"><x-ui.icon name="arrow-left" class="size-4" /> Back</button>
    <button type="button" @click="next()" x-show="!last" class="btn btn-primary btn-lg">Next <x-ui.icon name="arrow-right" class="size-4" /></button>
    <button type="submit" id="upload-submit" x-show="last" x-cloak class="btn btn-primary btn-lg disabled:opacity-50">Upload movie</button>
    <a href="{{ route('admin.movies.index') }}" class="btn btn-ghost btn-lg ml-auto">Cancel</a>
  </div>
  </div>
</form>
@endsection

