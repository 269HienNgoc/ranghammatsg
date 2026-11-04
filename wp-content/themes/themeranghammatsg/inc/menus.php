<?php
/**
 * Tùy biến menu chính: icon theo CSS class + mục "Trang chủ" dạng icon.
 *
 * Gắn icon: Giao diện → Menu → Tùy chọn hiển thị → bật "Lớp CSS",
 * nhập class icon-<tên> cho mục menu. Tên hợp lệ: khóa của rhm_menu_icons() trong inc/icons.php
 * (implant, braces, crown, tooth, price, consult, user, contact).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chèn SVG trước tiêu đề mục menu có class icon-<tên> (chỉ menu primary). Desktop và mobile hiển thị giống nhau.
 */
function rhm_menu_item_icon( string $title, WP_Post $item, stdClass $args ): string {
	if ( 'primary' !== ( $args->theme_location ?? '' ) ) {
		return $title;
	}

	$menu_icons = rhm_menu_icons();

	foreach ( (array) $item->classes as $class ) {
		$name = str_starts_with( (string) $class, 'icon-' ) ? substr( $class, 5 ) : '';
		if ( isset( $menu_icons[ $name ] ) ) {
			return rhm_get_icon( $name, 'menu-icon' ) . '<span class="menu-text">' . $title . '</span>';
		}
	}

	return $title;
}
add_filter( 'nav_menu_item_title', 'rhm_menu_item_icon', 10, 3 );

/**
 * Mục có menu con (menu primary): thêm nút mũi tên ngay sau link để mở/đóng menu con.
 * - Desktop: hover/focus tự mở; nút dùng cho bàn phím và màn hình cảm ứng.
 * - Mobile: menu con đóng sẵn, bấm nút để mở (accordion) — xử lý trong src/js/main.js.
 * Không thêm nút ở cấp sâu nhất theo 'depth' (vì menu con ở đó không được in ra).
 */
function rhm_menu_item_submenu_toggle( string $item_output, WP_Post $item, int $depth, stdClass $args ): string {
	if ( 'primary' !== ( $args->theme_location ?? '' ) || ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		return $item_output;
	}

	$max_depth = (int) ( $args->depth ?? 0 );
	if ( $max_depth > 0 && $depth + 1 >= $max_depth ) {
		return $item_output;
	}

	$label = sprintf(
		/* translators: %s: menu item title */
		__( 'Mở menu con: %s', 'themeranghammatsg' ),
		wp_strip_all_tags( $item->title )
	);

	return $item_output . sprintf(
		'<button class="submenu-toggle" type="button" aria-expanded="false" aria-label="%1$s">%2$s</button>',
		esc_attr( $label ),
		rhm_get_icon( 'chevron' )
	);
}
add_filter( 'walker_nav_menu_start_el', 'rhm_menu_item_submenu_toggle', 10, 4 );

/**
 * <li> Trang chủ (icon) đặt đầu menu chính, dùng làm items_wrap cho wp_nav_menu.
 */
function rhm_primary_menu_items_wrap(): string {
	$home = sprintf(
		'<li class="menu-item menu-item-home%1$s"><a href="%2$s"%3$s>%4$s<span class="menu-text menu-text--home">%5$s</span></a></li>',
		is_front_page() ? ' current-menu-item' : '',
		esc_url( home_url( '/' ) ),
		is_front_page() ? ' aria-current="page"' : '',
		rhm_get_icon( 'home', 'menu-icon' ),
		esc_html__( 'Trang chủ', 'themeranghammatsg' )
	);

	// items_wrap là chuỗi sprintf: escape mọi ký tự % có sẵn.
	return '<ul id="%1$s" class="%2$s">' . str_replace( '%', '%%', $home ) . '%3$s</ul>';
}
