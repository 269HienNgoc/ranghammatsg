<?php
/**
 * Landing – Chặng đường phát triển (timeline NGANG, theo mẫu "m-trip" người dùng đưa 2026-11-18):
 * tiêu đề (H2) + mô tả canh giữa → hàng năm trên 1 đường kẻ → slider nội dung (mỗi năm 1 slide: H3 + nội dung + ảnh).
 * Field: inc/landing/layouts/timeline.php. Thiếu tiêu đề section hoặc không có mốc hợp lệ (năm + tiêu đề) → không hiện.
 *
 * Hàng năm = [data-rhm-showcase-thumbs] (nút aria-controls = track nội dung), nút ‹ › 2 đầu hàng = [data-rhm-tiles-controls];
 * JS dùng lại src/js/_showcase.js + _tiles.js (carousel.js nạp động, chỉ khi ≥ 2 mốc). Hiệu ứng (đường tiến trình chạy tới
 * năm đang chọn, năm đã qua có chấm + nhãn trượt xuống, năm đang chọn là viên màu, slide mới hiện rõ dần) là CSS thuần
 * `.rhm-timeline-*` trong page-about.css, tính từ aria-current / data-current → chỉ transform/opacity.
 * Track nội dung data-rhm-autoheight: cao theo slide đang xem; slide 2+ cao 0 tới khi có data-ready → CLS lúc tải = 0.
 * Ảnh: ≤ 3 ảnh lưới (1–2 ảnh 2 cột, 3 ảnh 3 cột từ 640px), > 3 ảnh slider lồng (vuốt trên slider ảnh không đổi năm).
 * Chú thích ảnh = caption của attachment. Chỉ ảnh đầu của mốc đầu, khi section đứng đầu trang, là ảnh LCP.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data    = $args['data'] ?? array();
$rhm_h3_attr = rhm_landing_color_attr( rhm_landing_color( $rhm_data, 'h3_color' ) ); // Màu mọi H3 của section ('' = màu thiết kế).
$rhm_title   = rhm_landing_text( $rhm_data, 'title' );
$rhm_items   = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_year = rhm_landing_text( $rhm_row, 'year' );
	$rhm_name = rhm_landing_text( $rhm_row, 'title' );
	if ( '' !== $rhm_year && '' !== $rhm_name ) {
		$rhm_items[] = array( $rhm_year, $rhm_name, rhm_landing_rich( $rhm_row, 'text' ), rhm_landing_gallery( $rhm_row, 'images' ) );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_id      = $args['id'];
$rhm_head_id = $rhm_id . '-title';
$rhm_track   = $rhm_id . '-track';
$rhm_total   = count( $rhm_items );
$rhm_multi   = $rhm_total > 1;
$rhm_rich    = rhm_landing_rich( $rhm_data, 'description' );
if ( $rhm_multi ) {
	rhm_landing_enqueue_module( 'carousel' );
}
$rhm_arrow = 'pointer-events-auto flex size-8 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-card transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-primary';

// sizes: slide rộng hết khung W = 1248px (≥ 1280px) | 100vw − 32px. Khoảng cách ảnh 12px (< 640) | 16px (sm) | 24px (lg).
// 2 cột: (W − gap) / 2 → 612px | 50vw − 28px (lg) | 50vw − 24px (sm) | 50vw − 22px (mobile).
// 3 cột / slide 1/3: (W − 2 gap) / 3 → 400px | 33.33vw − 27px (lg) | 33.33vw − 21px (sm); mobile 3 ảnh = lưới 2 cột.
// Mobile: 1 ảnh rộng hết (100vw − 32px); slider slide 80% = 80vw − 26px.
// 1024–1479px dư nhẹ do chừa chỗ cột nút liên hệ nhanh (giống các section khác).
$rhm_sizes_2 = '(min-width: 1280px) 612px, (min-width: 1024px) calc(50vw - 28px), (min-width: 640px) calc(50vw - 24px), ';
$rhm_sizes_3 = '(min-width: 1280px) 400px, (min-width: 1024px) calc(33.33vw - 27px), (min-width: 640px) calc(33.33vw - 21px), ';

/**
 * In 1 ảnh: khung 4:3 + chú thích (caption của attachment, chỉ khi có).
 */
