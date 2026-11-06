<?php
/**
 * Customizer (Giao diện → Tùy biến):
 * - "Thông tin phòng khám": dùng lại nhiều nơi (header, footer, CTA).
 * - Các section trang chủ: Hero banner, Dịch vụ nổi bật, Giới thiệu, Tin tức, Chứng nhận.
 * - "Chân trang (Footer)", "Trang Liên hệ", "Bài viết" (mục lục).
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
		'rhm_map_url'         => array( __( 'Link nhúng Google Maps (Chia sẻ → Nhúng bản đồ → lấy link trong src). Trống: tự tạo từ địa chỉ', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
		'rhm_booking_page'    => array( __( 'Trang Liên hệ / Đặt lịch', 'themeranghammatsg' ), 'dropdown-pages', 'absint' ),
		'rhm_booking_form_id' => array( __( 'ID form đặt lịch (Fluent Forms)', 'themeranghammatsg' ), 'number', 'absint' ),
	);
}

/**
 * Thêm setting + control cho danh sách field. Control 'image' = chọn ảnh từ thư viện (lưu ID).
 * Phần tử thứ 4 (tùy chọn): 'default' → giá trị mặc định của setting; các khóa khác (vd 'choices') → control.
 *
 * @param array<string, array{0:string, 1:string, 2:callable-string, 3?:array}> $fields id => [nhãn, 'text'|'textarea'|'url'|'email'|'number'|'checkbox'|'select'|'dropdown-pages'|'image'|'rhm-icon', sanitize, extra].
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
		} elseif ( 'rhm-icon' === $type ) {
			// Chọn icon từ thư viện assets/icons/<icon_set>/ (extra: 'icon_set').
			require_once RHM_THEME_DIR . '/inc/class-rhm-customize-icon-control.php';
			$wp_customize->add_control( new RHM_Customize_Icon_Control( $wp_customize, $id, $args ) );
		} else {
			$wp_customize->add_control( $id, $args + array( 'type' => $type ) );
		}
	}
}

/**
 * Nhóm field lặp lại N lần (slide, mục…). Setting: "{$prefix}_{$i}_{$field}" (vd rhm_hero_1_desktop).
 *
 * @param array<string, array{0:string, 1:string, 2:callable-string, 3?:array}> $fields field => [nhãn, kiểu, sanitize, extra] như rhm_customize_fields().
 */
