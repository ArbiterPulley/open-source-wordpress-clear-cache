<?php
/**
 * Uninstall routine.
 *
 * @package Thistle Press Cache
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

foreach (array('tpc_stats', 'tpc_stats_url', 'tpc_report_at', 'tpc_install_at', 'tpc_settings', 'tpc_consent', 'tpc_consent_secrets', 'tpc_report_v', 'tpc_touch', 'tpc_metrics_due') as $option) {
    delete_option($option);
}

foreach (array('tpc_seen', 'tpc_due', 'tpc_sent', 'tpc_since', 'tpc_env_check', 'tpc_cloud', 'tpc_policy') as $key) {
    delete_transient($key);
}

foreach (array('tpc_cache_expire', 'tpc_cache_preload', 'tpc_db_maintenance') as $hook) {
    wp_clear_scheduled_hook($hook);
}

global $wpdb;
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'thistle-press-cache_redirects');
delete_option('tpc_redirects_db');

$dir = WP_CONTENT_DIR . '/cache/thistle-press-cache';

foreach ((array) glob($dir . '/*') as $file) {
    if (is_file($file)) {
        @unlink($file);
    }
}

if (is_dir($dir)) {
    @rmdir($dir);
}
