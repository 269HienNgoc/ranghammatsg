<?php
/**
 * Trang "Liên hệ – Đặt lịch" (page-templates/tpl-lien-he.php): chữ từ Customizer → "Trang Liên hệ".
 * Hotline / email / địa chỉ / giờ làm việc / bản đồ / ID form: "Thông tin phòng khám".
 * Chi nhánh + mạng xã hội: dùng chung danh sách của "Chân trang (Footer)" (inc/footer.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chữ mặc định (dùng chung cho Customizer và giao diện). Setting: rhm_contact_{khóa}.
 */
function rhm_contact_defaults(): array {
	return array(
		'intro'      => __( 'Đội ngũ bác sĩ và tư vấn viên luôn sẵn sàng lắng nghe. Đặt lịch khám online hoặc liên hệ trực tiếp để được tư vấn và sắp xếp thời gian phù hợp nhất.', 'themeranghammatsg' ),
		'form_title' => __( 'Đặt lịch hẹn', 'themeranghammatsg' ),
		'form_note'  => __( 'Để lại thông tin, đội ngũ tư vấn sẽ gọi lại xác nhận lịch hẹn trong thời gian sớm nhất.', 'themeranghammatsg' ),
		'cta_title'  => __( 'Cần tư vấn ngay?', 'themeranghammatsg' ),
		'cta_text'   => __( 'Gọi hotline để được bác sĩ tư vấn trực tiếp về tình trạng răng của bạn.', 'themeranghammatsg' ),
	);
}

/**
 * Chữ của 1 khóa trong rhm_contact_defaults() (chưa escape).
 */
function rhm_contact_text( string $key ): string {
	return trim( (string) get_theme_mod( "rhm_contact_{$key}", rhm_contact_defaults()[ $key ] ?? '' ) );
}

/**
 * URL nhúng bản đồ: link embed nhập trong Customizer (chỉ nhận link /maps/embed hoặc output=embed — link chia sẻ thường
 * bị Google chặn hiển thị trong iframe), không có thì tự tạo từ địa chỉ phòng khám. '' = không có bản đồ.
 */
function rhm_get_contact_map_src(): string {
	$url = rhm_get_option( 'rhm_map_url' );
	if ( str_contains( $url, '/maps/embed' ) || str_contains( $url, 'output=embed' ) ) {
		return $url;
	}

	$address = trim( rhm_get_option( 'rhm_address' ) );
	return '' !== $address ? 'https://maps.google.com/maps?q=' . rawurlencode( $address ) . '&hl=vi&z=16&output=embed' : '';
}

/**
 * Link "Chỉ đường" mở Google Maps (ứng dụng trên điện thoại) tới địa chỉ.
 */
function rhm_get_directions_url( string $address ): string {
	return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address );
}
