# Moonshine Docx Editor

[![MoonShine](https://img.shields.io/badge/MoonShine-4.x-purple)](https://moonshine-laravel.com)

WYSIWYG-редактор `.docx` прямо в админке [MoonShine](https://moonshine-laravel.com). Открывает файл в браузере, правки сохраняются как новая редакция. Редактор — веб-компонент [docen](https://github.com/DemoMacro/docen) (`<docen-document>`), серверный Office не нужен.

> docen до версии 1.0 нестабилен (так написано в его README). Версия в пакете зафиксирована: `@docen/editor` 0.7.0. Проверяйте свои документы на сохранение форматирования до боевого использования.

## Требования

| | Версия |
|---|---|
| PHP | 8.2+ |
| Laravel | 10, 11, 12, 13 |
| MoonShine | 4.x |

## Установка

```bash
composer require zhandos717/moonshine-docx-editor
php artisan vendor:publish --tag=moonshine-docx-editor-config
php artisan vendor:publish --tag=moonshine-docx-editor-assets
```

Собранный JS-бандл (~3,7 МБ, ~1 МБ в gzip) лежит в `dist/` и публикуется в `public/vendor/moonshine-docx-editor`. `npm` на сервере не нужен. Ассеты также входят в тег `laravel-assets`: если в `composer.json` приложения есть `vendor:publish --tag=laravel-assets --force` (как в стандартном Laravel), они обновляются сами при `composer install/update`.

## Использование

Перечислите документы в `config/docx-editor.php`:

```php
'documents' => [
    'offer' => 'Публичная оферта',
    'privacy-policy' => 'Политика конфиденциальности',
],
```

В меню MoonShine появится страница «Редактор документов» (отключается `auto_menu => false`; тогда добавьте `DocxEditorPage::class` в меню сами). На ней переключатель документов, редактор, поле примечания, галочка «Требует повторного согласия» и кнопка «Сохранить как новую редакцию».

## Свое хранилище

По умолчанию каждая редакция пишется на диск Laravel: `docx-editor/{key}/{Y-m-d_His}.docx`, текущая — последняя. Чтобы хранить редакции в БД, вести журнал или привязывать согласия пользователей, реализуйте контракт и укажите класс в `storage`:

```php
use Zhandos717\MoonshineDocxEditor\Contracts\DocxStorage;

class LegalDocxStorage implements DocxStorage
{
    public function read(string $key): ?string { /* байты текущей редакции или null */ }

    public function write(string $key, string $contents, array $meta = []): array
    {
        // $meta: note, requires_reaccept, user
        return ['version' => '2026-10-28'];
    }
}
```

## Конфигурация

- `auto_menu` — добавлять страницу в меню автоматически
- `documents` — ключ => заголовок доступных документов
- `ability` — Gate-ability для доступа к редактору (`null` — любой авторизованный пользователь MoonShine); получает ключ документа
- `storage` — класс, реализующий `Contracts\DocxStorage`
- `disk`, `directory` — параметры стандартного хранилища
- `max_size_kb` — максимальный размер загружаемого файла (10240)

## Безопасность

По умолчанию редактировать документы может любой пользователь админки. Ограничьте это через `ability`:

```php
// config/docx-editor.php
'ability' => 'edit-legal-documents',

// AppServiceProvider
Gate::define('edit-legal-documents', fn ($user, $key = null) => $user->moonshine_user_role_id === 1);
```

Маршруты `docx-editor/{key}/file` и `docx-editor/{key}` закрыты middleware авторизации MoonShine. Ключ документа проверяется по списку `documents`, содержимое по сигнатуре DOCX (ZIP).

## Сборка ассетов (для разработчиков пакета)

```bash
npm install
npm run build   # dist/docx-editor.js
```

## Лицензия

MIT
