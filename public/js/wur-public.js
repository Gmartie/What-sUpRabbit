/* What's Up Rabbit? — Public Widget v2.0 */
(function () {
    'use strict';

    var cfg = window.wurData || {};
    if (!cfg.whatsappNumber && (!cfg.agents || !cfg.agents.length)) return;

    var faqs     = cfg.faqs     || [];
    var agents   = cfg.agents   || [];
    var position = cfg.position || 'right';
    var logoUrl  = cfg.logoUrl  || '';
    var isOpen   = false;
    var selected = null;
    // Notification dot state
    var STORAGE_KEY   = 'wur_last_opened';
    var timeoutMins   = parseInt(cfg.notificationTimeout, 10) || 30;

    /* ── Schedule / Online status ──────────────────────────────────── */
    function isOnline() {
        if (!cfg.scheduleEnabled) return true;
        var now  = new Date();
        var day  = ['sun','mon','tue','wed','thu','fri','sat'][now.getDay()];
        var days = cfg.scheduleDays || ['mon','tue','wed','thu','fri'];
        if (days.indexOf(day) === -1) return false;
        var cur   = now.getHours() * 60 + now.getMinutes();
        var start = timeToMins(cfg.scheduleStart || '09:00');
        var end   = timeToMins(cfg.scheduleEnd   || '18:00');
        return cur >= start && cur < end;
    }

    function timeToMins(str) {
        var parts = (str || '').split(':');
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1] || 0, 10);
    }

    /* ── Notification dot logic ────────────────────────────────────── */
    function shouldShowDot() {
        try {
            var last = parseInt(localStorage.getItem(STORAGE_KEY), 10) || 0;
            if (!last) return true;
            var elapsed = (Date.now() - last) / 60000; // minutes
            return elapsed >= timeoutMins;
        } catch (e) { return true; }
    }

    function markOpened() {
        try { localStorage.setItem(STORAGE_KEY, Date.now()); } catch (e) {}
    }

    /* ── SVG icon ──────────────────────────────────────────────────── */
    function whatsappIcon() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';
    }

    /* ── Build widget ──────────────────────────────────────────────── */
    function buildWidget() {
        var online = isOnline();
        var wrap = document.createElement('div');
        wrap.className = 'wur-widget-wrap wur-pos-' + position;
        wrap.id = 'wur-widget';

        // Bubble
        var bubble = document.createElement('button');
        bubble.className = 'wur-bubble';
        bubble.setAttribute('aria-label', 'Abrir chat WhatsApp');
        var bubbleColor   = cfg.bubbleColor   || '#2d8a4e';
        var bubbleMode    = cfg.bubbleMode    || 'logo';
        var bubbleBgColor = cfg.bubbleBgColor || bubbleColor;

        var showDot = shouldShowDot();

        if (bubbleMode === 'circle') {
            var iconHtml = cfg.bubbleIconUrl
                ? '<img src="' + cfg.bubbleIconUrl + '" alt="" class="wur-bubble-inner-img">'
                : whatsappIcon();
            bubble.innerHTML = '<span class="wur-bubble-circle" style="background:' + bubbleBgColor + '">'
                + iconHtml + '</span>'
                + (showDot ? '<span class="wur-bubble-pulse"></span>' : '');
        } else {
            if (cfg.bubbleIconUrl) {
                bubble.innerHTML = '<img src="' + cfg.bubbleIconUrl + '" alt="" class="wur-bubble-img wur-bubble-img-custom">'
                    + (showDot ? '<span class="wur-bubble-pulse"></span>' : '');
            } else {
                var colorFilter = hexToFilter(bubbleColor);
                bubble.innerHTML = '<img src="' + logoUrl + '" alt="" class="wur-bubble-img" style="filter:' + colorFilter + '">'
                    + (showDot ? '<span class="wur-bubble-pulse"></span>' : '');
            }
        }

        // Button style
        var btnStyle       = cfg.buttonStyle || '';
        var btnFontWeight  = btnStyle.indexOf('bold')      !== -1 ? 'bold'      : 'normal';
        var btnFontStyle   = btnStyle.indexOf('italic')    !== -1 ? 'italic'    : 'normal';
        var btnTextDecor   = btnStyle.indexOf('underline') !== -1 ? 'underline' : 'none';

        // Panel
        var panel = document.createElement('div');
        panel.className = 'wur-panel';
        panel.id = 'wur-panel';

        var headerBg        = cfg.headerColor     || '#2d8a4e';
        var headerTxt       = cfg.headerTextColor || '#ffffff';
        var headerIcon      = cfg.headerIconUrl   || logoUrl;
        var headerIconMode  = cfg.headerIconMode  || 'bubble';
        var headerIconColor = cfg.headerIconColor || '';
        var msgColor        = cfg.msgTextColor    || '#333333';

        var headerImgStyle = headerIconColor
            ? ' style="filter:' + hexToFilter(headerIconColor) + '"'
            : ' style="filter:brightness(0) invert(1)"';
        var headerLogoClass = 'wur-panel-header-logo ' + (headerIconMode === 'direct' ? 'wur-header-direct' : 'wur-header-bubble');

        var statusHtml = online
            ? '<span class="wur-status-dot"></span> En línea'
            : '<span class="wur-status-dot wur-status-offline"></span> Fuera de horario';

        var welcomeFormatted = formatWelcome(cfg.welcomeMessage || '¡Hola! ¿En qué podemos ayudarte?');

        var buttonLabel = online
            ? escHtml(cfg.buttonText || 'Chatea por WhatsApp')
            : escHtml(cfg.buttonText || 'Déjanos un mensaje');

        // Build agents selector or single-number footer
        var footerHtml = buildFooter(online, btnFontWeight, btnFontStyle, btnTextDecor, buttonLabel);

        panel.innerHTML =
            '<div class="wur-panel-header" style="background:' + headerBg + ';color:' + headerTxt + '">'
            + '<div class="' + headerLogoClass + '"><img src="' + headerIcon + '" alt=""' + headerImgStyle + '></div>'
            + '<div class="wur-panel-header-info">'
            + '<div class="wur-panel-header-name">' + escHtml(cfg.companyName || 'Nuestro equipo') + '</div>'
            + '<div class="wur-panel-header-status">' + statusHtml + '</div>'
            + '</div>'
            + '<button class="wur-panel-close" id="wur-close" aria-label="Cerrar">✕</button>'
            + '</div>'

            + '<div class="wur-panel-body" id="wur-body">'
            + '<div class="wur-welcome-bubble" style="color:' + msgColor + '">' + welcomeFormatted + '</div>'
            + (!online ? '<div class="wur-offline-msg">' + escHtml(cfg.offlineMessage || 'Estamos offline. Te responderemos pronto.') + '</div>' : '')
            + buildFaqList(msgColor)
            + '<div class="wur-answer-bubble" id="wur-answer"></div>'
            + '</div>'

            + footerHtml;

        wrap.appendChild(panel);
        wrap.appendChild(bubble);
        document.body.appendChild(wrap);

        // Events
        bubble.addEventListener('click', togglePanel);
        document.getElementById('wur-close').addEventListener('click', closePanel);

        // Single WA button (no agents mode)
        var singleBtn = document.getElementById('wur-open-wa');
        if (singleBtn) singleBtn.addEventListener('click', function () { openWhatsApp(cfg.whatsappNumber); });

        // Agents click
        var agentList = document.getElementById('wur-agents-list');
        if (agentList) {
            agentList.addEventListener('click', function (e) {
                var btn = e.target.closest('.wur-agent-btn');
                if (!btn) return;
                openWhatsApp(btn.dataset.number);
            });
        }

        // FAQ delegation
        var body = document.getElementById('wur-body');
        body.addEventListener('click', function (e) {
            var btn = e.target.closest('.wur-faq-btn');
            if (!btn) return;
            selectFaq(parseInt(btn.dataset.idx, 10), btn);
        });
    }

    /* ── Welcome message formatter ─────────────────────────────────── */
    function formatWelcome(str) {
        if (!str) return '';
        // Support **bold**, newlines → <br>
        var escaped = escHtml(str);
        escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\n/g, '<br>');
        return escaped;
    }

    /* ── Footer builder ────────────────────────────────────────────── */
    function buildFooter(online, fw, fs, td, label) {
        var btnBg  = cfg.buttonColor     || '#25d366';
        var btnTxt = cfg.buttonTextColor || '#fff';

        if (agents && agents.length > 0) {
            // Multi-agent mode
            var agentItems = agents.map(function (ag) {
                var photo = ag.photo
                    ? '<img src="' + escHtml(ag.photo) + '" alt="" class="wur-agent-photo">'
                    : '<span class="wur-agent-avatar">' + escHtml((ag.name || '?')[0].toUpperCase()) + '</span>';
                return '<button class="wur-agent-btn" data-number="' + escHtml(ag.number) + '" style="border-color:' + btnBg + '">'
                    + photo
                    + '<span class="wur-agent-info"><strong>' + escHtml(ag.name) + '</strong>'
                    + (ag.role ? '<small>' + escHtml(ag.role) + '</small>' : '')
                    + '</span>'
                    + '<span class="wur-agent-arrow" style="color:' + btnBg + '">' + whatsappIcon() + '</span>'
                    + '</button>';
            }).join('');
            return '<div class="wur-panel-footer wur-footer-agents">'
                + '<div class="wur-agents-title">Elige con quién hablar:</div>'
                + '<div id="wur-agents-list" class="wur-agents-list">' + agentItems + '</div>'
                + '<div class="wur-powered">Powered by What\'s Up Rabbit?</div>'
                + '</div>';
        }

        // Single number mode
        return '<div class="wur-panel-footer">'
            + '<button class="wur-wa-btn" id="wur-open-wa" style="background:' + btnBg + ';color:' + btnTxt + ';font-weight:' + fw + ';font-style:' + fs + ';text-decoration:' + td + '">'
            + whatsappIcon()
            + '<span>' + label + '</span>'
            + '</button>'
            + '<div class="wur-powered">Powered by What\'s Up Rabbit?</div>'
            + '</div>';
    }

    /* ── FAQ list ──────────────────────────────────────────────────── */
    function buildFaqList(msgColor) {
        if (!faqs.length) return '';
        var color = msgColor || '#333333';
        var html = '<div class="wur-faq-title">Preguntas frecuentes</div><div class="wur-faq-list">';
        faqs.forEach(function (faq, i) {
            html += '<button class="wur-faq-btn" data-idx="' + i + '" style="color:' + color + '">' + escHtml(faq.question) + '</button>';
        });
        html += '</div>';
        return html;
    }

    /* ── Select FAQ with typing animation ──────────────────────────── */
    function selectFaq(idx, btn) {
        document.querySelectorAll('.wur-faq-btn').forEach(function (b) { b.classList.remove('wur-faq-selected'); });
        btn.classList.add('wur-faq-selected');
        selected = idx;

        var answerEl = document.getElementById('wur-answer');
        var msgColor = cfg.msgTextColor || '#333333';

        // Show typing indicator
        answerEl.innerHTML = '<div class="wur-typing-indicator"><span></span><span></span><span></span></div>';
        answerEl.classList.add('wur-show');

        // Scroll to bottom
        var body = document.getElementById('wur-body');
        if (body) body.scrollTop = body.scrollHeight;

        // After delay, show real answer
        setTimeout(function () {
            answerEl.innerHTML =
                '<div class="wur-answer-question">' + escHtml(faqs[idx].question) + '</div>'
                + '<div class="wur-answer-text" style="color:' + msgColor + '">' + linkify(faqs[idx].answer) + '</div>';
            if (body) body.scrollTop = body.scrollHeight;
        }, 1200);
    }

    /* ── Open WhatsApp ─────────────────────────────────────────────── */
    function openWhatsApp(number) {
        var num = number || cfg.whatsappNumber;
        var msg = selected !== null && faqs[selected] ? faqs[selected].question + '\n\n' : '';
        var url = 'https://wa.me/' + num + '?text=' + encodeURIComponent(msg);
        window.open(url, '_blank', 'noopener');
    }

    /* ── Panel toggle ──────────────────────────────────────────────── */
    function togglePanel() { isOpen ? closePanel() : openPanel(); }

    function openPanel() {
        isOpen = true;
        document.getElementById('wur-panel').classList.add('wur-open');
        // Hide notification dot
        var pulse = document.querySelector('.wur-bubble-pulse');
        if (pulse) pulse.style.display = 'none';
        markOpened();
    }

    function closePanel() {
        isOpen = false;
        document.getElementById('wur-panel').classList.remove('wur-open');
    }

    /* ── Helpers ───────────────────────────────────────────────────── */
    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function hexToFilter(hex) {
        var r = parseInt(hex.slice(1,3),16);
        var g = parseInt(hex.slice(3,5),16);
        var b = parseInt(hex.slice(5,7),16);
        var hue = Math.round(Math.atan2(Math.sqrt(3)*(g/255 - b/255), 2*r/255 - g/255 - b/255) * (180/Math.PI));
        if (hue < 0) hue += 360;
        var lum = (0.299*r + 0.587*g + 0.114*b) / 255;
        var bright = Math.max(0.6, lum * 1.4).toFixed(2);
        return 'invert(1) sepia(1) saturate(4) hue-rotate(' + hue + 'deg) brightness(' + bright + ')';
    }

    /**
     * Renderiza texto con enlaces [texto](url){target=_blank}
     * Solo permite URLs que empiecen por https://, http:// o /
     */
    function linkify(str) {
        if (!str) return '';
        var parts = [];
        var re = /\[([^\]]+)\]\(([^)]+)\)(\{target=_blank\})?/g;
        var last = 0, m;
        while ((m = re.exec(str)) !== null) {
            if (m.index > last) parts.push(escHtml(str.slice(last, m.index)));
            var linkText = escHtml(m[1]);
            var rawUrl   = m[2];
            // Sanitize: only allow safe URL schemes
            if (/^(https?:\/\/|\/)/.test(rawUrl)) {
                var linkUrl    = escHtml(rawUrl);
                var targetAttr = m[3] ? ' target="_blank" rel="noopener noreferrer"' : '';
                parts.push('<a href="' + linkUrl + '"' + targetAttr + ' class="wur-faq-link">' + linkText + '</a>');
            } else {
                // Unsafe URL: render as plain text
                parts.push(escHtml('[' + m[1] + '](' + rawUrl + ')'));
            }
            last = re.lastIndex;
        }
        if (last < str.length) parts.push(escHtml(str.slice(last)));
        return parts.join('');
    }

    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildWidget);
    } else {
        buildWidget();
    }
})();