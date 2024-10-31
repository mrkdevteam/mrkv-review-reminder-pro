<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_OPTIONS'))
{
	/**
	 * Class for setup plugin global options
	 */
	class MRKV_REVIEW_REMINDER_OPTIONS
	{
		/**
		 * Constructor for plugin global options
		 * */
		function __construct()
		{
			# Register settings
			add_action('admin_init', array($this, 'mrkv_review_reminder_register_settings'));
		}

		/**
		 * Register plugin options
		 * 
		 * */
	    public function mrkv_review_reminder_register_settings()
	    {
	    	# List of plugin options
	        $options = array(
	            'mrkv_review_reminder',
	        );

	        # Loop of option
	        foreach ($options as $option) 
	        {
	        	# Register option
	            register_setting('mrkv-review-reminder-settings-group', $option);
	        }
	    }
	}
}