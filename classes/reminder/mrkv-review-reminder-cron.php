<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_CRON'))
{
	/**
	 * Class for setup cron
	 */
	class MRKV_REVIEW_REMINDER_CRON
	{
		/**
		 * Constructor for cron
		 * */
		function __construct()
		{
			# Register the deactivation hook
        	register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        	# Check cron exist
        	if (!wp_next_scheduled('mrkv_next_reminder')) 
        	{
	            # Schedule the event if it's not already scheduled
	            wp_schedule_event(time(), 'twicedaily', 'mrkv_next_reminder');
	        }

	        # Hook the review_reminder function to the cron event
	        add_action('mrkv_next_reminder', array($this, 'review_reminder_cron_function'));
		}

		/**
		 * Function to deactivate the cron job
		 * */ 
	    public function deactivate() 
	    {
	        # Clear the scheduled event on deactivation
	        $timestamp = wp_next_scheduled('mrkv_next_reminder');

	        # Check timestamp
	        if ($timestamp) 
	        {
	        	# Remove cron
	            wp_unschedule_event($timestamp, 'mrkv_next_reminder');
	        }
	    }

	    /**
	     * Edit orders query
	     * */
	    public function modify_order_query_clauses($clauses, $query) 
	    {
		    global $wpdb;

		    $clauses['where'] .= $wpdb->prepare(" 
		        AND {$wpdb->postmeta}.meta_key = %s 
		        AND {$wpdb->postmeta}.meta_value != %s
		    ", 'mrkv_review-reminder_sent', '1');

		    $clauses['join'] .= " INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";

		    return $clauses;
		}

	    /**
	     * The function that will be called on the cron event
	     * */ 
	    public function review_reminder_cron_function() 
	    {
	    	$transient_key = 'pending_review_orders';
			$orders = get_transient($transient_key);

			if (false === $orders) 
			{
			    add_filter('posts_clauses', array($this, 'modify_order_query_clauses'), 10, 2);

			    $args = [
			        'limit' => -1,
			    ];

			    $orders = wc_get_orders($args);

			    remove_filter('posts_clauses', array($this, 'modify_order_query_clauses'), 10);

			    set_transient($transient_key, $orders, 10 * MINUTE_IN_SECONDS);
			}

    		# Load file sender
    		require_once MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'classes/reminder/mrkv-review-reminder-sender.php';

    		# Check orders
    		if(is_array($orders) && !empty($orders))
    		{
    			# Loop all orders
    			foreach($orders as $order)
	    		{
	    			$mrkv_review_reminder_sent = $order->get_meta('mrkv_review-reminder_sent');

	    			if ($this->is_valid_date($mrkv_review_reminder_sent)) 
	    			{
	    				# Get date
	    				$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mrkv_review_reminder_sent);

	    				# Check date format
	    				if ($dateTime === false) 
	    				{
	    					# Go to next step
					        continue;
					    }

					    # Check date beetween
					    if($dateTime <= new DateTime())
					    {
					    	# Get reminder object
							$reminder = new MRKV_REVIEW_REMINDER_SENDER($order);

							# Send reminder
							$message = $reminder->send_reminder_review();
					    }
	    			}
	    		}
    		}
	    }

	    /**
	     * Check if is valid date
	     * @var string Date
	     * @return boolean Answer
	     * */
	    public function is_valid_date($date) 
	    {
	    	# Create date format
	    	$date_new = DateTime::createFromFormat('Y-m-d H:i:s', $date);

	    	# Adjust format based on stored date
    		return $date_new && $date_new->format('Y-m-d H:i:s') === $date;
	    }
	}
}