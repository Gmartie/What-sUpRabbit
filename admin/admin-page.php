<?php
if (!defined('ABSPATH')) exit;

/* ─── Menú lateral ─────────────────────────────────────────────────────── */
add_action('admin_menu', function () {
    $svg     = file_get_contents(WUR_PATH . 'WURabbitLogo.svg');
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
    // Colors
    $btn_color        = get_option('wur_button_color', '#25d366');
    $btn_text_color   = get_option('wur_button_text_color', '#ffffff');
    $header_color     = get_option('wur_header_color', '#2d8a4e');
    $header_text_col  = get_option('wur_header_text_color', '#ffffff');
    $bubble_color     = get_option('wur_bubble_color', '#2d8a4e');
    $bubble_text_col  = get_option('wur_bubble_text_color', '#ffffff');
    $bubble_icon_url  = get_option('wur_bubble_icon_url', '');
    $bubble_mode      = get_option('wur_bubble_mode', 'logo');
    $bubble_bg_color  = get_option('wur_bubble_bg_color', '#25d366');
    $button_style     = get_option('wur_button_style', '');
    $header_icon_url  = get_option('wur_header_icon_url', '');
    $header_icon_mode = get_option('wur_header_icon_mode', 'bubble');
    $header_icon_color= get_option('wur_header_icon_color', '');
    $msg_text_color   = get_option('wur_msg_text_color', '#333333');
    // Schedule
    $sched_enabled    = get_option('wur_schedule_enabled', '0');
    $sched_days_raw   = get_option('wur_schedule_days', '["mon","tue","wed","thu","fri"]');
    $sched_days       = json_decode($sched_days_raw, true) ?: ['mon','tue','wed','thu','fri'];
    $sched_start      = get_option('wur_schedule_start', '09:00');
    $sched_end        = get_option('wur_schedule_end', '18:00');
    $notif_timeout    = get_option('wur_notification_timeout', '30');
    // Agents
    $agents_raw       = get_option('wur_agents', '[]');
    $agents           = json_decode($agents_raw, true) ?: [];

    $all_days = ['mon'=>'Lun','tue'=>'Mar','wed'=>'Mié','thu'=>'Jue','fri'=>'Vie','sat'=>'Sáb','sun'=>'Dom'];
    ?>
    <div class="wrap wur-wrap">

        <!-- ══ BANNER ════════════════════════════════════════════════════ -->
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
                <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="button button-secondary wur-test-btn" id="wur-test-widget-btn" title="Abre tu web para probar el widget">🔍 Probar widget</a>
            </div>
        </div>

        <?php if (empty($whatsapp) && empty($agents)) : ?>
        <div class="notice notice-warning wur-notice">
            <p><strong>⚠ Atención:</strong> No has configurado un número de WhatsApp ni agentes. El widget no se mostrará hasta que configures al menos uno.</p>
        </div>
        <?php else : ?>
        <div class="notice notice-success wur-notice is-dismissible">
            <p><strong>✓ Activo:</strong> El widget de WhatsApp está configurado<?php if ($whatsapp) echo ' con el número <code>' . esc_html($whatsapp) . '</code>'; if (!empty($agents)) echo ' — ' . count($agents) . ' agente(s) configurados'; ?>.</p>
        </div>
        <?php endif; ?>

        <!-- ══ TABS ═══════════════════════════════════════════════════════ -->
        <div class="wur-tabs">
            <button class="wur-tab active" data-tab="general">General</button>
            <button class="wur-tab" data-tab="faqs">Preguntas Frecuentes</button>
            <button class="wur-tab" data-tab="agents">Agentes</button>
            <button class="wur-tab" data-tab="schedule">Horario</button>
            <button class="wur-tab" data-tab="design">Diseño</button>
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
                            <th><label for="wur_whatsapp_number">Número de WhatsApp (único)</label></th>
                            <td>
                                <input type="text" name="wur_whatsapp_number" id="wur_whatsapp_number"
                                       value="<?php echo esc_attr($whatsapp); ?>"
                                       placeholder="34612345678" class="regular-text">
                                <p class="description">Número en formato internacional sin espacios ni guiones. Si usas <strong>múltiples agentes</strong>, configúralos en la pestaña «Agentes» y deja este campo vacío.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_welcome_message">Mensaje de bienvenida</label></th>
                            <td>
                                <textarea name="wur_welcome_message" id="wur_welcome_message"
                                          rows="4" cols="50"><?php echo esc_textarea($welcome); ?></textarea>
                                <p class="description">Puedes usar <code>**texto**</code> para <strong>negrita</strong> y saltos de línea para separar párrafos.</p>
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
                                <p class="description">Mensaje que aparece en el widget cuando está fuera del horario de atención.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_notification_timeout">Punto rojo — minutos de inactividad</label></th>
                            <td>
                                <input type="number" name="wur_notification_timeout" id="wur_notification_timeout"
                                       value="<?php echo esc_attr($notif_timeout); ?>"
                                       min="1" max="10080" step="1" class="small-text"> minutos
                                <p class="description">El punto rojo de la burbuja desaparece al abrir el widget y vuelve a aparecer tras este tiempo de inactividad.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ══ TAB: FAQs ═════════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-faqs">
                <div class="wur-card">
                    <h2 class="wur-card-title">Preguntas Frecuentes</h2>
                    <p class="wur-card-desc">Arrastra las preguntas para reordenarlas. El usuario podrá seleccionarlas en el widget y ver la respuesta antes de abrir WhatsApp.</p>

                    <div id="wur-faqs-wrapper">
                        <?php foreach ($faqs as $i => $faq) : ?>
                        <div class="wur-faq-row">
                            <div class="wur-faq-header">
                                <span class="wur-drag-handle" title="Arrastra para reordenar">⠿</span>
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
                        <strong>Consejo:</strong> Añade entre 5 y 10 preguntas frecuentes. En las respuestas puedes insertar enlaces con el botón 🔗.
                    </div>
                </div>
            </div>

            <!-- ══ TAB: AGENTES ══════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-agents">
                <div class="wur-card">
                    <h2 class="wur-card-title">Múltiples Agentes</h2>
                    <p class="wur-card-desc">Cuando configures agentes, el widget mostrará una lista para que el usuario elija con quién hablar. <strong>Si hay agentes configurados, el número único de la pestaña General se ignora.</strong></p>

                    <div id="wur-agents-wrapper">
                        <?php foreach ($agents as $ai => $agent) : ?>
                        <div class="wur-agent-row">
                            <div class="wur-faq-header">
                                <span class="wur-drag-handle" title="Arrastra para reordenar">⠿</span>
                                <span class="wur-faq-num"><?php echo $ai + 1; ?></span>
                                <button type="button" class="wur-remove-agent" title="Eliminar agente">✕</button>
                            </div>
                            <div class="wur-agent-fields">
                                <input type="text" class="wur-agent-name regular-text" placeholder="Nombre del agente"
                                       value="<?php echo esc_attr($agent['name'] ?? ''); ?>">
                                <input type="text" class="wur-agent-role regular-text" placeholder="Cargo o especialidad (opcional)"
                                       value="<?php echo esc_attr($agent['role'] ?? ''); ?>">
                                <input type="text" class="wur-agent-number regular-text" placeholder="Número WhatsApp (34612345678)"
                                       value="<?php echo esc_attr($agent['number'] ?? ''); ?>">
                                <div class="wur-agent-photo-wrap">
                                    <input type="text" class="wur-agent-photo regular-text" placeholder="URL de la foto (opcional)"
                                           value="<?php echo esc_attr($agent['photo'] ?? ''); ?>">
                                    <button type="button" class="button wur-agent-photo-btn">Seleccionar foto</button>
                                    <?php if (!empty($agent['photo'])) : ?>
                                    <img src="<?php echo esc_url($agent['photo']); ?>" class="wur-agent-preview-img" alt="Foto">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="button wur-add-agent">+ Añadir agente</button>
                    <input type="hidden" name="wur_agents" id="wur-agents-json" value="<?php echo esc_attr($agents_raw); ?>">
                </div>
            </div>

            <!-- ══ TAB: HORARIO ══════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-schedule">
                <div class="wur-card">
                    <h2 class="wur-card-title">Horario de Atención</h2>
                    <table class="form-table wur-table">
                        <tr>
                            <th>Activar horario</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wur_schedule_enabled" value="1"
                                           <?php checked('1', $sched_enabled); ?> id="wur_schedule_enabled">
                                    Mostrar estado online/offline según horario
                                </label>
                                <p class="description">Cuando está activado, el widget muestra «En línea» dentro del horario y «Fuera de horario» + el mensaje offline fuera de él.</p>
                            </td>
                        </tr>
                        <tr id="wur-sched-days-row" <?php echo $sched_enabled !== '1' ? 'style="display:none"' : ''; ?>>
                            <th>Días de atención</th>
                            <td>
                                <fieldset style="display:flex;gap:10px;flex-wrap:wrap;">
                                    <?php foreach ($all_days as $val => $label) : ?>
                                    <label style="background:#f5f5f5;padding:6px 12px;border-radius:6px;cursor:pointer;">
                                        <input type="checkbox" name="wur_schedule_days[]" value="<?php echo $val; ?>"
                                               <?php checked(in_array($val, $sched_days), true); ?>>
                                        <?php echo $label; ?>
                                    </label>
                                    <?php endforeach; ?>
                                </fieldset>
                            </td>
                        </tr>
                        <tr id="wur-sched-hours-row" <?php echo $sched_enabled !== '1' ? 'style="display:none"' : ''; ?>>
                            <th>Franja horaria</th>
                            <td>
                                <label>Desde <input type="time" name="wur_schedule_start" value="<?php echo esc_attr($sched_start); ?>" style="margin:0 8px"></label>
                                <label>Hasta <input type="time" name="wur_schedule_end" value="<?php echo esc_attr($sched_end); ?>" style="margin:0 8px"></label>
                                <p class="description">Horario en la zona horaria del servidor WordPress.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ══ TAB: DISEÑO ═══════════════════════════════════════════ -->
            <div class="wur-tab-content" id="tab-design">
                <div class="wur-card">
                    <h2 class="wur-card-title">Personalización del Widget</h2>
                    <table class="form-table wur-table">
                        <tr>
                            <th>Burbuja flotante — color del logo</th>
                            <td>
                                <input type="text" name="wur_bubble_color" value="<?php echo esc_attr($bubble_color); ?>" class="wur-color-picker" data-default-color="#2d8a4e">
                                <p class="description">Solo aplica en modo «Logo sin fondo».</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_bubble_icon_url">Icono de la burbuja flotante</label></th>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <input type="text" name="wur_bubble_icon_url" id="wur_bubble_icon_url"
                                           value="<?php echo esc_attr($bubble_icon_url); ?>"
                                           class="regular-text" placeholder="URL de la imagen">
                                    <button type="button" class="button" id="wur-icon-upload-btn">Seleccionar imagen</button>
                                    <button type="button" class="button" id="wur-icon-remove-btn"<?php echo empty($bubble_icon_url) ? ' style="display:none"' : ''; ?>>Quitar icono</button>
                                </div>
                                <div id="wur-bubble-icon-preview" style="margin-top:10px;<?php echo empty($bubble_icon_url) ? 'display:none' : ''; ?>">
                                    <img src="<?php echo esc_url($bubble_icon_url); ?>"
                                         style="max-width:64px;max-height:64px;border:1px solid #ddd;padding:4px;background:#f9f9f9;border-radius:6px;" alt="Vista previa burbuja">
                                </div>
                                <p class="description">PNG, SVG o JPG. Si lo dejas vacío se usará el logo de What's Up Rabbit?</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Estilo de la burbuja flotante</label></th>
                            <td>
                                <fieldset>
                                    <label style="margin-right:16px">
                                        <input type="radio" name="wur_bubble_mode" value="logo" <?php checked('logo', $bubble_mode); ?>>
                                        Logo sin fondo (por defecto)
                                    </label>
                                    <label>
                                        <input type="radio" name="wur_bubble_mode" value="circle" <?php checked('circle', $bubble_mode); ?>>
                                        Burbuja circular con color de fondo
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr id="wur-bubble-bg-row" <?php echo $bubble_mode !== 'circle' ? 'style="display:none"' : ''; ?>>
                            <th><label for="wur_bubble_bg_color">Burbuja circular — color de fondo</label></th>
                            <td>
                                <input type="text" name="wur_bubble_bg_color" id="wur_bubble_bg_color"
                                       value="<?php echo esc_attr($bubble_bg_color); ?>"
                                       class="wur-color-picker" data-default-color="#25d366">
                            </td>
                        </tr>
                        <tr>
                            <th>Cabecera del widget — fondo</th>
                            <td><input type="text" name="wur_header_color" value="<?php echo esc_attr($header_color); ?>" class="wur-color-picker" data-default-color="#2d8a4e" id="wur_header_color_picker"></td>
                        </tr>
                        <tr>
                            <th>Cabecera del widget — texto</th>
                            <td><input type="text" name="wur_header_text_color" value="<?php echo esc_attr($header_text_col); ?>" class="wur-color-picker" data-default-color="#ffffff" id="wur_header_text_picker"></td>
                        </tr>
                        <tr>
                            <th><label for="wur_header_icon_url">Cabecera del widget — icono</label></th>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <input type="text" name="wur_header_icon_url" id="wur_header_icon_url"
                                           value="<?php echo esc_attr($header_icon_url); ?>"
                                           class="regular-text" placeholder="Deja vacío para usar el logo de What's Up Rabbit?">
                                    <button type="button" class="button" id="wur-header-icon-upload-btn">Seleccionar imagen</button>
                                    <button type="button" class="button" id="wur-header-icon-remove-btn"<?php echo empty($header_icon_url) ? ' style="display:none"' : ''; ?>>Quitar icono</button>
                                </div>
                                <div id="wur-header-icon-preview" style="margin-top:8px;<?php echo empty($header_icon_url) ? 'display:none' : ''; ?>">
                                    <img src="<?php echo esc_url($header_icon_url); ?>"
                                         style="max-width:48px;max-height:48px;border:1px solid #ddd;padding:4px;background:#f9f9f9;border-radius:6px;" alt="Vista previa cabecera">
                                </div>
                                <p class="description">Icono a la izquierda del nombre de empresa en la cabecera.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Icono de cabecera — estilo</label></th>
                            <td>
                                <label style="margin-right:16px">
                                    <input type="radio" name="wur_header_icon_mode" value="bubble" <?php checked('bubble', $header_icon_mode); ?>>
                                    Con burbuja (círculo semitransparente)
                                </label>
                                <label>
                                    <input type="radio" name="wur_header_icon_mode" value="direct" <?php checked('direct', $header_icon_mode); ?>>
                                    Directo (solo el icono)
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wur_header_icon_color">Icono de cabecera — color</label></th>
                            <td>
                                <input type="text" name="wur_header_icon_color" id="wur_header_icon_color"
                                       value="<?php echo esc_attr($header_icon_color); ?>"
                                       class="wur-color-picker" data-default-color="#ffffff">
                                <p class="description">Deja en blanco para icono blanco sobre cabecera de color.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Texto de mensajes y respuestas</th>
                            <td>
                                <input type="text" name="wur_msg_text_color" value="<?php echo esc_attr($msg_text_color); ?>" class="wur-color-picker" data-default-color="#333333">
                                <p class="description">Color del mensaje de bienvenida, respuestas de FAQ y botones de pregunta.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Botón «Abrir WhatsApp» — fondo</th>
                            <td><input type="text" name="wur_button_color" value="<?php echo esc_attr($btn_color); ?>" class="wur-color-picker" data-default-color="#25d366" id="wur_btn_color_picker"></td>
                        </tr>
                        <tr>
                            <th>Botón «Abrir WhatsApp» — texto</th>
                            <td><input type="text" name="wur_button_text_color" value="<?php echo esc_attr($btn_text_color); ?>" class="wur-color-picker" data-default-color="#ffffff" id="wur_btn_text_picker"></td>
                        </tr>
                        <tr>
                            <th><label>Botón «Abrir WhatsApp» — formato</label></th>
                            <td>
                                <label style="margin-right:12px">
                                    <input type="checkbox" name="wur_button_style[]" value="bold"
                                           <?php checked(true, strpos($button_style, 'bold') !== false); ?>>
                                    <strong>Negrita</strong>
                                </label>
                                <label style="margin-right:12px">
                                    <input type="checkbox" name="wur_button_style[]" value="italic"
                                           <?php checked(true, strpos($button_style, 'italic') !== false); ?>>
                                    <em>Cursiva</em>
                                </label>
                                <label>
                                    <input type="checkbox" name="wur_button_style[]" value="underline"
                                           <?php checked(true, strpos($button_style, 'underline') !== false); ?>>
                                    <span style="text-decoration:underline">Subrayado</span>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <div class="wur-preview-section">
                        <h3>Vista previa del widget</h3>
                        <div class="wur-preview-widget">
                            <div class="wur-preview-header" id="wur-preview-header"
                                 style="background:<?php echo esc_attr($header_color); ?>; color:<?php echo esc_attr($header_text_col); ?>">
                                <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="" class="wur-preview-logo" id="wur-preview-logo">
                                <div>
                                    <strong id="wur-preview-name"><?php echo esc_html($company ?: 'Tu empresa'); ?></strong>
                                    <span style="font-size:.8em;opacity:.8;display:block">Normalmente responde en minutos</span>
                                </div>
                            </div>
                            <div class="wur-preview-body">
                                <div class="wur-preview-msg" id="wur-preview-msg"><?php echo esc_html($welcome ?: '¡Hola! ¿En qué podemos ayudarte?'); ?></div>
                                <div class="wur-preview-faqs">
                                    <?php foreach (array_slice($faqs, 0, 3) as $faq) : ?>
                                    <div class="wur-preview-faq-item"><?php echo esc_html($faq['question']); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="wur-preview-footer">
                                <button class="wur-preview-btn" id="wur-preview-btn"
                                        style="background:<?php echo esc_attr($btn_color); ?>;color:<?php echo esc_attr($btn_text_color); ?>">
                                    <?php echo esc_html($btn_text ?: 'Chatea con nosotros'); ?>
                                </button>
                            </div>
                        </div>
                        <p style="font-size:.8rem;color:#888;margin-top:6px;">Los colores se actualizan en tiempo real. El icono de burbuja/cabecera se aplica al guardar.</p>
                    </div>
                </div>
            </div>

            <?php submit_button('Guardar cambios', 'primary wur-save-btn', 'submit', true); ?>
        </form>

    </div>

    <!-- ══ ASISTENTE FLOTANTE ═════════════════════════════════════════════ -->
    <div id="wur-assistant-bubble" class="wur-assistant-bubble" title="Asistente What's Up Rabbit?">
        <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="Asistente" width="32" height="32">
    </div>

    <div id="wur-assistant-panel" class="wur-assistant-panel" style="display:none">
        <div class="wur-assistant-header">
            <div class="wur-assistant-header-left">
                <img src="<?php echo esc_url(WUR_URL . 'WURabbitLogo.svg'); ?>" alt="" width="24" height="24" class="wur-assistant-icon">
                <span>What's Up Rabbit? — Asistente</span>
            </div>
            <button id="wur-assistant-close" type="button">✕</button>
        </div>
        <div id="wur-assistant-messages" class="wur-assistant-messages">
            <div class="wur-msg wur-msg-bot">¡Hola! Soy el asistente de What's Up Rabbit?. ¿En qué puedo ayudarte?</div>
        </div>
        <div class="wur-assistant-suggestions">
            <div class="wur-suggestion" data-answer="Ve a la pestaña 'General' e introduce tu número en formato internacional (ej: 34612345678). Sin espacios ni guiones.">¿Cómo añado mi número?</div>
            <div class="wur-suggestion" data-answer="En la pestaña 'Preguntas Frecuentes' puedes añadir, editar y eliminar preguntas. Arrastra el icono ⠿ para reordenarlas.">¿Cómo edito las FAQs?</div>
            <div class="wur-suggestion" data-answer="Ve a la pestaña 'Agentes', añade cada agente con su nombre, cargo, número de WhatsApp y foto opcional. El widget mostrará la lista para que el usuario elija.">¿Cómo configuro varios agentes?</div>
            <div class="wur-suggestion" data-answer="Ve a la pestaña 'Horario', activa la opción y elige los días y franja horaria. Fuera de ese horario el widget mostrará el estado offline.">¿Cómo activo el horario?</div>
            <div class="wur-suggestion" data-answer="Revisa que tienes un número de WhatsApp válido o agentes configurados. Si el widget sigue sin aparecer, desactiva plugins de caché y recarga.">¿Por qué no veo el widget?</div>
            <div class="wur-suggestion" data-answer="El número debe estar en formato internacional: código de país + número sin espacios. Para España: 34 + 9 dígitos (ej: 34612345678).">Formato correcto del número</div>
        </div>
    </div>

    <?php
}