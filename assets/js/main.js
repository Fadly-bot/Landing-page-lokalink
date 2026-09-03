// Lokalink - JavaScript minimal
// Hanya untuk interaksi UI ringan. Fungsi utama website tetap jalan tanpa JS.
(function () {
    'use strict';

    var toggle = document.getElementById('menu-toggle');
    var menu = document.getElementById('mobile-menu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', function () {
        var open = menu.classList.toggle('hidden') === false;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    });

    // Tutup menu mobile setelah link diklik.
    menu.addEventListener('click', function (e) {
        if (e.target.tagName === 'A') {
            menu.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();
