<?php
/**
 * Thẻ bài viết trong danh sách (tin tức, danh mục, tìm kiếm). CSS: archive.css
 *
 * - < 640px: dạng hàng — ảnh trái (38%, nhãn chuyên mục đè góc dưới), phải là tiêu đề, mô tả, ngày + nút mũi tên.
 * - ≥ 640px: thẻ dọc — ảnh trên, ngày, tiêu đề, mô tả, "Xem chi tiết".
 * - Bác sĩ (bac-si): ảnh khung dọc 4:5, object-top (không cắt mất đầu), ảnh gốc + srcset; bài viết: rhm-card 3:2.
 * Ngày là 1 phần tử duy nhất, đổi vị trí bằng order (mobile xuống cuối).
 *
 * $args['sizes']: độ rộng hiển thị thật của ảnh theo lưới đang dùng. Bỏ trống = lưới 3 cột của index.php / archive.php / search.php
 * (grid gap-6 sm:grid-cols-2 lg:grid-cols-3 trong .site-container: khung 1440px, lề 16px, khe 24px). Mobile ảnh rộng 38% khung → calc(38vw - 12px).
 * $args['loading']: 'lazy' (mặc định) | 'eager' (ảnh trong màn hình đầu) | 'lcp' (ảnh LCP: eager + fetchpriority=high) —
 * trang danh sách không có bài nổi bật dùng rhm_list_image_loading() (inc/template-tags.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_loading     = $args['loading'] ?? 'lazy';
$rhm_image_attrs = array(
	'class'    => 'aspect-[3/2] w-full object-cover',
	'sizes'    => $args['sizes'] ?? '(min-width: 1440px) 453px, (min-width: 1024px) calc((100vw - 80px) / 3), (min-width: 640px) calc((100vw - 56px) / 2), calc(38vw - 12px)',
	'loading'  => 'lazy' === $rhm_loading ? 'lazy' : 'eager',
	'decoding' => 'lcp' === $rhm_loading ? 'auto' : 'async', // Ảnh LCP: không async (WordPress tự gắn async nếu bỏ trống).
);
if ( 'lcp' === $rhm_loading ) {
	$rhm_image_attrs['fetchpriority'] = 'high';
}

$rhm_is_post   = 'post' === get_post_type();
$rhm_is_doctor = 'bac-si' === get_post_type();
$rhm_category = $rhm_is_post ? ( get_the_category()[0] ?? null ) : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'flex items-start gap-3 sm:flex-col sm:items-stretch sm:gap-0 sm:overflow-hidden sm:rounded-lg sm:border sm:border-line sm:bg-white sm:shadow-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="relative block w-[38%] shrink-0 overflow-hidden rounded-lg sm:w-auto sm:rounded-none" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php if ( $rhm_is_doctor ) : ?>
				<?php
				// Chân dung bác sĩ: khung dọc 4:5, giữ phần đầu (object-top), ảnh gốc + srcset thay vì bản cắt sẵn rhm-card 3:2
				// (cắt giữa → mất đầu). sizes nhân theo tỉ lệ khi ảnh nguồn ngang hơn khung (rhm_get_doctor_image_sizes, inc/doctors.php).
				$rhm_thumb_id = (int) get_post_thumbnail_id();
				?>
				<span class="relative block aspect-[4/5] bg-soft">
					<?php
					the_post_thumbnail(
						'full',
						array_merge(
							$rhm_image_attrs,
							array(
								'class' => 'absolute inset-0 size-full object-cover object-top',
								'sizes' => rhm_get_doctor_image_sizes( $rhm_thumb_id, $rhm_image_attrs['sizes'], 4 / 5 ),
							)
						)
					);
					?>
				</span>
			<?php else : ?>
				<?php the_post_thumbnail( 'rhm-card', $rhm_image_attrs ); ?>
			<?php endif; ?>
			<?php if ( $rhm_category ) : ?>
				<span class="absolute bottom-1.5 left-1.5 max-w-[calc(100%-12px)] truncate rounded-md bg-mint px-2 py-0.5 text-[10px] font-bold text-teal-dark uppercase sm:hidden"><?php echo esc_html( $rhm_category->name ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>
	<div class="relative flex min-w-0 flex-1 flex-col sm:p-6">
		<?php if ( $rhm_is_post ) : ?>
			<p class="mt-1.5 flex items-center gap-1 pr-10 text-xs text-muted max-sm:order-1 sm:mt-0 sm:mb-2 sm:pr-0 sm:text-sm">
				<?php echo rhm_get_icon( 'calendar', 'size-3.5 shrink-0 sm:hidden' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				<?php rhm_posted_on(); ?>
			</p>
		<?php endif; ?>
		<?php if ( 'bac-si' === get_post_type() && rhm_get_doctor_field( 'position', get_the_ID() ) ) : ?>
			<p class="mb-1 text-sm font-semibold text-teal-dark"><?php echo esc_html( rhm_get_doctor_field( 'position', get_the_ID() ) ); ?></p>
		<?php endif; ?>
		<h2 class="line-clamp-3 text-[15px] leading-snug font-bold sm:mb-3 sm:line-clamp-none sm:text-xl"><a class="text-teal-dark hover:text-primary sm:text-dark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="mt-1 line-clamp-2 text-[13px] leading-snug text-muted sm:mt-0 sm:mb-4 sm:line-clamp-3 sm:text-base sm:leading-relaxed"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a class="mt-auto font-semibold max-sm:hidden" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Xem chi tiết', 'themeranghammatsg' ); ?></a>
		<a class="absolute right-0 bottom-0 flex size-8 items-center justify-center rounded-full bg-mint text-teal-dark transition-colors hover:bg-teal-dark hover:text-white sm:hidden" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</a>
	</div>
</article>
