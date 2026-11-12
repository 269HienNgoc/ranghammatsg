<?php
/**
 * Section: Hệ thống chi nhánh trang chủ (slider). Dữ liệu: inc/home-branches.php. CSS: utility + page-home.css
 *
 * Giao diện đồng bộ các section khác của trang chủ:
 * - Tiêu đề như "Đội ngũ bác sĩ": eyebrow có vạch xanh + H2 căn trái; ≥ 1024px nút ‹ › ở hàng tiêu đề
 *   (không đè mép thẻ → không chồng lên cột nút liên hệ nhanh bên phải), < 1024px nút nằm dưới slider.
 * - Thẻ như khối Tin tức: bo 2xl, viền nhạt, bóng mềm; ảnh 4:3 → tên (H3) + vạch gradient → địa chỉ / hotline có icon → link "Chỉ đường" (field URL Google Maps của chi nhánh, trống thì không in).
 *   Dòng nào thiếu dữ liệu thì không in (hotline tùy chọn).
 *
 * Hiệu năng:
 * - Độ rộng slide dựng sẵn bằng CSS (Swiper slidesPerView 'auto') → trước/sau khi JS chạy giống hệt, CLS = 0.
 * - JS: src/js/carousel.js (chỉ khi ≥ 2 chi nhánh), Swiper chỉ tải khi section sắp vào màn hình. Không tự chạy.
 * - Ảnh lazy (section nằm dưới màn hình đầu), sizes = độ rộng ảnh thật.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_items = rhm_get_home_branches();

if ( ! $rhm_items ) {
	return;
}

$rhm_count    = count( $rhm_items );
$rhm_id       = 'home-branches';
$rhm_title_id = $rhm_id . '-title';
$rhm_title    = trim( (string) get_theme_mod( 'rhm_home_branches_title', rhm_home_branches_default_title() ) ) ?: rhm_home_branches_default_title();
$rhm_eyebrow  = trim( (string) get_theme_mod( 'rhm_home_branches_eyebrow', rhm_home_branches_default_eyebrow() ) );

if ( $rhm_count > 1 && file_exists( RHM_THEME_DIR . '/dist/js/carousel.js' ) ) {
	wp_enqueue_script_module( 'rhm-carousel', RHM_THEME_URI . '/dist/js/carousel.js', array(), rhm_asset_version( 'dist/js/carousel.js' ) );
}

// Ít thẻ hơn số ô của khung → canh giữa (từ breakpoint mà tất cả thẻ vừa khung).
$rhm_center = match ( $rhm_count ) {
	1       => 'justify-center',
	2       => 'sm:justify-center',
	3       => 'md:justify-center',
	4       => 'lg:justify-center',
	default => '',
};

// Ẩn nút ‹ › từ breakpoint mà tất cả thẻ đã vừa khung (< 640px luôn hé thẻ kế → luôn cần nút).
$rhm_nav_hidden = match ( $rhm_count ) {
	2       => 'sm:hidden',
	3       => 'md:hidden',
	4       => 'lg:hidden',
	default => '',
};

// Độ rộng ảnh thật = slide − đệm slide (8px / 12px từ lg mỗi bên) − viền thẻ 1px mỗi bên.
// Khung .swiper = khung nội dung (min(100vw, 1440px) − lề 32px) + lề âm 2 bên.
$rhm_sizes = '(min-width: 1440px) 332px, (min-width: 1024px) calc(25vw - 28px), (min-width: 768px) calc(33.33vw - 24px), (min-width: 640px) calc(50vw - 26px), calc(80vw - 31px)';

$rhm_nav_class = 'flex size-11 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-[0_4px_14px_rgb(16_42_67/0.1)] transition-colors hover:border-primary hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:border-line disabled:hover:bg-white disabled:hover:text-primary';
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="home-branches overflow-hidden py-12 lg:py-16" aria-labelledby="<?php echo esc_attr( $rhm_title_id ); ?>">
	<div class="site-container flex flex-col lg:grid lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end lg:gap-x-8<?php echo $rhm_count > 1 ? ' js-carousel' : ''; ?>">
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
			<div class="order-3 mt-4 flex items-center justify-center gap-3 lg:order-none lg:mt-0 lg:mb-7 <?php echo esc_attr( $rhm_nav_hidden ); ?>">
				<button class="carousel-prev <?php echo esc_attr( $rhm_nav_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'Chi nhánh trước', 'themeranghammatsg' ); ?>" disabled>
					<?php echo rhm_get_icon( 'arrow-right', 'size-5 rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
				<button class="carousel-next <?php echo esc_attr( $rhm_nav_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'Chi nhánh sau', 'themeranghammatsg' ); ?>">
					<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
			</div>
		<?php endif; ?>

		<div class="order-2 lg:order-none lg:col-span-2">
			<?php // Slide đệm 8/12px ngang, 8px trên, 40px dưới (bóng 6+20px, khi rê chuột nổi 4px + bóng 12+28px) + lề âm tương ứng của .swiper
			// → bóng không bị overflow: hidden cắt thành vệt; lề âm dưới 32px nên khoảng trống thật dưới thẻ chỉ còn 8px. ?>
			<div class="swiper -mx-2 -mt-2 -mb-8 lg:-mx-3">
				<div class="swiper-wrapper <?php echo esc_attr( $rhm_center ); ?>">
					<?php foreach ( $rhm_items as $rhm_item ) : ?>
						<?php
						$rhm_hotline = trim( (string) ( $rhm_item['hotline'] ?? '' ) );
						// "Chỉ đường": đúng URL nhập ở field "URL Google Maps" của chi nhánh; trống → không in nút.
						$rhm_map_url = (string) ( $rhm_item['map_url'] ?? '' );
						?>
						<div class="swiper-slide w-[80%] px-2 pt-2 pb-10 sm:w-1/2 md:w-1/3 lg:w-1/4 lg:px-3">
							<article class="group flex w-full flex-col overflow-hidden rounded-2xl border border-[#edf1f5] bg-white shadow-[0_6px_20px_rgb(16_42_67/0.07)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_28px_rgb(16_42_67/0.12)]">
								<div class="overflow-hidden bg-soft">
									<?php
									echo wp_get_attachment_image(
										$rhm_item['image'],
										'full',
										false,
										array(
											'class'    => 'aspect-[4/3] h-auto w-full object-cover transition-transform duration-500 group-hover:scale-105',
											'alt'      => $rhm_item['name'],
											'sizes'    => $rhm_sizes,
											'loading'  => 'lazy',
											'decoding' => 'async',
										)
									);
									?>
								</div>

								<div class="flex flex-1 flex-col p-5 wrap-anywhere">
									<h3 class="text-lg leading-snug font-bold text-dark lg:text-xl">
										<?php echo esc_html( $rhm_item['name'] ); ?>
										<span class="mt-2.5 block h-[3px] w-12 rounded-full bg-linear-to-r from-primary to-green/40" aria-hidden="true"></span>
									</h3>

									<ul class="mt-4 mb-5 space-y-3 text-[15px] leading-relaxed text-body/85">
										<li class="flex gap-3">
											<span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-mint text-primary">
												<?php echo rhm_get_icon( 'map-pin', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
											</span>
											<span class="pt-1"><span class="sr-only"><?php esc_html_e( 'Địa chỉ:', 'themeranghammatsg' ); ?> </span><?php echo nl2br( esc_html( $rhm_item['address'] ), false ); ?></span>
										</li>
										<?php if ( '' !== $rhm_hotline ) : ?>
											<li class="flex items-center gap-3">
												<span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-mint text-primary">
													<?php echo rhm_get_icon( 'phone', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
												</span>
												<a class="font-semibold text-primary hover:text-primary-dark" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>">
													<span class="sr-only"><?php esc_html_e( 'Hotline:', 'themeranghammatsg' ); ?> </span><?php echo esc_html( $rhm_hotline ); ?>
												</a>
											</li>
										<?php endif; ?>
									</ul>

									<?php if ( '' !== $rhm_map_url ) : ?>
										<p class="mt-auto border-t border-line pt-4">
											<a class="inline-flex min-h-6 items-center gap-1.5 text-sm font-semibold text-primary hover:text-primary-dark" href="<?php echo esc_url( $rhm_map_url ); ?>"<?php echo rhm_external_link_attrs( $rhm_map_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
												<?php esc_html_e( 'Chỉ đường', 'themeranghammatsg' ); ?>
												<span class="sr-only"><?php echo esc_html( $rhm_item['name'] ); ?></span>
												<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
											</a>
										</p>
									<?php endif; ?>
								</div>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
