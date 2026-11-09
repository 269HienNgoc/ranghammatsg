<?php
/**
 * Dữ liệu ACF bổ sung cho bài viết (field: inc/post-fields.php): bác sĩ tham vấn, câu hỏi thường gặp, tài liệu tham khảo.
 * Hiển thị: page-banner.php (bác sĩ tham vấn), template-parts/components/post-faq.php, post-references.php.
 * Schema: inc/schema.php đọc lại đúng các hàm này → nội dung schema luôn khớp nội dung hiển thị.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_post_acf(): void {
	require_once RHM_THEME_DIR . '/inc/post-fields.php';
	rhm_register_post_fields();
}
add_action( 'acf/init', 'rhm_post_acf' );

/** Thẻ HTML cho phép trong câu trả lời FAQ (nằm trong nhóm thẻ Google chấp nhận ở Answer.text). */
const RHM_FAQ_ALLOWED_HTML = array(
	'a'      => array( 'href' => true ),
	'strong' => array(),
	'b'      => array(),
	'em'     => array(),
	'i'      => array(),
	'br'     => array(),
	'ul'     => array(),
	'ol'     => array(),
	'li'     => array(),
	'p'      => array(),
);

function rhm_get_post_field( string $name, int $post_id ) {
	return function_exists( 'get_field' ) ? get_field( 'rhm_post_' . $name, $post_id ) : null;
}

/** ID hồ sơ bác sĩ đã xuất bản, 0 nếu trống / hồ sơ bị xóa hoặc chuyển nháp. */
function rhm_get_post_reviewer( int $post_id ): int {
	$id = absint( rhm_get_post_field( 'reviewer', $post_id ) );
	return $id && 'bac-si' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ? $id : 0;
}

/**
 * Khối cuối bài ('faq' | 'references') có được hiện trên trang không (công tắc rhm_post_{section}_show).
 * Chỉ dùng cho phần hiển thị: schema (inc/schema.php) KHÔNG đọc hàm này. Chưa lưu / không có ACF → hiện.
 */
function rhm_post_section_visible( string $section, int $post_id ): bool {
	$show = rhm_get_post_field( $section . '_show', $post_id );
	return null === $show || (bool) $show;
}

/** @return array<int, array{question:string, answer:string}> answer là HTML đã lọc + wpautop. */
function rhm_get_post_faq( int $post_id ): array {
	$items = array();
	$rows  = rhm_get_post_field( 'faq', $post_id );
	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		$question = sanitize_text_field( (string) ( $row['question'] ?? '' ) );
		$answer   = trim( wp_kses( (string) ( $row['answer'] ?? '' ), RHM_FAQ_ALLOWED_HTML ) );
		if ( $question && $answer ) {
			$items[] = array( 'question' => $question, 'answer' => wpautop( $answer ) );
		}
	}
	return $items;
}

/** @return array<int, array{title:string, url:string}> url chỉ http(s), có thể rỗng. */
function rhm_get_post_references( int $post_id ): array {
	$items = array();
	$rows  = rhm_get_post_field( 'references', $post_id );
	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		$title = sanitize_text_field( (string) ( $row['title'] ?? '' ) );
		if ( $title ) {
			$items[] = array( 'title' => $title, 'url' => esc_url_raw( (string) ( $row['url'] ?? '' ), array( 'http', 'https' ) ) );
		}
	}
	return $items;
}
