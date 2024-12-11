<?php

add_action('admin_init', 'cnw_initialize_enable_performance_features');

function cnw_initialize_enable_performance_features() {
	// Retrieve the settings from the options table
	$options = get_option('cnw_settings');

	// Ensure the settings are an array
	$enable_performance_features = isset($options['performance_settings']) ? $options['performance_settings'] : array();

    if (!is_array($enable_performance_features)) {
        return;
    }

    // LiteSpeed Quick Purge URL
    if (isset($enable_performance_features['cnw-quickpurge'])) {
        add_action('init', 'cnw_clear_litespeed_cache_on_demand');
    }

    // Disable Author Archives
    if (isset($enable_performance_features['cnw-disableauthorarchives'])) {
        add_action('template_redirect', 'cnw_disable_author_archives');
		add_action('the_author_posts_link', 'cnw_disable_author_posts_link');
    }

    // Disable Image Compression
    if (isset($enable_performance_features['cnw-disablecompression'])) {
        add_filter('jpeg_quality', 'cnw_set_jpeg_quality');
        add_filter('wp_editor_set_quality', 'cnw_set_jpeg_quality');
        add_filter('wp_generate_attachment_metadata', 'cnw_set_metadata_quality');
    }

}

// Functions for each feature below:

function cnw_clear_litespeed_cache_on_demand() {
    $clear_value = sanitize_text_field($_GET['clear'] ?? '');
    if ($clear_value === date('mdY') . 'cnw') {
        if (function_exists('do_action')) {
            do_action('litespeed_purge_all');
            echo 'Cache cleared successfully.';
            exit;
        } else {
            echo 'LiteSpeed Cache is not installed or active.';
            exit;
        }
    }
}


// Disable Author Archives
function cnw_disable_author_archives() { 
	if ( is_author() || isset( $_GET['author'] ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}

function cnw_disable_author_posts_link ( $link ) {
	if ( ! is_admin() ) {
		return get_the_author();
	}
	return $link;
}

function cnw_set_jpeg_quality() {
	return 100;
}

function cnw_set_metadata_quality($metadata) {
	if (isset($metadata['file'])) {
		$type = wp_check_filetype($metadata['file']);
        if ($type['type'] === 'image/jpeg') {
            $metadata['quality'] = 100;
        }
    }
    return $metadata;
}



