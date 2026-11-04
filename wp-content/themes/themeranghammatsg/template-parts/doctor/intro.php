<?php
/** Chân dung, tên và nội dung Classic Editor, tiếp đến ảnh hoạt động chuyên môn. */
defined( 'ABSPATH' ) || exit;
$rhm_doctor   = $args['doctor'];
$rhm_portrait = (int) get_post_thumbnail_id();
?>
<section class="doctor-section doctor-intro" aria-labelledby="doctor-name">
	<div class="site-container">
		<?php rhm_breadcrumb(); ?>
		<div class="doctor-intro__grid<?php echo $rhm_portrait ? '' : ' doctor-intro__grid--text'; ?>">
			<?php if ( $rhm_portrait ) : ?>
				<div class="doctor-intro__portrait">
					<?php rhm_doctor_image( $rhm_portrait, RHM_DOCTOR_PORTRAIT_SIZES, 'doctor-portrait', get_the_title(), ! $rhm_doctor['banner_desktop'], 4 / 5 ); ?>
				</div>
			<?php endif; ?>
			<div class="doctor-intro__body">
				<p class="doctor-eyebrow"><?php esc_html_e( 'Đội ngũ bác sĩ', 'themeranghammatsg' ); ?></p>
				<h1 id="doctor-name"><?php the_title(); ?></h1>
				<?php if ( $rhm_doctor['position'] ) : ?>
					<p class="doctor-position"><?php echo esc_html( $rhm_doctor['position'] ); ?></p>
				<?php endif; ?>
				<?php if ( trim( get_the_content() ) ) : ?>
					<div class="entry-content doctor-bio"><?php the_content(); ?></div>
				<?php endif; ?>
				<a class="btn btn-primary doctor-booking-link" href="#dat-lich"><?php esc_html_e( 'Đặt lịch tư vấn', 'themeranghammatsg' ); ?><?php echo rhm_get_icon( 'arrow-right', 'size-5' ); ?></a>
			</div>
		</div>
		<?php if ( $rhm_doctor['gallery'] ) : ?>
			<div class="doctor-subsection">
				<h2 class="doctor-subtitle"><?php esc_html_e( 'Hoạt động chuyên môn', 'themeranghammatsg' ); ?></h2>
				<?php get_template_part( 'template-parts/doctor/carousel', null, array( 'items' => $rhm_doctor['gallery'], 'variant' => 'photos' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
