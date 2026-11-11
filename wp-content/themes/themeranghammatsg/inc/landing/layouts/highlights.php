<?php
/**
 * Layout "Dải điểm nổi bật": các ô icon nét + nhãn ngắn (vd ngay dưới banner). Hiển thị: template-parts/landing/highlights.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_highlights_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Điểm nổi bật', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 6,
				'layout'       => 'table',
				'button_label' => __( 'Thêm ô', 'themeranghammatsg' ),
				'instructions' => __( 'Nên 4 ô, nhãn ngắn (≤ 25 ký tự). Ô thiếu nhãn sẽ bị bỏ qua. Đặt ngay sau Banner: dải này nổi lên đè mép dưới banner.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_icon_field( $item, 'icon', array( 'wrapper' => array( 'width' => 35 ), 'instructions' => '' ) ),
					rhm_landing_field( $item, 'label', __( 'Nhãn', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Đội ngũ bác sĩ giỏi', 'themeranghammatsg' ) ) ),
				),
			)
		),
	);
}
