const shortFormatter = new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' });
const longFormatter = new Intl.DateTimeFormat('tr-TR', { year: 'numeric', month: 'long', day: 'numeric' });
const dateTimeFormatter = new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

export function formatDate(date) {
    if (!date) return '';
    return shortFormatter.format(new Date(date));
}

export function formatDateLong(date) {
    if (!date) return '';
    return longFormatter.format(new Date(date));
}

export function formatDateTime(date) {
    if (!date) return '';
    return dateTimeFormatter.format(new Date(date));
}
