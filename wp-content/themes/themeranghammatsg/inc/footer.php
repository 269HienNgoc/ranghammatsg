<?php
/**
 * Chân trang: dữ liệu từ Customizer → "Chân trang (Footer)". Hiển thị: footer.php. CSS: footer.css (@source file này).
 *
 * - Logo: Biểu tượng trang web (Nhận diện website → huy hiệu tròn, hợp nền tối); chưa có thì dùng Logo.
 * - Địa chỉ / hotline / email / giờ làm việc: dùng chung "Thông tin phòng khám".
 * - Chi nhánh, mạng xã hội, huy hiệu (Bộ Công Thương, DMCA…): danh sách động (inc/customize-list.php).
 * - Điều khoản chính sách: menu vị trí "footer" (Giao diện → Menu).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chữ mặc định (dùng chung cho Customizer và giao diện). Setting: rhm_footer_{khóa}.
 * Bản quyền: {year} được thay bằng năm hiện tại.
 */
function rhm_footer_defaults(): array {
	return array(
		'title'              => get_bloginfo( 'name' ),
		'slogan'             => __( 'Vì nụ cười Việt Nam', 'themeranghammatsg' ),
		'branch_title'       => __( 'Hệ thống đối tác', 'themeranghammatsg' ),
		'branch_button_text' => __( 'Xem chi tiết địa chỉ', 'themeranghammatsg' ),
		'policy_title'       => __( 'Điều khoản chính sách', 'themeranghammatsg' ),
		'social_title'       => __( 'Kết nối với chúng tôi', 'themeranghammatsg' ),
		/* translators: %s: site name */
		'copyright'          => sprintf( __( '© {year} %s. Giữ bản quyền nội dung trên website này.', 'themeranghammatsg' ), get_bloginfo( 'name' ) ),
	);
}

/**
 * Chữ của 1 khóa trong rhm_footer_defaults() (chưa escape).
 */
function rhm_footer_text( string $key ): string {
	$text = trim( (string) get_theme_mod( "rhm_footer_{$key}", rhm_footer_defaults()[ $key ] ?? '' ) );
	return 'copyright' === $key ? str_replace( '{year}', wp_date( 'Y' ), $text ) : $text;
}

/** Field danh sách chi nhánh. */
function rhm_footer_branch_fields(): array {
	return array(
		'name' => array( __( 'Tên chi nhánh', 'themeranghammatsg' ), 'text' ),
		'link' => array( __( 'Link (tùy chọn)', 'themeranghammatsg' ), 'url' ),
	);
}

/** Field danh sách mạng xã hội. */
function rhm_footer_social_fields(): array {
	return array(
		'icon' => array( __( 'Icon (.svg / .png vuông)', 'themeranghammatsg' ), 'image' ),
		'name' => array( __( 'Tên mạng xã hội (vd Facebook)', 'themeranghammatsg' ), 'text' ),
		'link' => array( __( 'Link trang', 'themeranghammatsg' ), 'url' ),
	);
}

/** Field danh sách huy hiệu cuối trang. */
function rhm_footer_badge_fields(): array {
	return array(
		'image' => array( __( 'Ảnh huy hiệu (.png / .jpg)', 'themeranghammatsg' ), 'image' ),
		'name'  => array( __( 'Tên (mô tả ảnh)', 'themeranghammatsg' ), 'text' ),
		'link'  => array( __( 'Link (tùy chọn)', 'themeranghammatsg' ), 'url' ),
	);
}

/**
 * ID ảnh logo chân trang (0 = chưa có).
 */
function rhm_get_footer_logo_id(): int {
	foreach ( array( (int) get_option( 'site_icon' ), (int) get_theme_mod( 'custom_logo' ) ) as $id ) {
		if ( wp_attachment_is_image( $id ) ) {
			return $id;
		}
	}
	return 0;
}

