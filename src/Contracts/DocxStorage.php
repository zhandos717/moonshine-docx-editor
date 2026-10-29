<?php

namespace Zhandos717\MoonshineDocxEditor\Contracts;

interface DocxStorage
{
    /** Содержимое текущей редакции документа или null, если документа ещё нет. */
    public function read(string $key): ?string;

    /**
     * Сохраняет новую редакцию.
     *
     * @param  array{note?: ?string, requires_reaccept?: bool, user?: ?string}  $meta
     * @return array{version: string}
     */
    public function write(string $key, string $contents, array $meta = []): array;
}
