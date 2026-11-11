<?php
/**
 * Chi nhánh trang chủ: ảnh → tên → địa chỉ, không gắn permalink.
 * Dữ liệu: inc/home-branches.php. Carousel dùng chung, không tự chạy.
 * Độ rộng slide dựng sẵn bằng CSS để không đổi bố cục khi Swiper khởi tạo.
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

// Độ rộng ảnh thật: khung 1440px, lề 16px, nút 40/44px + khoảng cách, đệm slide 8/12/32px, viền thẻ 1px mỗi bên.
$rhm_sizes = '(min-width: 1440px) 254px, (min-width: 1280px) calc(25vw - 106px), (min-width: 1024px) calc(25vw - 64px), (min-width: 768px) calc(33.33vw - 64px), (min-width: 640px) calc(50vw - 26px), calc(80vw - 31px)';
?>
<section<?php echo '' !== $rhm_id ? ' id="' . esc_attr( $rhm_id ) . '"' : ''; ?> class="home-branches py-8 lg:py-10" aria-labelledby="<?php echo esc_attr( $rhm_title_id ); ?>">
	<div class="site-container">
		<h2 id="<?php echo esc_attr( $rhm_title_id ); ?>" class="mb-4 text-center text-[22px] leading-tight font-bold text-[#143c45] uppercase sm:text-2xl lg:mb-6 lg:text-[32px]">
			<?php echo esc_html( $rhm_title ); ?>
		</h2>

		<div class="js-carousel flex items-center md:gap-3 lg:gap-4 xl:gap-5">
			<button class="carousel-prev hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#eff0f3] text-[#33393f] transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-[#eff0f3] disabled:hover:text-[#33393f] md:flex lg:size-11 <?php echo esc_attr( $rhm_nav_hidden ); ?>" type="button" aria-label="<?php esc_attr_e( 'Chi nhánh trước', 'themeranghammatsg' ); ?>" disabled>
				<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>

			<div class="swiper -mx-2 min-w-0 flex-1 md:mx-0">
				<div class="swiper-wrapper <?php echo esc_attr( $rhm_center ); ?>">
					<?php foreach ( $rhm_items as $rhm_item ) : ?>
						<div class="swiper-slide w-[80%] px-2 py-2 sm:w-1/2 md:w-1/3 lg:w-1/4 lg:px-3 xl:px-8">
							<article class="flex w-full flex-col overflow-hidden rounded-2xl border border-line bg-white shadow-card">
								<?php
								echo wp_get_attachment_image(
									$rhm_item['image'],
									'full',
									false,
									array(
										'class'    => 'aspect-[4/3] h-auto w-full object-cover',
										'alt'      => $rhm_item['name'],
										'sizes'    => $rhm_sizes,
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
								?>
							<div class="flex-1 p-4 wrap-anywhere">
									<h3 class="mb-2 text-xl leading-snug font-bold text-dark"><?php echo esc_html( $rhm_item['name'] ); ?></h3>
									<p class="text-lg leading-relaxed text-dark"><?php echo nl2br( esc_html( $rhm_item['address'] ) ); ?></p>
								</div>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<button class="carousel-next hidden size-10 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[#eff0f3] text-[#33393f] transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-[#eff0f3] disabled:hover:text-[#33393f] md:flex lg:size-11 <?php echo esc_attr( $rhm_nav_hidden ); ?>" type="button" aria-label="<?php esc_attr_e( 'Chi nhánh sau', 'themeranghammatsg' ); ?>">
				<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>
		</div>
	</div>
</section>
