<?php

namespace App\Http\Traits;

use App\Models\VideoUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Lets create forms save the record straight away and send the (large) video afterwards from the
 * browser. The form announces "{field}_name" and "{field}_size"; the record is created without the
 * file, and each announced file becomes a tracked VideoUpload the browser then fills in chunks.
 */
trait PlansBackgroundUploads
{
    protected function deferredFileRules(): array
    {
        $rules = [];
        foreach (['video', 'trailer'] as $field) {
            $rules["{$field}_name"] = ['nullable', 'string', 'max:255'];
            $rules["{$field}_size"] = ['nullable', 'integer', 'min:1'];
        }

        return $rules;
    }

    /**
     * @param  array<string, string>  $finalizeUrls  field => endpoint that attaches the finished upload
     * @return list<array{id: string, field: string, kind: string, name: string, size: int, url: string}>
     */
    protected function planUploads(Request $request, Model $target, array $finalizeUrls): array
    {
        $plan = [];

        foreach ($finalizeUrls as $field => $url) {
            if ($request->hasFile($field) || ! $request->filled("{$field}_size")) {
                continue;
            }

            $name   = (string) $request->input("{$field}_name", $field);
            $size   = (int) $request->input("{$field}_size");
            $upload = VideoUpload::start($request->user()->id, $target, $field, $name, $size);

            $plan[] = ['id' => $upload->id, 'field' => $field, 'kind' => $field, 'name' => $name, 'size' => $size, 'url' => $url];
        }

        return $plan;
    }
}
