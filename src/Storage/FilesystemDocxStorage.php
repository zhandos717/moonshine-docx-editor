<?php

namespace Zhandos717\MoonshineDocxEditor\Storage;

use Illuminate\Support\Facades\Storage;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;

class FilesystemDocxStorage implements DocxStorage
{
    public function list(): array
    {
        $root = trim(config('docx-editor.directory'), '/');
        $keys = array_map('basename', Storage::disk(config('docx-editor.disk'))->directories($root));
        sort($keys);

        return array_combine($keys, $keys);
    }

    public function read(string $key): ?string
    {
        $disk = Storage::disk(config('docx-editor.disk'));
        $path = $this->latest($key);

        return $path !== null ? $disk->get($path) : null;
    }

    public function write(string $key, string $contents, array $meta = []): array
    {
        $version = now()->format('Y-m-d_His');

        Storage::disk(config('docx-editor.disk'))->put($this->dir($key)."/$version.docx", $contents);

        return ['version' => $version];
    }

    private function latest(string $key): ?string
    {
        $files = Storage::disk(config('docx-editor.disk'))->files($this->dir($key));
        sort($files);

        return $files === [] ? null : end($files);
    }

    private function dir(string $key): string
    {
        return trim(config('docx-editor.directory'), '/')."/$key";
    }
}
