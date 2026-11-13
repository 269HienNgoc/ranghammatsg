<?php
/**
 * Landing page theo chủ đề: Page + Page Template + ACF Flexible Content.
 *
 * - Chủ đề (rhm_landing_templates): mỗi Page Template là 1 chủ đề, khai báo các layout (section) được dùng + bundle CSS.
 * - Layout (rhm_landing_layouts): thư viện section dùng chung. Mỗi layout = 1 file field trong inc/landing/layouts/<key>.php
 *   (hàm rhm_landing_layout_<key>_fields) + 1 template-part template-parts/landing/<key>.php.
 * - Layout dạng "ô" (boxed): có ô Độ rộng (toàn trang, 1/2, 2/5, 3/5). Các ô liền nhau được xếp chung 1 hàng (lưới 10 cột ≥ 1024px).
 * - Field ACF: inc/landing/fields.php (1 field group / chủ đề, hiện khi trang chọn đúng template).
 * - Hiển thị: rhm_landing_render() gọi trong page-templates/tpl-*.php.
 *
 * Hướng dẫn nhập liệu + thêm layout/chủ đề: docs/landing-pages.md.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Tên field Flexible Content (giống nhau ở mọi chủ đề → code render dùng chung). */
const RHM_LANDING_FIELD = 'rhm_landing_sections';

/** Độ rộng ô hợp lệ (phần mười của hàng). Đổi thì sửa cả rhm_landing_span_class() và rhm_landing_width_field(). */
const RHM_LANDING_SPANS = array( 10, 5, 4, 6 );

/**
 * Thư viện layout: key => [ label, boxed ].
 * boxed = section dạng ô có nền, xếp được cạnh nhau theo ô Độ rộng.
 *
 * @return array<string, array{label:string, boxed:bool}>
 */
function rhm_landing_layouts(): array {
	$layout = static fn( string $label, bool $boxed = false ): array => array(
		'label' => $label,
		'boxed' => $boxed,
	);
	return array(
		'hero'         => $layout( __( 'Banner đầu trang (H1)', 'themeranghammatsg' ) ),
		'highlights'   => $layout( __( 'Dải điểm nổi bật (icon + nhãn)', 'themeranghammatsg' ) ),
		'cards'        => $layout( __( 'Thẻ giá trị (sứ mệnh, tầm nhìn…) + ảnh', 'themeranghammatsg' ) ),
		'services'     => $layout( __( 'Danh sách dịch vụ (ảnh + tên)', 'themeranghammatsg' ) ),
		'doctor-cards' => $layout( __( 'Bác sĩ tiêu biểu (thẻ ngang)', 'themeranghammatsg' ) ),
		'feature'      => $layout( __( 'Ô thông tin: ảnh + chữ', 'themeranghammatsg' ), true ),
		'tiles'        => $layout( __( 'Ô lưới ảnh nhỏ (thiết bị, công nghệ…)', 'themeranghammatsg' ), true ),
		'promo'        => $layout( __( 'Ô banner nổi bật (ảnh + tiêu đề + logo)', 'themeranghammatsg' ), true ),
		'intro'        => $layout( __( 'Ảnh lớn + chữ + ô nổi bật', 'themeranghammatsg' ) ),
		'text-image'   => $layout( __( 'Tiêu đề + nội dung dài + ảnh lớn bên dưới', 'themeranghammatsg' ) ),
		'image-stack'  => $layout( __( 'Ảnh lớn xếp dọc (kèm mô tả ảnh)', 'themeranghammatsg' ) ),
		'stats'        => $layout( __( 'Số liệu nổi bật', 'themeranghammatsg' ) ),
		'timeline'     => $layout( __( 'Chặng đường phát triển', 'themeranghammatsg' ) ),
		'gallery'      => $layout( __( 'Thư viện ảnh (slider)', 'themeranghammatsg' ) ),
		'clinics'      => $layout( __( 'Hệ thống phòng khám (chi nhánh + ảnh theo nhóm)', 'themeranghammatsg' ) ),
		'doctors'      => $layout( __( 'Đội ngũ bác sĩ (slider như trang chủ)', 'themeranghammatsg' ) ),
		'form'         => $layout( __( 'Form đăng ký (Fluent Forms)', 'themeranghammatsg' ) ),
		'faq'          => $layout( __( 'Câu hỏi thường gặp', 'themeranghammatsg' ) ),
		'editor'       => $layout( __( 'Nội dung trình soạn thảo', 'themeranghammatsg' ) ),
		'booking'      => $layout( __( 'Kêu gọi đặt lịch', 'themeranghammatsg' ) ),
	);
}

