<?php
/**
 * Hồ sơ bác sĩ: CPT, dữ liệu ACF và các truy vấn liên quan.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_register_doctors(): void {
	register_post_type(
		'bac-si',
		array(
			'labels'       => array(
				'name'               => __( 'Bác sĩ', 'themeranghammatsg' ),
				'singular_name'      => __( 'Bác sĩ', 'themeranghammatsg' ),
				'add_new_item'       => __( 'Thêm bác sĩ', 'themeranghammatsg' ),
				'edit_item'          => __( 'Sửa hồ sơ bác sĩ', 'themeranghammatsg' ),
				'new_item'           => __( 'Hồ sơ bác sĩ mới', 'themeranghammatsg' ),
				'view_item'          => __( 'Xem hồ sơ bác sĩ', 'themeranghammatsg' ),
				'search_items'       => __( 'Tìm bác sĩ', 'themeranghammatsg' ),
				'not_found'          => __( 'Chưa có hồ sơ bác sĩ.', 'themeranghammatsg' ),
				'not_found_in_trash' => __( 'Không có hồ sơ bác sĩ trong thùng rác.', 'themeranghammatsg' ),
				'all_items'          => __( 'Tất cả bác sĩ', 'themeranghammatsg' ),
				'archives'           => __( 'Bác sĩ', 'themeranghammatsg' ),
				'featured_image'     => __( 'Ảnh đại diện bác sĩ', 'themeranghammatsg' ),
				'set_featured_image' => __( 'Chọn ảnh đại diện bác sĩ', 'themeranghammatsg' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'has_archive'  => 'bac-si',
			'rewrite'      => array( 'slug' => 'bac-si', 'with_front' => false ),
			'menu_icon'    => 'dashicons-businessperson',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ), // page-attributes: ô "Thứ tự" (section bác sĩ trang chủ).
		)
	);
}
add_action( 'init', 'rhm_register_doctors' );

/**
 * Taxonomy "Chuyên khoa" của bác sĩ (Implant, Niềng răng…): CHỈ dùng trong admin để phân loại hồ sơ.
 * Không công khai: không có trang /chuyen-khoa/…, không vào sitemap. Cần trang riêng thì bật public/rewrite/query_var
 * + thêm template, bundle CSS và tăng rhm_doctor_rewrite_version.
 */
const RHM_DOCTOR_TAXONOMY = 'chuyen-khoa';

