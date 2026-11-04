<?php
/**
 * Section: tin tức trang chủ — khối trái (1 bài lớn + danh sách) và khối phải (danh sách), theo chuyên mục.
 * Dữ liệu + hàm hiển thị: inc/news.php. CSS: page-home.css
 *
 * Bố cục: < 768px các khối/cột xếp dọc; 768–1023px khối trái chia 2 cột; ≥ 1024px 2 khối cạnh nhau (2/3 – 1/3).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_show = rhm_news_show();
$rhm_main = rhm_news_block( 'main', RHM_NEWS_MAIN_COUNT );
$rhm_side = rhm_news_block( 'side', RHM_NEWS_SIDE_COUNT, wp_list_pluck( $rhm_main['query']->posts, 'ID' ) );

if ( ! $rhm_main['query']->have_posts() && ! $rhm_side['query']->have_posts() ) {
	return;
}

// Độ rộng ảnh bài lớn theo bố cục (khung 1440px, lề 16px, thẻ đệm 16/24px, khoảng cách cột 24px).
$rhm_featured_sizes = '(min-width: 1440px) 425px, (min-width: 1024px) calc(33.33vw - 55px), (min-width: 768px) calc(50vw - 52px), (min-width: 640px) calc(100vw - 80px), calc(100vw - 64px)';
?>
<section class="bg-[#f6f9fe] py-12 lg:py-16">
	<div class="site-container grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
		<?php if ( $rhm_main['query']->have_posts() ) : ?>
			<div class="rounded-2xl bg-white p-4 shadow-[0_8px_30px_rgb(16_42_67/0.06)] sm:p-6">
				<?php rhm_news_heading( $rhm_main ); ?>

				<div class="grid gap-6 md:grid-cols-2">
					<?php
					$rhm_main['query']->the_post();
					?>
					<article>
						<a class="group block overflow-hidden rounded-xl" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php echo rhm_news_thumbnail( $rhm_featured_sizes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() / static markup. ?>
						</a>
						<h3 class="mt-4 line-clamp-2 text-lg leading-snug font-bold text-dark lg:text-xl">
							<a class="text-dark hover:text-primary" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<?php if ( $rhm_show['excerpt'] ) : ?>
							<p class="mt-2 line-clamp-2 text-[15px] leading-relaxed text-body/80"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
						<?php endif; ?>
						<?php rhm_news_meta( $rhm_show ); ?>
					</article>

					<?php if ( $rhm_main['query']->have_posts() ) : ?>
						<ul class="space-y-4">
							<?php
							while ( $rhm_main['query']->have_posts() ) :
								$rhm_main['query']->the_post();
								rhm_news_list_item( $rhm_show );
							endwhile;
							?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $rhm_side['query']->have_posts() ) : ?>
			<div class="rounded-2xl bg-white p-4 shadow-[0_8px_30px_rgb(16_42_67/0.06)] sm:p-6">
				<?php rhm_news_heading( $rhm_side ); ?>
				<ul class="space-y-4">
					<?php
					while ( $rhm_side['query']->have_posts() ) :
						$rhm_side['query']->the_post();
						rhm_news_list_item( $rhm_show );
					endwhile;
					?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
wp_reset_postdata();
