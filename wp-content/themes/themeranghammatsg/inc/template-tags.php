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
		return (string) apply_filters( 'rhm_booking_url', get_permalink( $page_id ) );
	}
	$page = get_page_by_path( 'lien-he' );
	return (string) apply_filters( 'rhm_booking_url', $page ? get_permalink( $page ) : home_url( '/' ) );
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
 * Có popup đặt lịch (inc/booking-popup.php) → thêm data-rhm-popup: main.js mở popup thay vì chuyển trang
 * (href vẫn là trang Liên hệ: không có JS vẫn dùng được, Google vẫn bò được).
 * Lưu ý: chuỗi class phải viết nguyên văn trong template để Tailwind quét được.
 */
function rhm_booking_button( string $class = '', string $icon_class = '', bool $arrow = false ): void {
	printf(
		'<a class="%1$s" href="%2$s"%6$s>%3$s<span>%4$s</span>%5$s</a>',
		esc_attr( trim( 'btn btn-primary ' . $class ) ),
		esc_url( rhm_get_booking_url() ),
		$icon_class ? rhm_get_icon( 'calendar', $icon_class ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		esc_html__( 'Đặt lịch hẹn', 'themeranghammatsg' ),
		$arrow ? rhm_get_icon( 'arrow-right', 'size-5 shrink-0' ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		rhm_booking_popup_enabled() ? ' data-rhm-popup="booking"' : ''
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
 * Thời gian đọc (phút, tối thiểu 1) của bài hiện tại: ~220 chữ/phút (tiếng Việt, mỗi tiếng tách bởi khoảng trắng).
 */
function rhm_get_reading_minutes(): int {
	$words = preg_split( '/\s+/u', trim( wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content' ) ) ) ), -1, PREG_SPLIT_NO_EMPTY );
	return max( 1, (int) round( count( (array) $words ) / 220 ) );
}

/**
 * Số ảnh card đầu tiên KHÔNG lazy ở trang danh sách không có bài nổi bật: mobile (< 640px) card dạng hàng, ~4 thẻ nằm trong
 * màn hình đầu; desktop hàng đầu 2–3 thẻ.
 */
const RHM_LIST_EAGER_IMAGES = 4;

/**
 * Cách tải ảnh card của bài hiện tại trong vòng lặp (chỉ đếm bài có ảnh đại diện) ở trang danh sách không có bài nổi bật
 * (Tin tức, tìm kiếm, lưu trữ, danh mục trang 2+) → truyền vào card-post ( 'loading' => rhm_list_image_loading() ):
 * 'lcp' = ảnh đầu (eager + fetchpriority=high) | 'eager' = ảnh 2…RHM_LIST_EAGER_IMAGES | 'lazy' = còn lại.
 * Ảnh trong màn hình đầu cùng cỡ nhau (mobile) → Chrome có thể chọn bất kỳ ảnh nào làm LCP (chênh nhau vài phần pixel)
 * nên cả nhóm phải tải ngay, chỉ 1 ảnh được ưu tiên cao.
 */
function rhm_list_image_loading(): string {
	static $count = 0;
	if ( ! has_post_thumbnail() ) {
		return 'lazy';
	}
	++$count;
	return 1 === $count ? 'lcp' : ( $count <= RHM_LIST_EAGER_IMAGES ? 'eager' : 'lazy' );
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
 * Breadcrumb đơn giản: Trang chủ › [chuyên mục cha › chuyên mục (bài viết) | trang cha] › Hiện tại.
 */
function rhm_breadcrumb(): void {
	if ( is_front_page() ) {
		return;
	}

	$items   = array();
	$items[] = array( __( 'Trang chủ', 'themeranghammatsg' ), home_url( '/' ) );

	if ( is_singular( 'post' ) ) {
		// Chuyên mục đầu tiên của bài + các chuyên mục cha (SEO + người đọc quay lại đúng nhóm bài); không có → trang Tin tức.
		$category = get_the_category( get_queried_object_id() )[0] ?? null;
		if ( $category ) {
			foreach ( array_reverse( get_ancestors( $category->term_id, 'category' ) ) as $ancestor ) {
				$items[] = array( get_cat_name( $ancestor ), get_category_link( $ancestor ) );
			}
			$items[] = array( $category->name, get_category_link( $category ) );
		} elseif ( (int) get_option( 'page_for_posts' ) ) {
			$items[] = array( get_the_title( (int) get_option( 'page_for_posts' ) ), get_permalink( (int) get_option( 'page_for_posts' ) ) );
		}
	} elseif ( is_singular( 'bac-si' ) ) {
		$items[] = array( __( 'Bác sĩ', 'themeranghammatsg' ), get_post_type_archive_link( 'bac-si' ) );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$items[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
	}

	$items[] = array( rhm_get_page_title(), '' );

	// 1 dòng: các mục trước giữ nguyên, mục cuối (trang hiện tại, vd tiêu đề bài dài) tự cắt "…" thay vì rớt xuống dòng.
	$last = count( $items ) - 1;
	// Link màu teal-dark (mục đầu kèm icon nhà), ngăn cách bằng mũi tên nhỏ, trang hiện tại in đậm.
	echo '<nav class="text-[15px] leading-6" aria-label="' . esc_attr__( 'Breadcrumb', 'themeranghammatsg' ) . '"><ol class="flex items-center gap-2">';
	foreach ( $items as $index => [ $label, $url ] ) {
		echo '<li class="' . ( $index === $last ? 'flex min-w-0 items-center gap-2' : 'flex shrink-0 items-center gap-2' ) . '">';
		if ( $index > 0 ) {
			echo rhm_get_icon( 'chevron', 'size-3.5 shrink-0 -rotate-90 text-muted' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		}
		if ( $url ) {
			printf(
				'<a class="inline-flex items-center gap-1.5 font-medium text-teal-dark transition-colors hover:text-primary" href="%1$s">%2$s%3$s</a>',
				esc_url( $url ),
				0 === $index ? rhm_get_icon( 'home', 'size-4 shrink-0' ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
				esc_html( $label )
			);
		} else {
			echo '<span class="truncate font-bold text-dark" aria-current="page" title="' . esc_attr( $label ) . '">' . esc_html( $label ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * <picture> cho ảnh hiển thị ở độ rộng CSS cố định theo từng khoảng màn hình (logo, icon…):
 * mỗi khoảng là 1 <source media> chỉ định sẵn file → trình duyệt tải đúng 1 file, không phải đoán từ sizes.
 * srcset mỗi khoảng: 1x + 2x = file nhỏ nhất cùng tỉ lệ (bản gốc + các size đã tạo) có độ rộng ≥ độ rộng CSS × mật độ điểm ảnh.
 *
 * @param array<string, int> $widths media query => độ rộng CSS (px), theo thứ tự ưu tiên (media lớn trước);
 *                                   khóa '' = mặc định cho <img> (bắt buộc).
 * @param array<string, string> $attrs Thuộc tính thêm cho <img> (class, alt, loading…).
 */
function rhm_get_picture( int $id, array $widths, array $attrs = array() ): string {
	$meta = wp_get_attachment_metadata( $id );
	$url  = (string) wp_get_attachment_url( $id );
	if ( empty( $meta['width'] ) || empty( $meta['height'] ) || '' === $url || ! isset( $widths[''] ) ) {
		return '';
	}

	$ratio = $meta['width'] / $meta['height'];
	$dir   = trailingslashit( dirname( $url ) );
	$files = array( (int) $meta['width'] => $url );
	foreach ( $meta['sizes'] ?? array() as $size ) {
		if ( abs( $size['width'] / $size['height'] - $ratio ) < 0.01 ) {
			$files[ (int) $size['width'] ] = $dir . $size['file'];
		}
	}
	ksort( $files );

	$pick   = static function ( int $min ) use ( $files ): string {
		foreach ( $files as $width => $file ) {
			if ( $width >= $min ) {
				return $file;
			}
		}
		return $files[ array_key_last( $files ) ];
	};
	$srcset = static function ( int $width ) use ( $pick ): string {
		$x1 = $pick( $width );
		$x2 = $pick( 2 * $width );
		return $x1 === $x2 ? $x1 : "{$x1} 1x, {$x2} 2x";
	};

	$html = '';
	foreach ( $widths as $media => $width ) {
		if ( '' !== $media ) {
			$html .= '<source' . rhm_html_attrs( array( 'media' => $media, 'srcset' => $srcset( $width ) ) ) . '>';
		}
	}

	$width = $widths[''];
	$img   = array(
		'src'    => $pick( $width ),
		'srcset' => $srcset( $width ),
		'width'  => $width,
		'height' => (int) round( $width / $ratio ),
	) + $attrs;

	return '<picture>' . $html . '<img' . rhm_html_attrs( $img ) . '></picture>';
}
