<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include ua shipping options
require_once 'global/mrkv-review-reminder-options.php'; 
# Include ua shipping menu
require_once 'admin/mrkv-review-reminder-menu.php'; 
# Include settings assets
require_once 'admin/mrkv-review-reminder-admin-assets.php';
# Include debug log
require_once 'log/mrkv-review-reminder-log.php'; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_SETTINGS'))
{
	/**
	 * Class for setup plugin settings
	 */
	class MRKV_REVIEW_REMINDER_SETTINGS
	{
		/**
		 * Constructor for plugin settings
		 * */
		function __construct()
		{
			# Setup woo plugin settings options
			new MRKV_REVIEW_REMINDER_OPTIONS();

			# Setup woo plugin settings menu
			new MRKV_REVIEW_REMINDER_MENU();

			# Setup woo plugin settings assets
			new MRKV_REVIEW_REMINDER_ADMIN_ASSETS();
		}
	}
}