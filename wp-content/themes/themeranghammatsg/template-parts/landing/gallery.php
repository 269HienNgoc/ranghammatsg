<?php
/**
 * Landing – Thư viện ảnh (slider, khung 4:3). Field: inc/landing/layouts/gallery.php. Thiếu tiêu đề hoặc ảnh → không hiện.
 *
 * Số ảnh / khung: < 640px 1 ảnh + hé ảnh kế (vuốt) | 640px 2 | 1024px 3 (+ nút ‹ › từ 768px).
 * Hiệu năng (cùng cách làm với template-parts/sections/certificates.php):
 * - Bố cục slider dựng sẵn bằng CSS (độ rộng slide = utility, Swiper slidesPerView 'auto') → CLS = 0 trước/sau khi JS chạy.
 * - JS (src/js/carousel.js) chỉ nạp khi có ≥ 2 ảnh; Swiper chỉ tải khi section sắp vào màn hình.
 * - Ảnh lazy (trừ khi section là section đầu trang), sizes = độ rộng ảnh thật theo bố cục.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data   = $args['data'] ?? array();
$rhm_title  = rhm_landing_text( $rhm_data, 'title' );
$rhm_images = rhm_landing_gallery( $rhm_data, 'images' );
if ( '' === $rhm_title || ! $rhm_images ) {
	return;
}

$rhm_count   = count( $rhm_images );
$rhm_head_id = $args['id'] . '-title';
if ( $rhm_count > 1 ) {
	rhm_landing_enqueue_module( 'carousel' );
}

// Ít ảnh hơn số ô của khung → canh giữa từ breakpoint mà tất cả ảnh vừa khung.
$rhm_center = match ( $rhm_count ) {
	1       => 'swiper-wrapper justify-center',
	2       => 'swiper-wrapper sm:justify-center',
	3       => 'swiper-wrapper lg:justify-center',
	default => 'swiper-wrapper',
};

// Nút ‹ › (hiện từ 768px) giữ chỗ nhưng ẩn khi mọi ảnh đã vừa khung.
$rhm_nav = match ( true ) {
	$rhm_count <= 2 => ' invisible',
	3 === $rhm_count => ' lg:invisible',
	default          => '',
};

// Độ rộng ảnh: khung 1408px − 2 × (nút 44 + cách 16) = 1288 / 3 − đệm 24 = 405px; 1024–1439px: (100vw − 32 − 120) / 3 − 24;
// 768–1023px: (100vw − 32 − 2 × (40 + 12)) / 2 − 16; 640–767px: (100vw − 16) / 2 − 16; < 640px: 85% × (100vw − 16) − 16.
$rhm_sizes = '(min-width: 1440px) 405px, (min-width: 1024px) calc(33.33vw - 75px), (min-width: 768px) calc(50vw - 84px), (min-width: 640px) calc(50vw - 24px), calc(85vw - 30px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), rhm_landing_text( $rhm_data, 'description' ), 'center' ); ?>

		<div class="<?php echo esc_attr( $rhm_count > 1 ? 'js-carousel flex items-center md:gap-3 lg:gap-4' : 'flex items-center md:gap-3 lg:gap-4' ); ?>">
			<?php if ( $rhm_count > 1 ) : ?>
				<button class="carousel-prev hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-card transition-colors hover:border-primary hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:border-line disabled:hover:bg-white disabled:hover:text-primary md:flex lg:size-11<?php echo esc_attr( $rhm_nav ); ?>" type="button" aria-label="<?php esc_attr_e( 'Ảnh trước', 'themeranghammatsg' ); ?>" disabled>
					<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
			<?php endif; ?>

			<div class="swiper -mx-2 min-w-0 flex-1 md:mx-0">
				<div class="<?php echo esc_attr( $rhm_center ); ?>">
					<?php foreach ( $rhm_images as $rhm_index => $rhm_image ) : ?>
						<figure class="swiper-slide w-[85%] px-2 py-2 sm:w-1/2 lg:w-1/3 lg:px-3">
							<span class="relative block aspect-[4/3] overflow-hidden rounded-2xl bg-soft shadow-card">
								<?php
								// Chỉ ảnh đầu của section đầu trang mới là ảnh LCP.
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
								echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', $args['is_first'] && 0 === $rhm_index );
								?>
							</span>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $rhm_count > 1 ) : ?>
				<button class="carousel-next hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-card transition-colors hover:border-primary hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:border-line disabled:hover:bg-white disabled:hover:text-primary md:flex lg:size-11<?php echo esc_attr( $rhm_nav ); ?>" type="button" aria-label="<?php esc_attr_e( 'Ảnh tiếp theo', 'themeranghammatsg' ); ?>">
					<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</section>
