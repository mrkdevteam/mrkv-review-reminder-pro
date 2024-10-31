<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include woo orders data
require_once 'mrkv-review-reminder-woo-orders.php';
require_once 'mrkv-review-reminder-woo-order.php';
require_once 'mrkv-review-reminder-woo-ajax.php';

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_WOOCOMMERCE'))
{
	/**
	 * Class for setup WOO settings
	 */
	class MRKV_REVIEW_REMINDER_WOOCOMMERCE
	{
		/**
		 * Constructor for WOO settings
		 * */
		function __construct()
		{
			# Setup woo plugin woocommerce orders
			new MRKV_REVIEW_REMINDER_WOO_ORDERS();

			# Setup woo plugin woocommerce order
			new MRKV_REVIEW_REMINDER_WOO_ORDER();

			# Setup ajax actions
			new MRKV_REVIEW_REMINDER_WOO_AJAX();
		}
	}
}