<?php
/**
 * Plugin Name: What's Up Rabbit?
 * Description: Bot de preguntas frecuentes para WhatsApp. Responde automáticamente las dudas más comunes sobre tu empresa de páginas web.
 * Version: alpha 1.1
 * Author: Gabriel
 * Text Domain: whatsup-rabbit
 */

if (!defined('ABSPATH')) exit;

define('WUR_PATH', plugin_dir_path(__FILE__));
define('WUR_URL',  plugin_dir_url(__FILE__));
define('WUR_VERSION', 'alpha 1.1');

require_once WUR_PATH . 'includes/settings.php';
require_once WUR_PATH . 'includes/ajax.php';
require_once WUR_PATH . 'includes/enqueue.php';
require_once WUR_PATH . 'admin/admin-page.php';

register_activation_hook(__FILE__, 'wur_activate');
function wur_activate() {
    $default_faqs = [
        ['question' => '¿Cuánto cuesta una página web?', 'answer' => 'El precio de una página web varía según las funcionalidades. Una web básica corporativa parte desde 500€, mientras que tiendas online o webs con funcionalidades avanzadas pueden costar desde 1.500€. Contáctanos para un presupuesto personalizado sin compromiso.'],
        ['question' => '¿Cuánto tiempo tardan en hacer mi web?', 'answer' => 'El tiempo de desarrollo depende del proyecto. Una web corporativa suele estar lista en 2–4 semanas. Un e-commerce o web compleja puede llevar entre 4–8 semanas. Te daremos un plazo exacto tras ver tus necesidades.'],
        ['question' => '¿Incluye mantenimiento?', 'answer' => 'Sí, ofrecemos planes de mantenimiento mensual que incluyen actualizaciones de seguridad, copias de seguridad, soporte técnico y pequeños cambios de contenido. Los precios de mantenimiento parten desde 50€/mes.'],
        ['question' => '¿La web será responsive (adaptada a móvil)?', 'answer' => 'Por supuesto. Todas nuestras webs están diseñadas de forma responsive, adaptándose perfectamente a móviles, tablets y ordenadores. Es un estándar que incluimos en todos nuestros proyectos.'],
        ['question' => '¿Me ayudáis con el posicionamiento SEO?', 'answer' => 'Sí, todas nuestras webs se desarrollan con una base SEO sólida: velocidad de carga optimizada, estructura de URLs, meta etiquetas y más. También ofrecemos servicios de SEO avanzado como servicio adicional.'],
        ['question' => '¿Puedo gestionar el contenido yo mismo?', 'answer' => 'Sí, trabajamos principalmente con WordPress, el gestor de contenidos más usado del mundo. Te formaremos para que puedas actualizar textos, imágenes y contenidos fácilmente, sin necesitar conocimientos técnicos.'],
        ['question' => '¿Hacéis tiendas online?', 'answer' => 'Sí, desarrollamos tiendas online completas con WooCommerce. Incluye carrito de compra, pasarela de pago, gestión de productos, pedidos e inventario. Contáctanos para ver qué necesitas.'],
        ['question' => '¿Cómo puedo contactar con vosotros?', 'answer' => 'Puedes contactarnos por WhatsApp (el canal más rápido), por email o rellenando el formulario de contacto de nuestra web. Respondemos en menos de 24 horas laborables.'],
    ];
    if (!get_option('wur_faqs')) {
        update_option('wur_faqs', json_encode($default_faqs));
    }
}
