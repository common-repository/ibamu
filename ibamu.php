<?php
/**
 *  @package Lawwwing
 */
/*
Plugin Name: Lawwwing
Plugin URI: https://lawwwing.com/
Description: Plugin todo en uno desarrollado por Lawwwing.com. Incluye las funcionalidades para ayudar a tu web a cumplir con las normativas RGPD, LOPDGDD, LSSI, LGDCU.
Version: 1.2.4
Author: ibamu
Author URI: https://profiles.wordpress.org/ibamu/
Tags: cookies, cookie law, cookie policy, cookie banner, privacy policy, cookie consent, privacy, gdpr, eprivacy
License: GPLv2 or later
Text Domain: Lawwwing
*/

if (! defined('ABSPATH')){
    die;
}

// First, I define a constant to see if site is network activated
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once(ABSPATH . '/wp-admin/includes/plugin.php' );
}

if (is_plugin_active_for_network('ibamu/ibamu.php')) {
    define("LW_NETWORK_MODE", true);
}
else {
    define("LW_NETWORK_MODE", false);
}

// Wordpress function 'get_site_option' and 'get_option' depending if multisite is activated
function get_lawwwing_option($option_name) {
    if(LW_NETWORK_MODE== true) {
        $lw_options = get_site_option("ibamu_options");
        if (isset($lw_options) && $lw_options != false) {
            return $lw_options[$option_name];
        }
        else {
            return false;
        }
    }
    else {
        $lw_options = get_option("ibamu_options");
        if (isset($lw_options) && $lw_options != false) {
            return $lw_options[$option_name];
        }
        else {
            return false;
        }
    }
}

/**
 * Register our ibamu_settings_init to the admin_init action hook.
 */
function ibamu_settings_init() {
    // Register a new setting for "ibamu" page.
    register_setting('ibamu', 'ibamu_options');
    // Register a new section in the "ibamu" page.
    add_settings_section(
        'ibamu_section_developers',
        "", "",
        'ibamu'
    );

    // Register a new field in the "ibamu_section_developers" section, inside the "ibamu" page.
    add_settings_field(
        'ibamu_widget_uuid',
        __( 'Plugin ID:', 'ibamu-plugin-domain' ),
        'ibamu_widget_uuid_render',
        'ibamu',
        'ibamu_section_developers',
        array(
            'label_for'         => 'ibamu_widget_uuid',
            'class'             => 'lawwwing-row',
            'ibamu_custom_data' => 'custom',
        )
    );

    // Migration from already setted options in network mode
    if (LW_NETWORK_MODE== true) {
        $lw_options = get_option("ibamu_options");
        if (isset($lw_options)) {
            add_site_option('ibamu_options', $lw_options);
        }
    }
}
add_action('admin_init', 'ibamu_settings_init');

/**
 * Hook into options page after save.
 * If network mode is activated, it will save the options in the network.
 */
function lw_hook_after_options_saved( $old_value, $new_value ) {
	if (LW_NETWORK_MODE== true) {
        add_site_option('ibamu_options', $new_value);
        if ($old_value != $new_value) {
            update_site_option('ibamu_options', $new_value);
        }
    } else {
        add_option('ibamu_options', $new_value);
        if ($old_value != $new_value) {
            update_option('ibamu_options', $new_value);
        }
    }
}
add_action('update_option_ibamu_options', 'lw_hook_after_options_saved', 10, 2);

/**
 * Register styles on admin_enqueue_scripts hook.
 */
