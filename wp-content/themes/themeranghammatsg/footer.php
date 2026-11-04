<?php
/**
 * Footer. Style: utility trong file này + src/css/pages/footer.css.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_hotline = rhm_get_option( 'rhm_hotline' );
$rhm_email   = rhm_get_option( 'rhm_email' );
$rhm_address = rhm_get_option( 'rhm_address' );
$rhm_hours   = rhm_get_option( 'rhm_working_hours' );
$rhm_socials = array_filter(
	array(
		'Facebook' => rhm_get_option( 'rhm_facebook' ),
		'Zalo'     => rhm_get_option( 'rhm_zalo' ),
		'YouTube'  => rhm_get_option( 'rhm_youtube' ),
	)
);
?>
</main>

<footer class="site-footer">
	<div class="site-container grid gap-10 pb-12 md:grid-cols-3">
		<div class="space-y-2">
			<p class="mb-3 font-bold text-white"><?php bloginfo( 'name' ); ?></p>
			<?php if ( $rhm_address ) : ?>
				<p><?php echo nl2br( esc_html( $rhm_address ) ); ?></p>
			<?php endif; ?>
			<?php if ( $rhm_hotline ) : ?>
				<p><?php esc_html_e( 'Hotline:', 'themeranghammatsg' ); ?> <a class="text-white hover:text-green" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>"><?php echo esc_html( $rhm_hotline ); ?></a></p>
			<?php endif; ?>
			<?php if ( $rhm_email ) : ?>
				<p><?php esc_html_e( 'Email:', 'themeranghammatsg' ); ?> <a class="text-white hover:text-green" href="<?php echo esc_url( 'mailto:' . $rhm_email ); ?>"><?php echo esc_html( $rhm_email ); ?></a></p>
			<?php endif; ?>
		</div>

		<?php if ( $rhm_hours ) : ?>
			<div>
				<p class="mb-3 font-bold text-white"><?php esc_html_e( 'Giờ làm việc', 'themeranghammatsg' ); ?></p>
				<p><?php echo nl2br( esc_html( $rhm_hours ) ); ?></p>
			</div>
		<?php endif; ?>

		<div>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => 'nav',
					'menu_class'     => 'footer-nav mb-4',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
			<?php if ( $rhm_socials ) : ?>
				<ul class="flex flex-wrap gap-4">
					<?php foreach ( $rhm_socials as $rhm_label => $rhm_url ) : ?>
						<li><a class="text-white hover:text-green" href="<?php echo esc_url( $rhm_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $rhm_label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<div class="border-t border-white/10 py-4 text-sm">
		<div class="site-container">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
