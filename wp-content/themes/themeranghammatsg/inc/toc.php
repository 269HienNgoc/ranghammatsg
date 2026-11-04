<?php
/**
 * Mục lục bài viết (TOC). CSS: src/css/components/_toc.css (import vào single-post.css, page.css).
 *
 * - Mặc định (Customizer → "Bài viết"): bài viết có ≥ N tiêu đề → tự chèn mục lục ở ĐẦU nội dung,
 *   tức ngay dưới mô tả bài viết (excerpt, in trong single.php).
 * - Shortcode [toc] đặt mục lục ở vị trí tùy ý (bài viết hoặc trang); khi có shortcode thì không tự chèn nữa.
 *   Thuộc tính: title="…" (tiêu đề), levels="2,3" (cấp tiêu đề), open="no" (thu gọn sẵn), hide="yes" (ẩn mục lục bài này).
 *
 * Cách chạy: shortcode chỉ in dấu RHM_TOC_MARKER (do_shortcode chạy ở ưu tiên 11, lúc đó chưa biết tiêu đề);
 * filter the_content ưu tiên 12 quét tiêu đề trong HTML cuối, gắn id (giữ id có sẵn), dựng danh sách lồng nhau rồi thay dấu.
 * Mục lục là link neo thật trong HTML (Google đọc được, có thể hiện "Chuyển đến" trên kết quả tìm kiếm); thu gọn bằng
 * <details> → không cần JS. Cuộn mượt + chừa chỗ header dính: base.css (scroll-smooth, scroll-padding-top).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_TOC_MARKER = '<!--rhm-toc-->';

/** Mặc định (dùng chung cho Customizer, shortcode và giao diện). */
const RHM_TOC_DEFAULTS = array(
	'auto'   => true,
	'levels' => '2,3',
	'min'    => 3,
	'open'   => true,
);

function rhm_toc_default_title(): string {
	return __( 'Mục lục', 'themeranghammatsg' );
}

/**
 * Tùy chọn hiện hành = Customizer, ghi đè bởi thuộc tính shortcode [toc] (nếu có trong bài).
 * Gọi với $atts: lưu thuộc tính của shortcode cho filter dùng.
 *
 * @return array{title:string, levels:int[], min:int, open:bool, auto:bool, hide:bool}
 */
function rhm_toc_options( ?array $atts = null ): array {
	static $shortcode = array();
	if ( null !== $atts ) {
		$shortcode = $atts;
	}

	$levels = (string) ( $shortcode['levels'] ?? get_theme_mod( 'rhm_toc_levels', RHM_TOC_DEFAULTS['levels'] ) );
	$levels = array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $levels ) ), static fn( int $l ): bool => $l >= 2 && $l <= 6 ) ) );
	sort( $levels );

	$open = $shortcode['open'] ?? null;

	return array(
		'title'  => trim( (string) ( $shortcode['title'] ?? get_theme_mod( 'rhm_toc_title', rhm_toc_default_title() ) ) ),
		'levels' => $levels ?: array( 2, 3 ),
		'min'    => max( 1, absint( get_theme_mod( 'rhm_toc_min', RHM_TOC_DEFAULTS['min'] ) ) ),
		'open'   => null === $open ? (bool) get_theme_mod( 'rhm_toc_open', RHM_TOC_DEFAULTS['open'] ) : ! in_array( strtolower( (string) $open ), array( 'no', '0', 'false' ), true ),
		'auto'   => (bool) get_theme_mod( 'rhm_toc_auto', RHM_TOC_DEFAULTS['auto'] ),
		'hide'   => in_array( strtolower( (string) ( $shortcode['hide'] ?? '' ) ), array( 'yes', '1', 'true' ), true ),
	);
}

/**
 * [toc title="" levels="2,3" open="yes|no" hide="yes"] → dấu vị trí; filter rhm_toc_content() thay bằng mục lục.
 */
function rhm_toc_shortcode( $atts ): string {
	rhm_toc_options( array_intersect_key( (array) $atts, array_flip( array( 'title', 'levels', 'open', 'hide' ) ) ) );
	return RHM_TOC_MARKER;
}
add_shortcode( 'toc', 'rhm_toc_shortcode' );

/**
 * Quét tiêu đề cấp $levels trong HTML, gắn id (giữ id có sẵn, tạo id không dấu từ chữ, không trùng).
 *
 * @param int[] $levels
 * @param array<int, array{level:int, id:string, text:string}> $headings Kết quả (truyền tham chiếu).
 */
