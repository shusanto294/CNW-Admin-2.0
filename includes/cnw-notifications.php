<?php

// Hook into 'admin_init' to ensure settings are available
add_action('admin_init', 'cnw_initialize_enable_notification_features');

function cnw_initialize_enable_notification_features() {

    // Retrieve the settings from the options table
    $options = get_option('cnw_settings');

    // Ensure the settings are an array
    $enable_notification_features = isset($options['notifications_settings']) ? $options['notifications_settings'] : array();


	if (!is_array($enable_notification_features)) {
		return;
	}
	
	// Disable Admin Email Confirm
	if (isset($enable_notification_features['cnw-admin-email-check'])) {
		add_filter( 'admin_email_check_interval', '__return_false' );
	}

	// Disable Update Emails
	if (isset($enable_notification_features['cnw-update-emails'])) {
		add_filter( 'send_core_update_notification_email', '__return_false' );
		add_filter( 'auto_plugin_update_send_email', '__return_false' );
		add_filter( 'auto_theme_update_send_email', '__return_false' );
	}

	// Disable New User Emails
	if (isset($enable_notification_features['cnw-newuser-emails'])) {
		add_filter( 'wp_send_new_user_notification_to_admin', '__return_false' );
	}

	// Disable Password Reset Emails
	if (isset($enable_notification_features['cnw-passwordreset-emails'])) {
		remove_action( 'after_password_reset', 'wp_password_change_notification' );
	}
}