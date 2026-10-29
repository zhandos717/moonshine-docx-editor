import {registerComponents, registerTranslation, applyTheme} from '@docen/editor';
import ru from './ru.json';
import {restoreFields} from './restore-fields.js';

registerComponents();
registerTranslation({languageTag: 'ru', $name: 'Русский', translations: ru});

const mount = document.querySelector('[data-docx-editor]');

if (mount) {
    const q = (selector) => document.querySelector(selector);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const openDialog = q('[data-docx-open-dialog]');
    const saveDialog = q('[data-docx-save-dialog]');
    const fileMode = mount.dataset.mode === 'file';

    const icon = (d) => {
        const ns = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('class', 'dx-ico');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '1.8');
        const path = document.createElementNS(ns, 'path');
        path.setAttribute('d', d);
        path.setAttribute('stroke-linecap', 'round');
        path.setAttribute('stroke-linejoin', 'round');
        svg.append(path);
        return svg;
    };
    const FOLDER = 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z';
    const DOC = 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z';

    document.querySelectorAll('[data-docx-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog').close());
    });

    q('[data-docx-open]').addEventListener('click', () => {
        openDialog.showModal();
        if (!filesPanel?.hidden) browse(currentPath);
    });

    q('[data-docx-new]')?.addEventListener('click', () => {
        const title = q('[data-docx-new-title]').value.trim();
        if (!title) return;
        const slug = title.toLowerCase().normalize('NFKD').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 64)
            || `document-${Math.random().toString(36).slice(2, 8)}`;
        window.location.href = `${mount.dataset.pageUrl}?key=${encodeURIComponent(slug)}&title=${encodeURIComponent(title)}`;
    });

    const filesPanel = q('[data-docx-panel="files"]');
    const docsPanel = q('[data-docx-panel="docs"]');
    let currentPath = fileMode ? mount.dataset.filePath.split('/').slice(0, -1).join('/') : '';

    const join = (base, name) => (base ? `${base}/${name}` : name);

    const browse = async (path) => {
        const res = await fetch(`${mount.dataset.browseUrl}?path=${encodeURIComponent(path)}`, {
            credentials: 'same-origin',
            headers: {Accept: 'application/json'},
        });
        if (!res.ok) return;
        const data = await res.json();
        currentPath = data.path;

        const crumbs = q('[data-docx-crumbs]');
        crumbs.replaceChildren();
        const parts = currentPath ? currentPath.split('/') : [];
        ['', ...parts].forEach((name, i) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = i === 0 ? '/' : name;
            button.addEventListener('click', () => browse(parts.slice(0, i).join('/')));
            crumbs.append(button);
            if (i < parts.length) crumbs.append(document.createTextNode('›'));
        });

        const list = q('[data-docx-list]');
        list.replaceChildren();
        data.dirs.forEach((name) => {
            const row = document.createElement('a');
            row.href = '#';
            row.className = 'dx-item dx-row';
            row.append(icon(FOLDER), document.createTextNode(name));
            row.addEventListener('click', (e) => {
                e.preventDefault();
                browse(join(currentPath, name));
            });
            list.append(row);
        });
        data.files.forEach((name) => {
            const full = join(currentPath, name);
            const row = document.createElement('a');
            row.href = `${mount.dataset.pageUrl}?file=${encodeURIComponent(full)}`;
            row.className = `dx-item dx-row${fileMode && full === mount.dataset.filePath ? ' is-active' : ''}`;
            row.append(icon(DOC), document.createTextNode(name));
            list.append(row);
        });
    };

    document.querySelectorAll('[data-docx-tab]').forEach((tab) => {
        tab.addEventListener('click', () => {
            const files = tab.dataset.docxTab === 'files';
            document.querySelectorAll('[data-docx-tab]').forEach((t) => t.classList.toggle('is-active', t === tab));
            docsPanel.hidden = files;
            filesPanel.hidden = !files;
            if (files) browse(currentPath);
        });
    });

    q('[data-docx-mkdir]')?.addEventListener('click', async () => {
        const input = q('[data-docx-folder-name]');
        const name = input.value.trim().replace(/[\\/]/g, '');
        if (!name) return;
        const res = await fetch(mount.dataset.mkdirUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'X-CSRF-TOKEN': csrf, Accept: 'application/json', 'Content-Type': 'application/json'},
            body: JSON.stringify({path: join(currentPath, name)}),
        });
        if (res.ok) {
            input.value = '';
            browse(currentPath);
        }
    });

    q('[data-docx-mkfile]')?.addEventListener('click', () => {
        const input = q('[data-docx-file-name]');
        let name = input.value.trim().replace(/[\\/]/g, '');
        if (!name) return;
        if (!name.toLowerCase().endsWith('.docx')) name += '.docx';
        window.location.href = `${mount.dataset.pageUrl}?file=${encodeURIComponent(join(currentPath, name))}`;
    });

    const doc = mount.querySelector('docen-document');

    if (doc) {
        const status = mount.querySelector('[data-docx-status]');
        const t = (key) => mount.dataset[`msg${key}`];

        const syncTheme = () => applyTheme(document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        syncTheme();
        new MutationObserver(syncTheme).observe(document.documentElement, {attributes: true, attributeFilter: ['class']});

        const say = (text, ok = true) => {
            status.textContent = text;
            status.style.color = ok ? '' : '#c0392b';
        };

        let ready = false;
        let original = null;

        const load = async () => {
            const res = await fetch(mount.dataset.fileUrl, {credentials: 'same-origin'});
            if (res.ok) {
                original = await res.arrayBuffer();
                await doc.openDOCX(original);
                const width = doc.getBoundingClientRect().width;
                doc.setZoom(Math.max(40, Math.min(100, Math.floor(((width - 48) / 794) * 100))));
                ready = true;
            } else if (res.status === 404) {
                ready = true;
            } else {
                say(t('LoadFailed'), false);
            }
        };

        const send = async (extra) => {
            let bytes = await doc.saveDOCX();
            if (original) bytes = restoreFields(original, bytes);
            const form = new FormData();
            form.append('file', new Blob([bytes], {type: mime}), 'document.docx');
            Object.entries(extra).forEach(([key, value]) => form.append(key, value));

            const res = await fetch(mount.dataset.saveUrl, {
                method: 'POST',
                body: form,
                credentials: 'same-origin',
                headers: {'X-CSRF-TOKEN': csrf, Accept: 'application/json'},
            });
            const json = await res.json().catch(() => ({}));
            const okText = fileMode ? t('Saved') : `${t('SavedEdition')} ${json.version}`;
            if (res.ok) original = bytes;
            say(res.ok ? okText : (json.message ?? t('SaveFailed')), res.ok);
        };

        const saveDocument = () => send({
            note: saveDialog.querySelector('[name=note]').value,
            requires_reaccept: saveDialog.querySelector('[name=requires_reaccept]').checked ? '1' : '0',
        });

        q('[data-docx-save-confirm]')?.addEventListener('click', async () => {
            saveDialog.close();
            await saveDocument();
        });

        doc.addEventListener('docen:save', (e) => {
            e.preventDefault();
            if (!ready) {
                say(t('NotReady'), false);
                return;
            }
            if (fileMode) send({path: mount.dataset.filePath});
            else saveDialog.showModal();
        });

        const setExpanded = (on) => {
            mount.classList.toggle('is-expanded', on);
            if (on) {
                mount.requestFullscreen?.().catch(() => {});
            } else if (document.fullscreenElement) {
                document.exitFullscreen();
            }
        };

        q('[data-docx-fullscreen]').addEventListener('click', () => setExpanded(!mount.classList.contains('is-expanded')));
        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) mount.classList.remove('is-expanded');
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !document.querySelector('dialog[open]')) setExpanded(false);
        });

        load();
    }
}
