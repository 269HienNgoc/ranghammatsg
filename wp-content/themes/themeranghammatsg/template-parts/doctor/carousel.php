<?php
/** Khung slider chung; giữ nguyên độ rộng và padding trước/sau Swiper. */
defined( 'ABSPATH' ) || exit;
$rhm_items   = $args['items'];
$rhm_variant = $args['variant'];
if ( ! $rhm_items || ! in_array( $rhm_variant, array( 'photos', 'documents', 'cases', 'doctors' ), true ) ) {
	return;
}
$rhm_count = count( $rhm_items );
if ( $rhm_count > 1 && file_exists( RHM_THEME_DIR . '/dist/js/carousel.js' ) ) {
	wp_enqueue_script_module( 'rhm-carousel', RHM_THEME_URI . '/dist/js/carousel.js', array(), rhm_asset_version( 'dist/js/carousel.js' ) );
}
// Mỗi slide có đệm phải 16px; desktop 3 ảnh hoạt động/ca hoặc 4 văn bằng/bác sĩ, tablet 2, mobile 85%.
$rhm_sizes = in_array( $rhm_variant, array( 'documents', 'doctors' ), true )
	? '(min-width: 1440px) 336px, (min-width: 1024px) calc(25vw - 24px), (min-width: 640px) calc(50vw - 32px), calc(85vw - 43px)'
	: '(min-width: 1440px) 453px, (min-width: 1024px) calc((100vw - 80px) / 3), (min-width: 640px) calc(50vw - 32px), calc(85vw - 43px)';
?>
<div class="doctor-carousel doctor-carousel--<?php echo esc_attr( $rhm_variant ); ?><?php echo $rhm_count > 1 ? ' js-carousel' : ''; ?>" data-count="<?php echo esc_attr( $rhm_count ); ?>">
	<?php if ( $rhm_count > 1 ) : ?>
		<div class="doctor-carousel__controls">
			<button class="carousel-prev" type="button" aria-label="<?php esc_attr_e( 'Xem mục trước', 'themeranghammatsg' ); ?>" disabled><?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); ?></button>
			<button class="carousel-next" type="button" aria-label="<?php esc_attr_e( 'Xem mục tiếp theo', 'themeranghammatsg' ); ?>"><?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); ?></button>
		</div>
	<?php endif; ?>
	<div class="swiper">
		<div class="swiper-wrapper">
			<?php foreach ( $rhm_items as $rhm_item ) : ?>
				<div class="swiper-slide">
					<?php
					if ( 'cases' === $rhm_variant ) {
						get_template_part( 'template-parts/doctor/case-card', null, array( 'item' => $rhm_item ) );
					} elseif ( 'doctors' === $rhm_variant ) {
						get_template_part( 'template-parts/doctor/profile-card', null, array( 'id' => $rhm_item->ID, 'sizes' => $rhm_sizes ) );
					} else {
						?>
						<figure class="doctor-gallery-image">
							<?php rhm_doctor_image( $rhm_item, $rhm_sizes, 'doctor-gallery-image__img', '', false, 'photos' === $rhm_variant ? 4 / 3 : 0 ); ?>
							<?php if ( 'documents' === $rhm_variant ) : ?>
								<figcaption><?php echo esc_html( get_the_title( $rhm_item ) ); ?></figcaption>
							<?php endif; ?>
						</figure>
						<?php
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