$rhm_figure = static function ( int $image, string $sizes, bool $priority, string $class ): void {
	$caption = trim( (string) wp_get_attachment_caption( $image ) );
	?>
	<figure class="<?php echo esc_attr( $class ); ?>">
		<div class="relative aspect-[4/3] overflow-hidden rounded-md bg-soft">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
			echo rhm_landing_picture( $image, $sizes, 'absolute inset-0 size-full object-cover', $priority );
			?>
		</div>
		<?php if ( '' !== $caption ) : ?>
			<figcaption class="mt-2 text-center text-sm leading-snug text-muted italic"><?php echo esc_html( $caption ); ?></figcaption>
		<?php endif; ?>
	</figure>
	<?php
};
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>
		<?php if ( '' !== $rhm_rich ) : ?>
			<div class="rhm-rich mx-auto -mt-2 mb-6 max-w-4xl text-center leading-relaxed text-body/85 lg:mb-8"><?php echo $rhm_rich; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
		<?php endif; ?>

		<?php if ( $rhm_multi ) : ?>
			<?php // Hàng năm + nút ‹ › 2 đầu (nút canh theo đường kẻ, cao 32px = tâm đường kẻ ở 16px). Ô năm: 3 / 4 ô một khung (basis 1/3 | 1/4, ít năm thì giãn đều), không hẹp hơn nhãn (min-w-max). ?>
			<div class="relative mb-4 px-10 lg:mb-6 lg:px-12" data-rhm-tiles>
				<div class="rhm-timeline-years flex snap-x overflow-x-auto overscroll-x-contain [scrollbar-width:none]" role="group" aria-label="<?php esc_attr_e( 'Chọn năm', 'themeranghammatsg' ); ?>" data-rhm-showcase-thumbs>
					<?php foreach ( $rhm_items as $rhm_n => [ $rhm_year ] ) : ?>
						<button type="button" class="rhm-timeline-year w-1/3 min-w-max flex-auto shrink-0 snap-start sm:w-1/4" aria-controls="<?php echo esc_attr( $rhm_track ); ?>"<?php echo $rhm_n ? '' : ' aria-current="true"'; ?>>
							<span class="rhm-timeline-dot" aria-hidden="true"></span>
							<span class="rhm-timeline-label"><?php echo esc_html( $rhm_year ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="pointer-events-none invisible absolute inset-x-0 top-0 flex h-8 items-center justify-between" data-rhm-tiles-controls>
					<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Năm trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-prev disabled>
						<?php echo rhm_get_icon( 'chevron', 'size-4 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</button>
					<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Năm tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-next disabled>
						<?php echo rhm_get_icon( 'chevron', 'size-4 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</button>
				</div>
			</div>

			<div id="<?php echo esc_attr( $rhm_track ); ?>" class="rhm-timeline-track flex touch-pan-y snap-x snap-mandatory items-start overflow-x-auto overflow-y-hidden overscroll-x-contain transition-[height] duration-300 ease-out motion-reduce:transition-none [scrollbar-width:none] [&:not([data-ready])>:not(:first-child)]:h-0 [&:not([data-ready])>:not(:first-child)]:overflow-hidden" role="region" aria-label="<?php echo esc_attr( $rhm_title ); ?>" data-rhm-autoheight tabindex="0">
		<?php else : ?>
			<p class="mb-4 text-center lg:mb-6"><span class="inline-block rounded-full bg-primary px-4 py-0.5 text-base font-semibold text-white lg:text-lg"><?php echo esc_html( $rhm_items[0][0] ); ?></span></p>
			<div>
		<?php endif; ?>

			<?php foreach ( $rhm_items as $rhm_n => [ $rhm_year, $rhm_name, $rhm_text, $rhm_images ] ) : ?>
				<?php
				$rhm_count    = count( $rhm_images );
				$rhm_priority = $args['is_first'] && 0 === $rhm_n;
				?>
				<div class="w-full shrink-0 snap-start"<?php echo $rhm_multi ? ' role="group" aria-roledescription="' . esc_attr__( 'mốc', 'themeranghammatsg' ) . '" aria-label="' . esc_attr( ( $rhm_n + 1 ) . ' / ' . $rhm_total . ': ' . $rhm_year ) . '"' . ( $rhm_n ? '' : ' data-current' ) : ''; ?>>
					<h3 class="text-lg leading-snug font-bold text-pretty text-primary lg:text-xl"<?php echo $rhm_h3_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_color_attr(). ?>><?php echo esc_html( $rhm_name ); ?></h3>
					<?php if ( '' !== $rhm_text ) : ?>
						<div class="rhm-rich mt-2 leading-relaxed text-body/85"><?php echo $rhm_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
					<?php endif; ?>

					<?php if ( $rhm_count > 3 ) : ?>
						<?php $rhm_inner = $rhm_id . '-item-' . ( $rhm_n + 1 ) . '-images'; ?>
						<div class="mt-4 lg:mt-6" data-rhm-tiles>
							<div id="<?php echo esc_attr( $rhm_inner ); ?>" class="flex snap-x snap-mandatory items-start gap-3 overflow-x-auto overscroll-x-contain [scrollbar-width:none] sm:gap-4 lg:gap-6" role="region" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: năm */ __( 'Ảnh năm %s', 'themeranghammatsg' ), $rhm_year ) ); ?>" tabindex="0" data-rhm-tiles-track>
								<?php
								foreach ( $rhm_images as $rhm_index => $rhm_image ) {
									$rhm_figure( $rhm_image, $rhm_sizes_3 . 'calc(80vw - 26px)', $rhm_priority && 0 === $rhm_index, 'w-[80%] shrink-0 snap-start sm:w-[calc((100%-2rem)/3)] lg:w-[calc((100%-3rem)/3)]' );
								}
								?>
							</div>
							<div class="pointer-events-none invisible mt-3 flex justify-end gap-2" data-rhm-tiles-controls>
								<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_inner ); ?>" data-rhm-tiles-prev disabled>
									<?php echo rhm_get_icon( 'chevron', 'size-4 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								</button>
								<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_inner ); ?>" data-rhm-tiles-next disabled>
									<?php echo rhm_get_icon( 'chevron', 'size-4 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								</button>
							</div>
						</div>
					<?php elseif ( $rhm_count ) : ?>
						<?php
						$rhm_three = 3 === $rhm_count;
						$rhm_sizes = ( $rhm_three ? $rhm_sizes_3 : $rhm_sizes_2 ) . ( 1 === $rhm_count ? 'calc(100vw - 32px)' : 'calc(50vw - 22px)' );
						$rhm_grid  = match ( $rhm_count ) {
							1       => 'mt-4 grid gap-3 sm:grid-cols-2 sm:gap-4 lg:mt-6 lg:gap-6',
							2       => 'mt-4 grid grid-cols-2 gap-3 sm:gap-4 lg:mt-6 lg:gap-6',
							default => 'mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:mt-6 lg:gap-6',
						};
						?>
						<div class="<?php echo esc_attr( $rhm_grid ); ?>">
							<?php
							foreach ( $rhm_images as $rhm_index => $rhm_image ) {
								$rhm_figure( $rhm_image, $rhm_sizes, $rhm_priority && 0 === $rhm_index, 'min-w-0' );
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
