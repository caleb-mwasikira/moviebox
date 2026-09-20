<?php

use App\Models\User;
use App\Support\ChunkedUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function chunkAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

it('resumes an interrupted upload from the bytes the server already holds', function () {
    $admin = chunkAdmin();
    $data  = random_bytes(1000);

    $id = $this->actingAs($admin)->postJson('/admin/uploads', ['name' => 'film.mp4', 'size' => 1000])->json('id');

    $this->actingAs($admin)->call('PUT', "/admin/uploads/{$id}", [], [], [], ['HTTP_X_UPLOAD_OFFSET' => '0', 'CONTENT_TYPE' => 'application/octet-stream'], substr($data, 0, 400))
        ->assertOk()->assertJson(['received' => 400]);

    // Connection dropped; the client asks where to continue.
    $this->actingAs($admin)->getJson("/admin/uploads/{$id}")->assertJson(['received' => 400]);

    // A stale offset is corrected instead of corrupting the file.
    $this->actingAs($admin)->call('PUT', "/admin/uploads/{$id}", [], [], [], ['HTTP_X_UPLOAD_OFFSET' => '0'], substr($data, 0, 400))
        ->assertStatus(409)->assertJson(['received' => 400]);

    $this->actingAs($admin)->call('PUT', "/admin/uploads/{$id}", [], [], [], ['HTTP_X_UPLOAD_OFFSET' => '400'], substr($data, 400))
        ->assertOk()->assertJson(['received' => 1000]);

    expect(file_get_contents(ChunkedUpload::find($id, $admin->id)->path()))->toBe($data);
});

it('does not let another user touch an upload', function () {
    $id = $this->actingAs(chunkAdmin())->postJson('/admin/uploads', ['name' => 'a.mp4', 'size' => 10])->json('id');

    $this->actingAs(chunkAdmin())->getJson("/admin/uploads/{$id}")->assertNotFound();
});

it('lets a movie form claim a chunk-uploaded video and then cleans it up', function () {
    Illuminate\Support\Facades\Storage::fake('public');
    Illuminate\Support\Facades\Http::fake();

    $admin = chunkAdmin();
    $tmp   = tempnam(sys_get_temp_dir(), 'vid') . '.mp4';
    exec('ffmpeg -loglevel error -y -f lavfi -i testsrc=size=64x64:rate=5 -t 1 -pix_fmt yuv420p ' . escapeshellarg($tmp));
    $bytes = file_get_contents($tmp);

    $id = $this->actingAs($admin)->postJson('/admin/uploads', ['name' => 'film.mp4', 'size' => strlen($bytes)])->json('id');
    $this->actingAs($admin)->call('PUT', "/admin/uploads/{$id}", [], [], [], ['HTTP_X_UPLOAD_OFFSET' => '0'], $bytes)->assertOk();

    $this->actingAs($admin)->post('/admin/movies', [
        'title' => 'Chunked', 'description' => 'd', 'status' => 'published', 'video_upload_id' => $id,
    ])->assertRedirect(route('admin.movies.index'));

    expect(App\Models\Movie::where('title', 'Chunked')->exists())->toBeTrue()
        ->and(ChunkedUpload::find($id, $admin->id))->toBeNull();
});
