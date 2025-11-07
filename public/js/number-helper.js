/**
 * Persian-English Digit Helper
 * Author: Faraz Gorgin Paveh
 * Converts digits automatically before sending forms or AJAX data.
 */

// --- Convert Persian → English ---
function toEnglishDigits(str) {
    if (!str) return '';
    const persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return str.replace(/[۰-۹]/g, d => persian.indexOf(d));
}

// --- Convert English → Persian ---
function toPersianDigits(str) {
    if (!str) return '';
    const persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return str.toString().replace(/\d/g, d => persian[d]);
}

// --- Auto Convert before form submit ---
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!form || !form.querySelectorAll) return;

    form.querySelectorAll('input, textarea').forEach(el => {
        if (el.name && el.value) {
            el.value = toEnglishDigits(el.value);
        }
    });
}, true);

// --- Optional: convert all visible numbers on page to Persian ---
document.addEventListener('DOMContentLoaded', function() {
    // Convert all visible numeric text to Persian (optional UX polish)
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
        const node = walker.currentNode;
        if (/\d/.test(node.nodeValue)) {
            node.nodeValue = toPersianDigits(node.nodeValue);
        }
    }
});
