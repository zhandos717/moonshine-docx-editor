<style>
    .dx { display: grid; gap: 16px; grid-template-columns: minmax(0, 1fr); align-items: start; }
    @media (min-width: 1000px) { .dx { grid-template-columns: 270px minmax(0, 1fr); } }
    .dx-card { border: 1px solid rgba(128, 128, 128, .28); border-radius: 12px; overflow: hidden; }
    .dx-head { padding: 12px 16px; font-weight: 600; border-bottom: 1px solid rgba(128, 128, 128, .28); }
    .dx-list { display: flex; flex-direction: column; padding: 8px; gap: 2px; }
    .dx-item { display: block; padding: 9px 12px; border-radius: 8px; text-decoration: none; color: inherit; font-size: 14px; line-height: 1.35; }
    .dx-item:hover { background: rgba(128, 128, 128, .14); }
    .dx-item.is-active { background: rgba(128, 128, 128, .2); font-weight: 600; box-shadow: inset 3px 0 0 var(--primary-color, #7843e9); }
    .dx-new { display: flex; gap: 8px; padding: 12px; border-top: 1px solid rgba(128, 128, 128, .28); }
    .dx-new input { flex: 1; min-width: 0; }
    .dx-bar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; padding: 10px 16px; }
    .dx-bar + .dx-bar { border-top: 1px solid rgba(128, 128, 128, .28); }
    .dx-spacer { flex: 1; }
    .dx-status { font-size: 13px; opacity: .85; }
    .dx-editor { display: block; height: 72vh; }
    .dx-bar label.dx-check { display: flex; gap: 6px; align-items: center; font-size: 14px; }
</style>

@if ($key === null && ! $canCreate)
    <p>{{ __('moonshine-docx-editor::ui.no_documents') }}</p>
@else
    <div class="dx" data-docx-editor
         @if ($key !== null)
         data-file-url="{{ route('moonshine.docx-editor.file', $key) }}"
         data-save-url="{{ route('moonshine.docx-editor.save', $key) }}"
         @endif
         data-page-url="{{ $pageUrl }}">
        <aside class="dx-card">
            <div class="dx-head">{{ __('moonshine-docx-editor::ui.documents') }}</div>
            <nav class="dx-list">
                @foreach ($documents as $docKey => $docTitle)
                    <a href="{{ $pageUrl }}?key={{ $docKey }}"
                       class="dx-item {{ $docKey === $key ? 'is-active' : '' }}">{{ $docTitle }}</a>
                @endforeach
            </nav>
            @if ($canCreate)
                <div class="dx-new">
                    <input type="text" data-docx-new-title class="form-input"
                           placeholder="{{ __('moonshine-docx-editor::ui.new_title') }}">
                    <button type="button" class="btn" data-docx-new>{{ __('moonshine-docx-editor::ui.create') }}</button>
                </div>
            @endif
        </aside>

        @if ($key !== null)
            <section class="dx-card">
                <div class="dx-bar">
                    <strong>{{ $title }}</strong>
                    <span class="dx-spacer"></span>
                    <label class="btn" style="cursor:pointer;margin:0">
                        {{ __('moonshine-docx-editor::ui.upload') }}
                        <input type="file" accept=".docx" data-docx-upload hidden>
                    </label>
                    <a class="btn" href="{{ route('moonshine.docx-editor.file', $key) }}?download=1">{{ __('moonshine-docx-editor::ui.download') }}</a>
                </div>
                <docen-document class="dx-editor" filename="{{ $title }}.docx"></docen-document>
                <div class="dx-bar">
                    <input type="text" name="note" maxlength="500" class="form-input" style="flex:1;min-width:200px"
                           placeholder="{{ __('moonshine-docx-editor::ui.note') }}">
                    <label class="dx-check">
                        <input type="checkbox" name="requires_reaccept">
                        {{ __('moonshine-docx-editor::ui.requires_reaccept') }}
                    </label>
                    <button type="button" class="btn btn-primary" data-docx-save>{{ __('moonshine-docx-editor::ui.save') }}</button>
                    <span class="dx-status" data-docx-status></span>
                </div>
            </section>
        @endif
    </div>
    <script type="module" src="{{ asset('vendor/moonshine-docx-editor/docx-editor.js') }}"></script>
@endif
