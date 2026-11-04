<?php
/** Video YouTube chỉ tải iframe sau khi người dùng bấm ảnh bìa. */
defined( 'ABSPATH' ) || exit;
if ( ! $args['items'] ) {
	return;
}
if ( file_exists( RHM_THEME_DIR . '/dist/js/embed.js' ) ) {
	wp_enqueue_script_module( 'rhm-embed', RHM_THEME_URI . '/dist/js/embed.js', array(), rhm_asset_version( 'dist/js/embed.js' ) );
}
// Khung 1408px, hai cột cách 24px = 692px; mobile 1 cột.
$rhm_sizes = '(min-width: 1440px) 692px, (min-width: 768px) calc((100vw - 56px) / 2), calc(100vw - 32px)';
?>
<section class="doctor-section" aria-labelledby="<?php echo esc_attr( $args['id'] ); ?>">
	<div class="site-container">
		<h2 id="<?php echo esc_attr( $args['id'] ); ?>" class="doctor-title"><?php echo esc_html( $args['title'] ); ?></h2>
		<div class="doctor-videos">
			<?php foreach ( $args['items'] as $rhm_video ) : ?>
				<article class="doctor-video">
					<div class="doctor-video__frame">
						<button type="button" data-embed-type="youtube" data-embed-src="<?php echo esc_url( $rhm_video['src'] ); ?>" data-embed-title="<?php echo esc_attr( $rhm_video['title'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Phát video: %s', 'themeranghammatsg' ), $rhm_video['title'] ) ); ?>">
							<?php rhm_doctor_image( $rhm_video['image'], $rhm_sizes, 'doctor-video__cover', $rhm_video['title'], false, 16 / 9 ); ?>
							<span class="doctor-video__play"><?php echo rhm_get_icon( 'play', 'size-7' ); ?></span>
						</button>
					</div>
					<h3><?php echo esc_html( $rhm_video['title'] ); ?></h3>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
