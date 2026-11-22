(function () {
    'use strict';

    var admin = window.tpcAdmin || { ajax: '', nonce: '' };

    function post(action) {
        var body = new URLSearchParams();
        body.set('action', action);
        body.set('nonce', admin.nonce);

        return fetch(admin.ajax, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body
        }).then(function (r) { return r.json(); });
    }

    var tabs = document.querySelectorAll('.thistle-press-cache-tabs .nav-tab');
    var panels = document.querySelectorAll('.thistle-press-cache-panel');

    function activate(hash) {
        var id = (hash || '#dashboard').replace('#', '');
        var found = false;

        panels.forEach(function (panel) {
            var on = panel.id === id;
            panel.classList.toggle('is-active', on);
            found = found || on;
        });

        tabs.forEach(function (tab) {
            tab.classList.toggle('nav-tab-active', tab.getAttribute('href') === '#' + id);
        });

        if (!found && panels.length) {
            panels[0].classList.add('is-active');
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            var hash = tab.getAttribute('href');
            history.replaceState(null, '', hash);
            activate(hash);
        });
    });

    activate(window.location.hash);

    function bind(id, action, render) {
        var btn = document.getElementById(id);
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            btn.disabled = true;
            post(action).then(function (res) {
                btn.disabled = false;
                render(res);
            }).catch(function () {
                btn.disabled = false;
            });
        });
    }

    bind('thistle-press-cache-clear', 'tpc_clear_cache', function (res) {
        var out = document.getElementById('thistle-press-cache-clear-result');
        if (out) {
            out.textContent = res && res.success ? 'Purged ' + (res.data.removed || 0) + ' file(s).' : 'Failed.';
        }
    });

    bind('thistle-press-cache-db', 'tpc_db_run', function (res) {
        var out = document.getElementById('thistle-press-cache-db-result');
        if (out) {
            out.textContent = res && res.success ? 'Cleaned and optimized.' : 'Failed.';
        }
    });

    bind('thistle-press-cache-webp', 'tpc_bulk_webp', function (res) {
        var out = document.getElementById('thistle-press-cache-webp-result');
        if (!out) {
            return;
        }

        if (!res || !res.success) {
            out.textContent = 'Server lacks GD WebP support.';
            return;
        }

        out.textContent = 'Processed ' + res.data.processed + ' image(s).';
    });

    bind('thistle-press-cache-refresh', 'tpc_refresh_env', function (res) {
        if (!res || !res.success) {
            return;
        }

        var map = {
            'thistle-press-cache-config': res.data.config_readable ? 'Available' : 'None',
            'thistle-press-cache-env': res.data.env_detected ? 'Available' : 'None',
            'thistle-press-cache-entries': String(res.data.entries)
        };

        Object.keys(map).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.textContent = map[id];
            }
        });
    });

    bind('thistle-press-cache-cloud', 'tpc_cloud_check', function (res) {
        var note = document.getElementById('thistle-press-cache-cloud-result');
        var score = document.getElementById('thistle-press-cache-score');
        var list = document.getElementById('thistle-press-cache-notes');

        if (!res || !res.success) {
            if (note) { note.textContent = 'Failed.'; }
            return;
        }

        if (score) {
            score.textContent = res.data.score === null ? 'No score' : res.data.score + '/100';
        }

        if (list) {
            list.innerHTML = '';
            (res.data.notes || []).forEach(function (text) {
                var li = document.createElement('li');
                li.textContent = text;
                list.appendChild(li);
            });
        }

        var consent = document.querySelector('input[name="tpc_consent"]');
        if (consent) {
            consent.checked = true;
        }

        if (note) {
            note.textContent = 'Sent. Consent saved.';
        }
    });
})();
