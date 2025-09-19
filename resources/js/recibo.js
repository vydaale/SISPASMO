document.querySelectorAll('[data-open]').forEach(btn => {
    btn.addEventListener('click', () => {
        const sel = btn.getAttribute('data-open');
        const el = document.querySelector(sel);
        if (el) el.style.display = 'flex';
    });
});
document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => {
        const sel = btn.getAttribute('data-close');
        const el = document.querySelector(sel);
        if (el) el.style.display = 'none';
    });
});