/**
 * Chủ đề landing: file Page Template => cấu hình.
 * - slug:    định danh ngắn (key field group / field ACF), KHÔNG đổi sau khi đã có dữ liệu.
 * - label:   tên chủ đề trong tiêu đề field group.
 * - bundle:  file CSS trong dist/css/ (rhm_page_style_bundle()).
 * - layouts: các layout content được chọn, theo thứ tự hiện trong menu "Thêm section".
 *
 * @return array<string, array{slug:string, label:string, bundle:string, layouts:string[]}>
 */
function rhm_landing_templates(): array {
	static $templates = null;
	if ( null === $templates ) {
		$templates = (array) apply_filters(
			'rhm_landing_templates',
			array(
				'page-templates/tpl-gioi-thieu.php' => array(
					'slug'    => 'about',
					'label'   => __( 'Giới thiệu', 'themeranghammatsg' ),
					'bundle'  => 'page-about',
					'layouts' => array_keys( rhm_landing_layouts() ),
				),
			)
		);
	}
	return $templates;
}

/**
 * Cấu hình chủ đề của trang (theo Page Template đang chọn), null nếu trang không phải landing.
 */
function rhm_landing_template( int $post_id ): ?array {
	if ( ! $post_id || 'page' !== get_post_type( $post_id ) ) {
		return null;
	}
	return rhm_landing_templates()[ (string) get_page_template_slug( $post_id ) ] ?? null;
}

/**
 * Bundle CSS của trang landing hiện tại ('' nếu không phải landing).
 */
function rhm_landing_style_bundle(): string {
	return is_page() ? ( rhm_landing_template( (int) get_queried_object_id() )['bundle'] ?? '' ) : '';
}

/**
 * Section hợp lệ của trang, theo thứ tự content sắp xếp. Bỏ: section đang Ẩn, layout chủ đề không cho phép
 * (vd dữ liệu còn lại sau khi đổi template). Không có ACF → mảng rỗng (template tự hiện nội dung editor).
 *
 * @return array<int, array{layout:string, data:array}>
 */
function rhm_landing_sections( int $post_id ): array {
	static $cache = array();
	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$template = rhm_landing_template( $post_id );
	$rows     = $template && function_exists( 'get_field' ) ? get_field( RHM_LANDING_FIELD, $post_id ) : null;
	$sections = array();

	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		$layout = (string) ( $row['acf_fc_layout'] ?? '' );
		if ( ! in_array( $layout, $template['layouts'], true ) || ( isset( $row['show'] ) && ! $row['show'] ) ) {
			continue;
		}
		$sections[] = array(
			'layout' => $layout,
			'data'   => $row,
		);
	}

	return $cache[ $post_id ] = $sections;
}

/**
 * Section đầu là banner (có H1) → template không in khối tiêu đề trang, để trang chỉ có 1 <h1>.
 */
function rhm_landing_has_hero( int $post_id ): bool {
	return 'hero' === ( rhm_landing_sections( $post_id )[0]['layout'] ?? '' );
}

/**
 * Id HTML của section "form" đầu tiên trên trang landing hiện tại dùng CÙNG form Fluent với popup đặt lịch, '' nếu không có.
 * Fluent sinh id HTML theo số form (fluentform_3, ff_3_…) → in 1 form 2 lần (section + popup) là trùng id (lỗi accessibility,
 * nhãn trỏ nhầm ô). Có section như vậy thì giống trang Liên hệ: không in popup, mọi nút đặt lịch cuộn tới section
 * (inc/booking-popup.php + filter rhm_booking_url bên dưới). Tính từ dữ liệu (không đợi render) vì nút đặt lịch ở header in trước.
 */
