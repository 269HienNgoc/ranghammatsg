<?php
/**
 * Template Name: Liên hệ – Đặt lịch
 * Template Post Type: page
 *
 * CSS: page-contact.css. Form đặt lịch: Fluent Forms, ID cấu hình ở Giao diện → Tùy biến → Thông tin phòng khám.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_form_id = absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
$rhm_hotline = rhm_get_option( 'rhm_hotline' );
$rhm_email   = rhm_get_option( 'rhm_email' );
$rhm_address = rhm_get_option( 'rhm_address' );
$rhm_hours   = rhm_get_option( 'rhm_working_hours' );
$rhm_map_url = rhm_get_option( 'rhm_map_url' );

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-banner' );
	?>
	<div class="site-container grid gap-12 py-12 lg:grid-cols-2 lg:py-20">
		<div>
			<div class="entry-content"><?php the_content(); ?></div>
			<ul class="mt-6 space-y-2">
				<?php if ( $rhm_address ) : ?>
					<li><strong><?php esc_html_e( 'Địa chỉ:', 'themeranghammatsg' ); ?></strong> <?php echo nl2br( esc_html( $rhm_address ) ); ?></li>
				<?php endif; ?>
				<?php if ( $rhm_hotline ) : ?>
					<li><strong><?php esc_html_e( 'Hotline:', 'themeranghammatsg' ); ?></strong> <a href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>"><?php echo esc_html( $rhm_hotline ); ?></a></li>
				<?php endif; ?>
				<?php if ( $rhm_email ) : ?>
					<li><strong><?php esc_html_e( 'Email:', 'themeranghammatsg' ); ?></strong> <a href="<?php echo esc_url( 'mailto:' . $rhm_email ); ?>"><?php echo esc_html( $rhm_email ); ?></a></li>
				<?php endif; ?>
				<?php if ( $rhm_hours ) : ?>
					<li><strong><?php esc_html_e( 'Giờ làm việc:', 'themeranghammatsg' ); ?></strong> <?php echo nl2br( esc_html( $rhm_hours ) ); ?></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="rounded-lg bg-soft p-6" id="dat-lich">
			<h2 class="mb-4 text-2xl font-bold text-dark"><?php esc_html_e( 'Đặt lịch hẹn', 'themeranghammatsg' ); ?></h2>
			<?php
			if ( $rhm_form_id && shortcode_exists( 'fluentform' ) ) {
				echo do_shortcode( sprintf( '[fluentform id="%d"]', $rhm_form_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fluent Forms output.
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
				echo '<p class="rounded-md border border-dashed border-primary p-4 text-sm">' . esc_html__( 'Chưa cấu hình form: tạo form trong Fluent Forms rồi nhập ID tại Giao diện → Tùy biến → Thông tin phòng khám.', 'themeranghammatsg' ) . '</p>';
			}
			?>
		</div>
	</div>

	<?php if ( $rhm_map_url ) : ?>
		<iframe class="block h-[420px] w-full border-0" src="<?php echo esc_url( $rhm_map_url ); ?>" title="<?php esc_attr_e( 'Bản đồ phòng khám', 'themeranghammatsg' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
