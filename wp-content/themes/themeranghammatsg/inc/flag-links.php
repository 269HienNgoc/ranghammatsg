<?php
/**
 * 2 lá cờ VN | Anh ở header: chỉ là link thường (KHÔNG phải chuyển ngôn ngữ).
 * URL nhập ở Giao diện → Tùy biến → Thông tin phòng khám. Để trống thì cờ hiển thị nhưng không bấm được.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Danh sách cờ: [ label, url, ảnh ].
 */
function rhm_get_flag_links(): array {
	return array(
		array(
			'label' => __( 'Việt Nam', 'themeranghammatsg' ),
			'url'   => rhm_get_option( 'rhm_flag_vn_url' ),
			'image' => RHM_THEME_URI . '/assets/images/flags/vn.svg',
		),
		array(
			'label' => __( 'English', 'themeranghammatsg' ),
			'url'   => rhm_get_option( 'rhm_flag_en_url' ),
			'image' => RHM_THEME_URI . '/assets/images/flags/gb.svg',
		),
	);
}

/**
 * In 2 cờ (style: utility trong header.css).
 */
function rhm_flag_links(): void {
	echo '<ul class="flex items-center gap-1.5">';
	foreach ( rhm_get_flag_links() as $flag ) {
		$img = sprintf(
			'<img class="h-5 w-[30px] object-cover xs:h-[22px] xs:w-[34px]" src="%1$s" alt="%2$s" width="34" height="22">',
			esc_url( $flag['image'] ),
			esc_attr( $flag['label'] )
		);

		echo '<li class="leading-none">';
		if ( $flag['url'] ) {
			printf(
				'<a class="block transition hover:-translate-y-px hover:opacity-85" href="%1$s"%3$s>%2$s</a>',
				esc_url( $flag['url'] ),
				$img, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
				rhm_external_link_attrs( $flag['url'] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes.
			);
		} else {
			echo '<span class="block">' . $img . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		}
		echo '</li>';
	}
	echo '</ul>';
}
