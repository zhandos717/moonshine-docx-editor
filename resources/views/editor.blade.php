@php
    $paths = [
        'open' => 'M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776',
        'folder' => 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z',
        'upload' => 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5',
        'download' => 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3',
        'expand' => 'M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15',
        'save' => 'm4.5 12.75 6 6 9-13.5',
        'plus' => 'M12 4.5v15m7.5-7.5h-15',
    ];
    $ico = fn (string $name) => '<svg class="dx-ico" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'.$paths[$name].'"/></svg>';
@endphp
<style>
    .dx { border: 1px solid rgba(128, 128, 128, .28); border-radius: 12px; overflow: hidden; }
    .dx .btn { display: inline-flex; align-items: center; gap: 6px; }
    .dx-ico { width: 18px; height: 18px; flex: none; }
    .dx-bar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding: 8px 12px; border-bottom: 1px solid rgba(128, 128, 128, .28); }
    .dx-title { font-weight: 600; margin: 0 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 50%; }
    .dx-spacer { flex: 1; }
    .dx-status { font-size: 13px; opacity: .85; }
    .dx-editor { display: block; height: calc(100vh - 190px); min-height: 480px; }
    .dx.is-expanded, .dx:fullscreen { position: fixed; inset: 0; z-index: 1000; background: var(--color-body, Canvas); border-radius: 0; border: 0; display: flex; flex-direction: column; }
    .dx.is-expanded .dx-editor, .dx:fullscreen .dx-editor { height: auto; flex: 1; min-height: 0; }
    .dx-dialog { margin: auto; border: 1px solid rgba(128, 128, 128, .35); border-radius: 14px; padding: 0; width: min(520px, calc(100vw - 32px)); background: var(--color-body, Canvas); color: inherit; }
    .dx-dialog::backdrop { background: rgba(0, 0, 0, .55); }
    .dx-dialog-wide { width: min(900px, calc(100vw - 32px)); }
    .dx-dialog-wide .dx-dialog-body { max-height: 70vh; min-height: 340px; }
    .dx-dialog-wide .dx-item { padding: 11px 14px; font-size: 15px; }
    .dx-dialog-wide [data-docx-list] { min-height: 220px; flex: 1; }
    .dx-dialog-head { padding: 14px 18px; font-weight: 600; border-bottom: 1px solid rgba(128, 128, 128, .28); display: flex; align-items: center; }
    .dx-dialog-body { padding: 12px 18px; display: flex; flex-direction: column; gap: 10px; max-height: 60vh; overflow: auto; }
    .dx-dialog-foot { padding: 12px 18px; border-top: 1px solid rgba(128, 128, 128, .28); display: flex; gap: 8px; justify-content: flex-end; }
    .dx-item { display: block; padding: 9px 12px; border-radius: 8px; text-decoration: none; color: inherit; font-size: 14px; line-height: 1.35; }
    .dx-item:hover { background: rgba(128, 128, 128, .14); }
    .dx-item.is-active { background: rgba(128, 128, 128, .2); font-weight: 600; box-shadow: inset 3px 0 0 var(--color-primary, #7843e9); }
    .dx-new { display: flex; gap: 8px; padding-top: 10px; border-top: 1px solid rgba(128, 128, 128, .28); }
    .dx-new input { flex: 1; min-width: 0; }
    .dx-tabs { display: flex; gap: 4px; padding: 10px 18px 0; }
    .dx-tab { padding: 6px 12px; border-radius: 8px 8px 0 0; border: 0; background: transparent; color: inherit; cursor: pointer; opacity: .7; }
    .dx-tab.is-active { opacity: 1; font-weight: 600; box-shadow: inset 0 -2px 0 var(--color-primary, #7843e9); }
    .dx-crumbs { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; font-size: 13px; }
    .dx-crumbs button { border: 0; background: transparent; color: inherit; cursor: pointer; padding: 2px 4px; border-radius: 4px; text-decoration: underline; }
    .dx-row { display: flex; gap: 8px; align-items: center; }
    .dx-row svg { opacity: .7; }
    .dx-check { display: flex; gap: 6px; align-items: center; font-size: 14px; }
</style>

@if ($key === null && $file === null && ! $canCreate && ! $browser)
    <p>{{ __('moonshine-docx-editor::ui.no_documents') }}</p>
@else
    <div class="dx" data-docx-editor
         data-page-url="{{ $pageUrl }}"
         data-hide-unwired="{{ config('docx-editor.hide_unwired') ? 1 : 0 }}"
         data-msg-load-failed="{{ __('moonshine-docx-editor::ui.load_failed') }}"
         data-msg-not-ready="{{ __('moonshine-docx-editor::ui.not_ready') }}"
         data-msg-saved="{{ __('moonshine-docx-editor::ui.saved') }}"
         data-msg-saved-edition="{{ __('moonshine-docx-editor::ui.saved_edition') }}"
         data-msg-save-failed="{{ __('moonshine-docx-editor::ui.save_failed') }}"
         data-browse-url="{{ route('moonshine.docx-editor.files.browse') }}"
         data-mkdir-url="{{ route('moonshine.docx-editor.files.mkdir') }}"
         @if ($file !== null)
         data-mode="file"
         data-file-path="{{ $file }}"
         data-file-url="{{ route('moonshine.docx-editor.files.content') }}?path={{ urlencode($file) }}"
         data-save-url="{{ route('moonshine.docx-editor.files.save') }}"
         @elseif ($key !== null)
         data-mode="document"
         data-file-url="{{ route('moonshine.docx-editor.file', $key) }}"
         data-save-url="{{ route('moonshine.docx-editor.save', $key) }}"
         @endif>
        <div class="dx-bar">
            <button type="button" class="btn" data-docx-open>{!! $ico('open') !!}{{ __('moonshine-docx-editor::ui.open') }}</button>
            @if ($key !== null || $file !== null)
                <span class="dx-title" title="{{ $title }}">{{ $title }}</span>
                <span class="dx-spacer"></span>
                <span class="dx-status" data-docx-status></span>
                <button type="button" class="btn" data-docx-fullscreen>{!! $ico('expand') !!}{{ __('moonshine-docx-editor::ui.fullscreen') }}</button>
            @endif
        </div>
        @if ($key !== null || $file !== null)
            <docen-document class="dx-editor" lang="{{ str_replace('_', '-', app()->getLocale()) }}" filename="{{ $title }}{{ $file !== null ? '' : '.docx' }}"></docen-document>
        @endif

        <dialog class="dx-dialog dx-dialog-wide" data-docx-open-dialog>
            <div class="dx-dialog-head">{{ __('moonshine-docx-editor::ui.open') }}</div>
            @if ($browser)
                <div class="dx-tabs">
                    <button type="button" class="dx-tab is-active" data-docx-tab="docs">{{ __('moonshine-docx-editor::ui.documents') }}</button>
                    <button type="button" class="dx-tab" data-docx-tab="files">{{ __('moonshine-docx-editor::ui.files') }}</button>
                </div>
            @endif
            <div class="dx-dialog-body" data-docx-panel="docs">
                <nav>
                    @foreach ($documents as $docKey => $docTitle)
                        <a href="{{ $pageUrl }}?key={{ $docKey }}"
                           class="dx-item {{ $file === null && $docKey === $key ? 'is-active' : '' }}">{{ $docTitle }}</a>
                    @endforeach
                </nav>
                @if ($canCreate)
                    <div class="dx-new">
                        <input type="text" data-docx-new-title class="form-input"
                               placeholder="{{ __('moonshine-docx-editor::ui.new_title') }}">
                        <button type="button" class="btn btn-primary" data-docx-new>{!! $ico('plus') !!}{{ __('moonshine-docx-editor::ui.create') }}</button>
                    </div>
                @endif
            </div>
            @if ($browser)
                <div class="dx-dialog-body" data-docx-panel="files" hidden>
                    <div class="dx-crumbs" data-docx-crumbs></div>
                    <nav data-docx-list></nav>
                    <div class="dx-new">
                        <input type="text" data-docx-folder-name class="form-input" placeholder="{{ __('moonshine-docx-editor::ui.folder_name') }}">
                        <button type="button" class="btn" data-docx-mkdir>{!! $ico('plus') !!}{{ __('moonshine-docx-editor::ui.folder') }}</button>
                    </div>
                    <div class="dx-new" style="border-top:0;padding-top:0">
                        <input type="text" data-docx-file-name class="form-input" placeholder="{{ __('moonshine-docx-editor::ui.file_name') }}">
                        <button type="button" class="btn" data-docx-mkfile>{!! $ico('plus') !!}{{ __('moonshine-docx-editor::ui.file') }}</button>
                    </div>
                </div>
            @endif
            <div class="dx-dialog-foot">
                <button type="button" class="btn" data-docx-close>{{ __('moonshine-docx-editor::ui.cancel') }}</button>
            </div>
        </dialog>

        @if ($key !== null && $file === null)
            <dialog class="dx-dialog" data-docx-save-dialog>
                <div class="dx-dialog-head">{{ __('moonshine-docx-editor::ui.save_title') }}</div>
                <div class="dx-dialog-body">
                    <input type="text" name="note" maxlength="500" class="form-input"
                           placeholder="{{ __('moonshine-docx-editor::ui.note') }}">
                    <label class="dx-check">
                        <input type="checkbox" name="requires_reaccept">
                        {{ __('moonshine-docx-editor::ui.requires_reaccept') }}
                    </label>
                </div>
                <div class="dx-dialog-foot">
                    <button type="button" class="btn" data-docx-close>{{ __('moonshine-docx-editor::ui.cancel') }}</button>
                    <button type="button" class="btn btn-primary" data-docx-save-confirm>{!! $ico('save') !!}{{ __('moonshine-docx-editor::ui.confirm') }}</button>
                </div>
            </dialog>
        @endif
    </div>

    <script type="module" src="{{ asset('vendor/moonshine-docx-editor/docx-editor.js') }}"></script>
@endif
