<?php
/**
 * Section "Đội ngũ bác sĩ" trang chủ (template-parts/sections/doctors.php): slider, mỗi slide 1 bác sĩ.
 *
 * Bác sĩ hiển thị: MỌI hồ sơ đã xuất bản có ảnh đại diện (theo chuyên khoa nếu đã chọn), sắp theo ô "Thứ tự"
 * (menu_order) rồi ngày đăng mới nhất, tối đa RHM_HOME_DOCTORS_MAX. Không còn điều kiện field rhm_doctor_home
 * (ô đã ẩn khỏi hồ sơ; meta cũ vẫn còn trong DB nhưng không được đọc).
 * Chữ của section: Customizer → "Đội ngũ bác sĩ (Trang chủ)".
 *
 * Nhiều bản (rhm_home_doctor_sections()): mỗi bản = 1 mục Customizer, theme mod "<tiền tố>_<field>", có ô chọn Chuyên khoa
 * (taxonomy chuyen-khoa). Chọn chuyên khoa → chỉ bác sĩ thuộc chuyên khoa đó (kể cả chuyên khoa con).
 * Bản 2 (sections/doctors-2.php) chỉ hiện khi đã chọn chuyên khoa.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Số bác sĩ tối đa trong slider (giới hạn DOM: ~35 node mỗi bác sĩ). */
const RHM_HOME_DOCTORS_MAX = 8;

/** Số dòng "Chứng chỉ chuyên môn" tối đa mỗi bác sĩ. */
const RHM_HOME_DOCTORS_CREDENTIALS = 5;

/**
 * Các bản section trên trang chủ: tiền tố theme mod (= id mục Customizer) => cấu hình.
 * - id:            id section HTML ('' = không in, giữ như bản gốc); tiêu đề H2 = "<id>-title" (bản gốc: doctors-title).
 * - require_term:  true = chưa chọn chuyên khoa thì không hiện (tránh 2 slider giống hệt nhau).
 *
 * @return array<string, array{id:string, label:string, require_term:bool}>
 */
function rhm_home_doctor_sections(): array {
	return array(
		'rhm_home_doctors'   => array(
			'id'           => '',
			'label'        => __( 'Đội ngũ bác sĩ (Trang chủ)', 'themeranghammatsg' ),
			'require_term' => false,
		),
		'rhm_home_doctors_2' => array(
			'id'           => 'doctors-2',
			'label'        => __( 'Đội ngũ bác sĩ 2 (Trang chủ)', 'themeranghammatsg' ),
			'require_term' => true,
		),
	);
}

/**
 * Chuyên khoa đã chọn cho 1 bản section (term_id); 0 = tất cả hoặc chuyên khoa đã bị xóa.
 */
function rhm_home_doctor_term( string $prefix = 'rhm_home_doctors' ): int {
	$term = absint( get_theme_mod( "{$prefix}_term", 0 ) );
	return ( $term && term_exists( $term, RHM_DOCTOR_TAXONOMY ) ) ? $term : 0;
}

/**
 * Chữ của section (Customizer, có mặc định). $prefix: khóa trong rhm_home_doctor_sections().
 */
function rhm_home_doctors_text( string $key, string $prefix = 'rhm_home_doctors' ): string {
	$defaults = array(
		'eyebrow'  => __( 'Đội ngũ chuyên môn', 'themeranghammatsg' ),
		'title'    => __( 'Bác sĩ đồng hành cùng nụ cười của bạn', 'themeranghammatsg' ),
		'all_text' => __( 'Xem tất cả bác sĩ', 'themeranghammatsg' ),
		'all_link' => '',
	);
	$value = rhm_get_option( "{$prefix}_{$key}", $defaults[ $key ] ?? '' );
	if ( 'all_link' === $key && '' === $value ) {
		$value = (string) get_post_type_archive_link( 'bac-si' );
	}
	return $value;
}

/**
 * ID bác sĩ cho slider: hồ sơ có ảnh đại diện, sắp "Thứ tự" rồi ngày đăng. $term > 0: chỉ bác sĩ thuộc chuyên khoa đó (kể cả con).
 *
 * @return int[]
 */
