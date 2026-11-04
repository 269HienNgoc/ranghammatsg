<?php
/**
 * Giới thiệu trang chủ: dữ liệu từ Customizer → "Giới thiệu (Trang chủ)".
 * Hiển thị: template-parts/sections/about.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_ABOUT_STATS_MAX = 4;

/**
 * Toàn bộ nội dung section (chuỗi rỗng = chưa nhập).
 */
function rhm_get_about(): array {
	$mod = static fn( string $key ): string => trim( (string) get_theme_mod( "rhm_about_{$key}", '' ) );

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

	$image = absint( get_theme_mod( 'rhm_about_image' ) );

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
