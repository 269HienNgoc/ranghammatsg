<?php
/**
 * Giới thiệu trang chủ (2 bản: ảnh trái / ảnh phải): dữ liệu từ Customizer → "Giới thiệu (Trang chủ)", "Giới thiệu 2 (Trang chủ)".
 * Hiển thị: template-parts/sections/about.php (bản 2 gọi qua about-2.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_ABOUT_STATS_MAX = 4;

/**
 * Các bản section Giới thiệu trên trang chủ: tiền tố theme mod (= id mục Customizer) => cấu hình.
 * - id:      id section HTML (tiêu đề H2 = "<id>-title").
 * - reverse: true = desktop ảnh bên phải, chữ bên trái (mobile luôn ảnh trên, chữ dưới).
 * Hiển thị: template-parts/sections/about.php (bản 1), about-2.php (bản 2). Thêm bản: thêm phần tử + file section + front-page.php.
 *
 * @return array<string, array{id:string, reverse:bool, label:string, description:string}>
 */
function rhm_about_sections(): array {
	return array(
		'rhm_about'   => array(
			'id'          => 'about',
			'reverse'     => false,
			'label'       => __( 'Giới thiệu (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Ảnh bên trái, nội dung bên phải.', 'themeranghammatsg' ),
		),
		'rhm_about_2' => array(
			'id'          => 'about-2',
			'reverse'     => true,
			'label'       => __( 'Giới thiệu 2 (Trang chủ) – ảnh bên phải', 'themeranghammatsg' ),
			'description' => __( 'Hiện ngay sau "Giới thiệu": ảnh bên phải, nội dung bên trái. Để trống Tiêu đề thì ẩn section.', 'themeranghammatsg' ),
		),
	);
}

/**
 * Toàn bộ nội dung 1 bản section (chuỗi rỗng = chưa nhập). $prefix: khóa trong rhm_about_sections().
 */
function rhm_get_about( string $prefix = 'rhm_about' ): array {
	$mod = static fn( string $key ): string => trim( (string) get_theme_mod( "{$prefix}_{$key}", '' ) );

	$stats = array();
	for ( $i = 1; $i <= RHM_ABOUT_STATS_MAX; $i++ ) {
		$number = $mod( "stat_{$i}_number" );
		if ( '' !== $number ) {
			$stats[] = array(
				'number' => $number,
				'label'  => $mod( "stat_{$i}_label" ),
			);
		}
	}

	$image = absint( get_theme_mod( "{$prefix}_image" ) );

	return array(
		'image'           => wp_attachment_is_image( $image ) ? $image : 0,
		'video'           => rhm_parse_video_url( $mod( 'video' ) ),
		'badge_title'     => $mod( 'badge_title' ),
		'badge_text'      => $mod( 'badge_text' ),
		'eyebrow'         => $mod( 'eyebrow' ),
		'title'           => $mod( 'title' ),
		'title_highlight' => $mod( 'title_highlight' ),
		'content'         => $mod( 'content' ),
		'button_text'     => $mod( 'button_text' ),
		'button_link'     => $mod( 'button_link' ),
		'stats'           => $stats,
	);
}

/**
 * Link video → nguồn phát. YouTube (watch / youtu.be / embed / shorts) phát qua youtube-nocookie;
 * file .mp4/.webm phát bằng <video>. Link khác → mảng rỗng (bỏ qua video).
 *
 * @return array{type?:string, src?:string}
 */
function rhm_parse_video_url( string $url ): array {
	if ( preg_match( '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~', $url, $match ) ) {
		return array(
			'type' => 'youtube',
			'src'  => 'https://www.youtube-nocookie.com/embed/' . $match[1] . '?autoplay=1&rel=0',
		);
	}

	if ( preg_match( '~\.(?:mp4|webm)(?:\?.*)?$~i', $url ) ) {
		return array(
			'type' => 'file',
			'src'  => $url,
		);
	}

	return array();
}