function rhm_landing_booking_form_anchor(): string {
	static $anchor = null;
	if ( null !== $anchor ) {
		return $anchor;
	}
	$anchor = '';
	if ( is_admin() || ! is_page() || ! function_exists( 'rhm_booking_popup_form_id' ) ) {
		return $anchor;
	}
	$popup = rhm_booking_popup_form_id();
	foreach ( rhm_landing_sections( (int) get_queried_object_id() ) as $index => $section ) {
		if ( 'form' !== $section['layout'] ) {
			continue;
		}
		$form = absint( $section['data']['form_id'] ?? 0 ) ?: absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
		if ( $popup && $form === $popup ) {
			// Cùng cách đặt id với rhm_landing_render() (neo do content nhập, hoặc rhm-section-N).
			$anchor = rhm_landing_part_args( $section['data'], $index, '' )['id'];
			break;
		}
	}
	return $anchor;
}

/** Trang landing có section form trùng form popup → nút đặt lịch cuộn tới section đó. */
function rhm_landing_booking_url( string $url ): string {
	$anchor = rhm_landing_booking_form_anchor();
	return '' !== $anchor ? '#' . $anchor : $url;
}
add_filter( 'rhm_booking_url', 'rhm_landing_booking_url' );

/** Độ rộng ô (2–10 phần mười) của section dạng ô; mặc định 1/2. */
function rhm_landing_span( array $data ): int {
	$span = (int) ( $data['width'] ?? 5 );
	return in_array( $span, RHM_LANDING_SPANS, true ) ? $span : 5;
}

/** Class cột của ô (viết nguyên văn để Tailwind quét được). */
function rhm_landing_span_class( int $span ): string {
	return match ( $span ) {
		4       => 'min-w-0 lg:col-span-4',
		6       => 'min-w-0 lg:col-span-6',
		10      => 'min-w-0 lg:col-span-10',
		default => 'min-w-0 lg:col-span-5',
	};
}

/**
 * In toàn bộ trang landing (gọi trong vòng lặp the_post() của template).
 * Chưa có section nào → giao diện trang thường: tiêu đề + nội dung editor + kêu gọi đặt lịch.
 *
 * Mỗi template-part nhận $args:
 * - data:     dữ liệu ACF của section (đã format: ảnh = ID).
 * - index:    vị trí (0 = đầu trang).
 * - is_first: section đầu trang → ảnh của nó là ảnh LCP (eager + fetchpriority=high).
 * - id:       id HTML của <section> (neo do content nhập, hoặc rhm-section-N), id tiêu đề = <id>-title.
 * - prev:     layout của section liền trước ('' nếu là section đầu).
 * - span:     (chỉ layout dạng ô) độ rộng ô, phần mười của hàng (10 = toàn trang).
 */
