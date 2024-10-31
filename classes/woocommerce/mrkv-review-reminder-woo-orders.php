<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_WOO_ORDERS'))
{
	/**
	 * Class for setup WOO settings orders
	 */
	class MRKV_REVIEW_REMINDER_WOO_ORDERS
	{
		/**
		 * Constructor for WOO settings orders
		 * */
		function __construct()
		{
			if(class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) &&  OrderUtil::custom_orders_table_usage_is_enabled()){
	            add_filter('manage_woocommerce_page_wc-orders_columns', array( $this, 'mrkv_review_reminder_woo_custom_column' ));
	            add_action('manage_woocommerce_page_wc-orders_custom_column', array( $this, 'mrkv_review_reminder_woo_column_get_data_hpos' ), 20, 2 );
	        }
	        else{
	            add_filter('manage_edit-shop_order_columns', array( $this, 'mrkv_review_reminder_woo_custom_column' ));
	            add_action('manage_shop_order_posts_custom_column', array( $this, 'mrkv_review_reminder_woo_column_get_data' ));
	        }
		}

		/**
	     * Creating custom column at woocommerce order page
	     * @param array Columns
	     * @since 1.1.0
	     */
	    public function mrkv_review_reminder_woo_custom_column($columns)
	    {
	    	# Add column
	        $columns['mrkv_review_reminder'] = __('Review Reminder', 'mrkv-review-reminder-pro');

	        return $columns;
	    }

	    /**
	     * Getting data of order column at order page
	     *
	     * @since 1.1.0
	     */
	    public function mrkv_review_reminder_woo_column_get_data($column)
	    {
	    	# Get order ID
	        global $post;
	        $the_order = '';

	        # Check order
	        if($post && $post->ID)
	        {
	        	# Set order
	        	$the_order = wc_get_order( $post->ID );
	        }

	        $this->mrkv_review_reminder_columns_content($column, $the_order);
	    }

	    /**
	     * Getting data of order column at order page
	     *
	     * @since 1.1.0
	     */
	    public function mrkv_review_reminder_woo_column_get_data_hpos($column, $the_order)
	    {
	    	$this->mrkv_review_reminder_columns_content($column, $the_order);
	    }

	    private function mrkv_review_reminder_columns_content($column, $the_order)
	    {
	    	if($the_order)
	    	{
	    		if ($column == 'mrkv_review_reminder') 
		    	{
		    		if($the_order->get_meta('mrkv_review-reminder_sent') === '1')
		    		{
		    			?>
		    				<a>
		    					<div class="mrkv_review_reminder__orders mrkv_review_reminder__order__notice">
			    					<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/notice-success.svg'); ?>" alt="Email" title="Email">
			    					<div class="mrkv_review_reminder__orders__data">
			    						<?php echo esc_html__('Already sent', 'mrkv-review-reminder-pro'); ?>
			    					</div>
			    				</div>
		    				</a>
		    			<?php
		    		}
		    		elseif($the_order->get_meta('mrkv_review-reminder_sent'))
		    		{
		    			?>
			    			<a>
			    				<div class="mrkv_review_reminder__orders mrkv_review_reminder__orders_cron">
			    					<div class="mrkv_review_reminder__orders__data">
			    						<b><?php echo esc_html__('Scheduled on:', 'mrkv-review-reminder-pro'); ?></b>
			    						<span><?php echo esc_html(gmdate('F j, Y', strtotime($the_order->get_meta('mrkv_review-reminder_sent')))); ?></span>
			    						<div class="mrkv_review_reminder__order__send_manually" data-order-id="<?php echo esc_html($the_order->get_id()); ?>" data-type="new">
				    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg'); ?>" alt="Email" title="Email">
				    						<?php echo esc_html__('Send now', 'mrkv-review-reminder-pro'); ?>
				    					</div>
			    					</div>
			    				</div>
			    			</a>
		    			<?php
		    		}
		    		else
		    		{
		    			?>
			    			<a>
			    				<div class="mrkv_review_reminder__orders mrkv_review_reminder__send_old">
			    					<div class="mrkv_review_reminder__order__send_manually" data-order-id="<?php echo esc_html($the_order->get_id()); ?>" data-type="new">
			    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg'); ?>" alt="Email" title="Email">
			    						<?php echo esc_html__('Send now', 'mrkv-review-reminder-pro'); ?>
			    					</div>
			    				</div>
			    			</a>
		    			<?php
		    		}
		        }
	    	}
	    }
	}
}