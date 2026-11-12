<?php
/**
 * Chi nhánh trang chủ: danh sách chọn riêng, không tự thêm và không dùng danh sách footer.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_home_branches_default_title(): string {
	return __( 'Hệ thống chi nhánh', 'themeranghammatsg' );
}

/** Dòng nhỏ phía trên tiêu đề (như section Đội ngũ bác sĩ); để trống trong Tùy biến → không in. */
function rhm_home_branches_default_eyebrow(): string {
	return __( 'Phòng khám gần bạn', 'themeranghammatsg' );
}

/** Chi nhánh đủ tên, địa chỉ và ảnh, theo thứ tự đã chọn; bỏ ID trùng. */
function rhm_get_home_branches(): array {
	$list = rhm_get_list( 'rhm_home_branch_ids' );
	if ( ! $list ) {
		return array();
	}
	$branches = array_column( rhm_get_branches(), null, 'id' );
	_prime_post_caches( array_filter( array_column( $branches, 'image' ) ) );
	$items = array();
	foreach ( $list as $item ) {
		$id     = absint( $item['branch'] ?? 0 );
		$branch = $branches[ $id ] ?? null;
		if ( ! $branch || isset( $items[ $id ] ) || '' === trim( $branch['name'] ) || '' === trim( $branch['address'] ) || ! wp_attachment_is_image( $branch['image'] ) || ! wp_get_attachment_image_src( $branch['image'], 'full' ) ) {
			continue;
		}
		$items[ $id ] = $branch;
	}
	return array_values( $items );
}

function rhm_customize_home_branches( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_home_branches',
		array(
			'title'       => __( 'Chi nhánh (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Chọn chi nhánh đã xuất bản, có tên, địa chỉ và ảnh đại diện. Danh sách độc lập với chân trang; để trống sẽ ẩn section.', 'themeranghammatsg' ),
			'priority'    => 34,
		)
	);
	rhm_customize_fields(
		$wp_customize,
		'rhm_home_branches',
		array(
			'rhm_home_branches_eyebrow' => array( __( 'Dòng nhỏ trên tiêu đề (để trống = ẩn)', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_branches_default_eyebrow() ) ),
			'rhm_home_branches_title'   => array( __( 'Tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_branches_default_title() ) ),
		)
	);
	rhm_customize_list(
		$wp_customize,
		'rhm_home_branches',
		'rhm_home_branch_ids',
		__( 'Chi nhánh hiển thị', 'themeranghammatsg' ),
		__( 'Chi nhánh', 'themeranghammatsg' ),
		rhm_footer_branch_fields(),
		__( 'Thêm mới để chọn chi nhánh, dùng ↑ ↓ để sắp xếp hoặc xóa mục để bỏ chọn. Chi nhánh mới không tự xuất hiện trên trang chủ.', 'themeranghammatsg' )
	);
}
add_action( 'customize_register', 'rhm_customize_home_branches' );
