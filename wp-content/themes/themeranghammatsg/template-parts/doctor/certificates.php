<?php
defined( 'ABSPATH' ) || exit;
$rhm_doctor = $args['doctor'];
if ( ! $rhm_doctor['certificates'] ) {
	return;
}
?>
<section class="doctor-section" aria-labelledby="doctor-certificates-title">
	<div class="site-container">
		<h2 id="doctor-certificates-title" class="doctor-title"><?php esc_html_e( 'Chứng chỉ và thành tựu', 'themeranghammatsg' ); ?></h2>
		<div class="doctor-identity">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php echo rhm_get_picture( (int) get_post_thumbnail_id(), array( '' => 64 ), array( 'class' => 'doctor-avatar', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
			<?php endif; ?>
			<div><p class="doctor-identity__name"><?php the_title(); ?></p><?php if ( $rhm_doctor['position'] ) : ?><p class="doctor-identity__position"><?php echo esc_html( $rhm_doctor['position'] ); ?></p><?php endif; ?></div>
		</div>
		<?php get_template_part( 'template-parts/doctor/carousel', null, array( 'items' => $rhm_doctor['certificates'], 'variant' => 'documents' ) ); ?>
	</div>
</section>
