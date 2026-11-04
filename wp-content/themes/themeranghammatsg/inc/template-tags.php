<?php
/**
 * Helper hiển thị dùng trong template.
 * Quy ước: hàm rhm_get_* trả giá trị thô; hàm rhm_* (không get) tự echo và đã escape.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lấy giá trị Customizer (chưa escape).
 */
function rhm_get_option( string $key, string $default = '' ): string {
	return (string) get_theme_mod( $key, $default );
}

/**
 * URL trang Liên hệ / Đặt lịch (Customizer), fallback trang có slug "lien-he".
 */
function rhm_get_booking_url(): string {
	$page_id = absint( get_theme_mod( 'rhm_booking_page', 0 ) );
	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return (string) get_permalink( $page_id );
	}
	$page = get_page_by_path( 'lien-he' );
	return $page ? (string) get_permalink( $page ) : home_url( '/' );
}

/**
 * Link tel: từ số hotline (bỏ ký tự không phải số / dấu +).
 */
function rhm_get_tel_href( string $phone ): string {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Nút Đặt lịch. $class: utility bổ sung/ghi đè (utility luôn thắng .btn vì nằm ở layer sau).
 * $icon_class: khác rỗng → thêm icon lịch phía trước (class của icon). $arrow: thêm mũi tên → phía sau.
 * Lưu ý: chuỗi class phải viết nguyên văn trong template để Tailwind quét được.
 */
function rhm_booking_button( string $class = '', string $icon_class = '', bool $arrow = false ): void {
	printf(
		'<a class="%1$s" href="%2$s">%3$s<span>%4$s</span>%5$s</a>',
		esc_attr( trim( 'btn btn-primary ' . $class ) ),
		esc_url( rhm_get_booking_url() ),
		$icon_class ? rhm_get_icon( 'calendar', $icon_class ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		esc_html__( 'Đặt lịch hẹn', 'themeranghammatsg' ),
		$arrow ? rhm_get_icon( 'arrow-right', 'size-5 shrink-0' ) : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	);
}

/**
 * Ngày đăng bài.
 */
function rhm_posted_on(): void {
	printf(
		'<time class="entry-date" datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

/**
 * Phân trang danh sách.
 */
function rhm_pagination(): void {
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => esc_html__( '« Trước', 'themeranghammatsg' ),
			'next_text' => esc_html__( 'Sau »', 'themeranghammatsg' ),
		)
	);
}

/**
 * Tiêu đề banner đầu trang cho từng ngữ cảnh.
 */
function rhm_get_page_title(): string {
	if ( is_search() ) {
		/* translators: %s: search query */
		return sprintf( __( 'Kết quả tìm kiếm: %s', 'themeranghammatsg' ), get_search_query() );
	}
	if ( is_404() ) {
		return __( 'Không tìm thấy trang', 'themeranghammatsg' );
	}
	if ( is_home() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		return $posts_page ? get_the_title( $posts_page ) : __( 'Tin tức', 'themeranghammatsg' );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_title() );
	}
	return get_the_title();
}

/**
 * Breadcrumb đơn giản: Trang chủ › [Tin tức | trang cha] › Hiện tại.
 */
function rhm_breadcrumb(): void {
	if ( is_front_page() ) {
		return;
	}

	$items   = array();
	$items[] = array( __( 'Trang chủ', 'themeranghammatsg' ), home_url( '/' ) );

	if ( is_singular( 'post' ) ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		if ( $posts_page ) {
			$items[] = array( get_the_title( $posts_page ), get_permalink( $posts_page ) );
		}
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$items[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
	}

	$items[] = array( rhm_get_page_title(), '' );

	echo '<nav class="text-sm text-muted" aria-label="' . esc_attr__( 'Breadcrumb', 'themeranghammatsg' ) . '"><ol class="flex flex-wrap items-center gap-2">';
	foreach ( $items as $index => [ $label, $url ] ) {
		echo '<li class="flex items-center gap-2">';
		if ( $index > 0 ) {
			echo '<span aria-hidden="true">›</span>';
		}
		if ( $url ) {
			echo '<a class="text-muted hover:text-primary" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( $label ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}
