import DOMPurify from 'dompurify';

const ALLOWED_TAGS = [
    // Yapısal
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span', 'br', 'hr',
    // Metin biçimlendirme
    'strong', 'em', 'b', 'i', 'u', 's', 'del', 'ins', 'mark', 'small', 'sub', 'sup',
    'code', 'pre', 'kbd', 'samp', 'var', 'abbr', 'cite', 'q', 'dfn',
    // Listeler
    'ul', 'ol', 'li', 'dl', 'dt', 'dd',
    // Tablolar
    'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'colgroup', 'col',
    // Medya & bağlantı
    'a', 'img', 'figure', 'figcaption', 'picture', 'source', 'video', 'audio',
    // Blok elemanlar
    'blockquote', 'details', 'summary', 'section', 'article', 'aside', 'header', 'footer', 'nav', 'main',
];

const ALLOWED_ATTR = [
    'href', 'src', 'alt', 'title', 'class', 'id', 'name',
    'target', 'rel', 'width', 'height', 'loading', 'decoding',
    'colspan', 'rowspan', 'scope', 'headers',
    'open', 'start', 'reversed', 'type',
    'controls', 'autoplay', 'loop', 'muted', 'preload', 'poster',
    'srcset', 'sizes', 'media',
];

/**
 * DOMPurify ile HTML sanitize eder.
 * Blog içerikleri (rendered markdown) için güvenli v-html kullanımı sağlar.
 */
export function useSanitize() {
    function sanitize(dirty) {
        if (!dirty) return '';

        return DOMPurify.sanitize(dirty, {
            ALLOWED_TAGS,
            ALLOWED_ATTR,
            ALLOW_DATA_ATTR: false,
            ADD_ATTR: ['target'],
            FORBID_TAGS: ['style', 'script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'button'],
            FORBID_ATTR: ['onerror', 'onload', 'onclick', 'onmouseover', 'onfocus', 'onblur'],
        });
    }

    return { sanitize };
}
