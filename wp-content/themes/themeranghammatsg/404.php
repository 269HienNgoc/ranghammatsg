<?php
/**
 * Trang 404. CSS: dist/css/404.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="py-16 lg:py-24">
	<div class="site-container flex flex-col items-center text-center">
		<p class="text-[clamp(4rem,3rem+6vw,8rem)] leading-none font-extrabold text-primary">404</p>
		<h1 class="mt-4 mb-3 text-2xl font-bold text-dark lg:text-3xl"><?php esc_html_e( 'Không tìm thấy trang', 'themeranghammatsg' ); ?></h1>
		<p class="mb-6 max-w-xl text-muted"><?php esc_html_e( 'Trang bạn tìm có thể đã bị xóa hoặc đổi địa chỉ. Hãy thử tìm kiếm hoặc quay về trang chủ.', 'themeranghammatsg' ); ?></p>
		<div class="w-full max-w-xl"><?php get_search_form(); ?></div>
		<div class="mt-8 flex flex-wrap justify-center gap-3">
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Về trang chủ', 'themeranghammatsg' ); ?></a>
			<?php rhm_booking_button( 'bg-transparent text-primary hover:bg-primary hover:text-white' ); ?>
		</div>
	</div>
</section>
<?php
get_footer();
