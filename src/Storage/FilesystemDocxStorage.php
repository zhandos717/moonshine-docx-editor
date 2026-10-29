<?php

namespace Zhandos717\MoonshineDocxEditor\Storage;

use Illuminate\Support\Facades\Storage;
use Zhandos717\MoonshineDocxEditor\Contracts\BrowsableDocxStorage;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;
use Zhandos717\MoonshineDocxEditor\Support\StoragePath;

class FilesystemDocxStorage implements BrowsableDocxStorage, DocxStorage
{
    public function list(): array
    {
        $root = trim(config('docx-editor.directory'), '/');
        $keys = array_values(array_filter(
            array_map('basename', Storage::disk(config('docx-editor.disk'))->directories($root)),
            fn ($name) => ! str_starts_with($name, '.'),
        ));
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

    public function browse(string $path): array
    {
        $disk = Storage::disk(config('docx-editor.disk'));
        $absolute = $this->absolute(StoragePath::clean($path));

        $dirs = array_values(array_filter(
            array_map('basename', $disk->directories($absolute)),
            fn ($name) => ! str_starts_with($name, '.'),
        ));
        $files = array_values(array_filter(
            array_map('basename', $disk->files($absolute)),
            fn ($name) => strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'docx',
        ));
        sort($dirs);
        sort($files);

        return ['dirs' => $dirs, 'files' => $files];
    }

    public function readFile(string $path): ?string
    {
        $disk = Storage::disk(config('docx-editor.disk'));
        $absolute = $this->absolute(StoragePath::docx($path));

        return $disk->exists($absolute) ? $disk->get($absolute) : null;
    }

    public function writeFile(string $path, string $contents): void
    {
        $disk = Storage::disk(config('docx-editor.disk'));
        $absolute = $this->absolute(StoragePath::docx($path));

        if ($disk->exists($absolute)) {
            $name = pathinfo($absolute, PATHINFO_FILENAME);
            $disk->copy($absolute, dirname($absolute)."/.history/$name/".now()->format('Y-m-d_His').'.docx');
        }

        $disk->put($absolute, $contents);
    }

    public function makeDirectory(string $path): void
    {
        $clean = StoragePath::clean($path);
        abort_if($clean === '', 422, 'Недопустимый путь');

        Storage::disk(config('docx-editor.disk'))->makeDirectory($this->absolute($clean));
    }

    private function absolute(string $path): string
    {
        $root = trim(config('docx-editor.browser_directory'), '/');

        return $path === '' ? $root : "$root/$path";
    }
}
