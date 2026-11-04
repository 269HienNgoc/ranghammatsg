<?php
/** Danh sách chung: một mục nổi bật + lưới 12 mục, chuyên mục giữ thanh lọc. */
defined( 'ABSPATH' ) || exit;
$rhm_is_doctor = is_post_type_archive( 'bac-si' );
global $wp_query;
update_post_thumbnail_cache( $wp_query );
?>
<div class="site-container py-4 lg:py-4">

	<?php if ( have_posts() ) : ?>
		<?php
		if ( ! is_paged() ) {
			the_post();
			get_template_part( 'template-parts/content/featured-post', null, array( 'label' => $rhm_is_doctor ? __( 'Bác sĩ', 'themeranghammatsg' ) : single_cat_title( '', false ) ) );
		}
		?>

		<?php
		// Mobile (< 640px): tiêu đề "Bài viết mới nhất" trên thanh lọc; "Xem tất cả" về chuyên mục cha khi đang ở chuyên mục con.
		$rhm_filter   = $rhm_is_doctor ? array() : rhm_get_category_filter();
		$rhm_view_all = $rhm_filter && ! $rhm_filter['all']['active'] ? $rhm_filter['all']['url'] : '';
		?>
		<div class="mt-6 mb-3 flex items-center justify-between gap-4 sm:hidden">
			<h2 class="flex items-center gap-2 text-lg leading-tight font-bold text-teal-dark">
				<span class="h-5 w-1 shrink-0 rounded-full bg-[#008775]" aria-hidden="true"></span>
				<?php echo esc_html( $rhm_is_doctor ? __( 'Đội ngũ bác sĩ', 'themeranghammatsg' ) : __( 'Bài viết mới nhất', 'themeranghammatsg' ) ); ?>
			</h2>
			<?php if ( $rhm_view_all ) : ?>
				<a class="inline-flex shrink-0 items-center gap-1 text-sm text-teal-dark hover:text-primary" href="<?php echo esc_url( $rhm_view_all ); ?>">
					<?php esc_html_e( 'Xem tất cả', 'themeranghammatsg' ); ?>
					<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( ! $rhm_is_doctor ) { get_template_part( 'template-parts/components/category-filter' ); } ?>

		<?php if ( have_posts() ) : ?>
			<div class="grid gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 xl:grid-cols-4<?php echo is_paged() ? '' : ' mt-4 sm:mt-10 lg:mt-12'; ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part(
						'template-parts/content/card',
						'post',
						array(
							'loading' => is_paged() ? rhm_list_image_loading() : 'lazy', // Trang 2+ không có bài nổi bật → ảnh card đầu là LCP.
							'sizes'   => '(min-width: 1440px) 334px, (min-width: 1280px) calc((100vw - 104px) / 4), (min-width: 1024px) calc((100vw - 80px) / 3), (min-width: 640px) calc((100vw - 56px) / 2), calc(38vw - 12px)',
						)
					);
				endwhile;
				?>
			</div>
		<?php endif; ?>
		<?php rhm_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content-none' ); ?>
	<?php endif; ?>
</div>