function ibamu_admin_add_assets() {
    wp_enqueue_style('lawwwing-admin-styles', plugins_url('/css/lw-styles.css', __FILE__ ), array(), "1.2.4");
    wp_enqueue_style('lawwwing-font-awesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css", array(), "1.2.4");
}
add_action('admin_enqueue_scripts', 'ibamu_admin_add_assets');


function ibamu_widget_uuid_render( $args ) {
    ?>
    <input
        id="<?php echo esc_attr( $args['label_for'] ); ?>"
        data-custom="<?php echo esc_attr( $args['ibamu_custom_data'] ); ?>"
        name="ibamu_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
        value="<?php echo esc_attr( get_lawwwing_option('ibamu_widget_uuid') ); ?>"
        placeholder="Plugin id"
        class="regular-text"
        type="text"
        required>
    </input>
    <p class="description">Introduce el Plugin ID que encontrarás en tu panel de Lawwwing</p>
    <?php
}

/**
 * Add the top level menu page.
 */
function ibamu_options_page() {
    add_menu_page(
        'ibamu',
        'Lawwwing',
        'manage_options',
        'ibamu',
        'ibamu_options_page_html',
        'https://cdn.lawwwing.com/static/assets/img/favicon/lawwwing/favicon-16x16.png'
    );
}


/**
 * Register our ibamu_options_page to the admin_menu action hook.
 */
add_action('admin_menu', 'ibamu_options_page');

/**
 * Top level menu callback function
 */
function ibamu_options_page_html() {
    // check user capabilities
    if ( ! current_user_can('manage_options') ) {
        return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error('ibamu_messages', 'ibamu_message', esc_html(__('Guardado correctamente'), 'ibamu-plugin-domain'), 'updated');
    }

    // show error/update messages
    esc_html(settings_errors('ibamu_messages'));
    ?>
    <div class="lawwwing-wrapper">
        <div class="lawwwing-header" id="ibamu-header">
            <div class="lawwwing-logo">
                <a href="https://lawwwing.com/?utm_source=wordpress" title="Lawwwing" target="_blank">
                    <img height="32" class="navbar-brand-dark rotate-logo" src="https://cdn.lawwwing.com/static/assets/img/logos/horizontal/logo_color2.png" alt="textos legales web">
                </a>
            </div>
        </div>
        <div class="lawwwing-content">
            <div class="lawwwing-title">
                <h1>Adapta <u class="tertiary">Lawwwing</u> a tus necesidades</h1>
                <h2>Actualiza en <strong>5 minutos</strong> y <strong>para siempre</strong> la legalidad de tu web con nuestro plugin</h2>
                <h4>Escoge el plan que mejor se adapte a tu negocio</h4>
            </div>

            <div class="lawwwing-plan-cards">
                <div class="lawwwing-plan-card">
                    <div class="lawwwing-plan-card-top">
                        <div>
                            <h3 class="lawwwing-primary lawwwing-plan-name">STARTER</h3>
                            <p class="lawwwing-plan-subtitle">Para webs informativas y blogs sin venta online</p>
                        </div>
                    </div>
                    <div class="lawwwing-plan-card-bottom">
                        <div class="lawwwing-plan-card-bottom-left">
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Banner de cookies</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Aviso legal</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Política de privacidad y cookies</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-circle-xmark"></i>Términos y Condiciones</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Cláusulas de consentimiento</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Diseño personalizado</p>
                        </div>
                        <div class="lawwwing-plan-card-bottom-right">
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Español / Inglés</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Escaneo semanal</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Soporte básico</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-circle-xmark"></i>IAB TCF v2.2</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Google Consent Mode v2</p>
                        </div>
                    </div>
                    <div class="lawwwing-plans-wrapper">
                        <div class="lawwwing-plan">
                            <h3 class="lawwwing-plan-price">11€<span class="lawwwing-plan-period">/mes</span></h3>
                        </div>
                        <p>o</p>
                        <div class="lawwwing-plan discount">
                            <h3 class="lawwwing-plan-price">99€<span class="lawwwing-plan-period">/año</span></h3>
                            <div class="lawwwing-plan-discount">-25%</div>
                        </div>
                    </div>
                    <div class="lawwwing-button-wrapper">
                        <a href="https://lawwwing.com/signup/?utm_source=wordpress&utm_campaign=plan-button" target="_blank" class="button-solid secondary">Empezar</a>
                    </div>
                </div>

                <div class="lawwwing-plan-card">
                    <div class="lawwwing-plan-card-top">
                        <div>
                            <h3 class="lawwwing-primary lawwwing-plan-name">GROWTH</h3>
                            <p class="lawwwing-plan-subtitle">Para tiendas online y webs con venta de productos o servicios</p>
                        </div>
                    </div>
                    <div class="lawwwing-plan-card-bottom">
                        <div class="lawwwing-plan-card-bottom-left">
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Banner de cookies</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Aviso legal</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Política de privacidad y cookies</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Términos y Condiciones</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Cláusulas de consentimiento</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Diseño personalizado</p>
                        </div>
                        <div class="lawwwing-plan-card-bottom-right">
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Español / Inglés</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Escaneo semanal</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Soporte básico</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-circle-xmark"></i>IAB TCF v2.2</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Google Consent Mode v2</p>
                        </div>
                    </div>
                    <div class="lawwwing-plans-wrapper">
                        <div class="lawwwing-plan">
                            <h3 class="lawwwing-plan-price">16€<span class="lawwwing-plan-period">/mes</span></h3>
                        </div>
                        <p>o</p>
                        <div class="lawwwing-plan discount">
                            <h3 class="lawwwing-plan-price">149€<span class="lawwwing-plan-period">/año</span></h3>
                            <div class="lawwwing-plan-discount">-25%</div>
                        </div>
                    </div>
                    <div class="lawwwing-button-wrapper">
                        <a href="https://lawwwing.com/signup/?utm_source=wordpress&utm_campaign=plan-button" target="_blank" class="button-solid secondary">Empezar</a>
                    </div>
                </div>

                <div class="lawwwing-plan-card">
                    <div class="lawwwing-plan-card-top">
                        <div>
                            <h3 class="lawwwing-primary lawwwing-plan-name">PROFESSIONAL</h3>
                            <p class="lawwwing-plan-subtitle">Pensado para los que buscan un nivel más avanzado de servicios y soporte</p>
                        </div>
                    </div>
                    <div class="lawwwing-plan-card-bottom">
                        <div class="lawwwing-plan-card-bottom-left">
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Banner de cookies</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Aviso legal</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Política de privacidad y cookies</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Términos y Condiciones</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Cláusulas de consentimiento</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Diseño personalizado</p>
                        </div>
                        <div class="lawwwing-plan-card-bottom-right">
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Mútiples idiomas</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Escaneo diario</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Soporte personalizado</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>IAB TCF v2.2</p>
                            <p class="lawwwing-bullet-element"><i class="fas fa-check-circle"></i>Google Consent Mode v2</p>
                        </div>
                    </div>
                    <div class="lawwwing-plans-wrapper">
                        <div class="lawwwing-plan">
                            <h3 class="lawwwing-plan-price">32€<span class="lawwwing-plan-period">/mes</span></h3>
                        </div>
                        <p>o</p>
                        <div class="lawwwing-plan discount">
                            <h3 class="lawwwing-plan-price">300€<span class="lawwwing-plan-period">/año</span></h3>
                            <div class="lawwwing-plan-discount">-25%</div>
                        </div>
                    </div>
                    <div class="lawwwing-button-wrapper">
                        <a href="https://lawwwing.com/signup/?utm_source=wordpress&utm_campaign=plan-button" target="_blank" class="button-solid secondary">Empezar</a>
                    </div>
                </div>
            </div>

            <div class="lawwwing-configuration-wrapper">
                <div class="lawwwing-configuration-title">
                    <h1 class="">Configuración</h1>
                </div>
                <div class="lawwwing-configuration-form-wrapper">
                    <div class="lawwwing-configuration-form">
                        <p class="lawwwing-text">
                        Para <strong>configurar</strong> el plugin necesitarás <strong>rellenar el formulario</strong> con el "plugin id" que encontrarás en tu panel de <a class="ibamu-url" href="https://lawwwing.com/?utm_source=wordpress" target="_blank">https://lawwwing.com/signup/</a>
                        </p>

                        <form action="options.php" method="post">
                            <?php
                            esc_html(settings_fields('ibamu'));
                            esc_html(do_settings_sections('ibamu'));
                            esc_html(submit_button(__('Guardar', 'ibamu-plugin-domain'), 'primary', 'submit', 'true', array( 'data-style' => 'lw-custom-submit' )));
                            ?>
                        </form>
                    </div>
                    <div class="lawwwing-configuration-extra">
                        <ul>
                            <li>Regístrate en <a class="ibamu-url" href="https://lawwwing.com/?utm_source=wordpress" target="_blank">https://lawwwing.com/</a> para obtener <strong>tu clave</strong></li>
                            <li>Instala Lawwwing de forma gratuita</li>
                            <li>Personaliza el <strong>comportamiento y diseño del plugin</strong> en tu panel de usuario</li>
                            <li>Elige uno de <strong>nuestros planes</strong> para garantizar el cumplimiento normativo</li>
                            <li>Escanearemos semanalmente tu web para asegurar que <strong>siempre cumple con la normativa</strong></li>
                        </ul>
                        <hr>
                        <div class="lawwwing-configuration-badges">
                            <img src="https://cdn.lawwwing.com/static/assets/img/iab/iab_logo_registered_cmp_base.webp" height="80">
                            <!-- <img src="https://cdn.lawwwing.com/static/assets/img/badges/google-cmp-badge.svg" height="100"> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Enqueue cookie-widget script
 */
add_action('wp_enqueue_scripts', 'lawwwing_include_plugin', 1);
function lawwwing_include_plugin() {
    $plugin_id = get_lawwwing_option("ibamu_widget_uuid");
    $base_script = "https://cdn.lawwwing.com/widgets/current/{$plugin_id}/cookie-widget.min.js";
    wp_enqueue_script("lawwwing-plugin", $base_script, "", "1.2.4");
}

function add_lawwwing_data_arguments($tag, $handle) {
    if ( 'lawwwing-plugin' !== $handle )
        return $tag;

    $plugin_id = get_lawwwing_option("ibamu_widget_uuid");
    return str_replace(' src', " data-lwid=\"{$plugin_id}\" src", $tag);
}
add_filter('script_loader_tag', 'add_lawwwing_data_arguments', 1, 2);

/**
 * WPRocket exclude script from minification
 * See: https://docs.wp-rocket.me/article/976-exclude-files-from-defer-js#exclude-files-and-domains
 */
add_filter('rocket_defer_inline_exclusions', function($inline_exclusions_list) {
    $inline_exclusions_list[] = 'cdn.lawwwing.com';
    return $inline_exclusions_list;
});

add_filter('rocket_excluded_inline_js_content', function($inline_exclusions_list) {
    $inline_exclusions_list[] = 'cdn.lawwwing.com';
    return $inline_exclusions_list;
});

add_filter('rocket_minify_excluded_external_js', function($excluded_external_js) {
    $excluded_external_js[] = 'cdn.lawwwing.com';
    return $excluded_external_js;
});

/**
 * SiteGround Optimizer exclude script from combination
 * See: https://wordpress.org/plugins/sg-cachepress/
 */
add_filter( 'sgo_javascript_combine_excluded_external_paths', 'lawwwing_js_combine_exclude_external_script' );
function lawwwing_js_combine_exclude_external_script( $exclude_list ) {
    $exclude_list[] = 'cdn.lawwwing.com';
    return $exclude_list;
}

?>
