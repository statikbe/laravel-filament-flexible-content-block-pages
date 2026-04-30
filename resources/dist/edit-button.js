/**
 * Keeps the Filament flexible pages "edit" button above the Laravel Debug Bar (php-debugbar)
 * when both are shown.
 */
(function () {
    var btn = document.getElementById('flexible-pages-edit-button');

    if (!btn) {
        return;
    }

    var resizeObserver = null;
    var debugbarMutationObserver = null;
    var bodyObserver = null;
    var bodyObserverTimeout = null;

    function reposition() {
        var debugbar = document.querySelector('div.phpdebugbar');
        if (!debugbar) {
            btn.style.bottom = '';
            return;
        }

        var rect = debugbar.getBoundingClientRect();
        if (rect.height > 0 && rect.top < window.innerHeight) {
            btn.style.bottom = (window.innerHeight - rect.top) + 'px';
        } else {
            btn.style.bottom = '';
        }
    }

    function watchDebugbar(debugbar) {
        if (resizeObserver) {
            resizeObserver.disconnect();
        }
        if (debugbarMutationObserver) {
            debugbarMutationObserver.disconnect();
        }

        resizeObserver = new ResizeObserver(reposition);
        resizeObserver.observe(debugbar);
        debugbarMutationObserver = new MutationObserver(reposition);
        debugbarMutationObserver.observe(debugbar, {
            attributes: true,
            attributeFilter: ['class', 'style'],
        });
        reposition();
    }

    function tryAttachDebugbar() {
        var debugbar = document.querySelector('div.phpdebugbar');
        if (!debugbar)
            return false;
        }

        if (bodyObserver) {
            bodyObserver.disconnect();
            bodyObserver = null;
        }
        if (bodyObserverTimeout !== null) {
            clearTimeout(bodyObserverTimeout);
            bodyObserverTimeout = null;
        }

        watchDebugbar(debugbar);
        return true;
    }

    if (!tryAttachDebugbar()) {
        bodyObserver = new MutationObserver(function () {
            tryAttachDebugbar();
        });
        bodyObserver.observe(document.body, { childList: true, subtree: true });
        bodyObserverTimeout = window.setTimeout(function () {
            if (bodyObserver) {
                bodyObserver.disconnect();
                bodyObserver = null;
            }
            bodyObserverTimeout = null;
        }, 30000);
    }

    window.addEventListener('resize', reposition);
})();

