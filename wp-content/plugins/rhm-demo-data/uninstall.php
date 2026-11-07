<?php
/**
 * Xóa plugin (Plugin → Xóa) = dọn sạch dữ liệu mẫu: bài viết, hồ sơ bác sĩ, ảnh (cả file), danh mục/thẻ mẫu,
 * banner mẫu (banner thật bị ghi đè được trả lại) và option/transient của plugin. Dữ liệu thật không bị đụng.
 *
 * @package rhm-demo-data
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

defined( 'RHM_DEMO_DIR' ) || define( 'RHM_DEMO_DIR', __DIR__ );
defined( 'RHM_DEMO_META' ) || define( 'RHM_DEMO_META', '_rhm_demo' );

require_once __DIR__ . '/includes/class-rhm-demo-images.php';
require_once __DIR__ . '/includes/class-rhm-demo-content.php';
require_once __DIR__ . '/includes/class-rhm-demo-generator.php';

( new RHM_Demo_Generator() )->delete();

// Option còn sót (term mẫu được giữ vì đang nằm trong menu) + thông báo admin.
delete_option( 'rhm_demo_terms' );
delete_option( 'rhm_demo_hero_backup' );

global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_rhm\\_demo\\_notice\\_%' OR option_name LIKE '\\_transient\\_timeout\\_rhm\\_demo\\_notice\\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
