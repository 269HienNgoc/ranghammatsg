<?php
/**
 * Bộ nút liên hệ nhanh (Gọi ngay, Messenger, Đặt lịch, Zalo, Khuyến mãi): desktop = cột nổi bên phải, mobile = thanh dưới đáy.
 * Markup: template-parts/components/floating-cta.php (gọi trong footer.php). Style: src/css/components/_floating-cta.css.
 * Customizer → "Nút liên hệ nhanh": bật/tắt, mỗi nút có nhãn, icon (bộ assets/icons/cta/), hành động, link, màu.
 *
 * Hành động "popup": mở popup đặt lịch (inc/booking-popup.php); trang không có popup (trang Liên hệ, chưa nhập ID form)
 * → thành link (link tự nhập, trống thì trang Liên hệ / Đặt lịch).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Số nút. */
const RHM_FLOATING_CTA_MAX = 5;

/** Màu nút: khóa (class floating-cta__item--<khóa>) => nhãn trong Customizer. */
const RHM_FLOATING_CTA_COLORS = array(
	'green'  => 'Xanh lá (thương hiệu)',
	'teal'   => 'Xanh ngọc đậm',
	'blue'   => 'Xanh dương (Messenger)',
	'zalo'   => 'Xanh Zalo',
	'orange' => 'Cam',
	'red'    => 'Đỏ',
);

/**
 * Giá trị mặc định từng nút (1–5).
 *
 * @return array<int, array{label:string, icon:string, action:string, link:string, color:string}>
 */
function rhm_floating_cta_defaults(): array {
	return array(
		1 => array(
			'label'  => __( 'Gọi ngay', 'themeranghammatsg' ),
			'icon'   => 'phone-ring',
			'action' => 'link',
			'link'   => '',
			'color'  => 'green',
		),
		2 => array(
			'label'  => __( 'Messenger', 'themeranghammatsg' ),
			'icon'   => 'messenger',
			'action' => 'link',
			'link'   => '',
			'color'  => 'blue',
		),
		3 => array(
			'label'  => __( 'Đặt lịch', 'themeranghammatsg' ),
			'icon'   => 'calendar',
			'action' => 'popup',
			'link'   => '',
			'color'  => 'teal',
		),
		4 => array(
			'label'  => __( 'Zalo', 'themeranghammatsg' ),
			'icon'   => 'zalo',
			'action' => 'link',
			'link'   => '',
			'color'  => 'zalo',
		),
		5 => array(
			'label'  => __( 'Khuyến mãi', 'themeranghammatsg' ),
			'icon'   => 'gift',
			'action' => 'popup',
			'link'   => '',
			'color'  => 'orange',
		),
	);
}

/**
 * Các nút hợp lệ để in. Bỏ nút thiếu nhãn, nút "link" không có link.
 * Nút "link" có icon điện thoại mà link trống → gọi hotline.
 *
 * @return array<int, array{label:string, icon:string, href:string, popup:bool, color:string, ring:bool}>
 */
function rhm_get_floating_cta_items(): array {
	if ( ! get_theme_mod( 'rhm_cta_enable', true ) ) {
		return array();
	}

	$items   = array();
	$hotline = rhm_get_option( 'rhm_hotline' );
	foreach ( rhm_floating_cta_defaults() as $i => $default ) {
		$label  = trim( rhm_get_option( "rhm_cta_{$i}_label", $default['label'] ) );
		$icon   = rhm_sanitize_icon_name( 'cta', get_theme_mod( "rhm_cta_{$i}_icon", $default['icon'] ) );
		$action = rhm_get_option( "rhm_cta_{$i}_action", $default['action'] );
		$link   = trim( rhm_get_option( "rhm_cta_{$i}_link", $default['link'] ) );
		$color  = rhm_get_option( "rhm_cta_{$i}_color", $default['color'] );
		$phone  = in_array( $icon, array( 'phone', 'phone-ring' ), true );

		if ( '' === $label ) {
			continue;
		}

		$popup = 'popup' === $action && rhm_booking_popup_enabled();
		if ( 'popup' === $action ) {
			$link = $link ?: rhm_get_booking_url();
		} elseif ( '' === $link && $phone && $hotline ) {
			$link = rhm_get_tel_href( $hotline );
		}
		if ( '' === $link ) {
			continue;
		}

		$items[] = array(
			'label' => $label,
			'icon'  => $icon,
			'href'  => $link,
			'popup' => $popup,
			'color' => isset( RHM_FLOATING_CTA_COLORS[ $color ] ) ? $color : 'green',
			'ring'  => $phone,
		);
	}

	return $items;
}

