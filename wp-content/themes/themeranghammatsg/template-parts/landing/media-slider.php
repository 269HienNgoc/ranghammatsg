<?php
/**
 * Landing – Ảnh slider + chữ (nhiều item): tiêu đề section (H2, canh giữa) + mô tả → lưới item (1/2/3 cột từ 1024px).
 * Mỗi item = thẻ nền gradient mint → trắng: slider ảnh + tiêu đề (H3) + nội dung. Vị trí ảnh chung cho mọi item
 * (image_position, mặc định phải); < 640px luôn ảnh trên, chữ dưới.
 * Field: inc/landing/layouts/media-slider.php. Thiếu tiêu đề section hoặc không có item hợp lệ → không hiện.
 *
 * Hiệu năng / truy cập:
 * - DOM mỗi thẻ luôn ảnh → chữ (= thứ tự mobile); vị trí phải / dưới đảo bằng sm:order-last (chỉ CSS).
 * - Slider native scroll-snap (vuốt được khi chưa có JS), khung ảnh 4:3 cố định + object-cover → CLS = 0.
 * - Item ≥ 2 ảnh: nút ‹ › do _tiles.js xử lý (carousel.js nạp động khi có [data-rhm-tiles-controls]); mỗi track có id
 *   riêng, nút trỏ track qua aria-controls → nhiều slider trong 1 section. Không Swiper.
 * - Chỉ ảnh đầu của item đầu, khi section đứng đầu trang, mới là ảnh LCP; còn lại lazy.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data    = $args['data'] ?? array();
$rhm_h3_attr = rhm_landing_color_attr( rhm_landing_color( $rhm_data, 'h3_color' ) ); // Màu mọi H3 của section ('' = màu thiết kế).
$rhm_title   = rhm_landing_text( $rhm_data, 'title' );

$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_images = rhm_landing_gallery( $rhm_row, 'images' );
	$rhm_name   = rhm_landing_text( $rhm_row, 'title' );
	$rhm_text   = rhm_landing_rich( $rhm_row, 'text' );
	if ( $rhm_images || '' !== $rhm_name || '' !== $rhm_text ) {
		$rhm_items[] = array( $rhm_images, $rhm_name, $rhm_text );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_position = (string) ( $rhm_data['image_position'] ?? 'right' );
$rhm_position = in_array( $rhm_position, array( 'left', 'right', 'top', 'bottom' ), true ) ? $rhm_position : 'right';
$rhm_side     = in_array( $rhm_position, array( 'left', 'right' ), true );
$rhm_columns  = (int) ( $rhm_data['columns'] ?? 1 );
$rhm_columns  = in_array( $rhm_columns, array( 1, 2, 3 ), true ) ? $rhm_columns : 1;
$rhm_id       = $args['id'];
$rhm_head_id  = $rhm_id . '-title';

// sizes: khung W = 1408px (≥ 1440px) | 100vw − 32px. Thẻ: viền 1 + đệm 20 (lg 24) mỗi bên → nội dung = thẻ − 42 (lg − 50).
// Cạnh nhau: ảnh = (nội dung − khoảng cách cột 16 | lg 24) / 2. Lưới thẻ cách 24px, nhiều cột chỉ từ 1024px.
// < 640px xếp dọc: 100vw − 32 − 42 = 100vw − 74px. 640–1023px: thẻ rộng hết khung → cạnh nhau (100vw − 74 − 16) / 2 = 50vw − 45px.
// 1 cột: thẻ = W → cạnh nhau từ 1024px ảnh 1/3 (dải ngang như thiết kế): (1408 − 74) / 3 = 445 | 33.33vw − 35; trên/dưới 1358 | 100vw − 82.
// 2 cột: thẻ = (W − 24) / 2 = 692 | 50vw − 28 → cạnh nhau 309 | 25vw − 51; trên/dưới 642 | 50vw − 78.
// 3 cột: thẻ = (W − 48) / 3 = 453 | 33.33vw − 27 → cạnh nhau 190 | 16.67vw − 50; trên/dưới 403 | 33.33vw − 77.
// 1024–1639px dư nhẹ do chừa chỗ cột nút liên hệ nhanh (giống các section khác).
$rhm_sizes = match ( $rhm_columns . ( $rhm_side ? 'side' : 'stack' ) ) {
	'1side'  => '(max-width: 639px) calc(100vw - 74px), (min-width: 1440px) 445px, (min-width: 1024px) calc(33.33vw - 35px), calc(50vw - 45px)',
	'2side'  => '(max-width: 639px) calc(100vw - 74px), (min-width: 1440px) 309px, (min-width: 1024px) calc(25vw - 51px), calc(50vw - 45px)',
	'3side'  => '(max-width: 639px) calc(100vw - 74px), (min-width: 1440px) 190px, (min-width: 1024px) calc(16.67vw - 50px), calc(50vw - 45px)',
	'2stack' => '(min-width: 1440px) 642px, (min-width: 1024px) calc(50vw - 78px), calc(100vw - 74px)',
	'3stack' => '(min-width: 1440px) 403px, (min-width: 1024px) calc(33.33vw - 77px), calc(100vw - 74px)',
	default  => '(min-width: 1440px) 1358px, (min-width: 1024px) calc(100vw - 82px), calc(100vw - 74px)',
};

$rhm_grid   = match ( $rhm_columns ) {
	2       => 'grid gap-5 lg:grid-cols-2 lg:gap-6',
	3       => 'grid gap-5 lg:grid-cols-3 lg:gap-6',
	default => 'grid gap-5 lg:gap-6',
};
$rhm_card   = 'left' === $rhm_position
	? 'overflow-hidden rounded-lg border border-line bg-linear-to-l from-mint to-white p-5 shadow-card lg:p-6'
	: 'overflow-hidden rounded-lg border border-line bg-linear-to-r from-mint to-white p-5 shadow-card lg:p-6';
// Cạnh nhau: khung cao hết thẻ (thẻ cùng hàng cao hơn → ảnh + chữ vẫn canh giữa theo chiều dọc).
// 640px+: 2 cột bằng nhau; 1 cột từ 1024px: ảnh 1/3, chữ 2/3 (cột đầu lưới = phần tử đứng trước sau khi đảo order).
$rhm_layout = match ( true ) {
	! $rhm_side                                 => 'flex h-full flex-col gap-4 lg:gap-5',
	1 === $rhm_columns && 'left' === $rhm_position => 'flex h-full flex-col gap-4 sm:grid sm:grid-cols-2 sm:items-center lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] lg:gap-6',
	1 === $rhm_columns                          => 'flex h-full flex-col gap-4 sm:grid sm:grid-cols-2 sm:items-center lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] lg:gap-6',
	default                                     => 'flex h-full flex-col gap-4 sm:grid sm:grid-cols-2 sm:items-center lg:gap-6',
};
$rhm_media  = in_array( $rhm_position, array( 'right', 'bottom' ), true ) ? 'relative min-w-0 sm:order-last' : 'relative min-w-0';
$rhm_arrow  = 'pointer-events-auto flex size-9 cursor-pointer items-center justify-center rounded-full border border-line bg-white/90 text-primary shadow-card transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-0';
$rhm_rich   = rhm_landing_rich( $rhm_data, 'description' );
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>
		<?php if ( '' !== $rhm_rich ) : ?>
			<div class="rhm-rich mx-auto -mt-2 mb-6 max-w-4xl text-center leading-relaxed text-body/85 lg:mb-8"><?php echo $rhm_rich; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
		<?php endif; ?>

		<ul class="<?php echo esc_attr( $rhm_grid ); ?>">
			<?php foreach ( $rhm_items as $rhm_n => [ $rhm_images, $rhm_name, $rhm_text ] ) : ?>
				<?php
				$rhm_track = $rhm_id . '-item-' . ( $rhm_n + 1 ) . '-track';
				$rhm_count = count( $rhm_images );
				$rhm_multi = $rhm_count > 1;
				if ( $rhm_multi ) {
					rhm_landing_enqueue_module( 'carousel' );
				}
				$rhm_label = '' !== $rhm_name ? $rhm_name : $rhm_title;
				?>
				<li class="<?php echo esc_attr( $rhm_card ); ?>">
					<div class="<?php echo esc_attr( $rhm_images ? $rhm_layout : 'flex h-full flex-col' ); ?>">
						<?php if ( $rhm_images ) : ?>
							<div class="<?php echo esc_attr( $rhm_media ); ?>" data-rhm-tiles>
								<div id="<?php echo esc_attr( $rhm_track ); ?>" class="flex snap-x snap-mandatory overflow-x-auto overscroll-x-contain rounded-md [scrollbar-width:none]" aria-label="<?php echo esc_attr( $rhm_label ); ?>" data-rhm-tiles-track<?php echo $rhm_multi ? ' tabindex="0"' : ''; ?>>
									<?php foreach ( $rhm_images as $rhm_index => $rhm_image ) : ?>
										<div class="relative block aspect-[4/3] w-full shrink-0 snap-start overflow-hidden bg-soft"<?php echo $rhm_multi ? ' role="group" aria-roledescription="' . esc_attr__( 'ảnh', 'themeranghammatsg' ) . '" aria-label="' . esc_attr( ( $rhm_index + 1 ) . ' / ' . $rhm_count ) . '"' : ''; ?>>
											<?php
											// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
											echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', $args['is_first'] && 0 === $rhm_n && 0 === $rhm_index );
											?>
										</div>
									<?php endforeach; ?>
								</div>

								<?php if ( $rhm_multi ) : ?>
									<div class="pointer-events-none invisible absolute inset-x-0 top-0 flex aspect-[4/3] items-center justify-between px-2" data-rhm-tiles-controls>
										<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-prev disabled>
											<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
										</button>
										<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-next disabled>
											<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
										</button>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( '' !== $rhm_name || '' !== $rhm_text ) : ?>
							<div class="min-w-0">
								<?php if ( '' !== $rhm_name ) : ?>
									<h3 class="text-lg leading-snug font-bold text-pretty text-primary lg:text-xl"<?php echo $rhm_h3_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_color_attr(). ?>><?php echo esc_html( $rhm_name ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== $rhm_text ) : ?>
									<div class="<?php echo esc_attr( '' !== $rhm_name ? 'rhm-rich mt-2 text-sm leading-relaxed text-body/85 lg:text-[15px]' : 'rhm-rich text-sm leading-relaxed text-body/85 lg:text-[15px]' ); ?>"><?php echo $rhm_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
