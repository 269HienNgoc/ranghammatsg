<?php
/**
 * Layout "Câu hỏi thường gặp" (<details>, không JS). Hiển thị: template-parts/landing/faq.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_faq_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Câu hỏi thường gặp', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Câu hỏi', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 20,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_question',
				'button_label' => __( 'Thêm câu hỏi', 'themeranghammatsg' ),
				'instructions' => __( 'Câu hỏi thiếu câu trả lời sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $item, 'question', __( 'Câu hỏi', 'themeranghammatsg' ), 'text' ),
					rhm_landing_wysiwyg_field( $item, 'answer', __( 'Trả lời', 'themeranghammatsg' ) ),
				),
			)
		),
	);
}
