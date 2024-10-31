<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include settings
require_once 'settings/mrkv-review-reminder-settings.php'; 
# Include woocommerce settings
require_once 'woocommerce/mrkv-review-reminder-woocommerce.php'; 
# Include cron
require_once 'reminder/mrkv-review-reminder-cron.php'; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_RUN'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_REVIEW_REMINDER_RUN
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
			# Setup woo plugin settings
			new MRKV_REVIEW_REMINDER_SETTINGS();

			# Load cron functions
			add_action( 'plugins_loaded', array($this, 'mrkv_review_reminder_cron_init'));

			# Load woocommerce functions
			add_action( 'before_woocommerce_init', array($this, 'mrkv_review_reminder_woocommerce_init'));
		}

		/**
		 * Load woocommerce functions
		 * */
		public function mrkv_review_reminder_woocommerce_init()
		{
			# Setup woo plugin woocommerce settings
			new MRKV_REVIEW_REMINDER_WOOCOMMERCE();
		}

		/**
		 * Load cron functions
		 * */
		public function mrkv_review_reminder_cron_init()
		{
			# Setup cron
			new MRKV_REVIEW_REMINDER_CRON();
		}
	}
}