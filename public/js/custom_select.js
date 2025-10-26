document.querySelectorAll('.custom-select').forEach(function(sel) {
    const btn = sel.querySelector('.select-btn');
    const list = sel.querySelector('.select-options');
    const options = sel.querySelectorAll('.select-options li');
    const value = sel.querySelector('.selected-value');

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = sel.classList.toggle('open');
        sel.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    options.forEach(function(opt) {
        opt.addEventListener('click', function(e) {
            e.stopPropagation();
            value.textContent = this.textContent;
            sel.classList.remove('open');
            sel.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('click', function() {
        sel.classList.remove('open');
        sel.setAttribute('aria-expanded', 'false');
    });

    sel.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            sel.classList.remove('open');
            sel.setAttribute('aria-expanded', 'false');
        }
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const isOpen = sel.classList.toggle('open');
            sel.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }
    });
});