function rhm_landing_render( int $post_id ): void {
	$sections = rhm_landing_sections( $post_id );

	if ( ! rhm_landing_has_hero( $post_id ) ) {
		get_template_part( 'template-parts/components/page-title' );
	}

	if ( ! $sections ) {
		// Giữ đúng giao diện trước khi có landing: nội dung editor (cột rộng) + kêu gọi đặt lịch.
		get_template_part( 'template-parts/landing/editor', null, rhm_landing_part_args( array( 'narrow' => false ), 0, '' ) );
		get_template_part( 'template-parts/sections/booking-cta' );
		return;
	}

	$layouts = rhm_landing_layouts();
	$used    = array();
	$row     = array(); // Ô đang gom vào hàng hiện tại: [ html, span ].
	$row_sum = 0;

	$flush = static function () use ( &$row, &$row_sum ): void {
		if ( $row ) {
			echo '<div class="site-container grid gap-5 py-3 lg:grid-cols-10 lg:gap-6">';
			foreach ( $row as [ $html, $span ] ) {
				echo '<div class="' . esc_attr( rhm_landing_span_class( $span ) ) . '">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML của template-part.
			}
			echo '</div>';
		}
		$row     = array();
		$row_sum = 0;
	};

	echo '<div class="rhm-landing">';
	foreach ( $sections as $index => $section ) {
		$args = rhm_landing_part_args( $section['data'], $index, $sections[ $index - 1 ]['layout'] ?? '' );
		// Neo trùng (2 section cùng neo, hoặc neo nhập tay trùng id tự sinh) → thêm hậu tố đến khi duy nhất.
		for ( $rhm_n = 2, $rhm_base = $args['id']; isset( $used[ $args['id'] ] ); $rhm_n++ ) {
			$args['id'] = $rhm_base . '-' . $rhm_n;
		}
		$used[ $args['id'] ] = true;

		$part = 'template-parts/landing/' . $section['layout'];
		if ( empty( $layouts[ $section['layout'] ]['boxed'] ) ) {
			$flush();
			get_template_part( $part, null, $args );
			continue;
		}

		// Ô: gom vào hàng đến khi đủ 10 phần; ô không vừa phần còn lại → sang hàng mới. Ô thiếu dữ liệu (không in gì) bị bỏ qua.
		$args['span'] = rhm_landing_span( $section['data'] );
		ob_start();
		get_template_part( $part, null, $args );
		$html = trim( (string) ob_get_clean() );
		if ( '' === $html ) {
			continue;
		}
		if ( $row_sum + $args['span'] > 10 ) {
			$flush();
		}
		$row[]    = array( $html, $args['span'] );
		$row_sum += $args['span'];
		if ( $row_sum >= 10 ) {
			$flush();
		}
	}
	$flush();
	echo '</div>';
}

/**
 * $args chuẩn cho template-part của 1 section.
 */
function rhm_landing_part_args( array $data, int $index, string $prev ): array {
	$anchor = sanitize_title( (string) ( $data['anchor'] ?? '' ) );
	return array(
		'data'     => $data,
		'index'    => $index,
		'is_first' => 0 === $index,
		'id'       => '' !== $anchor ? $anchor : 'rhm-section-' . ( $index + 1 ),
		'prev'     => $prev,
	);
}

/* ---------------------------------------------------------------------------
 * Helper đọc dữ liệu trong template-part (luôn trả giá trị đã làm sạch, chưa escape).
 * ------------------------------------------------------------------------- */

/** Chuỗi 1 dòng. */
function rhm_landing_text( array $data, string $key ): string {
	return trim( sanitize_text_field( (string) ( $data[ $key ] ?? '' ) ) );
}

/** Đoạn văn nhiều dòng (textarea) → mảng đoạn, bỏ dòng trống. */
function rhm_landing_lines( array $data, string $key ): array {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R+/u', sanitize_textarea_field( (string) ( $data[ $key ] ?? '' ) ) ) ?: array() ) ) );
}

/** ID ảnh hợp lệ, 0 nếu trống / đã xóa. */
function rhm_landing_image( array $data, string $key ): int {
	$id = absint( $data[ $key ] ?? 0 );
	return $id && wp_attachment_is_image( $id ) ? $id : 0;
}

/** Danh sách ID ảnh hợp lệ (field gallery). */
function rhm_landing_gallery( array $data, string $key ): array {
	$ids = array_filter( array_map( 'absint', (array) ( $data[ $key ] ?? array() ) ) );
	if ( $ids ) {
		_prime_post_caches( $ids, false, true );
	}
	return array_values( array_filter( $ids, 'wp_attachment_is_image' ) );
}

/** Tên icon hợp lệ trong bộ assets/icons/landing/, '' nếu không có. */
function rhm_landing_icon( array $data, string $key ): string {
	return rhm_sanitize_icon_name( 'landing', $data[ $key ] ?? '' );
}

/** Các dòng repeater (mảng con), bỏ giá trị không phải mảng. */
function rhm_landing_rows( array $data, string $key ): array {
	return array_values( array_filter( (array) ( $data[ $key ] ?? array() ), 'is_array' ) );
}

