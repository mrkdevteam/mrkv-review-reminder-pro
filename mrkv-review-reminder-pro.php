<?php
/**
 * Plugin Name: Morkva Review Reminder PRO
 * Plugin URI: https://morkva.co.ua/product-category/plugins/
 * Description: We send simple emails to remind you of your review
 * Version: 1.0.8
 * Author: MORKVA
 * Text Domain: mrkv-review-reminder-pro
 * Domain Path: /languages/
 * Tested up to: 6.8
 * WC requires at least: 3.8
 * WC tested up to: 9.8
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugData = get_plugin_data(__FILE__,false, false);

# Global File
define('MRKV_REVIEW_REMINDER_PLUGIN_FILE', __FILE__);

# Define constant of plugin direction and path
define('MORKVA_REVIEW_REMINDER_DIR', plugin_dir_path(__FILE__));
define('MORKVA_REVIEW_REMINDER_PATH_URL', plugin_dir_url(__FILE__));
define('MORKVA_REVIEW_REMINDER_PLUGIN_VERSION', $plugData['Version']);

# Include CONSTANTS
require_once 'constants-mrkv-review-reminder.php';

# Check if Woo plugin activated
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
{
    # Include plugin settings
    require_once 'classes/mrkv-review-reminder-run.php'; 

    # Setup plugin settings
    new MRKV_REVIEW_REMINDER_RUN();
}

add_action( 'plugins_loaded', function() {
    load_plugin_textdomain(
        'mrkv-review-reminder-pro',
        false,
        dirname( plugin_basename(__FILE__) ) . '/languages/'
    );
}, 11);

# Require License Managment
require_once 'morkva-pro-licensed.php';
new MORKVAPROLICENSED();

add_filter( 'plugin_row_meta', function( $links_array, $plugin_file_name, $plugin_data, $status ) {

    if( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {

        $links_array[] = sprintf(
            '<a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
            add_query_arg(
                array(
                    'tab' => 'plugin-information',
                    'plugin' => plugin_basename( __DIR__ ),
                    'TB_iframe' => true,
                    'width' => 772,
                    'height' => 788
                ),
                admin_url( 'plugin-install.php' )
            ),
            __( 'View details', 'mrkv-review-reminder-pro' )
        );

    }

    return $links_array;

}, 25, 4 );

add_action('init', 'mrkv_review_reminder_check_update');

function mrkv_review_reminder_check_update()
{
    $path = MORKVA_REVIEW_REMINDER_DIR . '/class-review-update-check.php';
    if (file_exists($path)) {
        require $path;
        $Checker = Checker::buildUpdateChecker('http://api.morkva.co.ua/api.json', __FILE__);
        $Checker->addQueryArgFilter('mrkv_review_reminder_query_arg_filter');
    }
}

function mrkv_review_reminder_query_arg_filter($query)
{
    $query['product'] = 'mrkv-review-reminder-pro';
    $query['secret'] = MORKVA_REVIEW_REMINDER_PLUGIN_VERSION;
    $query['website'] = get_home_url();
    $query['license'] = get_option('mrkv_licence_management_api');
    return $query;
}