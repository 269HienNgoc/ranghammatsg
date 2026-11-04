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
			'footer'  => __( 'Menu chân trang', 'themeranghammatsg' ),
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
 * Logo: thay sizes mặc định của WordPress ("100vw" → tải file 1000px+ cho logo ~110px)
 * bằng chiều rộng hiển thị thật, tính từ chiều cao CSS × tỉ lệ ảnh.
 * Chiều cao phải khớp `.site-branding .custom-logo` trong src/css/pages/header.css.
 */
function rhm_custom_logo_sizes( array $attr, int $logo_id ): array {
	$logo = wp_get_attachment_image_src( $logo_id, 'full' );
	if ( ! $logo || ! $logo[2] ) {
		return $attr;
	}

	$ratio = $logo[1] / $logo[2];
	$width = static fn( int $height ): int => (int) ceil( $height * $ratio );

	$attr['sizes'] = sprintf(
		'(min-width: 1280px) %1$dpx, (min-width: 1024px) %2$dpx, (min-width: 560px) %3$dpx, %4$dpx',
		$width( 84 ),
		$width( 64 ),
		$width( 56 ),
		$width( 46 )
	);

	return $attr;
}
add_filter( 'get_custom_logo_image_attributes', 'rhm_custom_logo_sizes', 10, 2 );
