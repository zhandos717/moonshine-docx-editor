<?php

use Zhandos717\MoonshineDocxEditor\Storage\FilesystemDocxStorage;

return [
    // Добавлять ли страницу редактора в меню MoonShine автоматически
    'auto_menu' => true,

    // Документы, доступные в редакторе: ключ => заголовок
    'documents' => [
        // 'offer' => 'Публичная оферта',
    ],

    // Реализация Contracts\DocxStorage: куда читать и сохранять файлы
    'storage' => FilesystemDocxStorage::class,

    // Настройки стандартного хранилища на диске Laravel
    'disk' => env('DOCX_EDITOR_DISK', 'local'),
    'directory' => 'docx-editor',

    // Максимальный размер загружаемого файла, КБ
    'max_size_kb' => 10240,

    // Подключать ли миграции и ассеты пакета автоматически
    'migrations' => true,
];
