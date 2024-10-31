<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_ADMIN_ASSETS'))
{
	/**
	 * Class for setup plugin admin assets
	 */
	class MRKV_REVIEW_REMINDER_ADMIN_ASSETS
	{
		/**
		 * Constructor for plugin admin assets
		 * */
		function __construct()
		{
			# Add plugin scripts and styles
			add_action('admin_enqueue_scripts', array($this, 'mrkv_review_reminder_styles_and_scripts'));
		}

		/**
		 * Register plugin admin assets
		 * @var string Hook

		 * */
	    public function mrkv_review_reminder_styles_and_scripts($hook)
	    {
	    	global $pagenow, $typenow;
	    	$nonce = wp_create_nonce('mrkv_review_reminder_sent_nonce');
	    	$screen = get_current_screen();

	    	$html_result_sent = '<div class="mrkv_review_reminder__order mrkv_review_reminder__order__notice">
				<img src="' .  MRKV_REVIEW_REMINDER_IMG_URL . '/global/notice-success.svg' . '" alt="Email" title="Email">
				<div class="mrkv_review_reminder__orders__data">
				' . __('Already sent', 'mrkv-review-reminder-pro') . '
				</div>
			</div>';

	    	if(($pagenow == 'admin.php' || $pagenow == 'post.php') && ('shop_order' === $typenow || (isset($screen->id) && $screen->id == 'woocommerce_page_wc-orders')) || (isset($screen->id) && $screen->id == 'woocommerce_page_shop_order'))
	    	{
	    		wp_enqueue_style('global-mrkv-review-reminder', MRKV_REVIEW_REMINDER_ASSETS_URL . '/css/global/global-mrkv-review-reminder.css', array(), MRKV_REVIEW_REMINDER_PLUGIN_VERSION);
	    		wp_register_script('admin-review-reminder-select2-js', MRKV_REVIEW_REMINDER_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_REVIEW_REMINDER_PLUGIN_VERSION, true);
            	wp_enqueue_script('admin-review-reminder-select2-js', MRKV_REVIEW_REMINDER_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_REVIEW_REMINDER_PLUGIN_VERSION, true);
	    		wp_enqueue_script('global-mrkv-review-reminder', MRKV_REVIEW_REMINDER_ASSETS_URL . '/js/global/global-review-reminder.js', array('jquery', 'jquery-ui-autocomplete', 'admin-review-reminder-select2-js'), MRKV_REVIEW_REMINDER_PLUGIN_VERSION, true);

    			wp_localize_script('global-mrkv-review-reminder', 'mrkv_review_rem_helper', [
	            	'ajax_url' => admin_url( "admin-ajax.php" ),
	            	'nonce' => $nonce,
	            	'already_sent' => $html_result_sent
	        	]);
	    	}
	    	
	    	$all_hooks = array('toplevel_page_mrkv_review_reminder_settings');
	    	$all_hooks_shipping = array();
	    	$method_page = false;
	    	$slug_shipping = '';

	    	# Check page
	    	if (!in_array($hook, $all_hooks)) {
	            return;
	        }

	        # Custom style and script
	        wp_enqueue_style('admin-mrkv-review-reminder-select2', MRKV_REVIEW_REMINDER_ASSETS_URL.'/css/global/select2.min.css', array(), MRKV_REVIEW_REMINDER_PLUGIN_VERSION);
	        wp_enqueue_style('admin-mrkv-review-reminder', MRKV_REVIEW_REMINDER_ASSETS_URL . '/css/admin/admin-mrkv-review-reminder.css', array(), MRKV_REVIEW_REMINDER_PLUGIN_VERSION);
	        wp_register_script('admin-review-reminder-select2-js', MRKV_REVIEW_REMINDER_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_REVIEW_REMINDER_PLUGIN_VERSION, true);
            	wp_enqueue_script('admin-review-reminder-select2-js', MRKV_REVIEW_REMINDER_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_REVIEW_REMINDER_PLUGIN_VERSION, true);
	        wp_enqueue_script('admin-mrkv-review-reminder', MRKV_REVIEW_REMINDER_ASSETS_URL . '/js/admin/admin-mrkv-review-reminder.js', array('jquery', 'admin-review-reminder-select2-js'), MRKV_REVIEW_REMINDER_PLUGIN_VERSION, true);

	        wp_localize_script('admin-mrkv-review-reminder', 'mrkv_review_rem_helper', [
            	'ajax_url' => admin_url( "admin-ajax.php" ),
            	'nonce' => $nonce,
            	'already_sent' => $html_result_sent
        	]);
	    }
	}
}