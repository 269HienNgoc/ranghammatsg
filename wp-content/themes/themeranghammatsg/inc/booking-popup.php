<?php
/**
 * Popup đặt lịch: <dialog> chứa form Fluent, in cuối trang (template-parts/components/booking-popup.php, gọi trong footer.php).
 * Mở bằng mọi phần tử có data-rhm-popup="booking" (nút rhm_booking_button(), nút liên hệ nhanh) — src/js/main.js.
 * Customizer → "Popup đặt lịch": ID form (trống = ID form đặt lịch ở "Thông tin phòng khám"), tiêu đề, mô tả.
 *
 * Trang Liên hệ đã có sẵn form → không in popup; nút đặt lịch trên trang đó cuộn tới form (#dat-lich).
 * Form trong popup tải JS kiểu "chạm mới tải" như mọi form Fluent (inc/plugin/fluentform.php): main.js focus ô đầu tiên khi mở
 * → fluent-lazy.js nhận focusin và tải jQuery + Fluent.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID form Fluent của popup (0 = không có).
 */
function rhm_booking_popup_form_id(): int {
	$id = absint( get_theme_mod( 'rhm_popup_form_id', 0 ) );
	return $id ?: absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
}

/**
 * Trang hiện tại có popup đặt lịch không: có ID form, Fluent đang bật, không phải trang Liên hệ, không phải admin,
 * không phải trang landing có section "form" dùng cùng form (rhm_landing_booking_form_anchor(): tránh trùng id HTML của Fluent).
 */
function rhm_booking_popup_enabled(): bool {
	static $enabled = null;
	if ( null === $enabled ) {
		$enabled = ! is_admin()
			&& rhm_booking_popup_form_id() > 0
			&& shortcode_exists( 'fluentform' )
			&& ! is_page_template( 'page-templates/tpl-lien-he.php' )
			&& ! ( function_exists( 'rhm_landing_booking_form_anchor' ) && '' !== rhm_landing_booking_form_anchor() );
	}
	return $enabled;
}

/**
 * Chữ trên popup (Customizer, có mặc định).
 */
function rhm_booking_popup_text( string $key ): string {
	$defaults = array(
		'title' => __( 'Đặt lịch hẹn', 'themeranghammatsg' ),
		'desc'  => __( 'Để lại thông tin, tư vấn viên sẽ gọi lại cho bạn trong ít phút.', 'themeranghammatsg' ),
	);
	return rhm_get_option( 'rhm_popup_' . $key, $defaults[ $key ] ?? '' );
}

/**
 * Trang Liên hệ: nút đặt lịch cuộn tới form ngay trên trang (không tải lại chính trang đó).
 */
function rhm_contact_booking_url( string $url ): string {
	return is_page_template( 'page-templates/tpl-lien-he.php' ) ? '#dat-lich' : $url;
}
add_filter( 'rhm_booking_url', 'rhm_contact_booking_url' );

/**
 * Customizer → "Popup đặt lịch".
 */
function rhm_customize_booking_popup( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_booking_popup',
		array(
			'title'       => __( 'Popup đặt lịch', 'themeranghammatsg' ),
			'description' => __( 'Popup mở khi bấm nút "Đặt lịch hẹn" ở header và các nút liên hệ nhanh chọn hành động "Mở popup". Cần plugin Fluent Forms.', 'themeranghammatsg' ),
			'priority'    => 30,
		)
	);

	rhm_customize_fields(
		$wp_customize,
		'rhm_booking_popup',
		array(
			'rhm_popup_form_id' => array( __( 'ID form đăng ký (Fluent Forms → cột ID). Trống: dùng ID form đặt lịch ở "Thông tin phòng khám"', 'themeranghammatsg' ), 'number', 'absint' ),
			'rhm_popup_title'   => array( __( 'Tiêu đề popup', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => rhm_booking_popup_text( 'title' ) ) ),
			'rhm_popup_desc'    => array( __( 'Mô tả dưới tiêu đề', 'themeranghammatsg' ), 'textarea', 'sanitize_textarea_field', array( 'default' => rhm_booking_popup_text( 'desc' ) ) ),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_booking_popup' );
