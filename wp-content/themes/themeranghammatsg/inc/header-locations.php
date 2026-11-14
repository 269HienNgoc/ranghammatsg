<?php
/**
 * Dòng địa điểm dưới logo header (vd "Biên Hòa · Cần Thơ · Sydney").
 *
 * In trong .site-branding của header.php, thẳng mép trái với chữ của logo (CSS: .header-locations trong
 * src/css/pages/header.css). Mọi tùy chọn ở Tùy biến → "Dòng địa điểm (Header)".
 * Class Tailwind viết nguyên văn trong file này (được @source trong header.css; chỉ có hiệu lực trong .site-header).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Giá trị mặc định của các theme mod. */
const RHM_HEADER_LOCATIONS_DEFAULTS = array(
	'show'      => true,
	'text'      => 'Biên Hòa - Cần Thơ - Sydney',
	'icon_show' => true,
	'icon'      => 'location',
	'separator' => '·',
	'color'     => 'muted',
	'device'    => 'all',
);

/** Dấu phân cách được phép (giá trị => nhãn trong ô chọn). */
const RHM_HEADER_LOCATIONS_SEPARATORS = array(
	'·' => '·  (chấm giữa)',
	'•' => '•  (chấm tròn)',
	'|' => '|  (gạch đứng)',
	'–' => '–  (gạch ngang)',
	'/' => '/  (gạch chéo)',
);

/** Màu chữ: khóa => class Tailwind (token trong src/css/theme.css). Cả 3 màu đạt tương phản ≥ 4,5:1 trên nền trắng. */
const RHM_HEADER_LOCATIONS_COLORS = array(
	'muted'   => 'text-muted',
	'primary' => 'text-primary',
	'deep'    => 'text-deep',
);

/** Hiển thị theo màn hình: khóa => class ẩn (1024px = breakpoint lg, cùng mốc với menu mobile). */
const RHM_HEADER_LOCATIONS_DEVICES = array(
	'all'     => '',
	'mobile'  => 'lg:hidden',
	'desktop' => 'max-lg:hidden',
);

/**
 * Giá trị 1 tùy chọn (theme mod), có mặc định.
 */
function rhm_header_locations_option( string $key ): mixed {
	return get_theme_mod( 'rhm_header_locations' . ( 'text' === $key ? '' : '_' . $key ), RHM_HEADER_LOCATIONS_DEFAULTS[ $key ] );
}

/**
 * Danh sách địa điểm đã tách (chưa escape). Rỗng khi tắt hoặc để trống.
 * Tách theo | · • (có hoặc không khoảng trắng) và - – / (PHẢI có khoảng trắng 2 bên → tên có gạch nối như "Bà Rịa-Vũng Tàu" giữ nguyên).
 *
 * @return string[]
 */
function rhm_get_header_locations(): array {
	if ( ! rhm_header_locations_option( 'show' ) ) {
		return array();
	}

	$parts = preg_split( '~\s*[|·•]\s*|\s+[-–/]\s+~u', trim( (string) rhm_header_locations_option( 'text' ) ) );

	return array_values( array_filter( array_map( 'trim', (array) $parts ), 'strlen' ) );
}

/**
 * In dòng địa điểm (gọi trong .site-branding của header.php). Không có địa điểm → không in gì.
 */