function rhm_toc_anchor_headings( string $html, array $levels, array &$headings ): string {
	$used = array();

	return (string) preg_replace_callback(
		'~<h([2-6])(\s[^>]*)?>(.*?)</h\1>~is',
		static function ( array $m ) use ( $levels, &$headings, &$used ): string {
			$level = (int) $m[1];
			$text  = trim( html_entity_decode( wp_strip_all_tags( $m[3] ), ENT_QUOTES, 'UTF-8' ) );
			if ( ! in_array( $level, $levels, true ) || '' === $text ) {
				return $m[0];
			}

			$attrs = $m[2] ?? '';
			if ( preg_match( '~\sid=(["\'])(.*?)\1~i', $attrs, $id_match ) ) {
				$id = $id_match[2];
			} else {
				$base = sanitize_title( $text ) ?: 'muc';
				$id   = $base;
				for ( $i = 2; isset( $used[ $id ] ); $i++ ) {
					$id = $base . '-' . $i;
				}
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}
			$used[ $id ] = true;

			$headings[] = array(
				'level' => $level,
				'id'    => $id,
				'text'  => $text,
			);

			return '<h' . $level . $attrs . '>' . $m[3] . '</h' . $level . '>';
		},
		$html
	);
}

/**
 * Danh sách lồng nhau theo cấp tiêu đề (cấp nhỏ nhất trong $levels là cấp 1 của mục lục).
 *
 * @param array<int, array{level:int, id:string, text:string}> $headings
 */
function rhm_toc_list( array $headings ): string {
	$html  = '';
	$depth = 0;
	$base  = min( array_column( $headings, 'level' ) );

	foreach ( $headings as $index => $heading ) {
		$level = $heading['level'] - $base + 1;
		if ( 0 === $index ) {
			$html .= '<ol class="rhm-toc__list">';
			$depth = 1;
		} elseif ( $level > $depth ) {
			// Nhảy nhiều cấp (vd H2 → H4) vẫn chỉ lồng 1 cấp để danh sách gọn.
			$html .= '<ol>';
			++$depth;
		} else {
			$html .= '</li>';
			for ( ; $depth > max( 1, $level ); --$depth ) {
				$html .= '</ol></li>';
			}
		}
		$html .= '<li><a href="#' . esc_attr( $heading['id'] ) . '">' . esc_html( $heading['text'] ) . '</a>';
	}

	return $html . '</li>' . str_repeat( '</ol></li>', $depth - 1 ) . '</ol>';
}

/**
 * Hộp mục lục.
 *
 * @param array<int, array{level:int, id:string, text:string}> $headings
 */
function rhm_toc_box( array $headings, array $options ): string {
	$title = '' !== $options['title'] ? $options['title'] : rhm_toc_default_title();

	return sprintf(
		'<nav class="rhm-toc" aria-label="%1$s"><details%2$s><summary class="rhm-toc__head">%3$s<span class="rhm-toc__title">%4$s</span>%5$s</summary>%6$s</details></nav>',
		esc_attr( $title ),
		$options['open'] ? ' open' : '',
		rhm_get_icon( 'list', 'rhm-toc__icon' ),
		esc_html( $title ),
		rhm_get_icon( 'chevron', 'rhm-toc__chevron' ),
		rhm_toc_list( $headings )
	);
}

/**
 * Chèn mục lục vào nội dung: thay dấu [toc], hoặc tự chèn đầu bài viết (chỉ bài viết, vòng lặp chính).
 */
function rhm_toc_content( string $content ): string {
	$has_marker = str_contains( $content, RHM_TOC_MARKER );
	$is_main    = is_singular() && in_the_loop() && is_main_query();
	$options    = rhm_toc_options();
	$auto       = ! $has_marker && $options['auto'] && $is_main && is_singular( 'post' );

	if ( ( ! $has_marker && ! $auto ) || $options['hide'] ) {
		return $has_marker ? rhm_toc_remove_marker( $content, '' ) : $content;
	}

	$headings = array();
	$anchored = rhm_toc_anchor_headings( $content, $options['levels'], $headings );

	// Tự chèn cần đủ số tiêu đề tối thiểu; shortcode đặt tay chỉ cần có tiêu đề.
	if ( ! $headings || ( $auto && count( $headings ) < $options['min'] ) ) {
		return $has_marker ? rhm_toc_remove_marker( $content, '' ) : $content;
	}

	$box = rhm_toc_box( $headings, $options );

	return $has_marker ? rhm_toc_remove_marker( $anchored, $box ) : $box . $anchored;
}
add_filter( 'the_content', 'rhm_toc_content', 12 );

/**
 * Thay dấu đầu tiên bằng $replacement (bỏ <p> do wpautop bọc quanh shortcode), xóa các dấu còn lại.
 */
function rhm_toc_remove_marker( string $content, string $replacement ): string {
	$pattern = '~(?:<p>\s*)?' . preg_quote( RHM_TOC_MARKER, '~' ) . '(?:\s*</p>)?~';
	$content = (string) preg_replace( $pattern, $replacement, $content, 1 );
	return (string) preg_replace( $pattern, '', $content );
}
