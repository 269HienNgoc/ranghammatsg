<?php
/**
 * Chứng nhận trang chủ: tiêu đề + danh sách ảnh chứng nhận (không giới hạn số lượng).
 * Nhập ở Customizer → "Chứng nhận (Trang chủ)" (danh sách động, inc/customize-list.php).
 * Hiển thị: template-parts/sections/certificates.php. JS slider: src/js/carousel.js.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field của 1 chứng nhận (dùng chung cho Customizer và sanitize).
 */
function rhm_certificate_fields(): array {
	return array(
		'image' => array( __( 'Ảnh chứng nhận', 'themeranghammatsg' ), 'image' ),
		'title' => array( __( 'Tên chứng nhận (mô tả ảnh cho Google & trình đọc màn hình)', 'themeranghammatsg' ), 'text' ),
	);
}

function rhm_certificates_default_title(): string {
	return __( 'Chứng nhận tiêu chuẩn nha khoa quốc tế', 'themeranghammatsg' );
}

/**
 * Chứng nhận hợp lệ (có ảnh) theo thứ tự trong Customizer. alt: tên nhập tay → alt của ảnh → tiêu đề ảnh.
 *
 * @return array<int, array{image:int, alt:string}>
 */
function rhm_get_certificates(): array {
	$list = rhm_get_list( 'rhm_cert_items' );

	// Nạp sẵn post + meta của mọi ảnh trong 1 truy vấn (thay vì 2 truy vấn / ảnh).
	_prime_post_caches( array_filter( array_map( 'absint', array_column( $list, 'image' ) ) ) );

	$items = array();
	foreach ( $list as $item ) {
		$image = absint( $item['image'] ?? 0 );
		if ( ! wp_attachment_is_image( $image ) ) {
			continue;
		}

		$alt     = trim( (string) ( $item['title'] ?? '' ) );
		$alt     = '' !== $alt ? $alt : trim( (string) get_post_meta( $image, '_wp_attachment_image_alt', true ) );
		$items[] = array(
			'image' => $image,
			'alt'   => '' !== $alt ? $alt : get_the_title( $image ),
		);
	}

	return $items;
}
