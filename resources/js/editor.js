import {registerComponents, applyTheme} from '@docen/editor';

registerComponents();

const mount = document.querySelector('[data-docx-editor]');

if (mount) {
    const doc = mount.querySelector('docen-document');
    const status = mount.querySelector('[data-docx-status]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    applyTheme(document.documentElement.classList.contains('dark') ? 'dark' : 'light');

    const say = (text, ok = true) => {
        status.textContent = text;
        status.style.color = ok ? '' : '#c0392b';
    };

    const load = async () => {
        const res = await fetch(mount.dataset.fileUrl, {credentials: 'same-origin'});
        if (res.ok) {
            await doc.openDOCX(await res.arrayBuffer());
        } else if (res.status !== 404) {
            say('Не удалось загрузить документ', false);
        }
    };

    const save = async () => {
        const bytes = await doc.saveDOCX();
        const form = new FormData();
        form.append('file', new Blob([bytes], {type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'}), 'document.docx');
        form.append('note', mount.querySelector('[name=note]')?.value ?? '');
        form.append('requires_reaccept', mount.querySelector('[name=requires_reaccept]')?.checked ? '1' : '0');

        const res = await fetch(mount.dataset.saveUrl, {
            method: 'POST',
            body: form,
            credentials: 'same-origin',
            headers: {'X-CSRF-TOKEN': csrf, Accept: 'application/json'},
        });
        const json = await res.json().catch(() => ({}));
        say(res.ok ? `Сохранено: редакция ${json.version}` : (json.message ?? 'Не удалось сохранить'), res.ok);
    };

    mount.querySelector('[data-docx-save]').addEventListener('click', save);
    doc.addEventListener('docen:save', (e) => {
        e.preventDefault();
        save();
    });
    load();
}