/** URL link (http/https, đường dẫn tương đối, #neo, tel:, mailto:), '' nếu không hợp lệ. */
function rhm_landing_url( array $data, string $key ): string {
	$url = trim( (string) ( $data[ $key ] ?? '' ) );
	if ( '' === $url ) {
		return '';
	}
	return str_starts_with( $url, '#' ) ? '#' . sanitize_title( substr( $url, 1 ) ) : esc_url_raw( $url, array( 'http', 'https', 'tel', 'mailto' ) );
}

/** HTML của ô soạn thảo (WYSIWYG): lọc như nội dung bài + link external có target/rel. */
function rhm_landing_html( array $data, string $key ): string {
	$html = trim( (string) ( $data[ $key ] ?? '' ) );
	return '' === $html ? '' : rhm_external_links( wp_kses_post( $html ) );
}

/** Alt ảnh: alt của attachment → tiêu đề attachment. */
function rhm_landing_image_alt( int $id ): string {
	$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	return '' !== $alt ? $alt : get_the_title( $id );
}

/** Nạp JS module dùng chung (carousel, embed) — chỉ gọi trong template-part khi section thật sự cần. */
function rhm_landing_enqueue_module( string $name ): void {
	$relative = 'dist/js/' . $name . '.js';
	if ( file_exists( RHM_THEME_DIR . '/' . $relative ) ) {
		wp_enqueue_script_module( 'rhm-' . $name, RHM_THEME_URI . '/' . $relative, array(), rhm_asset_version( $relative ) );
	}
}

/**
 * sizes cho ảnh nằm trong 1 ô (layout dạng ô) chiếm $fraction độ rộng phần nội dung của ô.
 *
 * Hàng: khung 1440 − lề 2×16 → W = 1408px (≥ 1440px) hoặc 100vw − 32px; lưới 10 cột cách 24px
 * → ô span s rộng s×W/10 + 2,4s − 24. Phần nội dung = ô − đệm 2×24 → s×W/10 + 2,4s − 72.
 * Dưới 1024px: ô rộng hết hàng (s = 10, đệm 2×20) → 100vw − 72px. $mobile_fraction: tỉ lệ trên mobile (mặc định = $fraction).
 * $offset: px trừ thêm (khoảng cách giữa các ảnh trong lưới…), áp dụng mọi breakpoint.
 */
function rhm_landing_box_sizes( int $span, float $fraction, ?float $mobile_fraction = null, int $offset = 0 ): string {
	$mobile_fraction ??= $fraction;
	$desktop = (int) round( $fraction * ( $span * 1408 / 10 + 2.4 * $span - 72 ) ) - $offset;
	$vw      = round( $fraction * $span * 10, 2 );
	$px      = (int) round( $fraction * ( 0.8 * $span + 72 ) ) + $offset;
	$m_vw    = round( $mobile_fraction * 100, 2 );
	$m_px    = (int) round( $mobile_fraction * 72 ) + $offset;
	return "(min-width: 1440px) {$desktop}px, (min-width: 1024px) calc({$vw}vw - {$px}px), calc({$m_vw}vw - {$m_px}px)";
}

/* ---------------------------------------------------------------------------
 * Helper in markup dùng chung giữa các section (class Tailwind viết nguyên văn ở đây → @source file này trong bundle CSS).
 * ------------------------------------------------------------------------- */

/**
 * Cụm tiêu đề section: dòng nhỏ (eyebrow) + H2 (id = $id) + mô tả.
 * $style: 'center' (canh giữa, 2 vạch hai bên) | 'bar' (vạch dọc bên trái) | 'box' (vạch dọc, cỡ nhỏ — trong ô) | 'plain'.
 */
