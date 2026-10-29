<?php

use Zhandos717\MoonshineDocxEditor\Storage\FilesystemDocxStorage;

return [
    // Добавлять ли страницу редактора в меню MoonShine автоматически
    'auto_menu' => true,

    // Документы, доступные в редакторе: ключ => заголовок
    'documents' => [
        // 'offer' => 'Публичная оферта',
    ],

    // Gate-ability, которую должен иметь пользователь для чтения и правки документов.
    // null — доступно любому авторизованному пользователю MoonShine. Ability получает ключ документа.
    'ability' => null,

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
