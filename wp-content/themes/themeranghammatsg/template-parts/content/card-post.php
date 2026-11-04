<?php
/**
 * Thẻ bài viết trong danh sách (tin tức, danh mục, tìm kiếm). CSS: archive.css
 *
 * $args['sizes']: độ rộng hiển thị thật của ảnh theo lưới đang dùng (bỏ trống = mặc định của WordPress).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_image_attrs = array(
	'class'    => 'aspect-[3/2] w-full object-cover',
	'loading'  => 'lazy',
	'decoding' => 'async',
);
if ( ! empty( $args['sizes'] ) ) {
	$rhm_image_attrs['sizes'] = $args['sizes'];
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'flex flex-col overflow-hidden rounded-lg border border-line bg-white shadow-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="block" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'rhm-card', $rhm_image_attrs ); ?>
		</a>
	<?php endif; ?>
	<div class="flex flex-1 flex-col p-6">
		<?php if ( 'post' === get_post_type() ) : ?>
			<p class="mb-2 text-sm text-muted"><?php rhm_posted_on(); ?></p>
		<?php endif; ?>
		<h2 class="mb-3 text-xl leading-snug font-bold"><a class="text-dark hover:text-primary" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="mb-4 line-clamp-3 text-muted"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a class="mt-auto font-semibold" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Xem chi tiết', 'themeranghammatsg' ); ?></a>
	</div>
</article>
