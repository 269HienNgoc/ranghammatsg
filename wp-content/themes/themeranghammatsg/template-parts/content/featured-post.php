<?php
/**
 * Bài nổi bật đầu trang danh mục: trái là nhãn danh mục, tiêu đề, mô tả, nút Đọc thêm; phải là ảnh phủ kín chiều cao thẻ,
 * mép trái mờ dần vào nền. Mọi kích thước màn hình đều 2 cột (56% chữ – 44% ảnh); mobile (< 640px) chữ nhỏ, màu teal-dark. CSS: archive.css
 *
 * Ảnh là LCP của trang nên tải ngay. Ảnh phủ kín cột (object-cover) nên độ rộng tải về phụ thuộc chiều cao thẻ,
 * không phải độ rộng cột: sizes đo thực tế với ảnh 3:2 (≈ 1,5 × chiều cao thẻ): < 640px ~483px, 640px ~602px, 1024px ~669px, 1440px 612px.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_thumbnail_id = (int) get_post_thumbnail_id();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'grid grid-cols-[56fr_44fr] overflow-hidden rounded-2xl border border-line bg-soft sm:rounded-3xl lg:min-h-96' ); ?>>
	<div class="relative z-10 flex flex-col items-start justify-center py-4 pr-1 pl-4 sm:p-8 lg:py-10 lg:pr-6 lg:pl-12">
		<p class="mb-2.5 inline-flex items-center gap-1.5 rounded-full bg-mint-dark px-2.5 py-1 text-[10px] font-bold text-teal-dark uppercase sm:mb-4 sm:gap-2 sm:bg-primary/10 sm:px-3.5 sm:py-1.5 sm:text-[13px] sm:text-primary-dark">
			<?php echo rhm_get_icon( 'document', 'size-3.5 shrink-0 sm:size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			<?php echo esc_html( $args['label'] ?? single_cat_title( '', false ) ); ?>
		</p>
		<h2 class="mb-2.5 text-xl leading-tight font-bold text-teal-dark max-sm:line-clamp-4 sm:mb-4 sm:text-2xl sm:text-primary lg:text-4xl"><a class="hover:text-primary-dark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="mb-4 line-clamp-5 text-[13px] leading-snug text-muted sm:mb-6 sm:line-clamp-4 sm:text-base sm:leading-relaxed lg:text-lg"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a class="btn btn-primary rounded-full max-sm:h-10 max-sm:border-0 max-sm:bg-teal-dark max-sm:px-5 max-sm:py-0 max-sm:text-sm max-sm:font-medium" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: post title */ __( 'Đọc thêm: %s', 'themeranghammatsg' ), get_the_title() ) ); ?>">
			<?php esc_html_e( 'Đọc thêm', 'themeranghammatsg' ); ?>
			<?php echo rhm_get_icon( 'arrow-right', 'size-4 sm:size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</a>
	</div>

	<a class="relative block bg-primary/10" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $rhm_thumbnail_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$rhm_thumbnail_id,
				'full',
				false,
				array(
					'class'         => 'absolute inset-0 size-full object-cover',
					'alt'           => '',
					'sizes'         => '(min-width: 1440px) 620px, (min-width: 1024px) 670px, (min-width: 640px) 600px, 480px',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'auto', // Ảnh LCP: không async (WordPress tự gắn async nếu bỏ trống).
				)
			);
			?>
			<span class="pointer-events-none absolute inset-y-0 left-0 w-1/3 bg-linear-to-r from-soft to-transparent"></span>
		<?php else : ?>
			<span class="absolute inset-0 flex items-center justify-center text-primary/30"><?php echo rhm_get_icon( 'tooth', 'size-1/3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
		<?php endif; ?>
	</a>
</article>