function rhm_home_doctor_ids( int $term = 0 ): array {
	$base = array(
		'post_type'           => 'bac-si',
		'post_status'         => 'publish',
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'meta_query'          => array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- vài chục hồ sơ.
	);
	if ( $term ) {
		$base['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- vài chục hồ sơ.
			array(
				'taxonomy'         => RHM_DOCTOR_TAXONOMY,
				'field'            => 'term_id',
				'terms'            => $term,
				'include_children' => true,
			),
		);
	}

	$ids = get_posts(
		array_merge(
			$base,
			array(
				'posts_per_page' => RHM_HOME_DOCTORS_MAX,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		)
	);

	return array_map( 'intval', $ids );
}

/**
 * Dữ liệu từng bác sĩ cho slider.
 *
 * @return array<int, array{id:int, name:string, url:string, degree:string, position:string, years:string, case_count:string, credentials:string[], image:int}>
 */
function rhm_get_home_doctors( int $term = 0 ): array {
	static $cache = array(); // 2 section cùng chuyên khoa (hoặc trang chủ + landing) không truy vấn lại.
	if ( isset( $cache[ $term ] ) ) {
		return $cache[ $term ];
	}
	$ids = rhm_home_doctor_ids( $term );
	if ( ! $ids ) {
		return $cache[ $term ] = array();
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

	return $cache[ $term ] = $doctors;
}

/**
 * Customizer: mỗi bản trong rhm_home_doctor_sections() là 1 mục "Đội ngũ bác sĩ…", cùng bộ field + ô Chuyên khoa.
 */
function rhm_customize_home_doctors( WP_Customize_Manager $wp_customize ): void {
	$terms   = get_terms(
		array(
			'taxonomy'   => RHM_DOCTOR_TAXONOMY,
			'hide_empty' => false,
		)
	);
	$choices = array( 0 => __( '— Tất cả chuyên khoa —', 'themeranghammatsg' ) );
	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			$choices[ $term->term_id ] = $term->name;
		}
	}
	foreach ( rhm_home_doctor_sections() as $prefix => $config ) {
		rhm_customize_home_doctors_section( $wp_customize, $prefix, $config, $choices );
	}
}
add_action( 'customize_register', 'rhm_customize_home_doctors' );

/**
 * 1 mục "Đội ngũ bác sĩ": section id = $prefix, theme mod "{$prefix}_<field>".
 *
 * @param array{id:string, label:string, require_term:bool} $config
 * @param array<int, string>                                $choices term_id => tên chuyên khoa.
 */
function rhm_customize_home_doctors_section( WP_Customize_Manager $wp_customize, string $prefix, array $config, array $choices ): void {
	$intro       = $config['require_term']
		? __( 'Slider thứ 2, ngay sau "Đội ngũ bác sĩ". Chỉ hiện khi đã chọn Chuyên khoa.', 'themeranghammatsg' )
		: __( 'Slider bác sĩ nằm trên section Tin tức. "Tất cả chuyên khoa" = mọi hồ sơ bác sĩ.', 'themeranghammatsg' );
	$description = $intro . ' ' . sprintf(
		/* translators: %d: số bác sĩ tối đa */
		__( 'Hiện mọi hồ sơ đã xuất bản có Ảnh đại diện, tối đa %d, sắp theo ô "Thứ tự" của hồ sơ (số nhỏ đứng trước). Học vị, kinh nghiệm, số ca, chứng chỉ: Bác sĩ → sửa hồ sơ → tab "Trang chủ".', 'themeranghammatsg' ),
		RHM_HOME_DOCTORS_MAX
	);
	if ( count( $choices ) < 2 ) {
		$description .= ' ' . __( 'Chưa có chuyên khoa: tạo ở Bác sĩ → Chuyên khoa rồi gán cho hồ sơ.', 'themeranghammatsg' );
	}

	$wp_customize->add_section(
		$prefix,
		array(
			'title'       => $config['label'],
			'description' => $description,
			'priority'    => 33,
		)
	);

	rhm_customize_fields(
		$wp_customize,
		$prefix,
		array(
			"{$prefix}_term"     => array( __( 'Chuyên khoa', 'themeranghammatsg' ), 'select', 'absint', array( 'choices' => $choices ) ),
			"{$prefix}_eyebrow"  => array( __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_doctors_text( 'eyebrow' ) ) ),
			"{$prefix}_title"    => array( __( 'Tiêu đề section', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_doctors_text( 'title' ) ) ),
			"{$prefix}_all_text" => array( __( 'Chữ link "xem tất cả" (để trống: ẩn link)', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_home_doctors_text( 'all_text' ) ) ),
			"{$prefix}_all_link" => array( __( 'Link "xem tất cả" (để trống: trang danh sách bác sĩ)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		)
	);
}