function rhm_customize_repeater( WP_Customize_Manager $wp_customize, string $section, string $prefix, int $max, string $item_label, array $fields ): void {
	for ( $i = 1; $i <= $max; $i++ ) {
		$group = array();
		foreach ( $fields as $field => $def ) {
			/* translators: 1: item label (Slide, Mục), 2: item number, 3: field label */
			$def[0]                             = sprintf( __( '%1$s %2$d – %3$s', 'themeranghammatsg' ), $item_label, $i, $def[0] );
			$group[ "{$prefix}_{$i}_{$field}" ] = $def; // Giữ phần tử thứ 4 (extra) nếu có.
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
			'description' => __( 'Hàng icon dưới banner. Mỗi mục: chọn icon trong bộ icon của theme, hoặc tải lên file .svg riêng (vuông, vd 40×40, màu vẽ sẵn) — icon chọn trong bộ được ưu tiên.', 'themeranghammatsg' ),
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
			'title'     => array( __( 'Tiêu đề (Enter để xuống dòng)', 'themeranghammatsg' ), 'textarea', 'sanitize_textarea_field' ),
			'link'      => array( __( 'Link', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
			'icon_name' => array( __( 'Icon (bộ icon của theme)', 'themeranghammatsg' ), 'rhm-icon', 'rhm_sanitize_service_icon', array( 'icon_set' => 'services' ) ),
			'icon'      => array( __( 'Hoặc tải lên icon riêng (.svg)', 'themeranghammatsg' ), 'image', 'absint' ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_quick_services' );

function rhm_sanitize_service_icon( $value ): string {
	return rhm_sanitize_icon_name( 'services', $value );
}

/**
 * Thư viện icon (inc/icon-library.php): URL CDN của thư mục assets/icons/.
 */
function rhm_customize_icons( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_icons',
		array(
			'title'       => __( 'Thư viện icon', 'themeranghammatsg' ),
			'description' => sprintf(
				/* translators: 1: theme icon folder, 2: number of menu icons, 3: number of service icons */
				__( 'Bộ icon nằm trong thư mục theme %1$s (menu: %2$d icon, dịch vụ nổi bật: %3$d icon). Thêm icon: chép file .svg vào thư mục menu/ hoặc services/. Icon menu chọn ở Giao diện → Menu; icon dịch vụ chọn ở mục "Dịch vụ nổi bật (Trang chủ)".', 'themeranghammatsg' ),
				'<code>assets/icons/</code>',
				count( rhm_icon_list( 'menu' ) ),
				count( rhm_icon_list( 'services' ) )
			),
			'priority'    => 33,
		)
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_icons',
		array(
			'rhm_icon_base_url' => array(
				__( 'URL thư mục icon trên CDN (tùy chọn)', 'themeranghammatsg' ),
				'url',
				'esc_url_raw',
				array(
					'description' => __( 'Đồng bộ NGUYÊN thư mục assets/icons/ (giữ menu/, services/) lên Cloudflare rồi nhập URL thư mục đó, vd https://cdn.example.com/icons. Để trống: tải icon từ theme. Icon menu luôn in trực tiếp trong HTML nên không phụ thuộc CDN.', 'themeranghammatsg' ),
					'input_attrs' => array( 'placeholder' => RHM_THEME_URI . '/assets/icons' ),
				),
			),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_icons' );

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

/**
 * Chân trang (inc/footer.php). Địa chỉ / hotline / email / giờ làm việc lấy từ "Thông tin phòng khám".
 */
function rhm_customize_footer( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_footer',
		array(
			'title'       => __( 'Chân trang (Footer)', 'themeranghammatsg' ),
			'description' => __( 'Logo: Biểu tượng trang web (Nhận diện website). Địa chỉ, hotline, email, giờ làm việc: sửa ở "Thông tin phòng khám". Điều khoản chính sách: Menu → vị trí "Menu chân trang".', 'themeranghammatsg' ),
			'priority'    => 36,
		)
	);

	$defaults = rhm_footer_defaults();
	$text     = static fn( string $key, string $label, string $type = 'text' ): array => array(
		$label,
		$type,
		'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field',
		array( 'default' => $defaults[ $key ] ),
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_footer',
		array(
			'rhm_footer_slogan'             => $text( 'slogan', __( 'Khẩu hiệu dưới logo', 'themeranghammatsg' ) ),
			'rhm_footer_title'              => $text( 'title', __( 'Tiêu đề cột liên hệ', 'themeranghammatsg' ) ),
			'rhm_footer_branch_title'       => $text( 'branch_title', __( 'Chi nhánh – tiêu đề', 'themeranghammatsg' ) ),
			'rhm_footer_branch_button_text' => $text( 'branch_button_text', __( 'Chi nhánh – chữ nút (để trống: ẩn nút)', 'themeranghammatsg' ) ),
			'rhm_footer_branch_button_link' => array( __( 'Chi nhánh – link nút (để trống: trang Liên hệ / Đặt lịch)', 'themeranghammatsg' ), 'url', 'esc_url_raw' ),
			'rhm_footer_policy_title'       => $text( 'policy_title', __( 'Điều khoản chính sách – tiêu đề', 'themeranghammatsg' ) ),
			'rhm_footer_social_title'       => $text( 'social_title', __( 'Mạng xã hội – tiêu đề', 'themeranghammatsg' ) ),
			'rhm_footer_copyright'          => $text( 'copyright', __( 'Dòng bản quyền ({year} = năm hiện tại)', 'themeranghammatsg' ), 'textarea' ),
		)
	);

	rhm_customize_list(
		$wp_customize,
		'rhm_footer',
		'rhm_footer_branches',
		__( 'Danh sách chi nhánh', 'themeranghammatsg' ),
		__( 'Chi nhánh', 'themeranghammatsg' ),
		rhm_footer_branch_fields()
	);

	rhm_customize_list(
		$wp_customize,
		'rhm_footer',
		'rhm_footer_socials',
		__( 'Mạng xã hội', 'themeranghammatsg' ),
		__( 'Mạng xã hội', 'themeranghammatsg' ),
		rhm_footer_social_fields(),
		__( 'Icon vuông (.svg hoặc .png ≥ 80×80px), hiển thị 40×40px. Mục thiếu icon hoặc link sẽ bị bỏ qua.', 'themeranghammatsg' )
	);

	rhm_customize_list(
		$wp_customize,
		'rhm_footer',
		'rhm_footer_badges',
		__( 'Huy hiệu cuối trang', 'themeranghammatsg' ),
		__( 'Huy hiệu', 'themeranghammatsg' ),
		rhm_footer_badge_fields(),
		__( 'Vd: "Đã thông báo Bộ Công Thương", DMCA. Ảnh .png/.jpg, hiển thị cao 40px.', 'themeranghammatsg' )
	);
}
add_action( 'customize_register', 'rhm_customize_footer' );

/**
 * Trang Liên hệ – Đặt lịch (inc/contact.php). Thông tin liên hệ, bản đồ, ID form: "Thông tin phòng khám"; chi nhánh, mạng xã hội: "Chân trang (Footer)".
 */
function rhm_customize_contact( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_contact',
		array(
			'title'       => __( 'Trang Liên hệ', 'themeranghammatsg' ),
			'description' => __( 'Hotline, email, địa chỉ, giờ làm việc, link bản đồ, ID form đặt lịch: sửa ở "Thông tin phòng khám". Chi nhánh và mạng xã hội: "Chân trang (Footer)". Bản đồ tự tạo từ địa chỉ nếu không nhập link embed.', 'themeranghammatsg' ),
			'priority'    => 37,
		)
	);

	$defaults = rhm_contact_defaults();
	$text     = static fn( string $key, string $label, string $type = 'text' ): array => array(
		$label,
		$type,
		'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field',
		array( 'default' => $defaults[ $key ] ),
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_contact',
		array(
			'rhm_contact_intro'      => $text( 'intro', __( 'Đoạn giới thiệu dưới tiêu đề trang', 'themeranghammatsg' ), 'textarea' ),
			'rhm_contact_form_title' => $text( 'form_title', __( 'Form – tiêu đề', 'themeranghammatsg' ) ),
			'rhm_contact_form_note'  => $text( 'form_note', __( 'Form – mô tả', 'themeranghammatsg' ), 'textarea' ),
			'rhm_contact_cta_title'  => $text( 'cta_title', __( 'Khối hotline – tiêu đề', 'themeranghammatsg' ) ),
			'rhm_contact_cta_text'   => $text( 'cta_text', __( 'Khối hotline – mô tả', 'themeranghammatsg' ), 'textarea' ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_contact' );

/**
 * Bài viết: dòng thông tin dưới tiêu đề (inc/post-meta.php) + mục lục tự động / mặc định cho shortcode [toc] (inc/toc.php).
 */
function rhm_customize_post( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_post',
		array(
			'title'       => __( 'Bài viết', 'themeranghammatsg' ),
			'description' => __( 'Dòng thông tin dưới tiêu đề bài: tích chọn mục muốn hiện (bỏ hết = ẩn cả dòng). Mục lục tự chèn ngay dưới mô tả bài viết khi bài có đủ số tiêu đề tối thiểu. Đặt mục lục ở vị trí khác: gõ [toc] trong nội dung. Ẩn mục lục 1 bài: [toc hide="yes"]. Thuộc tính khác: title="…", levels="2,3", open="no".', 'themeranghammatsg' ),
			'priority'    => 38,
		)
	);

	$meta = static fn( string $key, string $label ): array => array( $label, 'checkbox', 'rest_sanitize_boolean', array( 'default' => RHM_POST_META_DEFAULTS[ $key ] ) );

	rhm_customize_fields(
		$wp_customize,
		'rhm_post',
		array(
			'rhm_post_meta_author'  => $meta( 'author', __( 'Hiển thị tác giả', 'themeranghammatsg' ) ),
			'rhm_post_meta_date'    => $meta( 'date', __( 'Hiển thị ngày đăng', 'themeranghammatsg' ) ),
			'rhm_post_meta_updated' => $meta( 'updated', __( 'Hiển thị ngày cập nhật (khi bài được sửa sau ngày đăng)', 'themeranghammatsg' ) ),
			'rhm_post_meta_reading' => $meta( 'reading', __( 'Hiển thị thời gian đọc', 'themeranghammatsg' ) ),
			'rhm_toc_auto'   => array( __( 'Tự chèn mục lục vào bài viết', 'themeranghammatsg' ), 'checkbox', 'rest_sanitize_boolean', array( 'default' => RHM_TOC_DEFAULTS['auto'] ) ),
			'rhm_toc_title'  => array( __( 'Tiêu đề mục lục', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_toc_default_title() ) ),
			'rhm_toc_levels' => array(
				__( 'Cấp tiêu đề đưa vào mục lục', 'themeranghammatsg' ),
				'select',
				'sanitize_text_field',
				array(
					'default' => RHM_TOC_DEFAULTS['levels'],
					'choices' => array(
						'2'     => 'H2',
						'2,3'   => 'H2 – H3',
						'2,3,4' => 'H2 – H4',
					),
				),
			),
			'rhm_toc_min'    => array( __( 'Số tiêu đề tối thiểu để tự chèn', 'themeranghammatsg' ), 'number', 'absint', array( 'default' => RHM_TOC_DEFAULTS['min'], 'input_attrs' => array( 'min' => 1, 'max' => 20 ) ) ),
			'rhm_toc_open'   => array( __( 'Mở sẵn mục lục (bỏ chọn: thu gọn, bấm để mở)', 'themeranghammatsg' ), 'checkbox', 'rest_sanitize_boolean', array( 'default' => RHM_TOC_DEFAULTS['open'] ) ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_post' );
