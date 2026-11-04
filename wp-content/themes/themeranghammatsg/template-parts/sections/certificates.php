<?php
/**
 * Section: chứng nhận tiêu chuẩn (slider ảnh). Dữ liệu: inc/certificates.php. CSS: page-home.css
 *
 * Số ảnh / khung: < 640px 1 ảnh + hé ảnh kế (vuốt) | 640px 2 | 768px 3 (+ nút ‹ ›) | 1024px 4.
 * Hiệu năng:
 * - Bố cục slider dựng sẵn bằng CSS (độ rộng slide = utility bên dưới, Swiper dùng slidesPerView 'auto')
 *   → trước và sau khi JS chạy giống hệt nhau, CLS = 0.
 * - JS (src/js/carousel.js) chỉ nạp khi có ≥ 2 ảnh; Swiper chỉ tải khi section sắp vào màn hình.
 * - Ảnh lazy + decoding async, srcset từ size rhm-* với sizes = độ rộng ảnh thật theo bố cục.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_items = rhm_get_certificates();

if ( ! $rhm_items ) {
	return;
}

$rhm_count = count( $rhm_items );

if ( $rhm_count > 1 && file_exists( RHM_THEME_DIR . '/dist/js/carousel.js' ) ) {
	wp_enqueue_script_module( 'rhm-carousel', RHM_THEME_URI . '/dist/js/carousel.js', array(), rhm_asset_version( 'dist/js/carousel.js' ) );
}

// Ít ảnh hơn số ô của khung → canh giữa (từ breakpoint mà tất cả ảnh vừa khung).
$rhm_center = match ( $rhm_count ) {
	1       => 'justify-center',
	2       => 'sm:justify-center',
	3       => 'md:justify-center',
	4       => 'lg:justify-center',
	default => '',
};

// Nút ‹ › giữ chỗ (layout không đổi) nhưng ẩn khi tất cả ảnh đã vừa khung. Swiper cũng tự ẩn (.swiper-button-lock).
$rhm_nav_hidden = match ( true ) {
	$rhm_count <= 3 => 'invisible',
	4 === $rhm_count => 'lg:invisible',
	default          => '',
};

// Độ rộng ảnh thật: khung 1440px, lề 16px, nút 40/44px + khoảng cách, đệm slide 8/12/32px, đệm + viền thẻ 7px mỗi bên.
$rhm_sizes = '(min-width: 1440px) 242px, (min-width: 1280px) calc(25vw - 118px), (min-width: 1024px) calc(25vw - 76px), (min-width: 768px) calc(33.33vw - 76px), (min-width: 640px) calc(50vw - 38px), calc(80vw - 43px)';
?>
<section class="py-8 lg:py-10" aria-labelledby="certificates-title">
	<div class="site-container">
		<h2 id="certificates-title" class="mb-4 text-center text-[22px] leading-tight font-bold text-[#143c45] uppercase sm:text-2xl lg:mb-6 lg:text-[32px]">
			<?php echo esc_html( get_theme_mod( 'rhm_cert_title', rhm_certificates_default_title() ) ); ?>
		</h2>

		<div class="js-carousel flex items-center md:gap-3 lg:gap-4 xl:gap-5">
			<button class="carousel-prev hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#eff0f3] text-[#33393f] transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-[#eff0f3] disabled:hover:text-[#33393f] md:flex lg:size-11 <?php echo esc_attr( $rhm_nav_hidden ); ?>" type="button" aria-label="<?php esc_attr_e( 'Chứng nhận trước', 'themeranghammatsg' ); ?>" disabled>
				<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>

			<div class="swiper -mx-2 min-w-0 flex-1 md:mx-0">
				<div class="swiper-wrapper <?php echo esc_attr( $rhm_center ); ?>">
					<?php foreach ( $rhm_items as $rhm_item ) : ?>
						<div class="swiper-slide w-[80%] px-2 py-2 sm:w-1/2 md:w-1/3 lg:w-1/4 lg:px-3 xl:px-8">
							<figure class="rounded-sm border border-[#e3e7eb] bg-white p-1.5 shadow-[0_2px_10px_rgb(16_42_67/0.06)]">
								<?php
								echo wp_get_attachment_image(
									$rhm_item['image'],
									'full',
									false,
									array(
										'class'    => 'aspect-[4/3] w-full object-contain',
										'alt'      => $rhm_item['alt'],
										'sizes'    => $rhm_sizes,
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
								?>
							</figure>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<button class="carousel-next hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#eff0f3] text-[#33393f] transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-[#eff0f3] disabled:hover:text-[#33393f] md:flex lg:size-11 <?php echo esc_attr( $rhm_nav_hidden ); ?>" type="button" aria-label="<?php esc_attr_e( 'Chứng nhận sau', 'themeranghammatsg' ); ?>">
				<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>
		</div>
	</div>
</section>