function rhm_header_locations(): void {
	$locations = rhm_get_header_locations();
	if ( ! $locations ) {
		return;
	}

	$color     = RHM_HEADER_LOCATIONS_COLORS[ rhm_header_locations_option( 'color' ) ] ?? RHM_HEADER_LOCATIONS_COLORS['muted'];
	$device    = RHM_HEADER_LOCATIONS_DEVICES[ rhm_header_locations_option( 'device' ) ] ?? '';
	$separator = (string) rhm_header_locations_option( 'separator' );
	$separator = isset( RHM_HEADER_LOCATIONS_SEPARATORS[ $separator ] ) ? $separator : RHM_HEADER_LOCATIONS_DEFAULTS['separator'];

	$icon = '';
	if ( rhm_header_locations_option( 'icon_show' ) ) {
		$name = (string) rhm_header_locations_option( 'icon' );
		$icon = rhm_icon_svg( 'menu', '' !== $name ? $name : RHM_HEADER_LOCATIONS_DEFAULTS['icon'], 'mr-1 size-3.5 shrink-0 text-primary lg:size-4' );
		if ( '' === $icon ) { // Icon đã chọn bị xóa khỏi thư viện → icon mặc định.
			$icon = rhm_icon_svg( 'menu', RHM_HEADER_LOCATIONS_DEFAULTS['icon'], 'mr-1 size-3.5 shrink-0 text-primary lg:size-4' );
		}
	}

	$items = array();
	foreach ( $locations as $location ) {
		$items[] = '<span>' . esc_html( $location ) . '</span>';
	}

	// Dấu phân cách chỉ để trang trí (aria-hidden); trình đọc màn hình nghe dấu phẩy ẩn.
	$glue = sprintf(
		'<span class="sr-only">, </span><span class="mx-1.5 opacity-50" aria-hidden="true">%s</span>',
		esc_html( $separator )
	);

	printf(
		'<p class="%1$s">%2$s%3$s</p>',
		esc_attr( trim( 'header-locations flex items-center text-xs leading-[15px] font-medium tracking-wide whitespace-nowrap lg:text-[13px] lg:leading-4 xl:text-sm xl:leading-[18px] ' . $color . ' ' . $device ) ),
		$icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG của theme (rhm_icon_svg).
		implode( $glue, $items ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape ở trên.
	);
}

function rhm_sanitize_header_locations_icon( $value ): string {
	return rhm_sanitize_icon_name( 'menu', $value );
}

function rhm_sanitize_header_locations_separator( $value ): string {
	return isset( RHM_HEADER_LOCATIONS_SEPARATORS[ $value ] ) ? (string) $value : RHM_HEADER_LOCATIONS_DEFAULTS['separator'];
}

function rhm_sanitize_header_locations_color( $value ): string {
	return isset( RHM_HEADER_LOCATIONS_COLORS[ $value ] ) ? (string) $value : RHM_HEADER_LOCATIONS_DEFAULTS['color'];
}

function rhm_sanitize_header_locations_device( $value ): string {
	return isset( RHM_HEADER_LOCATIONS_DEVICES[ $value ] ) ? (string) $value : RHM_HEADER_LOCATIONS_DEFAULTS['device'];
}

/**
 * Tùy biến → "Dòng địa điểm (Header)" (ngay sau "Thông tin phòng khám").
 */
function rhm_customize_header_locations( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'rhm_header_locations',
		array(
			'title'       => __( 'Dòng địa điểm (Header)', 'themeranghammatsg' ),
			'description' => __( 'Dòng chữ nhỏ dưới logo ở header (mobile + desktop), vd "Biên Hòa · Cần Thơ · Sydney". Khi cuộn trang, dòng này thu nhỏ cùng logo (vẫn hiển thị).', 'themeranghammatsg' ),
			'priority'    => 30,
		)
	);

	$defaults = RHM_HEADER_LOCATIONS_DEFAULTS;

	rhm_customize_fields(
		$wp_customize,
		'rhm_header_locations',
		array(
			'rhm_header_locations_show'      => array( __( 'Hiện dòng địa điểm', 'themeranghammatsg' ), 'checkbox', 'rest_sanitize_boolean', array( 'default' => $defaults['show'] ) ),
			'rhm_header_locations'           => array(
				__( 'Các địa điểm', 'themeranghammatsg' ),
				'text',
				'sanitize_text_field',
				array(
					'default'     => $defaults['text'],
					'description' => __( 'Cách nhau bằng " - " (có khoảng trắng 2 bên), "|" hoặc "·". Để trống: ẩn dòng.', 'themeranghammatsg' ),
				),
			),
			'rhm_header_locations_icon_show' => array( __( 'Hiện icon đầu dòng', 'themeranghammatsg' ), 'checkbox', 'rest_sanitize_boolean', array( 'default' => $defaults['icon_show'] ) ),
			'rhm_header_locations_icon'      => array(
				__( 'Icon (bộ icon menu của theme)', 'themeranghammatsg' ),
				'rhm-icon',
				'rhm_sanitize_header_locations_icon',
				array(
					'default'  => $defaults['icon'],
					'icon_set' => 'menu',
				),
			),
			'rhm_header_locations_separator' => array(
				__( 'Dấu phân cách', 'themeranghammatsg' ),
				'select',
				'rhm_sanitize_header_locations_separator',
				array(
					'default' => $defaults['separator'],
					'choices' => RHM_HEADER_LOCATIONS_SEPARATORS,
				),
			),
			'rhm_header_locations_color'     => array(
				__( 'Màu chữ', 'themeranghammatsg' ),
				'select',
				'rhm_sanitize_header_locations_color',
				array(
					'default' => $defaults['color'],
					'choices' => array(
						'muted'   => __( 'Xám nhạt (nhẹ nhàng — khuyên dùng)', 'themeranghammatsg' ),
						'primary' => __( 'Xanh thương hiệu', 'themeranghammatsg' ),
						'deep'    => __( 'Xanh đậm', 'themeranghammatsg' ),
					),
				),
			),
			'rhm_header_locations_device'    => array(
				__( 'Hiển thị trên', 'themeranghammatsg' ),
				'select',
				'rhm_sanitize_header_locations_device',
				array(
					'default' => $defaults['device'],
					'choices' => array(
						'all'     => __( 'Mọi màn hình', 'themeranghammatsg' ),
						'mobile'  => __( 'Chỉ mobile (dưới 1024px)', 'themeranghammatsg' ),
						'desktop' => __( 'Chỉ desktop (từ 1024px)', 'themeranghammatsg' ),
					),
				),
			),
		)
	);
}
add_action( 'customize_register', 'rhm_customize_header_locations', 11 );
