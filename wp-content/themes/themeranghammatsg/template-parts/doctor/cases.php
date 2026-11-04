<?php
defined( 'ABSPATH' ) || exit;
if ( ! $args['doctor']['cases'] ) {
	return;
}
?>
<section class="doctor-section doctor-section--mint" aria-labelledby="doctor-cases-title">
	<div class="site-container">
		<h2 id="doctor-cases-title" class="doctor-title"><?php esc_html_e( 'Những ca điều trị', 'themeranghammatsg' ); ?></h2>
		<?php get_template_part( 'template-parts/doctor/carousel', null, array( 'items' => $args['doctor']['cases'], 'variant' => 'cases' ) ); ?>
	</div>
</section>
