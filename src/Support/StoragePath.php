<?php

namespace Zhandos717\MoonshineDocxEditor\Support;

class StoragePath
{
    public static function clean(string $path): string
    {
        $parts = array_values(array_filter(explode('/', str_replace('\\', '/', $path)), fn ($p) => $p !== ''));

        foreach ($parts as $part) {
            abort_if(str_starts_with($part, '.') || str_contains($part, "\0"), 422, 'Недопустимый путь');
        }

        return implode('/', $parts);
    }

    public static function docx(string $path): string
    {
        $path = self::clean($path);
        abort_unless($path !== '' && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'docx', 422, 'Допускаются только файлы .docx');

        return $path;
    }
}
