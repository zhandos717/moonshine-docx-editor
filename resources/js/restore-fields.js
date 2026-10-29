import {unzipSync, zipSync, strFromU8} from 'fflate';

const PART = /^word\/(footer|header)\d*\.xml$/;

const kind = (name) => name.match(PART)[1];
const hasRelationships = (data) => data !== undefined && /<Relationship\s/.test(strFromU8(data));
const hasFields = (xml) => /<w:fldChar|<w:fldSimple|<w:instrText/.test(xml);
const textOf = (xml) => [...xml.matchAll(/<w:t(?:\s[^>]*)?>([^<]*)<\/w:t>/g)].map((m) => m[1]).join('').replace(/\s+/g, '');

// docen при сохранении теряет поля колонтитулов (PAGE, NUMPAGES). Если колонтитул после сохранения
// совпадает с началом исходного колонтитула с полями, значит его не правили: возвращаем оригинал.
export function restoreFields(original, saved) {
    const before = unzipSync(new Uint8Array(original));
    const after = unzipSync(new Uint8Array(saved));

    const candidates = Object.entries(before)
        .filter(([name]) => PART.test(name) && !hasRelationships(before[`${name.replace(/^word\//, 'word/_rels/')}.rels`]))
        .map(([name, data]) => ({kind: kind(name), xml: strFromU8(data)}))
        .filter(({xml}) => hasFields(xml))
        .map((part) => ({...part, text: textOf(part.xml)}));

    if (candidates.length === 0) return new Uint8Array(saved);

    let changed = false;
    for (const name of Object.keys(after)) {
        if (!PART.test(name)) continue;

        const xml = strFromU8(after[name]);
        const text = textOf(xml);
        if (hasFields(xml) || text === '') continue;

        const index = candidates.findIndex((part) => part.kind === kind(name) && part.text.startsWith(text));
        if (index === -1) continue;

        after[name] = new TextEncoder().encode(candidates[index].xml);
        candidates.splice(index, 1);
        changed = true;
    }

    return changed ? zipSync(after) : new Uint8Array(saved);
}
