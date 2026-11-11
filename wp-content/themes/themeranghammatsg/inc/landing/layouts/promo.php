<?php
/**
 * Layout dạng ô "Ô banner nổi bật": ảnh (trái) + logo + tiêu đề + dòng phụ (phải), cả ô bấm được nếu có link.
 * Vd Cấy ghép toàn hàm All-on-4, Invisalign Diamond Provider. Hiển thị: template-parts/landing/promo.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_promo_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field( $prefix, 'subtitle', __( 'Dòng phụ', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '', 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_image_field( $prefix, 'image', __( 'Ảnh — khuyến nghị 800 × 500, nền trong suốt hoặc nhạt', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 50 ), 'instructions' => __( 'Chiếm ~45% bề ngang ô, không bị cắt.', 'themeranghammatsg' ) ) ),
		rhm_landing_image_field( $prefix, 'logo', __( 'Logo (tùy chọn) — cao ≥ 56px', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 50 ), 'mime_types' => 'jpg,jpeg,png,webp,avif,svg', 'instructions' => __( 'Hiện cao 28px phía trên tiêu đề, vd logo Invisalign.', 'themeranghammatsg' ) ) ),
		rhm_landing_field( $prefix, 'link', __( 'Link (tùy chọn)', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'https://…', 'instructions' => __( 'Có link: bấm vào ô sẽ mở trang này.', 'themeranghammatsg' ) ) ),
	);
}
