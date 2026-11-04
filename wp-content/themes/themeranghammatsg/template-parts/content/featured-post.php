<?php
/**
 * Bài nổi bật đầu trang danh mục: trái là nhãn danh mục, tiêu đề, mô tả, nút Đọc thêm; phải là ảnh.
 * Dưới 1024px ảnh nằm trên, nội dung nằm dưới. CSS: archive.css
 *
 * Ảnh là LCP của trang nên tải ngay. sizes: từ 1024px cột ảnh rộng 44% khung nhưng ảnh phủ
 * kín khung cao ~384px (object-cover), nên cần ít nhất 576px.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_thumbnail_id = (int) get_post_thumbnail_id();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'grid overflow-hidden rounded-3xl border border-line bg-soft lg:min-h-96 lg:grid-cols-[56fr_44fr]' ); ?>>
	<div class="order-2 flex flex-col items-start justify-center p-6 sm:p-8 lg:order-1 lg:py-10 lg:pr-6 lg:pl-12">
		<p class="mb-4 inline-flex items-center gap-2 rounded-full bg-primary/10 px-3.5 py-1.5 text-[13px] font-bold uppercase text-primary">
			<?php echo rhm_get_icon( 'document', 'size-5 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
			<?php echo esc_html( single_cat_title( '', false ) ); ?>
		</p>
		<h2 class="mb-4 text-2xl leading-tight font-bold text-primary lg:text-4xl"><a class="hover:text-primary-dark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="mb-6 line-clamp-4 text-muted lg:text-lg"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a class="btn btn-primary rounded-full" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: post title */ __( 'Đọc thêm: %s', 'themeranghammatsg' ), get_the_title() ) ); ?>">
			<?php esc_html_e( 'Đọc thêm', 'themeranghammatsg' ); ?>
			<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</a>
	</div>

	<a class="relative order-1 block bg-primary/10 lg:order-2" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $rhm_thumbnail_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$rhm_thumbnail_id,
				'full',
				false,
				array(
					'class'         => 'aspect-[3/2] w-full object-cover lg:absolute lg:inset-0 lg:aspect-auto lg:h-full',
					'alt'           => '',
					'sizes'         => '(min-width: 1440px) 620px, (min-width: 1024px) 576px, calc(100vw - 32px)',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
				)
			);
			?>
			<span class="pointer-events-none absolute inset-y-0 left-0 hidden w-1/3 bg-linear-to-r from-soft to-transparent lg:block"></span>
		<?php else : ?>
			<span class="flex aspect-[3/2] h-full w-full items-center justify-center text-primary/30"><?php echo rhm_get_icon( 'tooth', 'size-1/3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
		<?php endif; ?>
	</a>
</article>
