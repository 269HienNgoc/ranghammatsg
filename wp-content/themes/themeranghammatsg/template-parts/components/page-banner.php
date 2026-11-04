<?php
/**
 * Banner đầu bài viết (CHỈ dùng ở single.php, trong vòng lặp). CSS: single-post.css
 * Trang / Tin tức / tìm kiếm dùng template-parts/components/page-title.php.
 *
 * Bố cục (cùng cột 868px với nội dung bài): breadcrumb (Trang chủ › chuyên mục › bài) → nhãn chuyên mục → H1
 * → tác giả · ngày đăng · ngày cập nhật (khi khác ngày đăng) · thời gian đọc (bật/tắt từng mục: Customizer → "Bài viết")
 * → sao đánh giá (kk-star-ratings, nếu có).
 * Không có ảnh, không tải gì thêm (avatar tác giả = icon, không gọi Gravatar; nền vẽ bằng CSS gradient).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_categories = get_the_category();
$rhm_rating     = rhm_get_star_rating();
$rhm_meta       = rhm_post_meta_show(); // Bật/tắt từng mục: Customizer → "Bài viết" (inc/post-meta.php).
$rhm_meta['updated'] = $rhm_meta['updated'] && get_the_modified_date( 'Y-m-d' ) !== get_the_date( 'Y-m-d' );
?>
<header class="post-banner relative overflow-hidden">
	<?php // Nền (mint → trắng, vầng sáng, lưới chấm): .post-banner trong single-post.css. Hình răng chìm: sát mép phải cột chữ 868px (50% + 434px + 14px), chỉ hiện ≥ 1360px — đủ chỗ nằm trọn ngoài cột. ?>
	<?php echo rhm_get_icon( 'tooth', 'pointer-events-none absolute top-1/2 left-[calc(50%+28rem)] hidden size-56 -translate-y-1/2 text-teal-dark/[0.06] min-[1360px]:block' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>

	<div class="site-container relative max-w-[868px] pt-4 pb-4 lg:pt-4 lg:pb-5">
		<?php rhm_breadcrumb(); ?>

		<?php if ( $rhm_categories ) : ?>
			<ul class="mt-5 flex flex-wrap gap-2 lg:mt-7">
				<?php foreach ( $rhm_categories as $rhm_category ) : ?>
					<li>
						<a class="inline-flex items-center gap-1.5 rounded-full bg-mint-dark px-3 py-1 text-xs font-bold text-teal-dark uppercase transition-colors hover:bg-teal-dark hover:text-white lg:text-[13px]" href="<?php echo esc_url( get_category_link( $rhm_category ) ); ?>">
							<?php echo rhm_get_icon( 'document', 'size-4 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<?php echo esc_html( $rhm_category->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<h1 class="mt-3 text-[30px] leading-tight font-bold tracking-tight text-pretty text-deep sm:text-[34px] lg:text-[40px]"><?php the_title(); ?></h1>

		<?php if ( in_array( true, $rhm_meta, true ) ) : ?>
			<ul class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted lg:mt-5">
				<?php if ( $rhm_meta['author'] ) : ?>
					<li class="inline-flex items-center gap-1.5">
						<?php echo rhm_get_icon( 'user', 'size-4 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<span class="font-medium text-dark"><?php the_author(); ?></span>
					</li>
				<?php endif; ?>
				<?php if ( $rhm_meta['date'] ) : ?>
					<li class="inline-flex items-center gap-1.5">
						<?php echo rhm_get_icon( 'calendar', 'size-4 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></time>
					</li>
				<?php endif; ?>
				<?php if ( $rhm_meta['updated'] ) : ?>
					<li class="inline-flex items-center gap-1.5">
						<?php echo rhm_get_icon( 'refresh', 'size-4 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<span><?php esc_html_e( 'Cập nhật', 'themeranghammatsg' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_modified_date( 'd/m/Y' ) ); ?></time></span>
					</li>
				<?php endif; ?>
				<?php if ( $rhm_meta['reading'] ) : ?>
					<li class="inline-flex items-center gap-1.5">
						<?php echo rhm_get_icon( 'clock', 'size-4 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<?php
						/* translators: %d: reading time in minutes */
						echo esc_html( sprintf( __( '%d phút đọc', 'themeranghammatsg' ), rhm_get_reading_minutes() ) );
						?>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $rhm_rating ) : ?>
			<div class="mt-4"><?php echo $rhm_rating; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup của plugin kk-star-ratings (inc/plugin/kk-star-ratings.php). ?></div>
		<?php endif; ?>
	</div>
</header>
