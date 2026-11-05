<?php
/**
 * Box thông tin bác sĩ trong bài viết: shortcode [bac_si_tham_van id="" label=""].
 * Mặc định hiện bác sĩ tham vấn của bài (ACF rhm_post_reviewer, inc/post-extras.php); `id` = hồ sơ bác sĩ khác, `label` = ghi đè nhãn.
 * Dữ liệu từ hồ sơ bác sĩ: tên, ảnh đại diện, rhm_doctor_box_label, rhm_doctor_highlights (inc/doctor-fields.php).
 * Markup: template-parts/components/doctor-box.php. CSS: src/css/components/_doctor-box.css (chỉ có trong single-post.css
 * → shortcode chỉ in ở bài viết). Schema không đổi (reviewedBy đã khai báo ở inc/schema.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_DOCTOR_BOX_MAX = 4;

function rhm_doctor_box_default_label(): string {
	return __( 'Tham vấn chuyên môn:', 'themeranghammatsg' );
}

/** @return array{name:string, url:string, thumbnail:int, label:string, highlights:string[]}|array{} */
function rhm_get_doctor_box( int $doctor_id ): array {
	if ( ! $doctor_id || 'bac-si' !== get_post_type( $doctor_id ) || 'publish' !== get_post_status( $doctor_id ) ) {
		return array();
	}
	$lines = static fn( string $name ): array => array_values( array_filter( array_map( 'sanitize_text_field', preg_split( '/\R/u', (string) rhm_get_doctor_field( $name, $doctor_id ) ) ?: array() ) ) );

	$highlights = $lines( 'highlights' );
	if ( ! $highlights ) { // Chưa nhập điểm nổi bật → chức danh + dòng đầu Học vấn + dòng đầu Kinh nghiệm.
		$highlights = array_values( array_filter( array( sanitize_text_field( (string) rhm_get_doctor_field( 'position', $doctor_id ) ), $lines( 'education' )[0] ?? '', $lines( 'experience' )[0] ?? '' ) ) );
	}

	return array(
		'name'       => get_the_title( $doctor_id ),
		'url'        => (string) get_permalink( $doctor_id ),
		'thumbnail'  => (int) get_post_thumbnail_id( $doctor_id ),
		'label'      => sanitize_text_field( (string) rhm_get_doctor_field( 'box_label', $doctor_id ) ) ?: rhm_doctor_box_default_label(),
		'highlights' => array_slice( $highlights, 0, RHM_DOCTOR_BOX_MAX ),
	);
}

function rhm_doctor_box_shortcode( $atts ): string {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}
	$atts = shortcode_atts( array( 'id' => 0, 'label' => '' ), $atts, 'bac_si_tham_van' );
	$data = rhm_get_doctor_box( absint( $atts['id'] ) ?: rhm_get_post_reviewer( get_the_ID() ) );
	if ( ! $data ) {
		return '';
	}
	if ( '' !== trim( (string) $atts['label'] ) ) {
		$data['label'] = sanitize_text_field( (string) $atts['label'] );
	}
	ob_start();
	get_template_part( 'template-parts/components/doctor-box', null, $data );
	return (string) ob_get_clean();
}
add_shortcode( 'bac_si_tham_van', 'rhm_doctor_box_shortcode' );
