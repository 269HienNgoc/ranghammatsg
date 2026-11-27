<?php
/**
 * Trang "Liên hệ – Đặt lịch" (page-templates/tpl-lien-he.php): chữ từ Customizer → "Trang Liên hệ".
 * Hotline / email / địa chỉ / giờ làm việc / ID form: "Thông tin phòng khám".
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chữ mặc định (dùng chung cho Customizer và giao diện). Setting: rhm_contact_{khóa}.
 * 'form_note' còn dùng ở khối đặt lịch hồ sơ bác sĩ (template-parts/doctor/booking.php).
 */
function rhm_contact_defaults(): array {
	return array(
		'hero_eyebrow' => __( 'Kết nối cùng chúng tôi', 'themeranghammatsg' ),
		'intro'        => __( 'Chúng tôi luôn sẵn sàng lắng nghe, giải đáp thắc mắc và đồng hành cùng bạn trên hành trình chăm sóc nụ cười.', 'themeranghammatsg' ),
		'info_eyebrow' => __( 'Thông tin liên hệ', 'themeranghammatsg' ),
		'info_title'   => __( 'Chúng tôi luôn ở đây vì bạn', 'themeranghammatsg' ),
		'info_text'    => __( 'Để lại thông tin hoặc liên hệ trực tiếp với phòng khám để được hỗ trợ nhanh chóng và tận tình.', 'themeranghammatsg' ),
		'card_title'   => __( 'Kết nối với nha khoa', 'themeranghammatsg' ),
		'card_text'    => __( 'Liên hệ với đội ngũ tư vấn qua các kênh dưới đây.', 'themeranghammatsg' ),
		'card_note'    => __( 'Vui lòng đặt lịch trước để được phòng khám sắp xếp thời gian tư vấn phù hợp.', 'themeranghammatsg' ),
		'form_title'   => __( 'Gửi yêu cầu tư vấn', 'themeranghammatsg' ),
		'form_note'    => __( 'Để lại thông tin, đội ngũ tư vấn sẽ gọi lại xác nhận lịch hẹn trong thời gian sớm nhất.', 'themeranghammatsg' ),
		'form_privacy' => __( 'Thông tin của bạn chỉ được dùng để liên hệ tư vấn.', 'themeranghammatsg' ),
	);
}

/**
 * Chữ của 1 khóa trong rhm_contact_defaults() (chưa escape).
 */
function rhm_contact_text( string $key ): string {
	return trim( (string) get_theme_mod( "rhm_contact_{$key}", rhm_contact_defaults()[ $key ] ?? '' ) );
}
