<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_setup(): void {
	load_theme_textdomain( 'themeranghammatsg', RHM_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Menu chính', 'themeranghammatsg' ),
			'footer'  => __( 'Menu chân trang (Điều khoản chính sách)', 'themeranghammatsg' ),
		)
	);

	// Kích thước ảnh: chỉnh lại khi có ảnh thiết kế.
	add_image_size( 'rhm-card', 600, 400, true );
	// Tạo thêm bức ảnh mới từ bức ảnh mà mình upload với kích thước tùy chỉnh
	add_image_size( 'rhm-2048', 2048, 2048, false);
	add_image_size( 'rhm-1920', 1920, 1920, false);
	add_image_size( 'rhm-1600', 1600, 1600, false);
	add_image_size( 'rhm-1536', 1536, 1536, false);
	add_image_size( 'rhm-1440', 1440, 1440, false);
	add_image_size( 'rhm-1280', 1280, 1280, false);
	add_image_size( 'rhm-960', 960, 960, false);
	add_image_size( 'rhm-828', 828, 828, false);
	add_image_size( 'rhm-768', 768, 768, false);
	add_image_size( 'rhm-672', 672, 672, false);
	add_image_size( 'rhm-640', 640, 640, false);
	add_image_size( 'rhm-412', 412, 412, false);
	add_image_size( 'rhm-320', 330, 330, false);
	add_image_size( 'rhm-128', 128, 128, false);
	add_image_size( 'rhm-90', 90, 90, false);
}
add_action( 'after_setup_theme', 'rhm_setup' );

/**
 * Content width cho embeds.
 */
function rhm_content_width(): void {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'rhm_content_width', 0 );

/**
 * Chiều cao CSS của logo header theo breakpoint (media lớn trước, '' = mặc định).
 * Phải khớp class h-* của `.site-branding .custom-logo` trong src/css/pages/header.css.
 */
const RHM_LOGO_HEIGHTS = array(
	'(min-width: 1280px)' => 84,
	'(min-width: 1024px)' => 64,
	'(min-width: 360px)'  => 60,
	''                    => 50, // < 360px (máy rất hẹp): 60px làm logo đè lên nút Đặt lịch hẹn.
);

/**
 * In Logo (Nhận diện website → Logo) thay the_custom_logo(): <a class="custom-logo-link"><picture> (rhm_get_picture).
 * Logo hiển thị theo CHIỀU CAO CSS cố định mỗi breakpoint → độ rộng = chiều cao × tỉ lệ ảnh → mỗi <source media> trỏ đúng file.
 * Không dùng the_custom_logo(): WordPress gắn src = file gốc (1409px) và tự thêm fetchpriority="high" cho logo
 * (tranh băng thông với ảnh LCP thật: hero / ảnh bài nổi bật).
 *
 * Logo trong ngăn kéo menu (cao 44px) gọi cùng hàm → CÙNG file với logo header ở mọi breakpoint → trình duyệt chỉ tải 1 lần
 * (ngăn kéo ẩn trên desktop vẫn bị tải ảnh nếu file khác). Kích thước hiển thị do CSS quyết định.
 */
function rhm_custom_logo(): void {
	$heights = RHM_LOGO_HEIGHTS;
	$id   = (int) get_theme_mod( 'custom_logo' );
	$meta = wp_get_attachment_metadata( $id );
	if ( empty( $meta['width'] ) || empty( $meta['height'] ) ) {
		return;
	}

	$ratio = $meta['width'] / $meta['height'];
	$alt   = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );

	printf(
		'<a href="%1$s" class="custom-logo-link" rel="home"%2$s>%3$s</a>',
		esc_url( home_url( '/' ) ),
		is_front_page() ? ' aria-current="page"' : '',
		rhm_get_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped in rhm_html_attrs().
			$id,
			array_map( static fn( int $height ): int => (int) ceil( $height * $ratio ), $heights ),
			array(
				'class'    => 'custom-logo',
				'alt'      => '' !== $alt ? $alt : get_bloginfo( 'name' ),
				'decoding' => 'async',
			)
		)
	);
}
