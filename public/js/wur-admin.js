/* What's Up Rabbit? — Admin JS */
(function ($) {
    'use strict';

    /* ── Color pickers + live preview ────────────────────────────────── */
    function applyPreviewColors() {
        var headerBg  = $('[name="wur_header_color"]').val()      || '#2d8a4e';
        var headerTxt = $('[name="wur_header_text_color"]').val() || '#ffffff';
        var btnBg     = $('[name="wur_button_color"]').val()      || '#25d366';
        var btnTxt    = $('[name="wur_button_text_color"]').val() || '#ffffff';
        $('.wur-preview-header').css({ background: headerBg, color: headerTxt });
        $('.wur-preview-btn').css({ background: btnBg, color: btnTxt });
    }

    $('.wur-color-picker').wpColorPicker({
        change: function () { setTimeout(applyPreviewColors, 50); },
        clear:  function () { setTimeout(applyPreviewColors, 50); }
    });

    applyPreviewColors();

    /* ── Bubble mode toggle ──────────────────────────────────────────── */
    $('input[name="wur_bubble_mode"]').on('change', function () {
        $('#wur-bubble-bg-row').toggle($(this).val() === 'circle');
    });

    /* ── Tabs ────────────────────────────────────────────────────────── */
    $('.wur-tab').on('click', function () {
        var tabId = $(this).data('tab');
        $('.wur-tab').removeClass('active');
        $(this).addClass('active');
        $('.wur-tab-content').removeClass('active');
        $('#tab-' + tabId).addClass('active');
    });

    /* ── FAQ Manager ─────────────────────────────────────────────────── */
    var $wrapper = $('#wur-faqs-wrapper');

    function updateFaqNumbers() {
        $wrapper.find('.wur-faq-row').each(function (i) {
            $(this).find('.wur-faq-num').text(i + 1);
        });
    }

    /* Botón insertar enlace — abre un mini-diálogo inline */
    function insertLinkBtn() {
        return '<button type="button" class="button wur-insert-link-btn" title="Insertar enlace en la respuesta">🔗 Insertar enlace</button>';
    }

    function addFaqRow(question, answer) {
        var row = $('<div class="wur-faq-row">'
            + '<div class="wur-faq-header">'
            + '<span class="wur-faq-num">?</span>'
            + '<button type="button" class="wur-remove-faq" title="Eliminar pregunta">✕</button>'
            + '</div>'
            + '<div class="wur-faq-fields">'
            + '<input type="text" class="wur-faq-question regular-text" placeholder="Pregunta (ej: ¿Cuánto cuesta una web?)" value="">'
            + '<textarea class="wur-faq-answer" rows="3" placeholder="Respuesta que se mostrará al usuario"></textarea>'
            + '<div class="wur-faq-link-toolbar">'
            + insertLinkBtn()
            + '<div class="wur-link-popup" style="display:none">'
            + '<input type="text" class="wur-link-text-input" placeholder="Texto del enlace">'
            + '<input type="text" class="wur-link-url-input" placeholder="URL (https://... o /pagina/)">'
            + '<label class="wur-link-newwin"><input type="checkbox" class="wur-link-newwin-chk" checked> Abrir en nueva pestaña</label>'
            + '<div class="wur-link-actions">'
            + '<button type="button" class="button button-primary wur-link-confirm">Insertar</button>'
            + '<button type="button" class="button wur-link-cancel">Cancelar</button>'
            + '</div></div></div>'
            + '</div>'
            + '</div>');
        if (question) row.find('.wur-faq-question').val(question);
        if (answer)   row.find('.wur-faq-answer').val(answer);
        $wrapper.append(row);
        updateFaqNumbers();
    }

    /* Repopulate existing rows with the link toolbar */
    $wrapper.find('.wur-faq-row').each(function () {
        if (!$(this).find('.wur-faq-link-toolbar').length) {
            $(this).find('.wur-faq-fields').append(
                '<div class="wur-faq-link-toolbar">'
                + insertLinkBtn()
                + '<div class="wur-link-popup" style="display:none">'
                + '<input type="text" class="wur-link-text-input" placeholder="Texto del enlace">'
                + '<input type="text" class="wur-link-url-input" placeholder="URL (https://... o /pagina/)">'
                + '<label class="wur-link-newwin"><input type="checkbox" class="wur-link-newwin-chk" checked> Abrir en nueva pestaña</label>'
                + '<div class="wur-link-actions">'
                + '<button type="button" class="button button-primary wur-link-confirm">Insertar</button>'
                + '<button type="button" class="button wur-link-cancel">Cancelar</button>'
                + '</div></div></div>'
            );
        }
    });

    $('.wur-add-faq').on('click', function () {
        addFaqRow('', '');
    });

    $wrapper.on('click', '.wur-remove-faq', function () {
        $(this).closest('.wur-faq-row').remove();
        updateFaqNumbers();
    });

    /* ── Link popup logic ────────────────────────────────────────────── */
    $wrapper.on('click', '.wur-insert-link-btn', function () {
        var $row   = $(this).closest('.wur-faq-row');
        var $popup = $row.find('.wur-link-popup');
        // Pre-fill text with any selection in the textarea
        var $ta  = $row.find('.wur-faq-answer');
        var ta   = $ta[0];
        var sel  = ta.value.substring(ta.selectionStart, ta.selectionEnd);
        $popup.find('.wur-link-text-input').val(sel);
        $popup.find('.wur-link-url-input').val('');
        $popup.toggle();
    });

    $wrapper.on('click', '.wur-link-cancel', function () {
        $(this).closest('.wur-link-popup').hide();
    });

    $wrapper.on('click', '.wur-link-confirm', function () {
        var $popup  = $(this).closest('.wur-link-popup');
        var $row    = $popup.closest('.wur-faq-row');
        var $ta     = $row.find('.wur-faq-answer');
        var ta      = $ta[0];
        var text    = $popup.find('.wur-link-text-input').val().trim();
        var url     = $popup.find('.wur-link-url-input').val().trim();
        var newwin  = $popup.find('.wur-link-newwin-chk').is(':checked');

        if (!url) { alert('Introduce una URL.'); return; }
        if (!text) text = url;

        // Insert markdown-style link: [texto](url) [nueva_pestaña]
        var tag = newwin ? '[' + text + '](' + url + '){target=_blank}' : '[' + text + '](' + url + ')';

        var start = ta.selectionStart;
        var end   = ta.selectionEnd;
        ta.value  = ta.value.substring(0, start) + tag + ta.value.substring(end);
        ta.selectionStart = ta.selectionEnd = start + tag.length;
        $ta.trigger('input');
        $popup.hide();
    });

    // Serialize FAQs on form submit
    $('#wur-form').on('submit', function () {
        var faqs = [];
        $wrapper.find('.wur-faq-row').each(function () {
            var q = $(this).find('.wur-faq-question').val().trim();
            var a = $(this).find('.wur-faq-answer').val().trim();
            if (q || a) faqs.push({ question: q, answer: a });
        });
        $('#wur-faqs-json').val(JSON.stringify(faqs));
    });

    /* ── Asistente flotante ──────────────────────────────────────────── */
    var $bubble   = $('#wur-assistant-bubble');
    var $panel    = $('#wur-assistant-panel');
    var $msgs     = $('#wur-assistant-messages');
    var panelOpen = false;

    $bubble.on('click', function () {
        panelOpen = !panelOpen;
        $panel.toggle(panelOpen);
        if (panelOpen) scrollMsgs();
    });

    $('#wur-assistant-close').on('click', function () {
        panelOpen = false;
        $panel.hide();
    });

    $(document).on('click', '.wur-suggestion', function () {
        var question = $(this).text().trim();
        var answer   = $(this).data('answer');
        addMsg(question, 'user');
        setTimeout(function () { addMsg(answer, 'bot'); }, 400);
    });

    function addMsg(text, type) {
        var $msg = $('<div class="wur-msg wur-msg-' + type + '"></div>').text(text);
        $msgs.append($msg);
        scrollMsgs();
    }

    function scrollMsgs() {
        $msgs.scrollTop($msgs[0].scrollHeight);
    }

})(jQuery);
