<?php
/**
 * Định dạng nội dung soạn trong trình soạn thảo (the_content). CSS: src/css/components/_entry-content.css.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bọc mỗi <table> (vd bảng của plugin Advanced Editor Tools) trong <div class="rhm-table">:
 * khung bo góc + cuộn ngang TRONG khung trên mobile (trang không bị kéo ngang), <table> vẫn là display: table
 * nên độ rộng cột của trình soạn thảo (width: 25%…) giữ nguyên.
 * tabindex + role/aria-label: khung cuộn được bằng bàn phím (tiêu chí trợ năng của Lighthouse "scrollable-region-focusable").
 */
function rhm_wrap_tables( string $content ): string {
	if ( ! str_contains( $content, '<table' ) ) {
		return $content;
	}

	return (string) preg_replace(
		'~<table\b.*?</table>~is',
		'<div class="rhm-table" role="region" aria-label="' . esc_attr__( 'Bảng (vuốt ngang để xem thêm)', 'themeranghammatsg' ) . '" tabindex="0">$0</div>',
		$content
	);
}
add_filter( 'the_content', 'rhm_wrap_tables', 20 );