function rhm_sanitize_cta_icon( $value ): string {
	return rhm_sanitize_icon_name( 'cta', $value );
}

function rhm_sanitize_cta_action( $value ): string {
	return 'popup' === $value ? 'popup' : 'link';
}

function rhm_sanitize_cta_color( $value ): string {
	return isset( RHM_FLOATING_CTA_COLORS[ $value ] ) ? (string) $value : 'green';
}

/**
 * Customizer → "Nút liên hệ nhanh".
 */
function rhm_customize_floating_cta( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_floating_cta',
		array(
			'title'       => __( 'Nút liên hệ nhanh', 'themeranghammatsg' ),
			'description' => __( 'Desktop: cột nút nổi bên phải màn hình. Mobile: thanh nút dưới đáy. Nút để trống nhãn sẽ bị ẩn. Hành động "Mở popup đặt lịch" dùng form ở mục "Popup đặt lịch"; muốn dẫn tới trang khác thì chọn "Mở link" và nhập link. Link mẫu: tel:0901234567, https://m.me/<tên trang>, https://zalo.me/<số điện thoại>.', 'themeranghammatsg' ),
			'priority'    => 30,
		)
	);

	$fields = array(
		'rhm_cta_enable' => array( __( 'Hiện bộ nút liên hệ nhanh', 'themeranghammatsg' ), 'checkbox', 'rest_sanitize_boolean', array( 'default' => true ) ),
	);

	foreach ( rhm_floating_cta_defaults() as $i => $default ) {
		/* translators: %d: button number */
		$prefix = sprintf( __( 'Nút %d', 'themeranghammatsg' ), $i ) . ' – ';

		$fields[ "rhm_cta_{$i}_label" ]  = array( $prefix . __( 'Nhãn', 'themeranghammatsg' ), 'text', 'sanitize_text_field', array( 'default' => $default['label'] ) );
		$fields[ "rhm_cta_{$i}_icon" ]   = array( $prefix . __( 'Icon', 'themeranghammatsg' ), 'rhm-icon', 'rhm_sanitize_cta_icon', array( 'default' => $default['icon'], 'icon_set' => 'cta' ) );
		$fields[ "rhm_cta_{$i}_action" ] = array(
			$prefix . __( 'Khi bấm', 'themeranghammatsg' ),
			'select',
			'rhm_sanitize_cta_action',
			array(
				'default' => $default['action'],
				'choices' => array(
					'link'  => __( 'Mở link', 'themeranghammatsg' ),
					'popup' => __( 'Mở popup đặt lịch', 'themeranghammatsg' ),
				),
			),
		);
		$fields[ "rhm_cta_{$i}_link" ]   = array(
			$prefix . __( 'Link (với "Mở popup": link dự phòng khi trang không có popup; nút điện thoại để trống = gọi hotline)', 'themeranghammatsg' ),
			'text',
			'esc_url_raw',
			array( 'default' => $default['link'] ),
		);
		$fields[ "rhm_cta_{$i}_color" ]  = array(
			$prefix . __( 'Màu', 'themeranghammatsg' ),
			'select',
			'rhm_sanitize_cta_color',
			array(
				'default' => $default['color'],
				'choices' => RHM_FLOATING_CTA_COLORS,
			),
		);
	}

	rhm_customize_fields( $wp_customize, 'rhm_floating_cta', $fields );
}
add_action( 'customize_register', 'rhm_customize_floating_cta' );
