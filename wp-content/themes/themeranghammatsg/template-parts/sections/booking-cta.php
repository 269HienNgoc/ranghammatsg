<?php
/**
 * Section: kêu gọi đặt lịch.
 * Dùng ở: trang chủ, giới thiệu, chi tiết bài viết
 * → được @source trong page-home.css, page-about.css, single-post.css.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_hotline = rhm_get_option( 'rhm_hotline' );
?>
<section class="bg-primary py-12 text-white lg:py-16">
	<div class="site-container flex flex-wrap items-center justify-between gap-6">
		<div>
			<h2 class="mb-2 text-2xl font-bold lg:text-3xl"><?php esc_html_e( 'Đặt lịch khám ngay hôm nay', 'themeranghammatsg' ); ?></h2>
			<p class="text-white/85"><?php esc_html_e( 'Đội ngũ bác sĩ sẽ liên hệ xác nhận lịch hẹn của bạn trong thời gian sớm nhất.', 'themeranghammatsg' ); ?></p>
		</div>
		<div class="flex flex-wrap gap-3">
			<?php rhm_booking_button( 'border-white bg-white text-primary hover:border-white hover:bg-soft hover:text-primary' ); ?>
			<?php if ( $rhm_hotline ) : ?>
				<a class="btn border-white text-white hover:bg-white hover:text-primary" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>"><?php echo esc_html( $rhm_hotline ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
