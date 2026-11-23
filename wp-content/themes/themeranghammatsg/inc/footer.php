<?php
/**
 * Chân trang: dữ liệu từ Customizer → "Chân trang (Footer)". Hiển thị: footer.php. CSS: footer.css (@source file này).
 *
 * - Cột công ty: logo (Biểu tượng trang web → huy hiệu tròn, hợp nền tối; chưa có thì dùng Logo) + tên công ty
 *   (rhm_footer_title), địa chỉ ("Thông tin phòng khám") + mã số thuế (rhm_footer_tax_code).
 * - Cột "Kết nối với chúng tôi": hotline / email / giờ làm việc ("Thông tin phòng khám") + mạng xã hội.
 * - Hàng chi nhánh: chọn + sắp thứ tự chi nhánh (CPT, inc/branches.php) trong Tùy biến; mỗi chi nhánh in địa chỉ,
 *   link bản đồ, GPHĐ, hotline, email, giờ làm việc (dòng trống bỏ qua). Không chọn chi nhánh nào thì ẩn cả hàng.
 * - Mạng xã hội, huy hiệu (Bộ Công Thương, DMCA…): danh sách động (inc/customize-list.php).
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
		'title'        => get_bloginfo( 'name' ),
		'tax_code'     => '',
		'branch_title' => __( 'Hệ thống đối tác', 'themeranghammatsg' ),
		'policy_title' => __( 'Điều khoản chính sách', 'themeranghammatsg' ),
		'social_title' => __( 'Kết nối với chúng tôi', 'themeranghammatsg' ),
		/* translators: %s: site name */
		'copyright'    => sprintf( __( '© {year} %s. Giữ bản quyền nội dung trên website này.', 'themeranghammatsg' ), get_bloginfo( 'name' ) ),
	);
}

/**
 * Chữ của 1 khóa trong rhm_footer_defaults() (chưa escape).
 */
function rhm_footer_text( string $key ): string {
	$text = trim( (string) get_theme_mod( "rhm_footer_{$key}", rhm_footer_defaults()[ $key ] ?? '' ) );
	return 'copyright' === $key ? str_replace( '{year}', wp_date( 'Y' ), $text ) : $text;
}

/** Field danh sách "Chi nhánh hiển thị" (Tùy biến → Chân trang): mỗi mục chọn 1 chi nhánh. */
function rhm_footer_branch_fields(): array {
	return array(
		'branch' => array( __( 'Chi nhánh', 'themeranghammatsg' ), 'select', rhm_footer_branch_choices() ),
	);
}

/**
 * Lựa chọn cho ô chọn chi nhánh: ID => "Tên — địa chỉ rút gọn" (chi nhánh đã xuất bản, A → Z).
 *
 * @return array<int, string>
 */
function rhm_footer_branch_choices(): array {
	$choices = array();
	foreach ( rhm_get_branches() as $branch ) {
		$address                  = '' !== $branch['address'] ? wp_html_excerpt( str_replace( "\n", ', ', $branch['address'] ), 60, '…' ) : __( '(chưa có địa chỉ)', 'themeranghammatsg' );
		$choices[ $branch['id'] ] = $branch['name'] . ' — ' . $address;
	}
	return $choices;
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
 * Dòng thông tin công ty dưới logo (địa chỉ, mã số thuế): tên icon => HTML đã escape; dòng trống bị bỏ.
 *
 * @return array<string, string>
 */
function rhm_get_footer_company_lines(): array {
	$tax_code = rhm_footer_text( 'tax_code' );

	return array_filter(
		array(
			'map-pin'  => nl2br( esc_html( rhm_get_option( 'rhm_address' ) ) ),
			'document' => '' !== $tax_code ? sprintf( '<strong class="font-bold text-white">%s</strong> %s', esc_html__( 'MST:', 'themeranghammatsg' ), esc_html( $tax_code ) ) : '',
		)
	);
}

/**
 * Cột "Kết nối với chúng tôi": hotline, email, giờ làm việc. Dòng trống bị bỏ.
 *
 * @return array<string, array{label:string, html:string}> tên icon => nhãn (chưa escape) + giá trị HTML đã escape.
 */
function rhm_get_footer_contacts(): array {
	$hotline = rhm_get_option( 'rhm_hotline' );
	$email   = rhm_get_option( 'rhm_email' );
	$hours   = rhm_get_option( 'rhm_working_hours' );
	$items   = array(
		'phone' => array(
			__( 'Hotline', 'themeranghammatsg' ),
			$hotline ? sprintf( '<a class="text-lg font-bold text-white hover:text-footer-accent" href="%s">%s</a>', esc_url( rhm_get_tel_href( $hotline ) ), esc_html( $hotline ) ) : '',
		),
		'mail'  => array(
			__( 'Email', 'themeranghammatsg' ),
			$email ? sprintf( '<a class="wrap-anywhere text-white hover:text-footer-accent" href="%s">%s</a>', esc_url( 'mailto:' . $email ), esc_html( $email ) ) : '',
		),
		'clock' => array(
			__( 'Giờ làm việc', 'themeranghammatsg' ),
			'' !== trim( $hours ) ? '<span class="text-white">' . nl2br( esc_html( $hours ) ) . '</span>' : '',
		),
	);

	$lines = array();
	foreach ( $items as $icon => $item ) {
		if ( '' !== $item[1] ) {
			$lines[ $icon ] = array(
				'label' => $item[0],
				'html'  => $item[1],
			);
		}
	}
	return $lines;
}

/**
 * Chi nhánh hiển thị ở hàng chi nhánh của footer: chọn + sắp thứ tự trong Tùy biến (setting rhm_footer_branch_ids).
 * Footer in tên, địa chỉ, bản đồ, GPHĐ, hotline, email, giờ; trang Liên hệ (tpl-lien-he.php) in tên + link Google Maps.
 * Bỏ: ID trùng, chi nhánh đã xóa / nháp / trong thùng rác, chi nhánh chưa có địa chỉ.
 * Không chọn chi nhánh nào → mảng rỗng → footer.php ẩn cả hàng.
 *
 * @return array<int, array{id:int, name:string, address:string, map_url:string, hotline:string, license:string, email:string, hours:string}>
 *         đã làm sạch, chưa escape (address, hours có thể nhiều dòng).
 */
function rhm_get_footer_branches(): array {
	$branches = array_column( rhm_get_branches(), null, 'id' ); // Chi nhánh đã xuất bản (có cache) theo ID.
	$items    = array();

	foreach ( rhm_get_list( 'rhm_footer_branch_ids' ) as $item ) {
		$id = absint( $item['branch'] ?? 0 );
		if ( ! $id || isset( $items[ $id ] ) || '' === ( $branches[ $id ]['address'] ?? '' ) ) {
			continue;
		}
		$items[ $id ] = array(
			'id'      => $id,
			'name'    => (string) ( $branches[ $id ]['name'] ?? '' ),
			'address' => $branches[ $id ]['address'],
			'map_url' => (string) ( $branches[ $id ]['map_url'] ?? '' ),
			'hotline' => (string) ( $branches[ $id ]['hotline'] ?? '' ),
			'license' => (string) ( $branches[ $id ]['license'] ?? '' ),
			'email'   => (string) ( $branches[ $id ]['email'] ?? '' ),
			'hours'   => (string) ( $branches[ $id ]['hours'] ?? '' ),
		);
	}

	return array_values( $items );
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
