<?php
/**
 * Section: Ảnh trước – sau của khách hàng (trang chủ). Dữ liệu: inc/home-before-after.php
 * (Tùy biến → "Trước – sau (Trang chủ)"). CSS: utility (page-home.css).
 *
 * Mỗi thẻ: ảnh chân dung (trái, cao bằng cột phải) | ảnh "Trước" + ảnh "Sau" xếp dọc (phải, 3:2)
 * → tên khách (H3) + tỉnh/thành → dòng dịch vụ + dòng loại implant/vật liệu (có icon, trống thì không in).
 *
 * Slider native (scroll-snap, không Swiper) + nút ‹ › do src/js/_tiles.js xử lý (nạp động từ carousel.js):
 * - ≥ 1024px: mỗi "trang" = 4 thẻ (lưới 2×2) rộng hết khung, nút cuộn đúng 1 trang.
 * - 768–1023px: 2 thẻ / khung; < 768px: 1 thẻ rộng 88% (hé thẻ kế). Wrapper trang là display: contents
 *   dưới 1024px → thẻ là phần tử trực tiếp của track, nút cuộn 1 thẻ.
 * Bố cục dựng sẵn bằng CSS → chưa có JS vẫn vuốt được, CLS = 0. Ảnh lazy (section nằm dưới màn hình đầu).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_items = rhm_get_home_before_after();

if ( ! $rhm_items ) {
	return;
}

$rhm_count    = count( $rhm_items );
$rhm_id       = 'home-before-after';
$rhm_title_id = $rhm_id . '-title';
$rhm_track_id = $rhm_id . '-track';
$rhm_title    = trim( (string) get_theme_mod( 'rhm_home_ba_title', rhm_home_ba_default_title() ) ) ?: rhm_home_ba_default_title();
$rhm_eyebrow  = trim( (string) get_theme_mod( 'rhm_home_ba_eyebrow', rhm_home_ba_default_eyebrow() ) );

if ( $rhm_count > 1 && file_exists( RHM_THEME_DIR . '/dist/js/carousel.js' ) ) {
	wp_enqueue_script_module( 'rhm-carousel', RHM_THEME_URI . '/dist/js/carousel.js', array(), rhm_asset_version( 'dist/js/carousel.js' ) );
}

// Độ rộng 1 cột ảnh (chân dung / trước / sau cùng cột rộng; chân dung nên là ảnh dọc ~3:4 để object-cover không cần file lớn hơn).
// Khung nội dung W = min(100vw, 1440px) − 32px.
// ≥ 1440px: thẻ (1408 − 24) / 2 = 692 → trừ đệm 2×16 = 660 → (660 − 8) / 2 = 326px.
// 1024–1439px: thẻ 50vw − 28 → trừ đệm 32 → (50vw − 60 − 8) / 2 = 25vw − 34px.
// 768–1023px: thẻ W/2 − 8 = 50vw − 24 → trừ đệm 2×12 → (50vw − 48 − 8) / 2 = 25vw − 28px.
// < 768px: thẻ 88% W = 88vw − 28 → trừ đệm 24 → (88vw − 52 − 8) / 2 = 44vw − 30px.
$rhm_sizes = '(min-width: 1440px) 326px, (min-width: 1024px) calc(25vw - 34px), (min-width: 768px) calc(25vw - 28px), calc(44vw - 30px)';

$rhm_image = static function ( int $id, string $alt, string $class ) use ( $rhm_sizes ): string {
	return wp_get_attachment_image(
		$id,
		'full',
		false,
		array(
			'class'    => $class,
			'alt'      => $alt,
			'sizes'    => $rhm_sizes,
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
};

$rhm_nav_class = 'flex size-11 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-[0_4px_14px_rgb(16_42_67/0.1)] transition-colors hover:border-primary hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:border-line disabled:hover:bg-white disabled:hover:text-primary';
$rhm_label     = 'absolute bottom-0 left-0 rounded-tr-xl bg-deep px-3 py-1 text-sm font-bold text-white lg:px-4 lg:text-base';
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="py-12 lg:py-16" aria-labelledby="<?php echo esc_attr( $rhm_title_id ); ?>">
	<?php
	// < 1024px: tiêu đề → slider → nút ‹ ›. ≥ 1024px: hàng 1 = tiêu đề | nút ‹ › (không đặt nút ở mép thẻ: cột nút liên hệ nhanh
	// cố định ở mép phải màn hình sẽ đè lên), hàng 2 = slider rộng hết. Nút giữ chỗ bằng invisible (_tiles.js bật/tắt) → không xô bố cục.
	?>
	<div class="site-container flex flex-col lg:grid lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end lg:gap-x-8" data-rhm-tiles>
		<div class="mb-4 lg:mb-6">
			<?php if ( '' !== $rhm_eyebrow ) : ?>
				<p class="mb-3 flex items-center gap-2 text-[13px] font-semibold tracking-wide text-dark uppercase lg:text-sm">
					<span class="h-0.5 w-6 rounded-full bg-primary" aria-hidden="true"></span>
					<?php echo esc_html( $rhm_eyebrow ); ?>
				</p>
			<?php endif; ?>
			<h2 id="<?php echo esc_attr( $rhm_title_id ); ?>" class="text-[28px] leading-tight font-bold text-dark lg:text-4xl"><?php echo esc_html( $rhm_title ); ?></h2>
		</div>

		<?php if ( $rhm_count > 1 ) : ?>
			<div class="invisible order-3 mt-4 flex items-center justify-center gap-3 lg:order-none lg:mt-0 lg:mb-7" data-rhm-tiles-controls>
				<button class="<?php echo esc_attr( $rhm_nav_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'Khách hàng trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track_id ); ?>" data-rhm-tiles-prev disabled>
					<?php echo rhm_get_icon( 'arrow-right', 'size-5 rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
				<button class="<?php echo esc_attr( $rhm_nav_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'Khách hàng tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track_id ); ?>" data-rhm-tiles-next disabled>
					<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
			</div>
		<?php endif; ?>

		<?php
		// Đệm 8px ngang / 12px dọc + lề âm tương ứng: bóng thẻ không bị overflow của track cắt; scroll-padding giữ điểm dừng thẳng mép khung.
		?>
		<div id="<?php echo esc_attr( $rhm_track_id ); ?>" class="order-2 -mx-2 -my-3 flex snap-x snap-mandatory scroll-px-2 gap-4 overflow-x-auto overscroll-x-contain px-2 py-3 [scrollbar-width:none] lg:order-none lg:col-span-2 lg:gap-6" role="region" aria-labelledby="<?php echo esc_attr( $rhm_title_id ); ?>" data-rhm-tiles-track<?php echo $rhm_count > 1 ? ' tabindex="0"' : ''; ?>>
			<?php foreach ( array_chunk( $rhm_items, 4 ) as $rhm_page ) : ?>
				<div class="contents lg:grid lg:w-full lg:shrink-0 lg:snap-start lg:grid-cols-2 lg:gap-6">
					<?php foreach ( $rhm_page as $rhm_item ) : ?>
						<article class="w-[88%] shrink-0 snap-start rounded-3xl bg-white p-3 shadow-[0_6px_20px_rgb(16_42_67/0.1)] md:w-[calc(50%-8px)] lg:w-auto lg:p-4">
							<div class="grid grid-cols-2 gap-2">
								<div class="relative row-span-2 overflow-hidden rounded-2xl bg-soft">
									<?php echo $rhm_image( $rhm_item['portrait'], $rhm_item['name'], 'absolute inset-0 size-full object-cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
								</div>
								<div class="relative aspect-[3/2] overflow-hidden rounded-2xl bg-soft">
									<?php
									/* translators: %s: tên khách hàng. */
									echo $rhm_image( $rhm_item['before'], sprintf( __( 'Trước điều trị – %s', 'themeranghammatsg' ), $rhm_item['name'] ), 'size-full object-cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image().
									?>
									<span class="<?php echo esc_attr( $rhm_label ); ?>" aria-hidden="true"><?php esc_html_e( 'Trước', 'themeranghammatsg' ); ?></span>
								</div>
								<div class="relative aspect-[3/2] overflow-hidden rounded-2xl bg-soft">
									<?php
									/* translators: %s: tên khách hàng. */
									echo $rhm_image( $rhm_item['after'], sprintf( __( 'Sau điều trị – %s', 'themeranghammatsg' ), $rhm_item['name'] ), 'size-full object-cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image().
									?>
									<span class="<?php echo esc_attr( $rhm_label ); ?>" aria-hidden="true"><?php esc_html_e( 'Sau', 'themeranghammatsg' ); ?></span>
								</div>
							</div>

							<div class="mt-4 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 px-1 wrap-anywhere">
								<h3 class="text-base font-bold text-primary uppercase lg:text-lg"><?php echo esc_html( $rhm_item['name'] ); ?></h3>
								<?php if ( '' !== $rhm_item['location'] ) : ?>
									<p class="text-sm font-bold text-muted uppercase lg:text-base"><?php echo esc_html( $rhm_item['location'] ); ?></p>
								<?php endif; ?>
							</div>

							<?php if ( '' !== $rhm_item['service'] || '' !== $rhm_item['product'] ) : ?>
								<ul class="mt-2 space-y-1.5 px-1 text-[15px] leading-snug text-body lg:text-base">
									<?php if ( '' !== $rhm_item['service'] ) : ?>
										<li class="flex gap-2">
											<?php echo rhm_icon_svg( 'menu', 'tooth', 'mt-px size-5 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
											<?php echo esc_html( $rhm_item['service'] ); ?>
										</li>
									<?php endif; ?>
									<?php if ( '' !== $rhm_item['product'] ) : ?>
										<li class="flex gap-2">
											<?php echo rhm_icon_svg( 'menu', 'implant', 'mt-px size-5 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
											<?php echo esc_html( $rhm_item['product'] ); ?>
										</li>
									<?php endif; ?>
								</ul>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
