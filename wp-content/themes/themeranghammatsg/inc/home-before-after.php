<?php
/**
 * Section "Ảnh trước – sau" trang chủ: danh sách khách hàng nhập ở Tùy biến → "Trước – sau (Trang chủ)".
 * Hiển thị: template-parts/sections/before-after.php. Lưu: theme mod rhm_home_ba_items (JSON, inc/customize-list.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Tối đa số khách hiển thị (4 khách / trang): giữ DOM trang chủ nhỏ (~25 node / thẻ). */
const RHM_HOME_BA_MAX = 12;

function rhm_home_ba_default_title(): string {
	return __( 'Kết quả điều trị của khách hàng', 'themeranghammatsg' );
}

/** Dòng nhỏ phía trên tiêu đề; để trống trong Tùy biến → không in. */
function rhm_home_ba_default_eyebrow(): string {
	return __( 'Hình ảnh trước – sau', 'themeranghammatsg' );
}

/** Field của 1 khách hàng: field => [nhãn, kiểu] (rhm_customize_list()). */
function rhm_home_ba_fields(): array {
	return array(
		'portrait' => array( __( 'Ảnh khách hàng (chân dung)', 'themeranghammatsg' ), 'image' ),
		'before'   => array( __( 'Ảnh trước điều trị', 'themeranghammatsg' ), 'image' ),
		'after'    => array( __( 'Ảnh sau điều trị', 'themeranghammatsg' ), 'image' ),
		'name'     => array( __( 'Tên khách hàng (vd: Chú Nguyễn Văn Huy)', 'themeranghammatsg' ), 'text' ),
		'location' => array( __( 'Tỉnh / thành phố', 'themeranghammatsg' ), 'text' ),
		'service'  => array( __( 'Dịch vụ (vd: Trồng răng Implant All on 5 hàm trên)', 'themeranghammatsg' ), 'text' ),
		'product'  => array( __( 'Loại implant / vật liệu (vd: Implant Straumann Thụy Sĩ)', 'themeranghammatsg' ), 'text' ),
	);
}

/**
 * Khách hàng hợp lệ (đủ tên + 3 ảnh), đúng thứ tự trong Tùy biến, tối đa RHM_HOME_BA_MAX.
 *
 * @return array<int, array{portrait:int, before:int, after:int, name:string, location:string, service:string, product:string}>
 */
function rhm_get_home_before_after(): array {
	$list = rhm_get_list( 'rhm_home_ba_items' );
	if ( ! $list ) {
		return array();
	}

	$ids = array();
	foreach ( $list as $item ) {
		foreach ( array( 'portrait', 'before', 'after' ) as $key ) {
			$ids[] = absint( $item[ $key ] ?? 0 );
		}
	}
	_prime_post_caches( array_filter( $ids ) );

	$items = array();
	foreach ( $list as $item ) {
		$row = array(
			'portrait' => absint( $item['portrait'] ?? 0 ),
			'before'   => absint( $item['before'] ?? 0 ),
			'after'    => absint( $item['after'] ?? 0 ),
			'name'     => trim( (string) ( $item['name'] ?? '' ) ),
			'location' => trim( (string) ( $item['location'] ?? '' ) ),
			'service'  => trim( (string) ( $item['service'] ?? '' ) ),
			'product'  => trim( (string) ( $item['product'] ?? '' ) ),
		);

		if ( '' === $row['name'] || ! wp_attachment_is_image( $row['portrait'] ) || ! wp_attachment_is_image( $row['before'] ) || ! wp_attachment_is_image( $row['after'] ) ) {
			continue;
		}

		$items[] = $row;
		if ( count( $items ) >= RHM_HOME_BA_MAX ) {
			break;
		}
	}

	return $items;
}

function rhm_customize_home_before_after( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_home_before_after',
		array(
			'title'       => __( 'Trước – sau (Trang chủ)', 'themeranghammatsg' ),
			/* translators: %d: số khách hàng tối đa. */
			'description' => sprintf( __( 'Ảnh trước – sau của khách hàng. Mỗi khách cần đủ tên, ảnh chân dung, ảnh trước và ảnh sau mới hiển thị. Hiện tối đa %d khách, 4 khách / trang slider. Để trống sẽ ẩn section.', 'themeranghammatsg' ), RHM_HOME_BA_MAX ),
			'priority'    => 34,
		)
	);
	rhm_customize_fields(
		$wp_customize,
		'rhm_home_before_after',
		array(
			'rhm_home_ba_eyebrow' => array( __( 'Dòng nhỏ trên tiêu đề (để trống = ẩn)', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_ba_default_eyebrow() ) ),
			'rhm_home_ba_title'   => array( __( 'Tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_ba_default_title() ) ),
		)
	);
	rhm_customize_list(
		$wp_customize,
		'rhm_home_before_after',
		'rhm_home_ba_items',
		__( 'Khách hàng', 'themeranghammatsg' ),
		__( 'Khách hàng', 'themeranghammatsg' ),
		rhm_home_ba_fields(),
		__( 'Bấm "Thêm mới" để thêm khách hàng, dùng ↑ ↓ để sắp xếp. Ảnh trước / sau nên cùng tỉ lệ 3:2 (ngang), ảnh chân dung dọc.', 'themeranghammatsg' )
	);
}
add_action( 'customize_register', 'rhm_customize_home_before_after' );
