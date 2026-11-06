<?php
/**
 * Dịch vụ nổi bật trang chủ: hàng icon + tiêu đề + link dưới hero banner.
 * Nhập ở Customizer → "Dịch vụ nổi bật (Trang chủ)". Hiển thị: template-parts/sections/quick-services.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_QUICK_SERVICES_MAX = 8;

/**
 * Mục hợp lệ (có tiêu đề) theo thứ tự trong Customizer. title có thể nhiều dòng;
 * icon = URL icon (bộ icon theme hoặc ảnh tải lên; rỗng nếu chưa chọn).
 *
 * @return array<int, array{title:string, link:string, icon:string}>
 */
function rhm_get_quick_services(): array {
	$items = array();

	for ( $i = 1; $i <= RHM_QUICK_SERVICES_MAX; $i++ ) {
		$title = trim( (string) get_theme_mod( "rhm_qs_{$i}_title", '' ) );
		if ( '' === $title ) {
			continue;
		}

		// Ưu tiên icon chọn trong bộ icon theme (assets/icons/services, có thể qua CDN), sau đó mới tới ảnh tải lên.
		$icon = rhm_icon_url( 'services', (string) get_theme_mod( "rhm_qs_{$i}_icon_name", '' ) );
		if ( '' === $icon ) {
			$icon_id = absint( get_theme_mod( "rhm_qs_{$i}_icon" ) );
			$icon    = str_starts_with( (string) get_post_mime_type( $icon_id ), 'image/' ) ? (string) wp_get_attachment_url( $icon_id ) : '';
		}

		$items[] = array(
			'title' => $title,
			'link'  => (string) get_theme_mod( "rhm_qs_{$i}_link", '' ),
			'icon'  => $icon,
		);
	}

	return $items;
}
