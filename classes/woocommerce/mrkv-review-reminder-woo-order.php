<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_WOO_ORDER'))
{
	/**
	 * Class for setup WOO settings order
	 */
	class MRKV_REVIEW_REMINDER_WOO_ORDER
	{
		/**
		 * Constructor for WOO settings order
		 * */
		function __construct()
		{
			# Add metabox
			add_action('add_meta_boxes', array( $this, 'mrkv_review_reminder_add_meta_boxes' ));

			# Add function to order create
			add_action('woocommerce_checkout_order_processed', array($this, 'mrkv_review_reminder_add_meta'), 10, 1);
		}

		/**
		 * Add meta reminder after create order
		 * @var string Order ID
		 * */
		public function mrkv_review_reminder_add_meta($order_id)
		{
			# Get order object
            $order = wc_get_order($order_id);

            # Check order
            if($order)
            {
				# Get settings reminder
				$mrkv_review_reminder = get_option('mrkv_review_reminder');

				# Check if products exist settings categories
				if($this->is_product_cat_exist($order, $mrkv_review_reminder) && $this->is_order_status_exist($order, $mrkv_review_reminder))
				{
					# Get current date
					$current_date = new DateTime();

					# Check exist intervals
					if(isset($mrkv_review_reminder['interval']) && $mrkv_review_reminder['interval'])
					{
						# Set intervals
						$interval = $mrkv_review_reminder['interval'];

						# Change dates
						$current_date->modify("+$interval days");
					}

					# Get new date
					$new_date = $current_date->format('Y-m-d H:i:s');

					# Add meta to order
					$order->update_meta_data('mrkv_review-reminder_sent', $new_date);
					$order->save();
				}
			}
		}

		/**
		 * Add metabox to order
		 * */		
		public function mrkv_review_reminder_add_meta_boxes()
		{
			# Check hpos
	        if(class_exists( CustomOrdersTableController::class )){
	            $screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
	            ? wc_get_page_screen_id( 'shop-order' )
	            : 'shop_order';
	        }
	        else{
	            $screen = 'shop_order';
	        }

	        # Add metabox
        	add_meta_box('mrkv_review_reminder_data_box', __('Morkva Review Reminder', 'mrkv-review-reminder-pro'), array( $this, 'mrkv_review_reminder_add_plugin_meta_box' ), $screen, 'side', 'core');
		}

		/**
		 * Check if products exist settings categories
		 * @param object Order
		 * @return boolean Rool
		 * */
		private function is_order_status_exist($order, $mrkv_review_reminder)
		{
			if(isset($mrkv_review_reminder['status_filter']) && !empty($mrkv_review_reminder['status_filter']))
			{
				if ( in_array( 'wc-' . $order->get_status(), $mrkv_review_reminder['status_filter'], true ) ) 
				{
					# Return answer
					return true;
				}

				# Return answer
				return false;
			}

			# Return answer
			return true;
		}

		/**
		 * Check if products exist settings categories
		 * @param object Order
		 * @return boolean Rool
		 * */
		private function is_product_cat_exist($order, $mrkv_review_reminder)
		{
			# Create array excluded
			$excluded_categories = array();

			if(isset($mrkv_review_reminder['categories']) && !empty($mrkv_review_reminder['categories']))
			{
				# Set excluded products
				$excluded_categories = $mrkv_review_reminder['categories'];

				# Loop all products
				foreach ($order->get_items() as $item) 
				{
			        # Get product
			        $product = $item->get_product();
			        
			        # Check product
			        if ($product) 
			        {
			            # Get terms
			            $terms = get_the_terms($product->get_id(), 'product_cat');
			            	
			            # Check terms
			            if ($terms && !is_wp_error($terms)) 
			            {
			            	# Loop all terms
			                foreach ($terms as $term) 
			                {
			                    # Check excluded terms
			                    if (in_array($term->slug, $excluded_categories)) 
			                    {
			                    	# Return answer
			                        return true;
			                    }
			                }
			            }
			        }
			    }

			    # Return answer
				return false;
			}

			# Return answer
			return true;
		}

		public function mrkv_review_reminder_add_plugin_meta_box($post)
		{
			if ($post instanceof WP_Post && $post->ID) 
	        {
	            $order_id = $post->ID;
	            $order = wc_get_order($order_id);

	            if($order)
	            {
	            	if($order->get_meta('mrkv_review-reminder_sent') === '1')
		    		{
		    			?>
		    				<div class="mrkv_review_reminder__order mrkv_review_reminder__order__notice">
		    					<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/notice-success.svg'); ?>" alt="Email" title="Email">
		    					<div class="mrkv_review_reminder__orders__data">
		    						<?php echo esc_html__('Already sent', 'mrkv-review-reminder-pro'); ?>
		    					</div>
		    				</div>
		    				<?php
		    					$mrkv_review_reminder_sent_date = array();

		    					if($order->get_meta('mrkv_review-reminder_sent_date'))
		    					{
		    						$mrkv_review_reminder_sent_date = json_decode($order->get_meta('mrkv_review-reminder_sent_date'), true);
		    					}

		    					$counter = 0;

		    					foreach($mrkv_review_reminder_sent_date as $date)
		    					{
		    						$date_text = ($counter == 0) ? 'Sent:' : 'Re-sent:';
		    						?>
		    							<b><?php echo esc_html($date_text . ' ' . gmdate('F j, Y', strtotime($date))); ?></b>
		    						<?php

		    						++$counter;
		    					}
		    				?>
		    				<h3><?php echo esc_html__('Actions', 'mrkv-review-reminder-pro'); ?></h3>
		    				<div class="mrkv_review_reminder__order mrkv_review_reminder__send_old">
		    					<div class="mrkv_review_reminder__order__send_manually" data-order-id="<?php echo esc_html($order_id); ?>" data-type="resend">
		    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg'); ?>" alt="Email" title="Email">
		    						<?php echo esc_html__('Resend reminder', 'mrkv-review-reminder-pro'); ?>
		    					</div>
		    				</div>
		    			<?php
		    		}
		    		elseif($order->get_meta('mrkv_review-reminder_sent'))
		    		{
		    			?>
		    				<div class="mrkv_review_reminder__order mrkv_review_reminder__order_cron">
		    					<div class="mrkv_review_reminder__order_cron__inner">
		    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/clock.svg'); ?>" alt="Email" title="Email">
			    					<div class="mrkv_review_reminder__orders__data">
			    						<b><?php echo esc_html__('Scheduled on:', 'mrkv-review-reminder-pro'); ?></b><br>
			    						<span><?php echo esc_html($order->get_meta('mrkv_review-reminder_sent')); ?></span>
			    					</div>
		    					</div>
		    					<div class="mrkv_review_reminder__order__send_manually" data-order-id="<?php echo esc_html($order_id); ?>" data-type="new">
		    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg'); ?>" alt="Email" title="Email">
		    						<?php echo esc_html__('Send now', 'mrkv-review-reminder-pro'); ?>
		    					</div>
		    					<div class="mrkv_review_reminder__order__delete_manually" data-order-id="<?php echo esc_html($order_id); ?>">
		    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/trash-logo.svg'); ?>" alt="Email" title="Email">
		    						<?php echo esc_html__('Remove reminder', 'mrkv-review-reminder-pro'); ?>
		    					</div>
		    				</div>
		    			<?php
		    		}
		    		else
		    		{
		    			?>
		    				<h3><?php echo esc_html__('Actions', 'mrkv-review-reminder-pro'); ?></h3>
		    				<div class="mrkv_review_reminder__order mrkv_review_reminder__send_old">
		    					<div class="mrkv_review_reminder__order__send_manually" data-order-id="<?php echo esc_html($order_id); ?>" data-type="new">
		    						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg'); ?>" alt="Email" title="Email">
		    						<?php echo esc_html__('Send now', 'mrkv-review-reminder-pro'); ?>
		    					</div>
		    				</div>
		    			<?php
		    		}
	            }
	        }
		}
	}
}