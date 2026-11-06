<?php
/**
 * Section: hero banner trang chủ (Swiper). Ảnh + link quản lý ở Customizer → "Hero banner (Trang chủ)".
 * CSS: page-home.css. JS: src/js/home.js (chỉ nạp khi có từ 2 slide).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_slides = rhm_get_hero_slides();

if ( ! $rhm_slides ) {
	return;
}

$rhm_has_slider = count( $rhm_slides ) > 1;

if ( $rhm_has_slider && file_exists( RHM_THEME_DIR . '/dist/js/home.js' ) ) {
	wp_enqueue_script_module( 'rhm-home', RHM_THEME_URI . '/dist/js/home.js', array(), rhm_asset_version( 'dist/js/home.js' ) );
}
?>
<section class="relative mx-auto max-w-[2048px]" aria-label="<?php esc_attr_e( 'Banner', 'themeranghammatsg' ); ?>">
	<div class="swiper hero-swiper">
		<div class="swiper-wrapper">
			<?php foreach ( $rhm_slides as $rhm_index => $rhm_slide ) : ?>
				<div class="swiper-slide bg-soft">
					<?php
					$rhm_picture = rhm_hero_picture( $rhm_slide, 0 === $rhm_index );
					if ( $rhm_slide['link'] ) {
						$rhm_picture = '<a class="block" href="' . esc_url( $rhm_slide['link'] ) . '"' . rhm_external_link_attrs( $rhm_slide['link'] ) . '>' . $rhm_picture . '</a>';
					}
					echo $rhm_picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped in rhm_html_attrs().
					?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $rhm_has_slider ) : ?>
			<div class="hero-pagination absolute inset-x-0 bottom-1.5 z-10 flex justify-center lg:bottom-3.5"></div>
			<button class="hero-prev absolute top-1/2 left-4 z-10 hidden size-12 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-white/85 text-dark shadow-card transition-colors hover:bg-white hover:text-primary lg:flex" type="button">
				<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>
			<button class="hero-next absolute top-1/2 right-4 z-10 hidden size-12 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-white/85 text-dark shadow-card transition-colors hover:bg-white hover:text-primary lg:flex" type="button">
				<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			</button>
		<?php endif; ?>
	</div>
</section>
