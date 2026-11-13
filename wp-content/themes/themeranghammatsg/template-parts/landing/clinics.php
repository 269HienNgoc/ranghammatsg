<?php
/**
 * Landing – Hệ thống phòng khám: tiêu đề + mô tả canh giữa → hàng chi nhánh (tên + nút "Chỉ đường") → slider ảnh lớn
 * (1 ảnh rộng hết khung / lần, mỗi ảnh có tiêu đề + chú thích riêng ngay dưới) → dải ảnh nhỏ: bấm ô nào thì slider chuyển tới ảnh đó.
 * Field: inc/landing/layouts/clinics.php. Thiếu tiêu đề section hoặc không có ảnh hợp lệ (ảnh + tiêu đề) → không hiện.
 *
 * Hiệu năng / truy cập:
 * - Không Swiper: slider là native scroll + scroll-snap (vuốt được khi chưa có JS, bố cục dựng bằng CSS → CLS = 0).
 *   Slide = ảnh + chú thích; các slide cao bằng nhau (flex stretch) → chuyển ảnh không làm trang nhảy.
 * - JS nạp qua carousel.js: _tiles.js (nút ‹ ›, cuộn 1 slide) + _showcase.js (dải ảnh nhỏ ↔ slide đang xem).
 * - Ảnh lazy (trừ ảnh đầu khi section đứng đầu trang).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );

$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_image = rhm_landing_image( $rhm_row, 'image' );
	$rhm_name  = rhm_landing_text( $rhm_row, 'title' );
	if ( $rhm_image && '' !== $rhm_name ) {
		$rhm_items[] = array( $rhm_image, $rhm_name, rhm_landing_lines( $rhm_row, 'text' ) );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_branches = array();
foreach ( rhm_landing_rows( $rhm_data, 'branches' ) as $rhm_row ) {
	$rhm_name = rhm_landing_text( $rhm_row, 'name' );
	if ( '' !== $rhm_name ) {
		$rhm_branches[] = array( $rhm_name, rhm_landing_url( $rhm_row, 'map_url' ) );
	}
}

$rhm_id      = $args['id'];
$rhm_head_id = $rhm_id . '-title';
$rhm_track   = $rhm_id . '-track';
$rhm_count   = count( $rhm_items );
$rhm_multi   = $rhm_count > 1;
if ( $rhm_multi ) {
	rhm_landing_enqueue_module( 'carousel' );
}

// Ảnh lớn rộng hết khung W = 1408px (≥ 1440px) hoặc 100vw − 32px.
// ≥ 640px khung 16:9 = tỉ lệ ảnh khuyến nghị → tải theo độ rộng khung.
// < 640px khung 4:3 phủ ảnh 16:9 (object-cover): độ rộng ảnh cần = cao khung × 16/9 = (100vw − 32) × 4/3 → calc(133.33vw − 43px).
$rhm_sizes = '(min-width: 1440px) 1408px, (min-width: 640px) calc(100vw - 32px), calc(133.33vw - 43px)';
// Ảnh nhỏ 16:9: dải có đệm 4px mỗi bên (chừa chỗ viền ô đang xem), ô cách 12px.
// ≥ 1024px 6 ô: (W − 8 − 60) / 6 → 223px | calc(16.67vw − 17px). 640–1023px 4 ô: (W − 8 − 36) / 4 → calc(25vw − 19px).
// < 640px ô 40%: 0,4 × (100vw − 40) → calc(40vw − 16px).
$rhm_thumb_sizes = '(min-width: 1440px) 223px, (min-width: 1024px) calc(16.67vw - 17px), (min-width: 640px) calc(25vw - 19px), calc(40vw - 16px)';

$rhm_arrow = 'pointer-events-auto flex size-10 cursor-pointer items-center justify-center rounded-full border border-line bg-white/90 text-primary shadow-card transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-0 lg:size-11';
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>" data-rhm-showcase>
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'center' ); ?>

		<?php foreach ( rhm_landing_lines( $rhm_data, 'description' ) as $rhm_line ) : ?>
			<p class="mx-auto mb-3 max-w-4xl text-center leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
		<?php endforeach; ?>

		<?php if ( $rhm_branches ) : ?>
			<ul class="mt-5 mb-6 flex flex-wrap justify-center gap-3 lg:mb-8 lg:gap-4">
				<?php foreach ( $rhm_branches as [ $rhm_name, $rhm_url ] ) : ?>
					<?php // Mobile: viên rộng hết hàng, tên tự xuống dòng; nút Chỉ đường luôn 1 dòng. ?>
					<li class="<?php echo esc_attr( '' !== $rhm_url ? 'flex min-h-11 w-full items-center justify-between gap-3 rounded-full bg-mint py-1.5 pr-1.5 pl-5 sm:w-auto' : 'flex min-h-11 w-full items-center justify-center rounded-full bg-mint px-5 py-1.5 sm:w-auto' ); ?>">
						<span class="min-w-0 leading-snug font-bold text-primary"><?php echo esc_html( $rhm_name ); ?></span>
						<?php if ( '' !== $rhm_url ) : ?>
							<a class="inline-flex h-8 shrink-0 items-center gap-1 rounded-full bg-footer-accent px-3 text-sm font-semibold whitespace-nowrap text-dark transition hover:brightness-95" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo rhm_external_link_attrs( $rhm_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
								<?php echo rhm_get_icon( 'map-pin', 'size-4 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<?php esc_html_e( 'Chỉ đường', 'themeranghammatsg' ); ?>
								<span class="sr-only">
									<?php
									/* translators: %s: tên chi nhánh */
									echo esc_html( sprintf( __( 'tới %s', 'themeranghammatsg' ), $rhm_name ) );
									?>
								</span>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="relative" data-rhm-tiles>
			<div id="<?php echo esc_attr( $rhm_track ); ?>" class="flex snap-x snap-mandatory overflow-x-auto overscroll-x-contain [scrollbar-width:none]" aria-label="<?php echo esc_attr( $rhm_title ); ?>" data-rhm-tiles-track<?php echo $rhm_multi ? ' tabindex="0"' : ''; ?>>
				<?php foreach ( $rhm_items as $rhm_index => [ $rhm_image, $rhm_name, $rhm_lines ] ) : ?>
					<div class="flex w-full shrink-0 snap-start flex-col" role="group" aria-roledescription="<?php esc_attr_e( 'ảnh', 'themeranghammatsg' ); ?>" aria-label="<?php echo esc_attr( ( $rhm_index + 1 ) . ' / ' . $rhm_count . ': ' . $rhm_name ); ?>">
						<span class="relative block aspect-[4/3] overflow-hidden rounded-xl bg-soft sm:aspect-video">
							<?php
							// Chỉ ảnh đầu, khi section đứng đầu trang, mới là ảnh LCP.
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
							echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', $args['is_first'] && 0 === $rhm_index );
							?>
						</span>
						<div class="mt-4 flex-1 overflow-hidden rounded-xl border border-line bg-mint">
							<h3 class="bg-deep px-4 py-2.5 text-lg leading-snug font-bold text-white lg:px-5"><?php echo esc_html( $rhm_name ); ?></h3>
							<?php if ( $rhm_lines ) : ?>
								<div class="px-4 py-3 lg:px-5 lg:py-4">
									<?php foreach ( $rhm_lines as $rhm_line ) : ?>
										<p class="mt-2 leading-relaxed text-body first:mt-0"><?php echo esc_html( $rhm_line ); ?></p>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $rhm_multi ) : ?>
				<?php
				// Nút ‹ › canh giữa theo chiều cao ẢNH (lớp phủ cùng khung tỉ lệ với ảnh), không theo cả slide.
				// ≥ 1024px lùi vào 96px: cột nút liên hệ nhanh (fixed, cách mép phải màn hình ~88px) không che nút ›.
				?>
				<div class="pointer-events-none invisible absolute inset-x-0 top-0 flex aspect-[4/3] items-center justify-between px-2 sm:aspect-video lg:px-24" data-rhm-tiles-controls>
					<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-prev disabled>
						<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</button>
					<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-next disabled>
						<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</button>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $rhm_multi ) : ?>
			<div class="mt-6 flex snap-x snap-mandatory gap-3 overflow-x-auto overscroll-x-contain p-1 lg:mt-8" data-rhm-showcase-thumbs>
				<?php foreach ( $rhm_items as $rhm_index => [ $rhm_image, $rhm_name ] ) : ?>
					<button type="button" class="relative block aspect-video w-[40%] shrink-0 cursor-pointer snap-start overflow-hidden rounded-lg bg-soft opacity-70 ring-primary transition-opacity hover:opacity-100 aria-[current=true]:opacity-100 aria-[current=true]:ring-3 sm:w-[calc((100%-2.25rem)/4)] lg:w-[calc((100%-3.75rem)/6)]" aria-controls="<?php echo esc_attr( $rhm_track ); ?>"<?php echo $rhm_index ? '' : ' aria-current="true"'; ?>>
						<?php
						// Ảnh nhỏ chỉ minh họa, tên nằm ở chữ ẩn → alt rỗng (không đọc lặp).
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
						echo rhm_landing_picture( $rhm_image, $rhm_thumb_sizes, 'absolute inset-0 size-full object-cover', false, 0, '', '' );
						?>
						<span class="sr-only">
							<?php
							/* translators: 1: số thứ tự ảnh, 2: tiêu đề ảnh */
							echo esc_html( sprintf( __( 'Xem ảnh %1$d: %2$s', 'themeranghammatsg' ), $rhm_index + 1, $rhm_name ) );
							?>
						</span>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
