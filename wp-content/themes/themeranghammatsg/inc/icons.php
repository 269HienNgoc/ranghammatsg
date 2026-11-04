<?php
/**
 * Bộ icon SVG inline (màu theo currentColor).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon gắn được vào mục menu chính qua lớp CSS "icon-<tên>" (Giao diện → Menu → Lớp CSS).
 * Kiểu tô đặc, đồng bộ với nhau.
 */
function rhm_menu_icons(): array {
	$tooth = 'M12 5.6C10.4 4 8.6 3.5 7 3.8 4.6 4.3 3.5 6.6 4 9.3c.4 2.2 1.4 3.6 1.8 5.6.5 2.6.8 5.6 2.4 5.6 1.5 0 1.6-3.4 2.3-5.1.3-.8.8-1.2 1.5-1.2s1.2.4 1.5 1.2c.7 1.7.8 5.1 2.3 5.1 1.6 0 1.9-3 2.4-5.6.4-2 1.4-3.4 1.8-5.6.5-2.7-.6-5-3-5.5-1.6-.3-3.4.2-5 1.8z';

	return array(
		'implant' => '<path fill="currentColor" d="M7.2 2.3c1.6-.4 3.2.1 4.8 1 1.6-.9 3.2-1.4 4.8-1 2.2.6 3.1 2.8 2.5 5-.3 1.3-1.1 2.1-2.5 2.4H7.2C5.8 9.4 5 8.6 4.7 7.3c-.6-2.2.3-4.4 2.5-5zM9.8 10.6h4.4v2.2H9.8zM9 13.8h6l-.6 1.8H9.6zM9.6 16.6h4.8l-.6 1.8h-3.6zM10.2 19.4h3.6L12 22.2z"/>',
		'braces'  => '<path fill="currentColor" fill-rule="evenodd" d="' . $tooth . 'M2 9h20v1.8H2z"/><path fill="currentColor" d="M9.6 7.2h4.8v5.4H9.6z"/>',
		'crown'   => '<path fill="currentColor" d="M3.5 3.5 7.6 7 12 2l4.4 5 4.1-3.5-1.4 6.5H4.9zM5.4 12h13.2c-.2 1.5-.9 2.6-1.3 4.2-.5 2.4-.8 5.3-2.3 5.3-1.4 0-1.5-3-2.2-4.6-.2-.6-.5-.9-.8-.9s-.6.3-.8.9c-.7 1.6-.8 4.6-2.2 4.6-1.5 0-1.8-2.9-2.3-5.3-.4-1.6-1.1-2.7-1.3-4.2z"/>',
		'tooth'   => '<path fill="currentColor" d="' . $tooth . '"/>',
		'price'   => '<path fill="currentColor" d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm2 8v2h8v-2zm0 4v2h8v-2zm0-8v2h5V6z"/>',
		'consult' => '<path fill="currentColor" d="M9 2h6a1 1 0 0 1 1 1v1h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V3a1 1 0 0 1 1-1zm1 2v2h4V4zM8 10v2h8v-2zm0 4v2h6v-2z"/>',
		'user'    => '<circle cx="12" cy="7.5" r="4.5" fill="currentColor"/><path fill="currentColor" d="M3 21c0-4.4 4-7.5 9-7.5s9 3.1 9 7.5z"/>',
		'contact' => '<path fill="currentColor" d="M6.6 2.5h3.1l1.7 4.4-2.3 1.6a11.6 11.6 0 0 0 6.4 6.4l1.6-2.3 4.4 1.7v3.1a2.6 2.6 0 0 1-2.8 2.6C10 19.4 4.6 14 4 6.9 3.8 5.4 5 2.5 6.6 2.5z"/>',
	);
}

/**
 * Icon giao diện dùng trực tiếp trong template (header, tìm kiếm, nút…).
 */
function rhm_ui_icons(): array {
	return array(
		'home'        => '<path fill="currentColor" d="M12 2.6 1.5 11.4l1.3 1.5L4 11.9V21a1 1 0 0 0 1 1h5v-6h4v6h5a1 1 0 0 0 1-1v-9.1l1.2 1 1.3-1.5L17 7.6V4h-2.5v1.5z"/>',
		'phone'       => '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" d="M5 3.5h3.2l1.6 4.2-2.1 1.5a12 12 0 0 0 5.6 5.6l1.5-2.1 4.2 1.6V17a2.5 2.5 0 0 1-2.7 2.5C9.6 19 3.8 13.2 3.5 6.2A2.5 2.5 0 0 1 5 3.5z"/><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M14.5 3.5a6 6 0 0 1 6 6M14.5 7a2.6 2.6 0 0 1 2.5 2.5"/>',
		'search'      => '<circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2.4"/><path d="m16.5 16.5 5 5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>',
		'chevron'     => '<path fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>',
		'calendar'    => '<g fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5" width="17" height="15.5" rx="2.5"/><path d="M3.5 10h17M8 3v4M16 3v4M9 15l2 2 4-4"/></g>',
		'arrow-right' => '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>',
		'play'        => '<path fill="currentColor" d="M8 5.2v13.6a.8.8 0 0 0 1.2.7l10.6-6.8a.8.8 0 0 0 0-1.4L9.2 4.5A.8.8 0 0 0 8 5.2z"/>',
		'folder'      => '<path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" d="M3.5 6.5A1.5 1.5 0 0 1 5 5h4.2l2 2.2H19a1.5 1.5 0 0 1 1.5 1.5v9.3A1.5 1.5 0 0 1 19 19.5H5A1.5 1.5 0 0 1 3.5 18z"/>',
		'close'       => '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>',
		'document'    => '<g fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6.5A1.5 1.5 0 0 0 5 4.5v15A1.5 1.5 0 0 0 6.5 21h11a1.5 1.5 0 0 0 1.5-1.5V8z"/><path d="M14 3v5h5M8.5 12.5h7m-7 4h7m-7-8h2"/></g>',
	);
}

/**
 * Trả về SVG của icon (chuỗi hằng trong theme, an toàn để echo). Tên không tồn tại → chuỗi rỗng.
 */
function rhm_get_icon( string $name, string $class = '' ): string {
	$icons = rhm_ui_icons() + rhm_menu_icons();

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="%1$s" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">%2$s</svg>',
		esc_attr( trim( 'icon icon-' . $name . ' ' . $class ) ),
		$icons[ $name ]
	);
}