/**
 * Dòng liên hệ (địa chỉ, hotline, email, giờ làm việc) dạng tên icon => HTML đã escape; dòng trống bị bỏ.
 *
 * @return array<string, string>
 */
function rhm_get_footer_contacts(): array {
	$hotline = rhm_get_option( 'rhm_hotline' );
	$email   = rhm_get_option( 'rhm_email' );

	return array_filter(
		array(
			'map-pin' => nl2br( esc_html( rhm_get_option( 'rhm_address' ) ) ),
			'phone'   => $hotline ? sprintf( '<a class="font-bold text-white hover:text-footer-accent" href="%s">%s</a>', esc_url( rhm_get_tel_href( $hotline ) ), esc_html( $hotline ) ) : '',
			'mail'    => $email ? sprintf( '<a class="wrap-anywhere text-footer-text hover:text-footer-accent" href="%s">%s</a>', esc_url( 'mailto:' . $email ), esc_html( $email ) ) : '',
			'clock'   => nl2br( esc_html( rhm_get_option( 'rhm_working_hours' ) ) ),
		)
	);
}

/**
 * Chi nhánh có tên.
 *
 * @return array<int, array{name:string, link:string}>
 */
function rhm_get_footer_branches(): array {
	$items = array();
	foreach ( rhm_get_list( 'rhm_footer_branches' ) as $item ) {
		$name = trim( (string) ( $item['name'] ?? '' ) );
		if ( '' !== $name ) {
			$items[] = array(
				'name' => $name,
				'link' => (string) ( $item['link'] ?? '' ),
			);
		}
	}
	return $items;
}

/**
 * Mạng xã hội có đủ icon (ảnh bất kỳ, kể cả SVG) + link.
 *
 * @return array<int, array{icon:string, name:string, link:string}>
 */
function rhm_get_footer_socials(): array {
	$list = rhm_get_list( 'rhm_footer_socials' );
	_prime_post_caches( array_filter( array_map( 'absint', array_column( $list, 'icon' ) ) ) );

	$items = array();
	foreach ( $list as $item ) {
		$icon = absint( $item['icon'] ?? 0 );
		$link = (string) ( $item['link'] ?? '' );
		if ( '' === $link || ! str_starts_with( (string) get_post_mime_type( $icon ), 'image/' ) ) {
			continue;
		}
		$items[] = array(
			'icon' => (string) wp_get_attachment_url( $icon ),
			'name' => trim( (string) ( $item['name'] ?? '' ) ),
			'link' => $link,
		);
	}
	return $items;
}

/**
 * Huy hiệu cuối trang có ảnh (ảnh raster để có srcset + kích thước thật).
 *
 * @return array<int, array{image:int, name:string, link:string}>
 */
function rhm_get_footer_badges(): array {
	$list = rhm_get_list( 'rhm_footer_badges' );
	_prime_post_caches( array_filter( array_map( 'absint', array_column( $list, 'image' ) ) ) );

	$items = array();
	foreach ( $list as $item ) {
		$image = absint( $item['image'] ?? 0 );
		if ( ! wp_attachment_is_image( $image ) ) {
			continue;
		}
		$name    = trim( (string) ( $item['name'] ?? '' ) );
		$items[] = array(
			'image' => $image,
			'name'  => '' !== $name ? $name : get_the_title( $image ),
			'link'  => (string) ( $item['link'] ?? '' ),
		);
	}
	return $items;
}

/**
 * <img> hiển thị theo chiều cao CSS cố định ($height px): sizes = độ rộng thật (chiều cao × tỉ lệ ảnh).
 */
function rhm_footer_image( int $id, int $height, string $class, string $alt ): string {
	$full = wp_get_attachment_image_src( $id, 'full' );
	if ( ! $full || ! $full[2] ) {
		return '';
	}

	return wp_get_attachment_image(
		$id,
		'full',
		false,
		array(
			'class'    => $class,
			'alt'      => $alt,
			'sizes'    => (int) ceil( $height * $full[1] / $full[2] ) . 'px',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
}
