<?php
/**
 * Section "Đội ngũ bác sĩ" trang chủ (template-parts/sections/doctors.php): slider, mỗi slide 1 bác sĩ.
 *
 * Bác sĩ hiển thị: hồ sơ bật "Hiện ở section Đội ngũ bác sĩ" (tab Trang chủ, field rhm_doctor_home), sắp theo ô "Thứ tự"
 * (menu_order) rồi ngày đăng. Chưa bật hồ sơ nào → lấy các hồ sơ mới nhất. Hồ sơ không có ảnh đại diện bị bỏ qua.
 * Chữ của section: Customizer → "Đội ngũ bác sĩ (Trang chủ)".
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Số bác sĩ tối đa trong slider (giới hạn DOM: ~35 node mỗi bác sĩ). */
const RHM_HOME_DOCTORS_MAX = 8;

/** Số bác sĩ lấy khi chưa hồ sơ nào được chọn. */
const RHM_HOME_DOCTORS_FALLBACK = 6;

/** Số dòng "Chứng chỉ chuyên môn" tối đa mỗi bác sĩ. */
const RHM_HOME_DOCTORS_CREDENTIALS = 5;

/**
 * Chữ của section (Customizer, có mặc định).
 */
function rhm_home_doctors_text( string $key ): string {
	$defaults = array(
		'eyebrow'  => __( 'Đội ngũ chuyên môn', 'themeranghammatsg' ),
		'title'    => __( 'Bác sĩ đồng hành cùng nụ cười của bạn', 'themeranghammatsg' ),
		'all_text' => __( 'Xem tất cả bác sĩ', 'themeranghammatsg' ),
		'all_link' => '',
	);
	$value = rhm_get_option( 'rhm_home_doctors_' . $key, $defaults[ $key ] ?? '' );
	if ( 'all_link' === $key && '' === $value ) {
		$value = (string) get_post_type_archive_link( 'bac-si' );
	}
	return $value;
}

/**
 * ID bác sĩ cho slider.
 *
 * @return int[]
 */
function rhm_home_doctor_ids(): array {
	$base = array(
		'post_type'           => 'bac-si',
		'post_status'         => 'publish',
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'meta_query'          => array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- vài chục hồ sơ.
	);

	$ids = get_posts(
		array_merge(
			$base,
			array(
				'posts_per_page' => RHM_HOME_DOCTORS_MAX,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- vài chục hồ sơ.
					$base['meta_query'][0],
					array(
						'key'   => 'rhm_doctor_home',
						'value' => '1',
					),
				),
			)
		)
	);

	if ( ! $ids ) {
		$ids = get_posts( array_merge( $base, array( 'posts_per_page' => RHM_HOME_DOCTORS_FALLBACK ) ) );
	}

	return array_map( 'intval', $ids );
}

/**
 * Dữ liệu từng bác sĩ cho slider.
 *
 * @return array<int, array{id:int, name:string, url:string, degree:string, position:string, years:string, case_count:string, credentials:string[], image:int}>
 */
function rhm_get_home_doctors(): array {
	$ids = rhm_home_doctor_ids();
	if ( ! $ids ) {
		return array();
	}
	_prime_post_caches( $ids );
	update_postmeta_cache( $ids );

	$lines = static fn( $value ): array => array_values( array_filter( array_map( 'sanitize_text_field', preg_split( '/\R/u', (string) $value ) ?: array() ) ) );

	// Ảnh đại diện: nạp sẵn meta của mọi attachment một lần (tránh N+1).
	$thumbs = array_filter( array_map( static fn( int $id ): int => (int) get_post_thumbnail_id( $id ), $ids ) );
	if ( $thumbs ) {
		_prime_post_caches( $thumbs, false, true );
	}

	$doctors = array();
	foreach ( $ids as $id ) {
		$image = (int) get_post_thumbnail_id( $id );
		if ( ! $image ) {
			continue;
		}
		$field       = static fn( string $name ) => rhm_get_doctor_field( $name, $id );
		$credentials = $lines( $field( 'credentials' ) ) ?: $lines( $field( 'education' ) );

		$doctors[] = array(
			'id'          => $id,
			'name'        => get_the_title( $id ),
			'url'         => (string) get_permalink( $id ),
			'degree'      => sanitize_text_field( (string) $field( 'degree' ) ),
			'position'    => sanitize_text_field( (string) $field( 'position' ) ),
			'years'       => sanitize_text_field( (string) $field( 'years' ) ),
			'case_count'  => sanitize_text_field( (string) $field( 'case_count' ) ),
			'credentials' => array_slice( $credentials, 0, RHM_HOME_DOCTORS_CREDENTIALS ),
			'image'       => $image,
		);
	}

	return $doctors;
}

/**
 * Customizer → "Đội ngũ bác sĩ (Trang chủ)".
 */
function rhm_customize_home_doctors( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_home_doctors',
		array(
			'title'       => __( 'Đội ngũ bác sĩ (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Slider bác sĩ nằm trên section Tin tức. Chọn bác sĩ hiển thị trong từng hồ sơ: Bác sĩ → sửa hồ sơ → tab "Trang chủ" (học vị, kinh nghiệm, số ca, chứng chỉ). Thứ tự: ô "Thứ tự" của hồ sơ.', 'themeranghammatsg' ),
			'priority'    => 33,
		)
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_home_doctors',
		array(
			'rhm_home_doctors_eyebrow'  => array( __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_doctors_text( 'eyebrow' ) ) ),
			'rhm_home_doctors_title'    => array( __( 'Tiêu đề section', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_doctors_text( 'title' ) ) ),
			'rhm_home_doctors_all_text' => array( __( 'Chữ link "xem tất cả" (để trống: ẩn link)', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_doctors_text( 'all_text' ) ) ),
			'rhm_home_doctors_all_link' => array( __( 'Link "xem tất cả" (để trống: trang danh sách bác sĩ)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_home_doctors' );
