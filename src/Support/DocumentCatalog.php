<?php

namespace Zhandos717\MoonshineDocxEditor\Support;

use Illuminate\Support\Str;
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;

class DocumentCatalog
{
    /**
     * Документы из конфига плюс найденные в хранилище: ключ => заголовок.
     *
     * @return array<string, string>
     */
    public static function all(DocxStorage $storage): array
    {
        $found = config('docx-editor.discover') ? $storage->list() : [];

        return array_replace($found, config('docx-editor.documents'));
    }

    public static function isValidKey(string $key): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_-]{1,64}$/', $key);
    }

    public static function slug(string $title): string
    {
        $slug = Str::slug(Str::transliterate($title));

        return $slug !== '' ? Str::limit($slug, 64, '') : 'document-'.Str::lower(Str::random(6));
    }
}
