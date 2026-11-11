<?php
/**
 * Section: kêu gọi đặt lịch.
 * Dùng ở: trang chủ, chi tiết bài viết, landing page (layout "booking", template-parts/landing/booking.php)
 * → được @source trong page-home.css, page-about.css, single-post.css.
 * $args (tùy chọn): title, text (trống → chữ mặc định rhm_booking_cta_default()).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_hotline = rhm_get_option( 'rhm_hotline' );
$rhm_title   = (string) ( $args['title'] ?? '' );
$rhm_text    = (string) ( $args['text'] ?? '' );
$rhm_id      = (string) ( $args['id'] ?? '' );
?>
<section<?php echo '' !== $rhm_id ? ' id="' . esc_attr( $rhm_id ) . '"' : ''; ?> class="bg-primary py-12 text-white lg:py-16">
	<div class="site-container flex flex-wrap items-center justify-between gap-6">
		<div>
			<h2 class="mb-2 text-2xl font-bold lg:text-3xl"><?php echo esc_html( '' !== $rhm_title ? $rhm_title : rhm_booking_cta_default( 'title' ) ); ?></h2>
			<p class="text-white/90"><?php echo esc_html( '' !== $rhm_text ? $rhm_text : rhm_booking_cta_default( 'text' ) ); ?></p>
		</div>
		<div class="flex flex-wrap gap-3">
			<?php rhm_booking_button( 'border-white bg-white text-primary hover:border-white hover:bg-soft hover:text-primary' ); ?>
			<?php if ( $rhm_hotline ) : ?>
				<a class="btn border-white text-white hover:bg-white hover:text-primary" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>"><?php echo esc_html( $rhm_hotline ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
