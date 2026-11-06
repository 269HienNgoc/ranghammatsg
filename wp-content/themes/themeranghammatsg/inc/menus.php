<?php
/**
 * Tùy biến menu chính: icon cho từng mục + mục "Trang chủ" dạng icon.
 *
 * Gắn icon: Giao diện → Menu → mở mục menu → "Icon (menu chính)" → bấm chọn icon.
 * Icon lấy từ thư viện assets/icons/menu/ (inc/icon-library.php), in inline nên đổi màu theo CSS.
 * Cách cũ vẫn dùng được: Lớp CSS "icon-<tên file>" (vd icon-implant). Ô chọn icon được ưu tiên.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Meta lưu tên icon của mục menu. */
const RHM_MENU_ICON_META = '_rhm_menu_icon';

/** Tên icon của mục menu: ô chọn icon → class icon-<tên> → ''. */
function rhm_menu_item_icon_name( WP_Post $item ): string {
	$name = (string) get_post_meta( $item->ID, RHM_MENU_ICON_META, true );
	if ( rhm_icon_exists( 'menu', $name ) ) {
		return $name;
	}
	foreach ( (array) $item->classes as $class ) {
		$name = str_starts_with( (string) $class, 'icon-' ) ? substr( $class, 5 ) : '';
		if ( rhm_icon_exists( 'menu', $name ) ) {
			return $name;
		}
	}
	return '';
}

/**
 * Chèn SVG trước tiêu đề mục menu có icon (chỉ menu primary). Desktop và mobile hiển thị giống nhau.
 */
function rhm_menu_item_icon( string $title, WP_Post $item, stdClass $args ): string {
	if ( 'primary' !== ( $args->theme_location ?? '' ) ) {
		return $title;
	}

	$name = rhm_menu_item_icon_name( $item );

	return $name ? rhm_icon_svg( 'menu', $name, 'menu-icon' ) . '<span class="menu-text">' . $title . '</span>' : $title;
}
add_filter( 'nav_menu_item_title', 'rhm_menu_item_icon', 10, 3 );

/**
 * Ô chọn icon trong Giao diện → Menu (mỗi mục menu): lưới icon dạng radio, có ô "Không icon".
 */
function rhm_menu_icon_field( int $item_id, WP_Post $item ): void {
	$current = (string) get_post_meta( $item_id, RHM_MENU_ICON_META, true );
	$icons   = rhm_icon_list( 'menu' );
	if ( ! $icons ) {
		return;
	}
	$field = 'rhm_menu_icon[' . $item_id . ']';
	?>
	<fieldset class="field-rhm-icon description description-wide">
		<legend><?php esc_html_e( 'Icon (menu chính)', 'themeranghammatsg' ); ?></legend>
		<div class="rhm-icon-grid">
			<label title="<?php esc_attr_e( 'Không icon', 'themeranghammatsg' ); ?>">
				<input type="radio" name="<?php echo esc_attr( $field ); ?>" value="" <?php checked( '', $current ); ?>>
				<span class="rhm-icon-none">&times;</span>
			</label>
			<?php foreach ( $icons as $name => $label ) : ?>
				<label title="<?php echo esc_attr( $label ); ?>">
					<input type="radio" name="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( $name ); ?>" <?php checked( $name, $current ); ?>>
					<?php echo rhm_icon_svg( 'menu', $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
				</label>
			<?php endforeach; ?>
		</div>
	</fieldset>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'rhm_menu_icon_field', 10, 2 );

/**
 * Lưu icon khi bấm "Lưu menu". WordPress đã kiểm tra nonce update-nav_menu trước khi gọi hook này.
 * Chỉ lưu khi form có gửi ô icon (Customizer không có ô này → giữ nguyên giá trị cũ).
 */
function rhm_menu_icon_save( int $menu_id, int $item_id ): void {
	if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $_POST['rhm_menu_icon'][ $item_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce do nav-menus.php kiểm tra.
		return;
	}
	$name = rhm_sanitize_icon_name( 'menu', wp_unslash( $_POST['rhm_menu_icon'][ $item_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- như trên; sanitize trong rhm_sanitize_icon_name().
	if ( $name ) {
		update_post_meta( $item_id, RHM_MENU_ICON_META, $name );
	} else {
		delete_post_meta( $item_id, RHM_MENU_ICON_META );
	}
}
add_action( 'wp_update_nav_menu_item', 'rhm_menu_icon_save', 10, 2 );

/** CSS lưới chọn icon (chỉ trang Giao diện → Menu). */
function rhm_menu_icon_admin_css(): void {
	?>
	<style>
		.field-rhm-icon { margin: 8px 0 12px; }
		.field-rhm-icon legend { margin-bottom: 6px; }
		.rhm-icon-grid { display: flex; flex-wrap: wrap; gap: 6px; }
		.rhm-icon-grid label { position: relative; display: flex; width: 36px; height: 36px; align-items: center; justify-content: center; border: 1px solid #dcdcde; border-radius: 6px; background: #fff; color: #12a14a; cursor: pointer; }
		.rhm-icon-grid input { position: absolute; opacity: 0; pointer-events: none; }
		.rhm-icon-grid svg { width: 22px; height: 22px; }
		.rhm-icon-grid .rhm-icon-none { font-size: 20px; line-height: 1; color: #8c8f94; }
		.rhm-icon-grid label:hover { border-color: #0b7a55; }
		.rhm-icon-grid label:has(input:checked) { border-color: #0b7a55; background: #ecf7f3; box-shadow: 0 0 0 1px #0b7a55; }
		.rhm-icon-grid label:has(input:focus-visible) { outline: 2px solid #2271b1; outline-offset: 1px; }
	</style>
	<?php
}
add_action( 'admin_head-nav-menus.php', 'rhm_menu_icon_admin_css' );

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
