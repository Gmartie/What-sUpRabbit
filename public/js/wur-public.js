/* What's Up Rabbit? — Public Widget */
(function () {
    'use strict';

    var cfg = window.wurData || {};
    if (!cfg.whatsappNumber) return; // no number = no widget

    var faqs      = cfg.faqs || [];
    var position  = cfg.position || 'right';
    var logoUrl   = cfg.logoUrl || '';
    var isOpen    = false;
    var selected  = null;

    /* ── Build HTML ──────────────────────────────────────────────────── */
    function whatsappIcon() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';
    }

    function buildWidget() {
        var wrap = document.createElement('div');
        wrap.className = 'wur-widget-wrap wur-pos-' + position;
        wrap.id = 'wur-widget';

        // Bubble
        var bubble = document.createElement('button');
        bubble.className = 'wur-bubble';
        bubble.setAttribute('aria-label', "Abrir chat WhatsApp");
        var bubbleColor  = cfg.bubbleColor  || '#2d8a4e';
        var bubbleMode   = cfg.bubbleMode   || 'logo';   // 'logo' | 'circle'
        var bubbleBgColor = cfg.bubbleBgColor || bubbleColor;

        if (bubbleMode === 'circle') {
            // Modo burbuja: círculo coloreado con icono encima
            var iconHtml = cfg.bubbleIconUrl
                ? '<img src="' + cfg.bubbleIconUrl + '" alt="" class="wur-bubble-inner-img">'
                : whatsappIcon();
            bubble.innerHTML = '<span class="wur-bubble-circle" style="background:' + bubbleBgColor + '">'
                + iconHtml
                + '</span><span class="wur-bubble-pulse"></span>';
        } else {
            // Modo logo (por defecto)
            if (cfg.bubbleIconUrl) {
                bubble.innerHTML = '<img src="' + cfg.bubbleIconUrl + '" alt="" class="wur-bubble-img wur-bubble-img-custom"><span class="wur-bubble-pulse"></span>';
            } else {
                var colorFilter = hexToFilter(bubbleColor);
                bubble.innerHTML = '<img src="' + logoUrl + '" alt="" class="wur-bubble-img" style="filter:' + colorFilter + '"><span class="wur-bubble-pulse"></span>';
            }
        }

        // Formato del botón CTA
        var btnStyle = cfg.buttonStyle || '';
        var btnFontWeight  = btnStyle.indexOf('bold')      !== -1 ? 'bold'    : 'normal';
        var btnFontStyle   = btnStyle.indexOf('italic')    !== -1 ? 'italic'  : 'normal';
        var btnTextDecor   = btnStyle.indexOf('underline') !== -1 ? 'underline' : 'none';

        // Panel
        var panel = document.createElement('div');
        panel.className = 'wur-panel';
        panel.id = 'wur-panel';

        // Header
        var headerBg    = cfg.headerColor       || '#2d8a4e';
        var headerTxt   = cfg.headerTextColor   || '#ffffff';
        var headerIcon  = cfg.headerIconUrl     || logoUrl;
        var headerIconMode  = cfg.headerIconMode  || 'bubble'; // 'bubble' | 'direct'
        var headerIconColor = cfg.headerIconColor || '';        // hex o vacío = blanco por defecto
        var msgColor    = cfg.msgTextColor      || '#333333';

        // Filtro CSS para colorear el icono de cabecera
        var headerImgStyle = '';
        if (headerIconColor) {
            headerImgStyle = ' style="filter:' + hexToFilter(headerIconColor) + '"';
        } else {
            // Sin color personalizado → blanco (comportamiento previo)
            headerImgStyle = ' style="filter:brightness(0) invert(1)"';
        }
        var headerLogoClass = 'wur-panel-header-logo ' + (headerIconMode === 'direct' ? 'wur-header-direct' : 'wur-header-bubble');

        panel.innerHTML = '<div class="wur-panel-header" style="background:' + headerBg + ';color:' + headerTxt + '">'
            + '<div class="' + headerLogoClass + '"><img src="' + headerIcon + '" alt=""' + headerImgStyle + '></div>'
            + '<div class="wur-panel-header-info">'
            + '<div class="wur-panel-header-name">' + escHtml(cfg.companyName || 'Nuestro equipo') + '</div>'
            + '<div class="wur-panel-header-status"><span class="wur-status-dot"></span> En línea</div>'
            + '</div>'
            + '<button class="wur-panel-close" id="wur-close" aria-label="Cerrar">✕</button>'
            + '</div>'

            + '<div class="wur-panel-body" id="wur-body">'
            + '<div class="wur-welcome-bubble" style="color:' + msgColor + '">' + escHtml(cfg.welcomeMessage || '¡Hola! ¿En qué podemos ayudarte?') + '</div>'
            + buildFaqList(msgColor)
            + '<div class="wur-answer-bubble" id="wur-answer"></div>'
            + '</div>'

            + '<div class="wur-panel-footer">'
            + '<button class="wur-wa-btn" id="wur-open-wa" style="background:' + (cfg.buttonColor || '#25d366') + ';color:' + (cfg.buttonTextColor || '#fff') + ';font-weight:' + btnFontWeight + ';font-style:' + btnFontStyle + ';text-decoration:' + btnTextDecor + '">'
            + whatsappIcon()
            + '<span>' + escHtml(cfg.buttonText || 'Chatea por WhatsApp') + '</span>'
            + '</button>'
            + '<div class="wur-powered">Powered by What\'s Up Rabbit?</div>'
            + '</div>';

        wrap.appendChild(panel);
        wrap.appendChild(bubble);
        document.body.appendChild(wrap);

        // Events
        bubble.addEventListener('click', togglePanel);
        document.getElementById('wur-close').addEventListener('click', closePanel);
        document.getElementById('wur-open-wa').addEventListener('click', openWhatsApp);

        // FAQ clicks (event delegation)
        var body = document.getElementById('wur-body');
        body.addEventListener('click', function (e) {
            var btn = e.target.closest('.wur-faq-btn');
            if (!btn) return;
            var idx = parseInt(btn.dataset.idx, 10);
            selectFaq(idx, btn);
        });
    }

    function hexToFilter(hex) {
        // For a given hex color, compute a CSS filter chain that tints a black SVG to that color.
        // We use a known-good formula: invert → sepia → saturate → hue-rotate → brightness
        var r = parseInt(hex.slice(1,3),16);
        var g = parseInt(hex.slice(3,5),16);
        var b = parseInt(hex.slice(5,7),16);
        // hue in degrees
        var hue = Math.round(Math.atan2(
            Math.sqrt(3)*(g/255 - b/255),
            2*r/255 - g/255 - b/255
        ) * (180/Math.PI));
        if (hue < 0) hue += 360;
        // brightness based on luminance
        var lum = (0.299*r + 0.587*g + 0.114*b) / 255;
        var bright = Math.max(0.6, lum * 1.4).toFixed(2);
        return 'invert(1) sepia(1) saturate(4) hue-rotate(' + hue + 'deg) brightness(' + bright + ')';
    }

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

    function selectFaq(idx, btn) {
        // deselect all
        document.querySelectorAll('.wur-faq-btn').forEach(function (b) { b.classList.remove('wur-faq-selected'); });
        btn.classList.add('wur-faq-selected');
        selected = idx;

        var answerEl = document.getElementById('wur-answer');
        var msgColor = cfg.msgTextColor || '#333333';
        answerEl.innerHTML = '<div class="wur-answer-question">' + escHtml(faqs[idx].question) + '</div>'
                           + '<div class="wur-answer-text" style="color:' + msgColor + '">' + linkify(faqs[idx].answer) + '</div>';
        answerEl.classList.add('wur-show');
    }

    function openWhatsApp() {
        var msg = selected !== null && faqs[selected]
            ? faqs[selected].question + '\n\n'
            : '';
        var url = 'https://wa.me/' + cfg.whatsappNumber + '?text=' + encodeURIComponent(msg);
        window.open(url, '_blank', 'noopener');
    }

    function togglePanel() { isOpen ? closePanel() : openPanel(); }
    function openPanel() {
        isOpen = true;
        document.getElementById('wur-panel').classList.add('wur-open');
    }
    function closePanel() {
        isOpen = false;
        document.getElementById('wur-panel').classList.remove('wur-open');
    }

    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /**
     * Renderiza texto con enlaces en formato [texto](url) o [texto](url){target=_blank}
     * como etiquetas <a> reales. El resto del texto se escapa para seguridad.
     */
    function linkify(str) {
        if (!str) return '';
        // Regex: [texto](url){target=_blank} o [texto](url)
        var parts = [];
        var re = /\[([^\]]+)\]\(([^)]+)\)(\{target=_blank\})?/g;
        var last = 0, m;
        while ((m = re.exec(str)) !== null) {
            // texto plano antes del enlace
            if (m.index > last) {
                parts.push(escHtml(str.slice(last, m.index)));
            }
            var linkText   = escHtml(m[1]);
            var linkUrl    = escHtml(m[2]);
            var targetAttr = m[3] ? ' target="_blank" rel="noopener noreferrer"' : '';
            parts.push('<a href="' + linkUrl + '"' + targetAttr + ' class="wur-faq-link">' + linkText + '</a>');
            last = re.lastIndex;
        }
        // resto del texto
        if (last < str.length) {
            parts.push(escHtml(str.slice(last)));
        }
        return parts.join('');
    }

    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildWidget);
    } else {
        buildWidget();
    }
})();
