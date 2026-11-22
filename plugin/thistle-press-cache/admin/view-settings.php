<?php
/**
 * Settings screen.
 *
 * @package Thistle Press Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

$s     = tpc_settings();
$stats = tpc_stats();
$total = (int) $stats['hits'] + (int) $stats['misses'];
$rate  = $total ? round(((int) $stats['hits'] / $total) * 100) : 0;
$cloud = get_transient('tpc_cloud');
$sizes = TPC_DB::sizes();
$sys   = tpc_system_info();
$last  = (int) get_option('tpc_report_at', 0);
?>
<div class="wrap thistle-press-cache-wrap">
    <div class="tpc-hero">
        <div class="tpc-brand">
            <span class="tpc-mark">
                <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 22 20H2z" fill="currentColor"/></svg>
            </span>
            <div>
                <strong>Thistle Press Cache</strong>
                <span>v<?php echo esc_html(TPC_VER); ?> &middot; cache, SEO, benchmark </span>
            </div>
        </div>
        <div class="tpc-pills">
            <span class="tpc-pill <?php echo tpc_setting('cache_ttl') ? 'is-on' : ''; ?>"><?php esc_html_e('Page cache', 'thistle-press-cache'); ?></span>
            <span class="tpc-pill <?php echo tpc_setting('html_minify') ? 'is-on' : ''; ?>"><?php esc_html_e('Minify', 'thistle-press-cache'); ?></span>
            <span class="tpc-pill <?php echo (tpc_setting('webp') && function_exists('imagewebp')) ? 'is-on' : ''; ?>"><?php esc_html_e('WebP', 'thistle-press-cache'); ?></span>
            <span class="tpc-pill <?php echo tpc_setting('sitemap') ? 'is-on' : ''; ?>"><?php esc_html_e('Sitemap', 'thistle-press-cache'); ?></span>
        </div>
    </div>

    <h1><?php esc_html_e('Thistle Press Cache', 'thistle-press-cache'); ?></h1>

    <?php settings_errors('sl'); ?>

    <?php if (isset($_GET['imported'])) : ?>
        <div class="notice notice-<?php echo '1' === $_GET['imported'] ? 'success' : 'warning'; ?> is-dismissible">
            <p><?php echo '1' === $_GET['imported'] ? esc_html__('Settings imported.', 'thistle-press-cache') : esc_html__('No file received.', 'thistle-press-cache'); ?></p>
        </div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper thistle-press-cache-tabs">
        <a class="nav-tab nav-tab-active" href="#dashboard"><?php esc_html_e('Dashboard', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#cache"><?php esc_html_e('Cache', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#optimize"><?php esc_html_e('Optimize', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#database"><?php esc_html_e('Database', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#seo"><?php esc_html_e('SEO', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#redirects"><?php esc_html_e('Redirects', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#cloud"><?php esc_html_e('Cloud', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#diagnostics"><?php esc_html_e('Diagnostics', 'thistle-press-cache'); ?></a>
        <a class="nav-tab" href="#tools"><?php esc_html_e('Tools', 'thistle-press-cache'); ?></a>
    </nav>

    <form method="post" action="">
        <?php wp_nonce_field('tpc_save', 'tpc_nonce'); ?>

        <div class="thistle-press-cache-panel" id="dashboard">
            <h2><?php esc_html_e('Overview', 'thistle-press-cache'); ?></h2>
            <div class="thistle-press-cache-cards">
                <div class="thistle-press-cache-card"><span><?php esc_html_e('Cache hit rate', 'thistle-press-cache'); ?></span><strong><?php echo (int) $rate; ?>%</strong></div>
                <div class="thistle-press-cache-card"><span><?php esc_html_e('Average load', 'thistle-press-cache'); ?></span><strong><?php echo esc_html($stats['load']); ?> ms</strong></div>
                <div class="thistle-press-cache-card"><span><?php esc_html_e('Cached pages', 'thistle-press-cache'); ?></span><strong><?php echo (int) tpc_cache_count(); ?></strong></div>
                <div class="thistle-press-cache-card"><span><?php esc_html_e('Cache size', 'thistle-press-cache'); ?></span><strong><?php echo esc_html(tpc_bytes(tpc_cache_size())); ?></strong></div>
            </div>
            <p class="description">
                <?php printf(esc_html__('Requests: %1$d &middot; Misses: %2$d &middot; Cleanups: %3$d', 'thistle-press-cache'), (int) $stats['hits'], (int) $stats['misses'], (int) $stats['cleared']); ?>
            </p>
            <p>
                <button type="button" class="button" id="thistle-press-cache-clear"><?php esc_html_e('Purge cache', 'thistle-press-cache'); ?></button>
                <span class="thistle-press-cache-result" id="thistle-press-cache-clear-result"></span>
            </p>
        </div>

        <div class="thistle-press-cache-panel" id="cache">
            <h2><?php esc_html_e('Page cache', 'thistle-press-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Enable cache', 'thistle-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="tpc_settings[cache_enabled]" value="1" <?php checked($s['cache_enabled'], 1); ?>> <?php esc_html_e('Cache anonymous pages as static HTML', 'thistle-press-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="thistle-press-cache-ttl"><?php esc_html_e('Lifetime', 'thistle-press-cache'); ?></label></th>
                    <td><input type="number" id="thistle-press-cache-ttl" name="tpc_settings[cache_ttl]" value="<?php echo (int) $s['cache_ttl']; ?>" min="60" max="86400" class="small-text"> <?php esc_html_e('seconds', 'thistle-press-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Logged-in users', 'thistle-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="tpc_settings[cache_logged_in]" value="1" <?php checked($s['cache_logged_in'], 1); ?>> <?php esc_html_e('Serve cached pages to signed-in users too', 'thistle-press-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Purge on publish', 'thistle-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="tpc_settings[purge_on_update]" value="1" <?php checked($s['purge_on_update'], 1); ?>> <?php esc_html_e('Clear the cache when content changes', 'thistle-press-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="thistle-press-cache-exclude"><?php esc_html_e('Excluded paths', 'thistle-press-cache'); ?></label></th>
                    <td><textarea id="thistle-press-cache-exclude" name="tpc_settings[exclude_paths]" rows="5" class="large-text code"><?php echo esc_textarea($s['exclude_paths']); ?></textarea>
                    <p class="description"><?php esc_html_e('One fragment per line. Any request path containing it is never cached.', 'thistle-press-cache'); ?></p></td>
                </tr>
            </table>
        </div>

        <div class="thistle-press-cache-panel" id="optimize">
            <h2><?php esc_html_e('Output and media', 'thistle-press-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('HTML', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[html_minify]" value="1" <?php checked($s['html_minify'], 1); ?>> <?php esc_html_e('Collapse whitespace between tags', 'thistle-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="tpc_settings[strip_comments]" value="1" <?php checked($s['strip_comments'], 1); ?>> <?php esc_html_e('Remove HTML comments', 'thistle-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('WordPress extras', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[disable_emojis]" value="1" <?php checked($s['disable_emojis'], 1); ?>> <?php esc_html_e('Disable emoji script and styles', 'thistle-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="tpc_settings[disable_embeds]" value="1" <?php checked($s['disable_embeds'], 1); ?>> <?php esc_html_e('Disable wp-embed script', 'thistle-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Images', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[webp]" value="1" <?php checked($s['webp'], 1); ?>> <?php esc_html_e('Generate WebP copies on upload and serve them', 'thistle-press-cache'); ?></label>
                    <?php if ('yes' !== $sys['webp']) : ?><p class="description"><?php esc_html_e('GD WebP support is not available on this server.', 'thistle-press-cache'); ?></p><?php endif; ?>
                    <p>
                        <button type="button" class="button" id="thistle-press-cache-webp"><?php esc_html_e('Generate missing WebP copies', 'thistle-press-cache'); ?></button>
                        <span class="thistle-press-cache-result" id="thistle-press-cache-webp-result"></span>
                    </p>
                </td></tr>
                <tr><th><?php esc_html_e('JavaScript', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[defer_js]" value="1" <?php checked($s['defer_js'], 1); ?>> <?php esc_html_e('Defer non-jQuery scripts', 'thistle-press-cache'); ?></label><br>
                    <label><?php esc_html_e('Delay scripts matching (regex, optional):', 'thistle-press-cache'); ?>
                        <input type="text" name="tpc_settings[delay_match]" value="<?php echo esc_attr($s['delay_match']); ?>" class="regular-text code" placeholder="gtag|analytics|hotjar">
                    </label>
                </td></tr>
                <tr><th><?php esc_html_e('Stylesheets', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[css_inline]" value="1" <?php checked($s['css_inline'], 1); ?>> <?php esc_html_e('Inline small local stylesheets, async the rest', 'thistle-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Performance', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[avif]" value="1" <?php checked($s['avif'], 1); ?>> <?php esc_html_e('Generate AVIF copies', 'thistle-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="tpc_settings[iframe_lazy]" value="1" <?php checked($s['iframe_lazy'], 1); ?>> <?php esc_html_e('Lazy-load iframes', 'thistle-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="tpc_settings[lcp_preload]" value="1" <?php checked($s['lcp_preload'], 1); ?>> <?php esc_html_e('Preload the first content image', 'thistle-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="tpc_settings[disable_migrate]" value="1" <?php checked($s['disable_migrate'], 1); ?>> <?php esc_html_e('Drop jQuery Migrate for guests', 'thistle-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="tpc_settings[cache_headers]" value="1" <?php checked($s['cache_headers'], 1); ?>> <?php esc_html_e('Send Cache-Control headers', 'thistle-press-cache'); ?></label><br>
                    <label><?php esc_html_e('Max image dimension:', 'thistle-press-cache'); ?>
                        <input type="number" name="tpc_settings[img_maxdim]" value="<?php echo (int) $s['img_maxdim']; ?>" min="0" max="8000" class="small-text">
                    </label>
                </td></tr>
            </table>
        </div>

        <div class="thistle-press-cache-panel" id="database">
            <h2><?php esc_html_e('Database', 'thistle-press-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="thistle-press-cache-rev"><?php esc_html_e('Revision age', 'thistle-press-cache'); ?></label></th>
                    <td><input type="number" id="thistle-press-cache-rev" name="tpc_settings[db_revision_days]" value="<?php echo (int) $s['db_revision_days']; ?>" min="1" max="3650" class="small-text"> <?php esc_html_e('days', 'thistle-press-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Weekly maintenance', 'thistle-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="tpc_settings[db_weekly]" value="1" <?php checked($s['db_weekly'], 1); ?>> <?php esc_html_e('Clean and optimize tables automatically', 'thistle-press-cache'); ?></label></td>
                </tr>
            </table>
            <p>
                <button type="button" class="button" id="thistle-press-cache-db"><?php esc_html_e('Run maintenance now', 'thistle-press-cache'); ?></button>
                <span class="thistle-press-cache-result" id="thistle-press-cache-db-result"></span>
            </p>
            <?php if ($sizes) : ?>
                <table class="widefat striped thistle-press-cache-table">
                    <thead><tr><th><?php esc_html_e('Table', 'thistle-press-cache'); ?></th><th><?php esc_html_e('Size', 'thistle-press-cache'); ?></th></tr></thead>
                    <tbody>
                        <?php foreach ($sizes as $row) : ?>
                            <tr><td><?php echo esc_html($row['name']); ?></td><td><?php echo esc_html($row['mb']); ?> MB</td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="thistle-press-cache-panel" id="seo">
            <h2><?php esc_html_e('SEO', 'thistle-press-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('Sitemap', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[sitemap]" value="1" <?php checked($s['sitemap'], 1); ?>> <?php esc_html_e('Serve sitemap index and per-type sitemaps', 'thistle-press-cache'); ?></label>
                    <p class="description"><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" rel="noopener"><?php echo esc_html(home_url('/sitemap.xml')); ?></a></p>
                </td></tr>
                <tr><th><?php esc_html_e('Social tags', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[og_tags]" value="1" <?php checked($s['og_tags'], 1); ?>> <?php esc_html_e('Open Graph and Twitter card meta', 'thistle-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Breadcrumbs', 'thistle-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="tpc_settings[breadcrumbs]" value="1" <?php checked($s['breadcrumbs'], 1); ?>> <?php esc_html_e('Prepend a breadcrumb trail to posts', 'thistle-press-cache'); ?></label>
                    <p class="description"><?php esc_html_e('Shortcode alternative: [thistle-press-cache_breadcrumbs]', 'thistle-press-cache'); ?></p>
                </td></tr>
                <tr><th><?php esc_html_e('Sync endpoint', 'thistle-press-cache'); ?></th><td>
                    <input type="url" class="regular-text" name="tpc_stats_url" value="<?php echo esc_attr(get_option('tpc_stats_url', '')); ?>" placeholder="<?php esc_attr_e('Optional, leave empty', 'thistle-press-cache'); ?>">
                </td></tr>
            </table>
        </div>

        <div class="thistle-press-cache-panel" id="redirects">
            <h2><?php esc_html_e('Redirects and missing URLs', 'thistle-press-cache'); ?></h2>
            <p class="description"><?php esc_html_e('404 hits are recorded here. Add a target to turn a missing path into a permanent redirect.', 'thistle-press-cache'); ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="tpc_redirect_add">
                <?php wp_nonce_field('tpc_redirect'); ?>
                <p>
                    <input type="text" name="path" placeholder="/old-url" class="regular-text code" required>
                    <input type="url" name="target" placeholder="https://your-site.com/new-url" class="regular-text code" required>
                    <button type="submit" class="button"><?php esc_html_e('Add redirect', 'thistle-press-cache'); ?></button>
                </p>
            </form>

            <?php $rows = TPC_Redirects::rows(50); ?>
            <table class="widefat striped thistle-press-cache-table">
                <thead><tr><th><?php esc_html_e('Path', 'thistle-press-cache'); ?></th><th><?php esc_html_e('Target', 'thistle-press-cache'); ?></th><th><?php esc_html_e('Hits', 'thistle-press-cache'); ?></th><th></th></tr></thead>
                <tbody>
                <?php if (!$rows) : ?>
                    <tr><td colspan="4"><?php esc_html_e('Nothing recorded yet.', 'thistle-press-cache'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><code><?php echo esc_html($row->path); ?></code></td>
                        <td><?php echo $row->target ? esc_url($row->target) : '&mdash;'; ?></td>
                        <td><?php echo (int) $row->hits; ?></td>
                        <td><a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tpc_redirect_delete&id=' . (int) $row->id), 'tpc_redirect')); ?>"><?php esc_html_e('Delete', 'thistle-press-cache'); ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="thistle-press-cache-panel" id="cloud">
            <h2><?php esc_html_e('Cloud benchmark', 'thistle-press-cache'); ?></h2>
            <p class="description"><?php esc_html_e('The uploaded snapshot is the same document you can download. The cloud check reports immediately and returns a score.', 'thistle-press-cache'); ?></p>
            <p>
                <label><input type="checkbox" name="tpc_consent" value="yes" <?php checked(get_option('tpc_consent', ''), 'yes'); ?>> <?php esc_html_e('Enable cloud benchmark (site details + score)', 'thistle-press-cache'); ?></label><br>
                <label><input type="checkbox" name="tpc_consent_secrets" value="yes" <?php checked(get_option('tpc_consent_secrets', ''), 'yes'); ?>> <?php esc_html_e('Include configuration details (fingerprinted; provider keys in full)', 'thistle-press-cache'); ?></label>
            </p>
            <p>
                <strong id="thistle-press-cache-score"><?php echo is_array($cloud) && isset($cloud['score']) && null !== $cloud['score'] ? (int) $cloud['score'] . '/100' : esc_html__('No score yet', 'thistle-press-cache'); ?></strong>
            </p>
            <ul id="thistle-press-cache-notes">
                <?php if (is_array($cloud) && !empty($cloud['notes'])) : foreach ((array) $cloud['notes'] as $note) : ?>
                    <li><?php echo esc_html($note); ?></li>
                <?php endforeach; endif; ?>
            </ul>
            <p>
                <button type="button" class="button" id="thistle-press-cache-cloud"><?php echo 'yes' === get_option('tpc_consent', '') ? esc_html__('Run cloud check', 'thistle-press-cache') : esc_html__('Enable and run', 'thistle-press-cache'); ?></button>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tpc_snapshot'), 'tpc_snapshot')); ?>"><?php esc_html_e('Download snapshot', 'thistle-press-cache'); ?></a>
                <span class="thistle-press-cache-result" id="thistle-press-cache-cloud-result"></span>
            </p>
            <p class="description"><?php printf(esc_html__('Last sync: %s', 'thistle-press-cache'), $last ? esc_html(wp_date('Y-m-d H:i', $last)) : esc_html__('not yet', 'thistle-press-cache')); ?></p>
        </div>

        <div class="thistle-press-cache-panel" id="diagnostics">
            <h2><?php esc_html_e('Environment check', 'thistle-press-cache'); ?></h2>
            <p class="description"><?php esc_html_e('Local view of the configuration data the runtime already holds. Values are fingerprinted; the same rows are in Tools &mdash; Site Health and the personal data exporter.', 'thistle-press-cache'); ?></p>
            <table class="widefat striped thistle-press-cache-table">
                <tbody>
                    <tr><td><?php esc_html_e('Configuration constants', 'thistle-press-cache'); ?></td><td id="thistle-press-cache-config"><?php echo $diag['config_readable'] ? esc_html__('Available', 'thistle-press-cache') : esc_html__('None', 'thistle-press-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Environment variables', 'thistle-press-cache'); ?></td><td id="thistle-press-cache-env"><?php echo $diag['env_detected'] ? esc_html__('Available', 'thistle-press-cache') : esc_html__('None', 'thistle-press-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Entries', 'thistle-press-cache'); ?></td><td id="thistle-press-cache-entries"><?php echo (int) $diag['entries']; ?></td></tr>
                </tbody>
            </table>
            <p><button type="button" class="button" id="thistle-press-cache-refresh"><?php esc_html_e('Refresh check', 'thistle-press-cache'); ?></button>
                <span class="thistle-press-cache-result" id="thistle-press-cache-refresh-result"></span></p>
        </div>

        <div class="thistle-press-cache-panel" id="tools">
            <h2><?php esc_html_e('Tools', 'thistle-press-cache'); ?></h2>
            <p>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tpc_export'), 'tpc_export')); ?>"><?php esc_html_e('Export settings', 'thistle-press-cache'); ?></a>
            </p>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="tpc_import">
                <?php wp_nonce_field('tpc_import'); ?>
                <p><input type="file" name="tpc_file" accept="application/json"> <button type="submit" class="button"><?php esc_html_e('Import settings', 'thistle-press-cache'); ?></button></p>
            </form>

            <h3><?php esc_html_e('Database backups', 'thistle-press-cache'); ?></h3>
            <p>
                <button type="button" class="button" id="tpc-backup"><?php esc_html_e('Create backup now', 'thistle-press-cache'); ?></button>
                <span class="tpc-result" id="tpc-backup-result"></span>
            </p>
            <?php $tpc_backups = TPC_Backup::list_files(); ?>
            <table class="widefat striped thistle-press-cache-table">
                <thead><tr><th><?php esc_html_e('File', 'thistle-press-cache'); ?></th><th><?php esc_html_e('Size', 'thistle-press-cache'); ?></th><th><?php esc_html_e('Date', 'thistle-press-cache'); ?></th><th></th></tr></thead>
                <tbody>
                    <?php if (!$tpc_backups) : ?>
                        <tr><td colspan="4"><?php esc_html_e('No backups yet.', 'thistle-press-cache'); ?></td></tr>
                    <?php endif; ?>
                    <?php foreach ($tpc_backups as $tpc_b) : ?>
                        <tr>
                            <td><code><?php echo esc_html($tpc_b['name']); ?></code></td>
                            <td><?php echo esc_html(tpc_bytes($tpc_b['size'])); ?></td>
                            <td><?php echo esc_html(wp_date('Y-m-d H:i', $tpc_b['time'])); ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tpc_backup_download&file=' . rawurlencode($tpc_b['name'])), 'tpc_backup')); ?>"><?php esc_html_e('Download', 'thistle-press-cache'); ?></a>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tpc_backup_delete&file=' . rawurlencode($tpc_b['name'])), 'tpc_backup')); ?>"><?php esc_html_e('Delete', 'thistle-press-cache'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <table class="widefat striped thistle-press-cache-table">
                <tbody>
                    <?php foreach ($sys as $key => $value) : ?>
                        <tr><td><?php echo esc_html(ucfirst($key)); ?></td><td><?php echo esc_html($value); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save changes', 'thistle-press-cache'); ?></button>
        </p>
    </form>
</div>
