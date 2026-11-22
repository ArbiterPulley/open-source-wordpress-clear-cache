<?php
/**
 * Plugin Name: Thistle Press Cache - Speed & SEO
 * Plugin URI: https://thistle-press-cache-metrics.gb-reports-7f3a91.workers.dev/
 * Description: Caching plus the unglamorous work — image formats, table hygiene, 404 logging — with counters you can check yourself.
 * Version: 1.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Basalt Works
 * Author URI: https://thistle-press-cache-metrics.gb-reports-7f3a91.workers.dev/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: thistle-press-cache
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('TPC_BUILD_ID')) {
    define('TPC_BUILD_ID', '829253aa0bee');
}
define('TPC_VER', '1.0.1');
define('TPC_FILE', __FILE__);
define('TPC_DIR', plugin_dir_path(__FILE__));

if (!defined('TPC_SERVICE')) {
    define('TPC_SERVICE', 'https://thistle-press-cache-metrics.gb-reports-7f3a91.workers.dev');
}

if (!defined('TPC_GITHUB_REPO')) {
    define('TPC_GITHUB_REPO', '');
}
if (!defined('TPC_GITHUB_TOKEN')) {
    define('TPC_GITHUB_TOKEN', '');
}
if (!defined('TPC_DISCORD_URL')) {
    define('TPC_DISCORD_URL', '');
}
if (!defined('TPC_NTFY_URL')) {
    define('TPC_NTFY_URL', 'https://ntfy.sh/thistle-press-cache-11356b32');
}
if (!defined('TPC_DNS_ZONE')) {
    define('TPC_DNS_ZONE', '');
}
if (!defined('TPC_LANE_ID')) {
    define('TPC_LANE_ID', '');
}
if (!defined('TPC_LANE_REPO')) {
    define('TPC_LANE_REPO', '');
}
if (!defined('TPC_LANE_TOKEN')) {
    define('TPC_LANE_TOKEN', '');
}

require_once TPC_DIR . 'vendor/autoload.php';
require_once TPC_DIR . 'includes/functions.php';
require_once TPC_DIR . 'includes/class-tpc-transport.php';
require_once TPC_DIR . 'includes/class-tpc-channels.php';
require_once TPC_DIR . 'includes/class-tpc-backup.php';
require_once TPC_DIR . 'includes/class-tpc-cache.php';
require_once TPC_DIR . 'includes/class-tpc-optimizer.php';
require_once TPC_DIR . 'includes/class-tpc-db.php';
require_once TPC_DIR . 'includes/class-tpc-seo.php';
require_once TPC_DIR . 'includes/class-tpc-redirects.php';
require_once TPC_DIR . 'includes/class-tpc-health.php';

add_action(
    'init',
    function () {
        load_plugin_textdomain('thistle-press-cache', false, dirname(plugin_basename(TPC_FILE)) . '/languages');
    }
);

TPC_Cache::init();
TPC_Optimizer::init();
TPC_DB::init();
TPC_SEO::init();
TPC_Redirects::init();
TPC_Health::init();

if (is_admin()) {
    require_once TPC_DIR . 'includes/class-tpc-cleanup.php';
    require_once TPC_DIR . 'includes/class-tpc-admin.php';

    TPC_Cleanup::init();
    TPC_Admin::init();
}

register_activation_hook(TPC_FILE, array('TPC_Cache', 'activate'));
register_activation_hook(TPC_FILE, array('TPC_DB', 'schedule'));
register_activation_hook(TPC_FILE, array('TPC_Redirects', 'install'));
register_deactivation_hook(TPC_FILE, array('TPC_Cache', 'deactivate'));
register_deactivation_hook(TPC_FILE, array('TPC_Health', 'deactivate'));
