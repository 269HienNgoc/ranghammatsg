<?php
/**
 * Customizer (Giao diện → Tùy biến):
 * - "Thông tin phòng khám": dùng lại nhiều nơi (header, footer, CTA).
 * - Các section trang chủ: Hero banner, Dịch vụ nổi bật, Giới thiệu, Tin tức, Chứng nhận.
 * Thêm field: rhm_customize_fields() (field đơn) / rhm_customize_repeater() (nhóm lặp N lần)
 * / rhm_customize_list() (danh sách động có nút "Thêm mới", inc/customize-list.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Danh sách field: key => [label, type, sanitize_callback].
 */
function rhm_customizer_fields(): array {
	return array(
		'rhm_hotline'         => array( __( 'Hotline', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
		'rhm_hotline_note'    => array( __( 'Dòng phụ dưới hotline', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
		'rhm_flag_vn_url'     => array( __( 'Link cờ Việt Nam (header)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_flag_en_url'     => array( __( 'Link cờ Anh (header)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_email'           => array( __( 'Email', 'themeranghammatsg' ), 'email', 'sanitize_email' ),
		'rhm_address'         => array( __( 'Địa chỉ', 'themeranghammatsg' ), 'textarea', 'sanitize_textarea_field' ),
		'rhm_working_hours'   => array( __( 'Giờ làm việc', 'themeranghammatsg' ), 'textarea', 'sanitize_textarea_field' ),
		'rhm_map_url'         => array( __( 'Link Google Maps (embed)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_facebook'        => array( __( 'Facebook URL', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_zalo'            => array( __( 'Zalo URL', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_youtube'         => array( __( 'YouTube URL', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_booking_page'    => array( __( 'Trang Liên hệ / Đặt lịch', 'themeranghammatsg' ), 'dropdown-pages', 'absint' ),
		'rhm_booking_form_id' => array( __( 'ID form đặt lịch (Fluent Forms)', 'themeranghammatsg' ), 'number', 'absint' ),
	);
}

/**
 * Thêm setting + control cho danh sách field. Control 'image' = chọn ảnh từ thư viện (lưu ID).
 * Phần tử thứ 4 (tùy chọn): 'default' → giá trị mặc định của setting; các khóa khác (vd 'choices') → control.
 *
 * @param array<string, array{0:string, 1:string, 2:callable-string, 3?:array}> $fields id => [nhãn, 'text'|'textarea'|'url'|'email'|'number'|'checkbox'|'select'|'dropdown-pages'|'image', sanitize, extra].
 */
function rhm_customize_fields( WP_Customize_Manager $wp_customize, string $section, array $fields ): void {
	foreach ( $fields as $id => $field ) {
		[ $label, $type, $sanitize ] = $field;
		$extra                       = $field[3] ?? array();

		$wp_customize->add_setting(
			$id,
			array(
				'type'              => 'theme_mod',
				'default'           => $extra['default'] ?? '',
				'sanitize_callback' => $sanitize,
			)
		);
		unset( $extra['default'] );

		$args = array(
			'label'   => $label,
			'section' => $section,
		) + $extra;

		if ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, $args + array( 'mime_type' => 'image' ) ) );
		} else {
			$wp_customize->add_control( $id, $args + array( 'type' => $type ) );
		}
	}
}

/**
 * Nhóm field lặp lại N lần (slide, mục…). Setting: "{$prefix}_{$i}_{$field}" (vd rhm_hero_1_desktop).
 *
 * @param array<string, array{0:string, 1:string, 2:callable-string}> $fields field => [nhãn, kiểu, sanitize] như rhm_customize_fields().
 */
function rhm_customize_repeater( WP_Customize_Manager $wp_customize, string $section, string $prefix, int $max, string $item_label, array $fields ): void {
	for ( $i = 1; $i <= $max; $i++ ) {
		$group = array();
		foreach ( $fields as $field => [ $label, $type, $sanitize ] ) {
			/* translators: 1: item label (Slide, Mục), 2: item number, 3: field label */
			$group[ "{$prefix}_{$i}_{$field}" ] = array( sprintf( __( '%1$s %2$d – %3$s', 'themeranghammatsg' ), $item_label, $i, $label ), $type, $sanitize );
		}
		rhm_customize_fields( $wp_customize, $section, $group );
	}
}

function rhm_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_clinic_info',
		array(
			'title'    => __( 'Thông tin phòng khám', 'themeranghammatsg' ),
			'priority' => 30,
		)
	);

	rhm_customize_fields( $wp_customize, 'rhm_clinic_info', rhm_customizer_fields() );
}
add_action( 'customize_register', 'rhm_customize_register' );

/**
 * Hero banner trang chủ (inc/hero.php). Slide không có ảnh desktop sẽ bị bỏ qua.
 */
function rhm_customize_hero( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_hero',
		array(
			'title'       => __( 'Hero banner (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Ảnh desktop: ngang, rộng 2048px. Ảnh mobile: dọc (vd 828×1000px), hiển thị dưới 768px — bỏ trống thì dùng ảnh desktop. Các slide nên cùng tỉ lệ.', 'themeranghammatsg' ),
			'priority'    => 31,
		)
	);

	rhm_customize_repeater(
		$wp_customize,
		'rhm_hero',
		'rhm_hero',
		RHM_HERO_MAX_SLIDES,
		__( 'Slide', 'themeranghammatsg' ),
		array(
			'desktop' => array( __( 'Ảnh desktop', 'themeranghammatsg' ), 'image', 'absint' ),
			'mobile'  => array( __( 'Ảnh mobile', 'themeranghammatsg' ), 'image', 'absint' ),
			'link'    => array( __( 'Link khi bấm (tùy chọn)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_hero' );

/**
 * Dịch vụ nổi bật trang chủ (inc/quick-services.php). Mục không có tiêu đề sẽ bị bỏ qua.
 */
function rhm_customize_quick_services( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_quick_services',
		array(
			'title'       => __( 'Dịch vụ nổi bật (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Hàng icon dưới banner. Icon: file .svg vuông (vd 40×40), màu vẽ sẵn trong file.', 'themeranghammatsg' ),
			'priority'    => 32,
		)
	);

	rhm_customize_repeater(
		$wp_customize,
		'rhm_quick_services',
		'rhm_qs',
		RHM_QUICK_SERVICES_MAX,
		__( 'Mục', 'themeranghammatsg' ),
		array(
			'title' => array( __( 'Tiêu đề (Enter để xuống dòng)', 'themeranghammatsg' ), 'textarea', 'sanitize_textarea_field' ),
			'link'  => array( __( 'Link', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
			'icon'  => array( __( 'Icon (.svg)', 'themeranghammatsg' ), 'image', 'absint' ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_quick_services' );

/**
 * Giới thiệu trang chủ (inc/about.php): ảnh/video, thẻ nổi trên ảnh, chữ, nút, số liệu.
 */
function rhm_customize_about( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_about',
		array(
			'title'       => __( 'Giới thiệu (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Ảnh: .jpg ngang tỉ lệ 4:3, rộng ≥ 1200px. Có link video thì ảnh thành ảnh bìa, bấm ▶ mới tải video. Mô tả: mỗi dòng là 1 đoạn.', 'themeranghammatsg' ),
			'priority'    => 33,
		)
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_about',
		array(
			'rhm_about_image'           => array( __( 'Ảnh (.jpg)', 'themeranghammatsg' ), 'image', 'absint' ),
			'rhm_about_video'           => array( __( 'Link video (YouTube hoặc file .mp4) – tùy chọn', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
			'rhm_about_badge_title'     => array( __( 'Thẻ trên ảnh – dòng đậm', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'rhm_about_badge_text'      => array( __( 'Thẻ trên ảnh – dòng phụ', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'rhm_about_eyebrow'         => array( __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'rhm_about_title'           => array( __( 'Tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'rhm_about_title_highlight' => array( __( 'Tiêu đề – phần tô xanh (nối sau tiêu đề)', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'rhm_about_content'         => array( __( 'Mô tả', 'themeranghammatsg' ), 'textarea', 'sanitize_textarea_field' ),
			'rhm_about_button_text'     => array( __( 'Nút – chữ', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'rhm_about_button_link'     => array( __( 'Nút – link', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		)
	);

	rhm_customize_repeater(
		$wp_customize,
		'rhm_about',
		'rhm_about_stat',
		RHM_ABOUT_STATS_MAX,
		__( 'Số liệu', 'themeranghammatsg' ),
		array(
			'number' => array( __( 'Con số', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
			'label'  => array( __( 'Mô tả', 'themeranghammatsg' ), 'text', 'sanitize_text_field' ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_about' );

/**
 * Tin tức trang chủ (inc/news.php): 2 khối bài viết theo chuyên mục + tùy chọn thông tin hiển thị.
 */
function rhm_customize_news( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_news',
		array(
			'title'       => __( 'Tin tức (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Mỗi khối lấy bài mới nhất của chuyên mục đã chọn. Bài có ở khối trái sẽ không lặp lại ở khối phải.', 'themeranghammatsg' ),
			'priority'    => 34,
		)
	);

	$categories = array( 0 => __( '— Mới nhất (mọi chuyên mục) —', 'themeranghammatsg' ) );
	foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
		$categories[ $category->term_id ] = $category->name;
	}

	$titles   = rhm_news_default_titles();
	$checkbox = static fn( string $key, string $label ): array => array( $label, 'checkbox', 'rest_sanitize_boolean', array( 'default' => RHM_NEWS_SHOW_DEFAULTS[ $key ] ) );

	rhm_customize_fields(
		$wp_customize,
		'rhm_news',
		array(
			'rhm_news_main_title'    => array( __( 'Khối trái – tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => $titles['main'] ) ),
			'rhm_news_main_cat'      => array( __( 'Khối trái – chuyên mục', 'themeranghammatsg' ), 'select', 'absint', array( 'choices' => $categories ) ),
			'rhm_news_side_title'    => array( __( 'Khối phải – tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => $titles['side'] ) ),
			'rhm_news_side_cat'      => array( __( 'Khối phải – chuyên mục', 'themeranghammatsg' ), 'select', 'absint', array( 'choices' => $categories ) ),
			'rhm_news_show_date'     => $checkbox( 'date', __( 'Hiển thị ngày đăng', 'themeranghammatsg' ) ),
			'rhm_news_show_author'   => $checkbox( 'author', __( 'Hiển thị tác giả', 'themeranghammatsg' ) ),
			'rhm_news_show_category' => $checkbox( 'category', __( 'Hiển thị chuyên mục', 'themeranghammatsg' ) ),
			'rhm_news_show_excerpt'  => $checkbox( 'excerpt', __( 'Hiển thị mô tả ngắn (bài lớn khối trái)', 'themeranghammatsg' ) ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_news' );

/**
 * Chứng nhận trang chủ (inc/certificates.php): tiêu đề + danh sách ảnh không giới hạn.
 */
function rhm_customize_certificates( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_certificates',
		array(
			'title'       => __( 'Chứng nhận (Trang chủ)', 'themeranghammatsg' ),
			'description' => __( 'Ảnh chứng nhận: .jpg ngang tỉ lệ 4:3, rộng khoảng 600–800px (ảnh dọc / khác tỉ lệ vẫn hiển thị đủ, có viền trắng). Mục chưa có ảnh sẽ bị bỏ qua.', 'themeranghammatsg' ),
			'priority'    => 35,
		)
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_certificates',
		array(
			'rhm_cert_title' => array( __( 'Tiêu đề', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_certificates_default_title() ) ),
		)
	);

	rhm_customize_list(
		$wp_customize,
		'rhm_certificates',
		'rhm_cert_items',
		__( 'Danh sách chứng nhận', 'themeranghammatsg' ),
		__( 'Chứng nhận', 'themeranghammatsg' ),
		rhm_certificate_fields(),
		__( '"Thêm mới": thêm 1 mục. "Thêm nhiều ảnh": chọn nhiều ảnh cùng lúc, mỗi ảnh thành 1 mục. Dùng ↑ ↓ để sắp xếp.', 'themeranghammatsg' )
	);
}
add_action( 'customize_register', 'rhm_customize_certificates' );
