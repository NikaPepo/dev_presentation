import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * x-copy — click-to-copy. Usage: <button x-copy="text-to-copy">Copy</button>
 */
Alpine.directive('copy', (el, { expression }, { evaluate }) => {
    el.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(evaluate(expression) ?? '');
            const original = el.innerText;
            el.innerText = 'Copied!';
            setTimeout(() => { el.innerText = original; }, 1200);
        } catch {
            /* clipboard blocked */
        }
    });
});

/**
 * x-format-json — pretty-print a JSON string. Usage: <pre x-format-json="rawJson"></pre>
 */
Alpine.directive('format-json', (el, { expression }, { evaluate }) => {
    Alpine.effect(() => {
        const value = evaluate(expression);
        if (!value) { el.textContent = ''; return; }
        try {
            el.textContent = JSON.stringify(JSON.parse(value), null, 2);
        } catch {
            el.textContent = String(value);
        }
    });
});

Alpine.start();
