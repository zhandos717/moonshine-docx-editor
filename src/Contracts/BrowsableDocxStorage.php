<?php

namespace Zhandos717\MoonshineDocxEditor\Contracts;

interface BrowsableDocxStorage
{
    /**
     * Содержимое папки хранилища. Пути относительные, без ведущего «/»; '' — корень.
     *
     * @return array{dirs: list<string>, files: list<string>}
     */
    public function browse(string $path): array;

    public function readFile(string $path): ?string;

    /** Перезаписывает файл, предыдущую версию сохраняет в .history/ рядом. */
    public function writeFile(string $path, string $contents): void;

    public function makeDirectory(string $path): void;
}
