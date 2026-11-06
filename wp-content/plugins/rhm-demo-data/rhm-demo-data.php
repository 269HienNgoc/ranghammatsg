<?php
/**
 * Plugin Name:       RHM Demo Data
 * Description:       Tạo dữ liệu mẫu cho theme Răng Hàm Mặt SG: 1 bài viết + 1 hồ sơ bác sĩ đầy đủ thông tin (ảnh tự sinh bằng GD, field ACF), rồi nhân bản thành N bản. Công cụ → Dữ liệu mẫu RHM, hoặc WP-CLI: wp rhm-demo.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Aimedis
 * License:           GPL-2.0-or-later
 * Text Domain:       rhm-demo-data
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

define( 'RHM_DEMO_DIR', __DIR__ );

/** Meta đánh dấu mọi thứ plugin tạo ra (bài viết, bác sĩ, ảnh) → xóa sạch được, không đụng dữ liệu thật. */
const RHM_DEMO_META = '_rhm_demo';

require_once RHM_DEMO_DIR . '/includes/class-rhm-demo-images.php';
require_once RHM_DEMO_DIR . '/includes/class-rhm-demo-content.php';
require_once RHM_DEMO_DIR . '/includes/class-rhm-demo-generator.php';

if ( is_admin() ) {
	require_once RHM_DEMO_DIR . '/includes/admin.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once RHM_DEMO_DIR . '/includes/class-rhm-demo-cli.php';
	WP_CLI::add_command( 'rhm-demo', 'RHM_Demo_CLI' );
}
