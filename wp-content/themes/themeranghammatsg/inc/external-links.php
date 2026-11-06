<?php
/**
 * Link trỏ ra ngoài website: luôn có target="_blank" + rel="nofollow". Link nội bộ giữ nguyên.
 *
 * - Template PHP: in thêm rhm_external_link_attrs( $url ) vào thẻ <a> (không viết target/rel cứng).
 * - Chuỗi HTML (nội dung bài, câu trả lời FAQ…): rhm_external_links( $html ).
 * - Tự áp cho the_content (bài viết, trang, hồ sơ bác sĩ) và mục menu "Liên kết tự tạo".
 * - Domain khác muốn coi là nội bộ: filter 'rhm_internal_hosts'.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Host bỏ "www.", chữ thường. */
function rhm_normalize_host( string $host ): string {
	$host = strtolower( trim( $host ) );
	return str_starts_with( $host, 'www.' ) ? substr( $host, 4 ) : $host;
}

/**
 * Host được coi là nội bộ (host của home_url + filter rhm_internal_hosts).
 *
 * @return string[]
 */
function rhm_internal_hosts(): array {
	static $hosts = null;
	if ( null === $hosts ) {
		$list  = array( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
		$list  = (array) apply_filters( 'rhm_internal_hosts', $list );
		$hosts = array_values( array_unique( array_filter( array_map( 'rhm_normalize_host', array_map( 'strval', $list ) ) ) ) );
	}
	return $hosts;
}

/**
 * Link tuyệt đối (http/https hoặc //host) trỏ tới host không thuộc website.
 * Link tương đối, #anchor, tel:, mailto:, javascript: → không phải external.
 */
function rhm_is_external_url( string $url ): bool {
	$url = trim( html_entity_decode( $url, ENT_QUOTES ) );
	if ( ! preg_match( '~^(?:https?:)?//~i', $url ) ) {
		return false;
	}
	$host = (string) wp_parse_url( str_starts_with( $url, '//' ) ? 'https:' . $url : $url, PHP_URL_HOST );
	return '' !== $host && ! in_array( rhm_normalize_host( $host ), rhm_internal_hosts(), true );
}

/** Thuộc tính thêm vào thẻ <a> trong template: ' target="_blank" rel="nofollow"' khi external, '' khi nội bộ. */
function rhm_external_link_attrs( string $url ): string {
	return rhm_is_external_url( $url ) ? ' target="_blank" rel="nofollow"' : '';
}

/** Gộp token rel cũ + nofollow (giữ sponsored, ugc, noopener…, không trùng). */
function rhm_external_rel( string $rel ): string {
	$tokens = preg_split( '/\s+/', strtolower( trim( $rel ) ), -1, PREG_SPLIT_NO_EMPTY ) ?: array();
	$tokens[] = 'nofollow';
	return implode( ' ', array_unique( $tokens ) );
}

/** Thêm target="_blank" + rel="nofollow" cho mọi <a href> external trong chuỗi HTML. */
function rhm_external_links( string $html ): string {
	if ( ! str_contains( $html, '<a ' ) && ! str_contains( $html, '<A ' ) ) {
		return $html;
	}
	$tags = new WP_HTML_Tag_Processor( $html );
	while ( $tags->next_tag( 'a' ) ) {
		$href = $tags->get_attribute( 'href' );
		if ( ! is_string( $href ) || ! rhm_is_external_url( $href ) ) {
			continue;
		}
		$rel = $tags->get_attribute( 'rel' );
		$tags->set_attribute( 'target', '_blank' );
		$tags->set_attribute( 'rel', rhm_external_rel( is_string( $rel ) ? $rel : '' ) );
	}
	return $tags->get_updated_html();
}
// Sau shortcode (11), mục lục (12), bọc bảng (20).
add_filter( 'the_content', 'rhm_external_links', 25 );

/** Mục menu "Liên kết tự tạo" trỏ ra ngoài. */
function rhm_external_menu_link( array $atts ): array {
	if ( ! empty( $atts['href'] ) && rhm_is_external_url( (string) $atts['href'] ) ) {
		$atts['target'] = '_blank';
		$atts['rel']    = rhm_external_rel( (string) ( $atts['rel'] ?? '' ) );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'rhm_external_menu_link' );
