@extends('layouts.admin')

@section('admin-title', 'Add Series')

@section('admin-content')
<x-ui.page-header title="Add series" subtitle="After saving you can add seasons and episodes." />

<form method="POST" action="{{ route('admin.series.store') }}" enctype="multipart/form-data" id="upload-form" class="space-y-6 lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:items-start lg:gap-8 lg:space-y-0" x-data="stepper(2, {{ $errors->hasAny(['trailer', 'thumbnail', 'posters', 'posters.*']) ? 1 : 0 }}, false)" @keydown.enter="if (!last && $event.target.tagName === 'INPUT') { $event.preventDefault(); next(); }">
  @csrf

  <x-admin.stepper :steps="['Details', 'Artwork & trailer']" />

  <div class="min-w-0 space-y-6">

  <div class="card card-pad space-y-5" x-show="step === 0">
    <h2 class="card-title">Details</h2>
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title')" /></div>
      <x-admin.genre-select :selected="old('genre')" />
      <x-ui.field name="year" type="number" label="Year" :value="old('year')" placeholder="e.g. 2024" />
      <x-ui.select name="status" label="Status" :options="['hidden' => 'Hidden', 'published' => 'Published']" />
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description')" rows="5" />
  </div>

  <div class="card card-pad space-y-5" x-show="step === 1" x-cloak>
    <h2 class="card-title">Artwork &amp; trailer</h2>
    <x-admin.poster-picker />
    <x-ui.file name="trailer" label="Trailer" optional hint="Short preview clip — or add it later from the edit page" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
    <p class="alert alert-info"><x-ui.icon name="info" class="mt-0.5 size-4 shrink-0" />Episode videos are uploaded per episode after creating the series.</p>
    <x-admin.create-upload :fields="['trailer']" label="Create series" />
  </div>

  <div class="flex flex-wrap items-center gap-3">
    <button type="button" @click="prev()" x-show="step > 0" x-cloak class="btn btn-secondary btn-lg"><x-ui.icon name="arrow-left" class="size-4" /> Back</button>
    <button type="button" @click="next()" x-show="!last" class="btn btn-primary btn-lg">Next <x-ui.icon name="arrow-right" class="size-4" /></button>
    <button type="submit" id="upload-submit" x-show="last" x-cloak class="btn btn-primary btn-lg disabled:opacity-50">Create series</button>
    <a href="{{ route('admin.series.index') }}" class="btn btn-ghost btn-lg ml-auto">Cancel</a>
  </div>
  </div>
</form>
@endsection

