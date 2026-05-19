<?php
if (!defined('ABSPATH')) exit;

/* ─── Menú lateral ─────────────────────────────────────────────────────── */
add_action('admin_menu', function () {
    // Encode the SVG for the menu icon
    $svg = file_get_contents(WUR_PATH . 'WURabbitLogo.svg');
    $svg_b64 = 'data:image/svg+xml;base64,' . base64_encode($svg);

    add_menu_page(
        "What's Up Rabbit?",
        "What's Up Rabbit?",
        'manage_options',
        'whatsup-rabbit',
        'wur_render_admin_page',
        $svg_b64,
        58
    );
});

/* ─── Render página de ajustes ─────────────────────────────────────────── */
function wur_render_admin_page() {
    $whatsapp  = get_option('wur_whatsapp_number', '');
    $company   = get_option('wur_company_name', '');
    $welcome   = get_option('wur_welcome_message', '¡Hola! ¿En qué podemos ayudarte?');
    $btn_text  = get_option('wur_button_text', 'Chatea con nosotros');
    $offline   = get_option('wur_offline_message', 'Estamos offline. Te responderemos pronto.');
    $position  = get_option('wur_position', 'right');
    $faqs_raw  = get_option('wur_faqs', '[]');
    $faqs      = json_decode($faqs_raw, true) ?: [];
    $show_ass  = get_option('wur_show_assistant', '1');
    $ass_greet = get_option('wur_assistant_greeting', '¡Hola! Soy el asistente de What\'s Up Rabbit?. ¿En qué puedo ayudarte?');

    // Color defaults
    $btn_color       = get_option('wur_button_color', '#25d366');
    $btn_text_color  = get_option('wur_button_text_color', '#ffffff');
    $header_color    = get_option('wur_header_color', '#2d8a4e');
    $header_text_col = get_option('wur_header_text_color', '#ffffff');
    $bubble_color    = get_option('wur_bubble_color', '#2d8a4e');
    $bubble_text_col = get_option('wur_bubble_text_color', '#ffffff');
    $bubble_icon_url = get_option('wur_bubble_icon_url', '');
    ?>
    <div class="wrap wur-wrap">

        <!-- ══ BANNER / CABECERA ══════════════════════════════════════════ -->
        <div class="wur-header">
            <div class="wur-header-logo">
                <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="What's Up Rabbit?" class="wur-logo">
            </div>
            <div class="wur-header-text">
                <h1>What's Up Rabbit?</h1>
                <p class="wur-subtitle">Bot de preguntas frecuentes para WhatsApp · Conecta con tus clientes · Respuestas automáticas · Diseño personalizable</p>
            </div>
            <div class="wur-header-badge">
                <span class="wur-badge">WhatsApp FAQ Bot</span>
            </div>
        </div>

        <?php if (empty($whatsapp)) : ?>
        <div class="notice notice-warning wur-notice">
            <p><strong>⚠ Atención:</strong> No has configurado un número de WhatsApp. El widget no se mostrará en tu web hasta que añadas un número válido en la sección <strong>Configuración General</strong>.</p>
        </div>
        <?php else : ?>
        <div class="notice notice-success wur-notice is-dismissible">
            <p><strong>✓ Activo:</strong> El widget de WhatsApp está configurado con el número <code><?php echo esc_html($whatsapp); ?></code>.</p>
        </div>
        <?php endif; ?>

        <!-- ══ TABS ═══════════════════════════════════════════════════════ -->
        <div class="wur-tabs">
            <button class="wur-tab active" data-tab="general">General</button>
            <button class="wur-tab" data-tab="faqs">Preguntas Frecuentes</button>
            <button class="wur-tab" data-tab="design">Diseño</button>
            <button class="wur-tab" data-tab="assistant">Asistente</button>
        </div>

        <form method="post" action="options.php" id="wur-form">
            <?php settings_fields('wur_settings_group'); ?>

            <!-- ══ TAB: GENERAL ══════════════════════════════════════════ -->
            <div class="wur-tab-content active" id="tab-general">
                <div class="wur-card">
                    <h2 class="wur-card-title">Configuración General</h2>
                    <table class="form-table wur-table">
                        <tr>
                            <th><label for="wur_company_name">Nombre de la empresa</label></th>
                            <td>
                                <input type="text" name="wur_company_name" id="wur_company_name"
                                       value="<?php echo esc_attr($company); ?>"
                                       placeholder="Ej: WebCraft Studio" class="regular-text">
                                <p class="description">Se muestra en la cabecera del widget de chat.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_whatsapp_number">Número de WhatsApp</label></th>
                            <td>
                                <input type="text" name="wur_whatsapp_number" id="wur_whatsapp_number"
                                       value="<?php echo esc_attr($whatsapp); ?>"
                                       placeholder="34612345678" class="regular-text">
                                <p class="description">Número en formato internacional sin espacios ni guiones (ej. <code>34612345678</code> para España).</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_welcome_message">Mensaje de bienvenida</label></th>
                            <td>
                                <textarea name="wur_welcome_message" id="wur_welcome_message"
                                          rows="3" cols="50"><?php echo esc_textarea($welcome); ?></textarea>
                                <p class="description">Texto que aparece en la cabecera del widget cuando el usuario lo abre.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_button_text">Texto del botón</label></th>
                            <td>
                                <input type="text" name="wur_button_text" id="wur_button_text"
                                       value="<?php echo esc_attr($btn_text); ?>"
                                       placeholder="Chatea con nosotros" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_position">Posición del widget</label></th>
                            <td>
                                <select name="wur_position" id="wur_position">
                                    <option value="right" <?php selected($position, 'right'); ?>>Abajo a la derecha</option>
                                    <option value="left"  <?php selected($position, 'left');  ?>>Abajo a la izquierda</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_offline_message">Mensaje offline</label></th>
                            <td>
                                <textarea name="wur_offline_message" id="wur_offline_message"
                                          rows="2" cols="50"><?php echo esc_textarea($offline); ?></textarea>
                                <p class="description">Mensaje alternativo que se envía si el usuario escribe fuera de horario comercial.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ══ TAB: FAQs ═════════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-faqs">
                <div class="wur-card">
                    <h2 class="wur-card-title">Preguntas Frecuentes</h2>
                    <p class="wur-card-desc">Estas son las preguntas que aparecerán como opciones rápidas en el widget. El usuario podrá seleccionarlas y la respuesta se mostrará antes de abrir WhatsApp.</p>

                    <div id="wur-faqs-wrapper">
                        <?php foreach ($faqs as $i => $faq) : ?>
                        <div class="wur-faq-row">
                            <div class="wur-faq-header">
                                <span class="wur-faq-num"><?php echo $i + 1; ?></span>
                                <button type="button" class="wur-remove-faq" title="Eliminar pregunta">✕</button>
                            </div>
                            <div class="wur-faq-fields">
                                <input type="text" class="wur-faq-question regular-text"
                                       placeholder="Pregunta (ej: ¿Cuánto cuesta una web?)"
                                       value="<?php echo esc_attr($faq['question']); ?>">
                                <textarea class="wur-faq-answer" rows="3"
                                          placeholder="Respuesta que se mostrará al usuario"><?php echo esc_textarea($faq['answer']); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="button wur-add-faq">+ Añadir pregunta</button>
                    <input type="hidden" name="wur_faqs" id="wur-faqs-json" value="<?php echo esc_attr($faqs_raw); ?>">

                    <div class="wur-faq-tip">
                        <strong>Consejo:</strong> Añade entre 5 y 10 preguntas frecuentes sobre tu negocio de páginas web. Cuantas más preguntas tengas, mejor experiencia tendrá el usuario antes de contactarte.
                    </div>
                </div>
            </div>

            <!-- ══ TAB: DISEÑO ═══════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-design">
                <div class="wur-card">
                    <h2 class="wur-card-title">Personalización del Widget</h2>
                    <table class="form-table wur-table">
                        <tr>
                            <th>Burbuja flotante — fondo</th>
                            <td><input type="text" name="wur_bubble_color" value="<?php echo esc_attr($bubble_color); ?>" class="wur-color-picker" data-default-color="#2d8a4e"></td>
                        </tr>
                        <tr>
                            <th>Burbuja flotante — icono</th>
                            <td><input type="text" name="wur_bubble_text_color" value="<?php echo esc_attr($bubble_text_col); ?>" class="wur-color-picker" data-default-color="#ffffff"></td>
                        </tr>
                        <tr>
                            <th><label for="wur_bubble_icon_url">Icono de la burbuja flotante</label></th>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <input type="text" name="wur_bubble_icon_url" id="wur_bubble_icon_url"
                                           value="<?php echo esc_attr($bubble_icon_url); ?>"
                                           class="regular-text" placeholder="URL de la imagen (o selecciona desde biblioteca)">
                                    <button type="button" class="button" id="wur-icon-upload-btn">Seleccionar imagen</button>
                                    <?php if (!empty($bubble_icon_url)) : ?>
                                    <button type="button" class="button" id="wur-icon-remove-btn">Quitar icono</button>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($bubble_icon_url)) : ?>
                                <div style="margin-top:10px;">
                                    <img src="<?php echo esc_url($bubble_icon_url); ?>"
                                         style="max-width:64px;max-height:64px;border:1px solid #ddd;padding:4px;background:#f9f9f9;border-radius:6px;" alt="Vista previa">
                                </div>
                                <?php endif; ?>
                                <p class="description">
                                    Sube tu propio icono (PNG, SVG, JPG) para la burbuja flotante. Por ejemplo, el logo de WhatsApp, tu logo de empresa, etc.<br>
                                    Si lo dejas vacío se usará el logo de What's Up Rabbit? en el color que elijas.
                                </p>
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var uploadBtn = document.getElementById('wur-icon-upload-btn');
                                    var removeBtn = document.getElementById('wur-icon-remove-btn');
                                    var input     = document.getElementById('wur_bubble_icon_url');
                                    if (!uploadBtn || !input) return;
                                    var mediaFrame;
                                    uploadBtn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        if (mediaFrame) { mediaFrame.open(); return; }
                                        mediaFrame = wp.media({
                                            title: 'Seleccionar icono para la burbuja',
                                            button: { text: 'Usar este icono' },
                                            multiple: false,
                                            library: { type: ['image/png','image/jpeg','image/svg+xml','image/gif','image/webp'] }
                                        });
                                        mediaFrame.on('select', function() {
                                            var attachment = mediaFrame.state().get('selection').first().toJSON();
                                            input.value = attachment.url;
                                            // Show preview
                                            var preview = input.parentNode.parentNode.querySelector('img[alt="Vista previa"]');
                                            if (preview) {
                                                preview.src = attachment.url;
                                            } else {
                                                var wrap = document.createElement('div');
                                                wrap.style.marginTop = '10px';
                                                var img = document.createElement('img');
                                                img.src = attachment.url;
                                                img.alt = 'Vista previa';
                                                img.style.cssText = 'max-width:64px;max-height:64px;border:1px solid #ddd;padding:4px;background:#f9f9f9;border-radius:6px;';
                                                wrap.appendChild(img);
                                                input.closest('td').appendChild(wrap);
                                            }
                                        });
                                        mediaFrame.open();
                                    });
                                    if (removeBtn) {
                                        removeBtn.addEventListener('click', function() {
                                            input.value = '';
                                            var preview = input.closest('td').querySelector('img[alt="Vista previa"]');
                                            if (preview) preview.parentNode.remove();
                                        });
                                    }
                                });
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <th>Cabecera del widget — fondo</th>
                            <td><input type="text" name="wur_header_color" value="<?php echo esc_attr($header_color); ?>" class="wur-color-picker" data-default-color="#2d8a4e"></td>
                        </tr>
                        <tr>
                            <th>Cabecera del widget — texto</th>
                            <td><input type="text" name="wur_header_text_color" value="<?php echo esc_attr($header_text_col); ?>" class="wur-color-picker" data-default-color="#ffffff"></td>
                        </tr>
                        <tr>
                            <th>Botón "Abrir WhatsApp" — fondo</th>
                            <td><input type="text" name="wur_button_color" value="<?php echo esc_attr($btn_color); ?>" class="wur-color-picker" data-default-color="#25d366"></td>
                        </tr>
                        <tr>
                            <th>Botón "Abrir WhatsApp" — texto</th>
                            <td><input type="text" name="wur_button_text_color" value="<?php echo esc_attr($btn_text_color); ?>" class="wur-color-picker" data-default-color="#ffffff"></td>
                        </tr>
                    </table>

                    <div class="wur-preview-section">
                        <h3>Vista previa del widget</h3>
                        <div class="wur-preview-widget">
                            <div class="wur-preview-header" style="background:<?php echo esc_attr($header_color); ?>; color:<?php echo esc_attr($header_text_col); ?>">
                                <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="" class="wur-preview-logo">
                                <div>
                                    <strong><?php echo esc_html($company ?: 'Tu empresa'); ?></strong>
                                    <span style="font-size:.8em;opacity:.8;display:block">Normalmente responde en minutos</span>
                                </div>
                            </div>
                            <div class="wur-preview-body">
                                <div class="wur-preview-msg"><?php echo esc_html($welcome ?: '¡Hola! ¿En qué podemos ayudarte?'); ?></div>
                                <div class="wur-preview-faqs">
                                    <?php foreach (array_slice($faqs, 0, 3) as $faq) : ?>
                                    <div class="wur-preview-faq-item"><?php echo esc_html($faq['question']); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="wur-preview-footer">
                                <button class="wur-preview-btn" style="background:<?php echo esc_attr($btn_color); ?>;color:<?php echo esc_attr($btn_text_color); ?>">
                                    <?php echo esc_html($btn_text ?: 'Chatea con nosotros'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ TAB: ASISTENTE ════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-assistant">
                <div class="wur-card">
                    <h2 class="wur-card-title">Asistente What's Up Rabbit?</h2>
                    <p class="wur-card-desc">El asistente es una burbuja flotante dentro del panel de administración de WordPress que te ayuda a configurar y sacar el máximo partido al plugin.</p>

                    <table class="form-table wur-table">
                        <tr>
                            <th><label for="wur_show_assistant">Mostrar asistente en admin</label></th>
                            <td>
                                <input type="checkbox" name="wur_show_assistant" id="wur_show_assistant"
                                       value="1" <?php checked('1', $show_ass); ?>>
                                <label for="wur_show_assistant">Activar el asistente en el panel de WordPress</label>
                                <p class="description">El asistente aparece en la esquina inferior derecha del panel de administración.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_assistant_greeting">Mensaje inicial del asistente</label></th>
                            <td>
                                <textarea name="wur_assistant_greeting" id="wur_assistant_greeting"
                                          rows="3" cols="50"><?php echo esc_textarea($ass_greet); ?></textarea>
                            </td>
                        </tr>
                    </table>

                    <div class="wur-assistant-faqs-info">
                        <h3>Preguntas que responde el asistente</h3>
                        <div class="wur-assistant-faq-list">
                            <div class="wur-assistant-faq-item">
                                <strong>¿Cómo añado mi número de WhatsApp?</strong>
                                <p>Ve a la pestaña "General" e introduce tu número en formato internacional (ej: 34612345678 para España, sin espacios ni símbolos).</p>
                            </div>
                            <div class="wur-assistant-faq-item">
                                <strong>¿Cómo personalizo las preguntas frecuentes?</strong>
                                <p>En la pestaña "Preguntas Frecuentes" puedes añadir, editar o eliminar las preguntas. Recomendamos entre 5 y 10 preguntas relevantes para tu negocio.</p>
                            </div>
                            <div class="wur-assistant-faq-item">
                                <strong>¿Por qué no se ve el widget en mi web?</strong>
                                <p>Asegúrate de haber introducido un número de WhatsApp válido y de haber guardado los cambios. El widget aparece automáticamente en todas las páginas públicas.</p>
                            </div>
                            <div class="wur-assistant-faq-item">
                                <strong>¿Cómo cambio los colores del widget?</strong>
                                <p>Ve a la pestaña "Diseño" y usa los selectores de color para personalizar la burbuja flotante, la cabecera y el botón de WhatsApp.</p>
                            </div>
                            <div class="wur-assistant-faq-item">
                                <strong>¿El bot funciona 24/7?</strong>
                                <p>El widget siempre se muestra. Cuando el usuario selecciona una pregunta, ve la respuesta antes de abrir WhatsApp. Configura el mensaje offline para cuando no estés disponible.</p>
                            </div>
                            <div class="wur-assistant-faq-item">
                                <strong>¿Puedo añadir el widget solo en algunas páginas?</strong>
                                <p>Por defecto el widget aparece en todas las páginas. Si necesitas mostrarlo solo en algunas, puedes usar shortcodes o plugins de visibilidad como "Widget Logic".</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php submit_button('Guardar cambios', 'primary wur-save-btn', 'submit', true); ?>
        </form>

    </div>

    <!-- ══ ASISTENTE FLOTANTE (solo en esta página) ══════════════════════ -->
    <?php if ($show_ass === '1') : ?>
    <div id="wur-assistant-bubble" class="wur-assistant-bubble" title="Asistente What's Up Rabbit?">
        <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="Asistente" width="32" height="32">
    </div>

    <div id="wur-assistant-panel" class="wur-assistant-panel" style="display:none">
        <div class="wur-assistant-header">
            <div class="wur-assistant-header-left">
                <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="" width="24" height="24" class="wur-assistant-icon">
                <span>What's Up Rabbit? — Asistente</span>
            </div>
            <button id="wur-assistant-close">✕</button>
        </div>
        <div id="wur-assistant-messages" class="wur-assistant-messages">
            <div class="wur-msg wur-msg-bot"><?php echo esc_html($ass_greet); ?></div>
        </div>
        <div class="wur-assistant-suggestions">
            <div class="wur-suggestion" data-answer="Ve a la pestaña 'General' e introduce tu número en formato internacional (ej: 34612345678). Sin espacios ni guiones.">¿Cómo añado mi número de WhatsApp?</div>
            <div class="wur-suggestion" data-answer="En la pestaña 'Preguntas Frecuentes' puedes añadir, editar y eliminar preguntas. Recomendamos entre 5 y 10 preguntas relevantes.">¿Cómo edito las FAQs?</div>
            <div class="wur-suggestion" data-answer="Revisa que tienes un número de WhatsApp válido guardado. El widget se muestra automáticamente en todas las páginas públicas de tu web.">¿Por qué no veo el widget?</div>
            <div class="wur-suggestion" data-answer="Ve a la pestaña 'Diseño' y usa los selectores de color para personalizar todos los elementos visuales del widget.">¿Cómo cambio los colores?</div>
            <div class="wur-suggestion" data-answer="El widget siempre se muestra. Las respuestas se muestran dentro del widget. Para chats en vivo necesitas personal o un chatbot externo.">¿El bot responde automáticamente?</div>
            <div class="wur-suggestion" data-answer="El número debe estar en formato internacional: código de país + número sin espacios. Para España: 34 + 9 dígitos (ej: 34612345678).">Formato correcto del número</div>
        </div>
    </div>
    <?php endif; ?>

    <?php
}
