<?php
/**
 * Nạp CSS/JS đã build bằng Vite (thư mục dist/).
 *
 * CSS tách theo vị trí: base + header + footer nạp mọi trang, cộng thêm ĐÚNG 1 file theo loại trang
 * (rhm_page_style_bundle). Nguồn: src/css/pages/<tên>.css → dist/css/<tên>.css.
 * Version = filemtime để tự phá cache sau mỗi lần build.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Version string from file modification time (fallback: theme version).
 */
function rhm_asset_version( string $relative_path ): string {
	$file = RHM_THEME_DIR . '/' . ltrim( $relative_path, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : (string) wp_get_theme()->get( 'Version' );
}

/**
 * File CSS riêng cho trang hiện tại (tên file trong dist/css/, không có .css), hoặc '' nếu không cần.
 * Thêm loại trang mới: tạo src/css/pages/<tên>.css rồi thêm điều kiện ở đây.
 */
function rhm_page_style_bundle(): string {
	return match ( true ) {
		is_front_page()                                         => 'page-home',
		'' !== rhm_landing_style_bundle()                       => rhm_landing_style_bundle(), // Landing page theo chủ đề (inc/landing/landing.php).
		is_page_template( 'page-templates/tpl-lien-he.php' )    => 'page-contact',
		is_singular( 'post' )                                   => 'single-post',
		is_singular( 'bac-si' )                                 => 'single-doctor',
		is_page()                                               => 'page',
		is_404()                                                => '404',
		is_home(), is_archive(), is_search()                    => 'archive',
		default                                                 => '',
	};
}

/**
 * Danh sách bundle CSS của trang hiện tại, theo thứ tự nạp.
 */
function rhm_style_bundles(): array {
	$bundles = array( 'base', 'header', 'footer', rhm_page_style_bundle() );
	return array_values( array_filter( (array) apply_filters( 'rhm_style_bundles', $bundles ) ) );
}

function rhm_enqueue_assets(): void {
	$has_base = false;

	foreach ( rhm_style_bundles() as $bundle ) {
		$relative = 'dist/css/' . $bundle . '.css';
		if ( ! file_exists( RHM_THEME_DIR . '/' . $relative ) ) {
			continue;
		}

		$handle = 'rhm-' . $bundle;
		wp_enqueue_style(
			$handle,
			RHM_THEME_URI . '/' . $relative,
			( 'base' !== $bundle && $has_base ) ? array( 'rhm-base' ) : array(),
			rhm_asset_version( $relative )
		);
		// Có 'path' → WP core in nội dung file thành <style> inline (xem rhm_styles_inline_size_limit()).
		wp_style_add_data( $handle, 'path', RHM_THEME_DIR . '/' . $relative );

		if ( 'base' === $bundle ) {
			$has_base = true;
		}
	}

	if ( file_exists( RHM_THEME_DIR . '/dist/js/main.js' ) ) {
		wp_enqueue_script(
			'rhm-main',
			RHM_THEME_URI . '/dist/js/main.js',
			array(),
			rhm_asset_version( 'dist/js/main.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'rhm_enqueue_assets' );

/**
 * Inline toàn bộ CSS của theme vào <head> → không còn request CSS chặn render (PSI "Render-blocking requests").
 *
 * WP core (wp_maybe_inline_styles) inline các style có 'path', file nhỏ trước, đến khi tổng kích thước (raw) chạm giới hạn;
 * file vượt giới hạn tự quay về <link>. url(../fonts/…) được core đổi sang đường dẫn tuyệt đối.
 * 120 kB raw ≈ 24 kB gzip: đủ cho trang nặng nhất (trang chủ ≈ 85 kB raw, ≈ 17 kB gzip) và vẫn giữ ngân sách CSS ≤ 20 kB gzip.
 * Đổi lại: CSS nằm trong HTML (không cache riêng) → sau mỗi lần build phải xóa page cache.
 */
function rhm_styles_inline_size_limit(): int {
	return 120000;
}
add_filter( 'styles_inline_size_limit', 'rhm_styles_inline_size_limit' );

/**
 * Preload font Roboto (2 subset dùng cho tiếng Việt) để chữ hiển thị sớm.
 */
function rhm_preload_fonts(): void {
	foreach ( array( 'roboto-latin', 'roboto-vietnamese' ) as $font ) {
		$relative = 'dist/fonts/' . $font . '.woff2';
		if ( file_exists( RHM_THEME_DIR . '/' . $relative ) ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( RHM_THEME_URI . '/' . $relative )
			);
		}
	}
}
add_action( 'wp_head', 'rhm_preload_fonts', 1 );

/**
 * Nhắc admin build asset nếu chưa có dist/.
 */
function rhm_missing_build_notice(): void {
	if ( ! current_user_can( 'edit_theme_options' ) || file_exists( RHM_THEME_DIR . '/dist/css/base.css' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Theme Răng Hàm Mặt SG: chưa build CSS/JS. Chạy "npm install && npm run build" trong thư mục theme.', 'themeranghammatsg' ) . '</p></div>';
}
add_action( 'admin_notices', 'rhm_missing_build_notice' );