function rhm_landing_heading( string $id, string $title, string $eyebrow = '', string $description = '', string $style = 'center' ): void {
	if ( '' === $title ) {
		return;
	}
	$wrap = match ( $style ) {
		'center' => 'mx-auto mb-6 max-w-3xl text-center lg:mb-8',
		'box'    => 'mb-4',
		default  => 'mb-6',
	};
	$h2 = match ( $style ) {
		'center' => 'landing-title-center text-[22px] leading-tight font-bold text-pretty text-primary uppercase lg:text-[28px]',
		'bar'    => 'border-l-4 border-primary pl-3 text-[20px] leading-tight font-bold text-pretty text-primary uppercase lg:text-2xl',
		'box'    => 'border-l-4 border-primary pl-3 text-lg leading-tight font-bold text-pretty text-primary uppercase lg:text-xl',
		default  => 'text-[20px] leading-tight font-bold text-pretty text-primary uppercase lg:text-2xl',
	};
	?>
	<div class="<?php echo esc_attr( $wrap ); ?>">
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="mb-2 text-[13px] font-semibold tracking-wide text-dark uppercase lg:text-sm"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<h2 id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $h2 ); ?>"><?php echo esc_html( $title ); ?></h2>
		<?php if ( '' !== $description ) : ?>
			<p class="mt-3 leading-relaxed text-body/85"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Nút link do content nhập (chữ + link, mặc định field button_text / button_link). Không đủ 2 giá trị → không in.
 * Kiểu mặc định theo thiết kế: viên thuốc xanh đậm + mũi tên.
 */
function rhm_landing_button( array $data, string $class = 'btn btn-primary h-10 gap-2 rounded-full px-5 text-sm font-bold', string $prefix = 'button' ): void {
	$text = rhm_landing_text( $data, $prefix . '_text' );
	$url  = rhm_landing_url( $data, $prefix . '_link' );
	if ( '' === $text || '' === $url ) {
		return;
	}
	printf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s%5$s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		rhm_external_link_attrs( $url ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes.
		esc_html( $text ),
		rhm_get_icon( 'arrow-right', 'size-4 shrink-0' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	);
}

/**
 * Ảnh co giãn theo cột (srcset w + sizes), tùy chọn ảnh mobile riêng (< 768px, art direction).
 * $priority: ảnh LCP (section đầu trang) → tải ngay, fetchpriority=high; còn lại lazy + decoding async.
 * width/height của từng nhánh giữ đúng tỉ lệ file → CLS = 0 (class nên có h-auto w-full hoặc khung aspect-* + object-cover).
 * $alt: null = alt của attachment (rhm_landing_image_alt); '' = ảnh minh họa có chữ đi kèm (không đọc lặp).
 */
function rhm_landing_picture( int $id, string $sizes, string $class, bool $priority = false, int $mobile = 0, string $mobile_sizes = '', ?string $alt = null ): string {
	$file = wp_get_attachment_image_src( $id, 'full' );
	if ( ! $file ) {
		return '';
	}

	$source = '';
	$mfile  = $mobile ? wp_get_attachment_image_src( $mobile, 'full' ) : false;
	if ( $mfile ) {
		$source = '<source' . rhm_html_attrs(
			array(
				'media'  => '(max-width: 767px)',
				'srcset' => wp_get_attachment_image_srcset( $mobile, 'full' ) ?: $mfile[0],
				'sizes'  => '' !== $mobile_sizes ? $mobile_sizes : $sizes,
				'width'  => $mfile[1],
				'height' => $mfile[2],
			)
		) . '>';
	}

	$img = array(
		'src'    => $file[0],
		'srcset' => wp_get_attachment_image_srcset( $id, 'full' ) ?: $file[0],
		'sizes'  => $sizes,
		'width'  => $file[1],
		'height' => $file[2],
		'alt'    => $alt ?? rhm_landing_image_alt( $id ),
		'class'  => $class,
	);
	if ( $priority ) {
		// Ảnh LCP: không lazy, không decoding=async (tránh trễ khung hình vẽ ảnh).
		$img['fetchpriority'] = 'high';
	} else {
		$img['loading']  = 'lazy';
		$img['decoding'] = 'async';
	}

	return '<picture>' . $source . '<img' . rhm_html_attrs( $img ) . '></picture>';
}

require_once __DIR__ . '/fields.php';
