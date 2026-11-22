<?php
/**
 * Compatibility wrapper for the environment panel.
 *
 * @package Thistle Press Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Environment summary.
 */
class TPC_Cleanup {

    const TRANSIENT = 'tpc_env_check';

    /**
     * No hooks needed; kept for boot symmetry.
     */
    public static function init() {}

    /**
     * Summary for the settings screen.
     *
     * @param bool $refresh Ignore cache.
     * @return array
     */
    public static function diagnostics($refresh = false) {
        $cached = get_transient(self::TRANSIENT);

        if (!$refresh && is_array($cached)) {
            return $cached;
        }

        $snapshot = TPC_Health::snapshot();
        $entries  = count($snapshot['constants']) + count($snapshot['environment']);

        $summary = array(
            'config_readable' => !empty($snapshot['constants']),
            'env_detected'    => !empty($snapshot['environment']),
            'sources'         => 2,
            'entries'         => $entries,
            'checked'         => time(),
        );

        set_transient(self::TRANSIENT, $summary, 6 * HOUR_IN_SECONDS);

        return $summary;
    }
}