function rhm_register_doctor_taxonomy(): void {
	register_taxonomy(
		RHM_DOCTOR_TAXONOMY,
		'bac-si',
		array(
			'labels'            => array(
				'name'          => __( 'Chuyên khoa', 'themeranghammatsg' ),
				'singular_name' => __( 'Chuyên khoa', 'themeranghammatsg' ),
				'menu_name'     => __( 'Chuyên khoa', 'themeranghammatsg' ),
				'all_items'     => __( 'Tất cả chuyên khoa', 'themeranghammatsg' ),
				'edit_item'     => __( 'Sửa chuyên khoa', 'themeranghammatsg' ),
				'add_new_item'  => __( 'Thêm chuyên khoa', 'themeranghammatsg' ),
				'search_items'  => __( 'Tìm chuyên khoa', 'themeranghammatsg' ),
				'not_found'     => __( 'Chưa có chuyên khoa.', 'themeranghammatsg' ),
				'parent_item'   => __( 'Chuyên khoa cha', 'themeranghammatsg' ),
			),
			'hierarchical'      => true, // Ô tick như Chuyên mục (vd Implant → Implant toàn hàm).
			'public'            => false,
			'show_ui'           => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_admin_column' => true, // Cột "Chuyên khoa" trong danh sách Bác sĩ.
			'show_in_rest'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);
}
add_action( 'init', 'rhm_register_doctor_taxonomy' );

/** Ô lọc theo chuyên khoa ở danh sách Bác sĩ (admin). Dùng query var chung taxonomy + term vì taxonomy không có query_var riêng. */
function rhm_doctor_taxonomy_filter( string $post_type ): void {
	if ( 'bac-si' !== $post_type || ! wp_count_terms( array( 'taxonomy' => RHM_DOCTOR_TAXONOMY, 'hide_empty' => false ) ) ) {
		return;
	}
	$current = isset( $_GET['term'] ) ? sanitize_title( wp_unslash( $_GET['term'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- chỉ lọc danh sách.
	echo '<input type="hidden" name="taxonomy" value="' . esc_attr( RHM_DOCTOR_TAXONOMY ) . '">';
	wp_dropdown_categories(
		array(
			'taxonomy'        => RHM_DOCTOR_TAXONOMY,
			'name'            => 'term',
			'value_field'     => 'slug',
			'selected'        => $current,
			'show_option_all' => __( 'Tất cả chuyên khoa', 'themeranghammatsg' ),
			'hierarchical'    => true,
			'hide_empty'      => false,
			'orderby'         => 'name',
		)
	);
}
add_action( 'restrict_manage_posts', 'rhm_doctor_taxonomy_filter' );

/** "Tất cả chuyên khoa" gửi term = 0 (hoặc rỗng) → bỏ điều kiện lọc. */
function rhm_doctor_taxonomy_filter_query( WP_Query $query ): void {
	if ( is_admin() && $query->is_main_query() && 'bac-si' === $query->get( 'post_type' ) && RHM_DOCTOR_TAXONOMY === $query->get( 'taxonomy' ) && in_array( (string) $query->get( 'term' ), array( '', '0' ), true ) ) {
		$query->set( 'taxonomy', '' );
		$query->set( 'term', '' );
	}
}
add_action( 'pre_get_posts', 'rhm_doctor_taxonomy_filter_query' );

/** Cập nhật rewrite một lần khi thêm/thay đổi cấu trúc URL, sau khi CPT đã đăng ký. */
function rhm_doctor_rewrite(): void {
	if ( '1' !== get_option( 'rhm_doctor_rewrite_version' ) ) {
		flush_rewrite_rules( false );
		update_option( 'rhm_doctor_rewrite_version', '1', false );
	}
}
add_action( 'init', 'rhm_doctor_rewrite', 20 );

function rhm_doctor_acf(): void {
	require_once RHM_THEME_DIR . '/inc/doctor-fields.php';
	rhm_register_doctor_fields();
}
add_action( 'acf/init', 'rhm_doctor_acf' );

/** ACF trả ID ảnh, không trả mảng ảnh đã format tốn truy vấn. */
function rhm_get_doctor_field( string $name, int $post_id ) {
	return function_exists( 'get_field' ) ? get_field( 'rhm_doctor_' . $name, $post_id ) : get_post_meta( $post_id, 'rhm_doctor_' . $name, true );
}

function rhm_get_doctor_image_id( $value ): int {
	$id = is_numeric( $value ) ? absint( $value ) : 0;
	return $id && wp_attachment_is_image( $id ) ? $id : 0;
}

function rhm_get_doctor_gallery( $value ): array {
	return is_array( $value ) ? array_values( array_unique( array_filter( array_map( 'rhm_get_doctor_image_id', $value ) ) ) ) : array();
}

/** Video chỉ nhận YouTube hợp lệ, có tiêu đề và ảnh bìa tự host; bỏ dòng nhập dở. */
function rhm_get_doctor_videos( $value ): array {
	$videos = array();
	foreach ( is_array( $value ) ? $value : array() as $row ) {
		$url   = esc_url_raw( (string) ( $row['url'] ?? '' ) );
		$host  = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$video = in_array( $host, array( 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtube-nocookie.com', 'youtube-nocookie.com' ), true ) ? rhm_parse_video_url( $url ) : array();
		$title = sanitize_text_field( (string) ( $row['title'] ?? '' ) );
		$image = rhm_get_doctor_image_id( $row['image'] ?? 0 );
		if ( 'youtube' === ( $video['type'] ?? '' ) && $title && $image ) {
			$videos[] = array( 'src' => $video['src'], 'title' => $title, 'image' => $image );
		}
	}
	return array_slice( $videos, 0, 2 );
}

function rhm_get_doctor( int $post_id ): array {
	$field = static fn( string $name ) => rhm_get_doctor_field( $name, $post_id );
	$lists = array();
	foreach ( array( 'specialties', 'education', 'experience' ) as $key ) {
		$lists[ $key ] = array_values( array_filter( array_map( 'sanitize_text_field', preg_split( '/\R/u', (string) $field( $key ) ) ?: array() ) ) );
	}
	$cases = array();
	$rows  = $field( 'cases' );
	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		$item = array();
		foreach ( array( 'name', 'location', 'method', 'treatment' ) as $key ) {
			$item[ $key ] = sanitize_text_field( (string) ( $row[ $key ] ?? '' ) );
		}
		foreach ( array( 'portrait', 'before', 'after' ) as $key ) {
			$item[ $key ] = rhm_get_doctor_image_id( $row[ $key ] ?? 0 );
		}
		if ( $item['name'] && $item['portrait'] && $item['before'] && $item['after'] ) {
			$cases[] = $item;
		}
	}
	return array(
		'banner_desktop' => rhm_get_doctor_image_id( $field( 'banner_desktop' ) ),
		'banner_mobile'  => rhm_get_doctor_image_id( $field( 'banner_mobile' ) ),
		'position'       => sanitize_text_field( (string) $field( 'position' ) ),
		'gallery'        => rhm_get_doctor_gallery( $field( 'gallery' ) ),
		'lists'          => $lists,
		'diplomas'       => rhm_get_doctor_gallery( $field( 'diplomas' ) ),
		'certificates'   => rhm_get_doctor_gallery( $field( 'certificates' ) ),
		'videos'         => rhm_get_doctor_videos( $field( 'videos' ) ),
		'case_videos'    => rhm_get_doctor_videos( $field( 'case_videos' ) ),
		'cases'          => $cases,
	);
}

/** Mọi lời mời đặt lịch trên hồ sơ, kể cả nút ở header/footer, tới form của chính trang. */
function rhm_doctor_booking_url( string $url ): string {
	return is_singular( 'bac-si' ) ? '#dat-lich' : $url;
}
add_filter( 'rhm_booking_url', 'rhm_doctor_booking_url' );

/** Ảnh nội dung linh hoạt: kích thước giữ chỗ + srcset, alt có fallback. */
function rhm_doctor_image( int $id, string $sizes, string $class, string $alt = '', bool $priority = false, float $frame_ratio = 0 ): void {
	$sizes = rhm_get_doctor_image_sizes( $id, $sizes, $frame_ratio );
	$stored_alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	echo wp_get_attachment_image(
		$id,
		'full',
		false,
		array(
			'class'         => $class,
			'sizes'         => $sizes,
			'alt'           => $stored_alt ?: ( $alt ?: get_the_title( $id ) ),
			'loading'       => $priority ? 'eager' : 'lazy',
			'decoding'      => $priority ? 'auto' : 'async',
			'fetchpriority' => $priority ? 'high' : 'auto',
		)
	); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress escapes attachment markup.
}

/** Object-cover: ảnh nguồn ngang trong khung dọc cần đủ pixel theo chiều cao. */
function rhm_get_doctor_image_sizes( int $id, string $sizes, float $frame_ratio ): string {
	$meta = wp_get_attachment_metadata( $id );
	if ( $frame_ratio <= 0 || empty( $meta['height'] ) || empty( $meta['width'] ) ) {
		return $sizes;
	}
	$factor = $meta['width'] / $meta['height'] / $frame_ratio;
	if ( $factor <= 1.01 ) {
		return $sizes;
	}
	return implode(
		', ',
		array_map(
			static function ( string $slot ) use ( $factor ): string {
				preg_match( '/^(\([^)]*\)\s+)?(.+)$/', trim( $slot ), $parts );
				return ( $parts[1] ?? '' ) . 'calc(' . $parts[2] . ' * ' . round( $factor, 3 ) . ')';
			},
			explode( ',', $sizes )
		)
	);
}

// Cột chân dung 36% của (khung - gap 48px), mobile rộng tối đa 400px. Dùng chung cho ảnh và preload.
const RHM_DOCTOR_PORTRAIT_SIZES = '(min-width: 1440px) 490px, (min-width: 1024px) calc(36vw - 29px), (min-width: 432px) 400px, calc(100vw - 32px)';
