<?php
# Get plugin data
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugData = get_plugin_data(MRKV_REVIEW_REMINDER_PLUGIN_FILE,false, false);

# Constans 

# Directories
define('MRKV_REVIEW_REMINDER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MRKV_REVIEW_REMINDER_PLUGIN_PATH_TEMP', plugin_dir_path(__FILE__) . 'templates');

# Links
define('MRKV_REVIEW_REMINDER_PLUGIN_DIR', plugin_dir_url(__FILE__));
define('MRKV_REVIEW_REMINDER_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets');
define('MRKV_REVIEW_REMINDER_IMG_URL', plugin_dir_url(__FILE__) . 'assets/images');

# Data
define('MRKV_REVIEW_REMINDER_NAME', $plugData['Name']);
define('MRKV_REVIEW_REMINDER_PLUGIN_VERSION', $plugData['Version']);
define('MRKV_REVIEW_REMINDER_PLUGIN_TEXT_DOMAIN', 'mrkv-review-reminder-pro');