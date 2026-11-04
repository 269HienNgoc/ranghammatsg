<?php
defined( 'ABSPATH' ) || exit;
$rhm_doctor = $args['doctor'];
if ( ! array_filter( $rhm_doctor['lists'] ) && ! $rhm_doctor['diplomas'] ) {
	return;
}
?>
<section class="doctor-section doctor-section--mint" aria-labelledby="doctor-qualifications-title">
	<div class="site-container">
		<h2 id="doctor-qualifications-title" class="doctor-title"><?php esc_html_e( 'Chuyên môn – Học vấn – Kinh nghiệm', 'themeranghammatsg' ); ?></h2>
		<?php if ( array_filter( $rhm_doctor['lists'] ) ) : ?>
			<div class="doctor-qualifications">
				<?php foreach ( array( 'specialties' => __( 'Chuyên môn', 'themeranghammatsg' ), 'education' => __( 'Học vấn', 'themeranghammatsg' ), 'experience' => __( 'Kinh nghiệm', 'themeranghammatsg' ) ) as $rhm_key => $rhm_label ) : ?>
					<?php if ( $rhm_doctor['lists'][ $rhm_key ] ) : ?>
						<div class="doctor-qualification">
							<h3><?php echo esc_html( $rhm_label ); ?></h3>
							<ul><?php foreach ( $rhm_doctor['lists'][ $rhm_key ] as $rhm_item ) : ?><li><?php echo esc_html( $rhm_item ); ?></li><?php endforeach; ?></ul>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( $rhm_doctor['diplomas'] ) : ?>
			<div class="doctor-subsection">
				<h3 class="doctor-subtitle"><?php esc_html_e( 'Văn bằng đào tạo', 'themeranghammatsg' ); ?></h3>
				<?php get_template_part( 'template-parts/doctor/carousel', null, array( 'items' => $rhm_doctor['diplomas'], 'variant' => 'documents' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
