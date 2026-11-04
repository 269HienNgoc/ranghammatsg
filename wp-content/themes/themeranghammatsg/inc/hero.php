<?php
/**
 * Hero banner trang chủ: dữ liệu slide (Customizer) + ảnh responsive <picture>.
 *
 * Ảnh: mỗi slide có ảnh desktop (rộng 2048px) và ảnh mobile dọc (tùy chọn).
 * Trình duyệt tự chọn file vừa màn hình từ các size rhm-* (inc/setup.php) — WordPress tự gom mọi size
 * cùng tỉ lệ vào srcset, tối đa 2048px:
 * - < 768px:  <source media> → ảnh mobile (srcset rhm-412 … rhm-828 …)
 * - ≥ 768px:  <img> → ảnh desktop (srcset rhm-768 … rhm-2048)
 * Chỉ ảnh slide đầu tải cùng trang; slide sau tải khi sắp hiện (src/js/home.js).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_HERO_MAX_SLIDES = 5;

/**
 * Slide hợp lệ (có ảnh desktop) theo thứ tự trong Customizer.
 *
 * @return array<int, array{desktop:int, mobile:int, link:string}>
 */
function rhm_get_hero_slides(): array {
	$slides = array();

	for ( $i = 1; $i <= RHM_HERO_MAX_SLIDES; $i++ ) {
		$desktop = absint( get_theme_mod( "rhm_hero_{$i}_desktop" ) );
		if ( ! wp_attachment_is_image( $desktop ) ) {
			continue;
		}

		$mobile   = absint( get_theme_mod( "rhm_hero_{$i}_mobile" ) );
		$slides[] = array(
			'desktop' => $desktop,
			'mobile'  => wp_attachment_is_image( $mobile ) ? $mobile : 0,
			'link'    => (string) get_theme_mod( "rhm_hero_{$i}_link", '' ),
		);
	}

	return $slides;
}

/**
 * <picture> của 1 slide.
 * - Slide đầu (ảnh LCP): srcset thật, tải ngay với fetchpriority=high.
 * - Slide sau: chỉ có data-srcset → trình duyệt KHÔNG tải; src/js/home.js chuyển data-srcset → srcset
 *   khi slide sắp hiện. width/height vẫn giữ chỗ nên không gây CLS.
 */
function rhm_hero_picture( array $slide, bool $is_first ): string {
	$srcset_attr = $is_first ? 'srcset' : 'data-srcset';

	$source = '';
	if ( $slide['mobile'] ) {
		$mobile = wp_get_attachment_image_src( $slide['mobile'], 'full' );
		$source = '<source' . rhm_html_attrs(
			array(
				'media'      => '(max-width: 767px)',
				$srcset_attr => wp_get_attachment_image_srcset( $slide['mobile'], 'full' ) ?: $mobile[0],
				'sizes'      => '100vw',
				'width'      => $mobile[1],
				'height'     => $mobile[2],
			)
		) . '>';
	}

	$desktop = wp_get_attachment_image_src( $slide['desktop'], 'full' );
	$alt     = (string) get_post_meta( $slide['desktop'], '_wp_attachment_image_alt', true );
	$img     = array(
		'class'      => 'block h-auto w-full',
		'width'      => $desktop[1],
		'height'     => $desktop[2],
		'alt'        => '' !== $alt ? $alt : get_the_title( $slide['desktop'] ),
		$srcset_attr => wp_get_attachment_image_srcset( $slide['desktop'], 'full' ) ?: $desktop[0],
		'sizes'      => '(min-width: 2048px) 2048px, 100vw',
		'decoding'   => 'async',
	);

	if ( $is_first ) {
		$img['src']           = $desktop[0];
		$img['fetchpriority'] = 'high';
		$img['decoding']      = 'sync';
	}

	return '<picture>' . $source . '<img' . rhm_html_attrs( $img ) . '></picture>';
}

/**
 * Chuỗi thuộc tính HTML đã escape: ` name="value" …`.
 */
function rhm_html_attrs( array $attrs ): string {
	$html = '';
	foreach ( $attrs as $name => $value ) {
		$html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}
	return $html;
}
