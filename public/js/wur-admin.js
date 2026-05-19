/* What's Up Rabbit? — Admin JS */
(function ($) {
    'use strict';

    /* ── Color pickers ───────────────────────────────────────────────── */
    $('.wur-color-picker').wpColorPicker();

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

    function addFaqRow(question, answer) {
        var row = $('<div class="wur-faq-row">'
            + '<div class="wur-faq-header">'
            + '<span class="wur-faq-num">?</span>'
            + '<button type="button" class="wur-remove-faq" title="Eliminar pregunta">✕</button>'
            + '</div>'
            + '<div class="wur-faq-fields">'
            + '<input type="text" class="wur-faq-question regular-text" placeholder="Pregunta (ej: ¿Cuánto cuesta una web?)" value="">'
            + '<textarea class="wur-faq-answer" rows="3" placeholder="Respuesta que se mostrará al usuario"></textarea>'
            + '</div>'
            + '</div>');
        if (question) row.find('.wur-faq-question').val(question);
        if (answer)   row.find('.wur-faq-answer').val(answer);
        $wrapper.append(row);
        updateFaqNumbers();
    }

    $('.wur-add-faq').on('click', function () {
        addFaqRow('', '');
    });

    $wrapper.on('click', '.wur-remove-faq', function () {
        $(this).closest('.wur-faq-row').remove();
        updateFaqNumbers();
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
    var $bubble  = $('#wur-assistant-bubble');
    var $panel   = $('#wur-assistant-panel');
    var $msgs    = $('#wur-assistant-messages');
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
