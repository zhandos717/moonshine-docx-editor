const BUTTONS = 'docen-ribbon-button, docen-ribbon-split-button';
const CONTROLS = `${BUTTONS}, docen-ribbon-combobox, docen-ribbon-menu, docen-ribbon-checkbox, docen-ribbon-gallery`;

const walk = (root, visit) => {
    visit(root);
    root.querySelectorAll('*').forEach((el) => {
        if (el.shadowRoot) walk(el.shadowRoot, visit);
    });
};

const isHidden = (el) => el.style.display === 'none';

const controlsOf = (group) => {
    const found = [];
    const collect = (root) => root.querySelectorAll(CONTROLS).forEach((control) => found.push(control));

    walk(group, collect);
    if (group.shadowRoot) walk(group.shadowRoot, collect);

    return found;
};

// docen показывает недоделанные команды ленты серыми (атрибут disabled). Скрываем их, пустые группы
// и вкладки; если кнопка позже станет активной, она вернётся.
export function hideUnwired(host, hiddenCommands = []) {
    const apply = () => {
        if (document.hidden || !host.shadowRoot) return;

        const roots = [];
        walk(host.shadowRoot, (root) => roots.push(root));

        roots.forEach((root) => root.querySelectorAll(BUTTONS).forEach((button) => {
            const off = button.hasAttribute('disabled') || hiddenCommands.includes(button.getAttribute('event'));
            button.style.display = off ? 'none' : '';
        }));

        roots.forEach((root) => root.querySelectorAll('docen-ribbon-group').forEach((group) => {
            const controls = controlsOf(group);
            group.style.display = controls.length > 0 && controls.every(isHidden) ? 'none' : '';
        }));

        roots.forEach((root) => root.querySelectorAll('docen-ribbon').forEach((ribbon) => {
            const panels = [...ribbon.children].filter((el) => el.tagName === 'DOCEN-RIBBON-PANEL');
            const tabs = [...(ribbon.querySelector('fluent-tablist')?.children ?? [])];
            if (panels.length !== tabs.length) return;

            panels.forEach((panel, i) => {
                const groups = [...panel.querySelectorAll('docen-ribbon-group')];
                tabs[i].style.display = groups.length > 0 && groups.every(isHidden) ? 'none' : '';
            });
        }));
    };

    apply();
    return setInterval(apply, 1000);
}
