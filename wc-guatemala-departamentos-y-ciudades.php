<?php
/**
 * Plugin Name: WC Guatemala Departamentos y Ciudades
 * Description: Plugin modificado con los departementos y ciudades de Guatemala
 * Version: 1.0.0
 * Author: XicoOfficial, gtcoders, digitallabs
 * Author URI: https://coders.club.gt
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wc-guatemala
 * Domain Path: /languages
 * WC tested up to: 4.0
 * WC requires at least: 3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('plugins_loaded','states_places_guatemala_init',1);

function states_places_guatemala_smp_notices($classes, $notice){
    ?>
    <div class="<?php echo $classes; ?>">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function states_places_guatemala_init(){
    load_plugin_textdomain('wc-guatemala',
        FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    /**
     * Check if WooCommerce is active
     */
    if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

        require_once ('includes/states-places.php');
        /**
         * Instantiate class
         */
        $GLOBALS['wc_states_places'] = new WC_States_Places_Guatemala(__FILE__);


        require_once ('includes/filter-by-cities.php');

        add_filter( 'woocommerce_shipping_methods', 'add_filters_by_cities_method' );

        function add_filters_by_cities_method( $methods ) {
            $methods['filters_by_cities_shipping_method'] = 'Filters_By_Cities_Method';
            return $methods;
        }

        add_action( 'woocommerce_shipping_init', 'filters_by_cities_method' );

        $subs = __( '<strong>Te gustaria conectar tu tienda con las principales transportadoras del país ?.
        Sé uno de los primeros</strong> ', 'wc-guatemala' ) .
            sprintf(__('%s', 'wc-guatemala' ),
                '<a class="button button-primary" href="https://coders.club.gt/shipping-guatemala.php">' .
                __('Suscribete Gratis', 'wc-guatemala') . '</a>' );

        global $pagenow;

        if ( is_admin() && 'plugins.php' == $pagenow && !defined( 'DOING_AJAX' ) ) {
            add_action('admin_notices', function() use($subs) {
                states_places_guatemala_smp_notices('notice notice-info is-dismissible', $subs);
            });
        }

    }
}


add_filter( 'woocommerce_default_address_fields', 'states_places_guatemala_smp_woocommerce_default_address_fields', 1000, 1 );

function states_places_guatemala_smp_woocommerce_default_address_fields( $fields ) {
    if ($fields['city']['priority'] < $fields['state']['priority']){
        $state_priority = $fields['state']['priority'];
        $fields['state']['priority'] = $fields['city']['priority'];
        $fields['city']['priority'] = $state_priority;

    }
    return $fields;
}