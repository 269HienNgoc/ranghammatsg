<?php
/**
 * Landing – Nội dung trình soạn thảo (the_content của trang, style .entry-content). Field: inc/landing/layouts/editor.php.
 * Cũng là phần hiển thị mặc định khi trang landing chưa có section nào. Nội dung trống → không hiện.
 * Gọi trong vòng lặp the_post() của template.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( (string) get_post_field( 'post_content', get_the_ID() ) ) ) {
	return;
}

$rhm_data   = $args['data'] ?? array();
$rhm_narrow = ! isset( $rhm_data['narrow'] ) || (bool) $rhm_data['narrow'];
?>
<div id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $rhm_narrow ? 'site-container max-w-[868px] py-10 lg:py-14' : 'site-container py-10 lg:py-14' ); ?>">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-content' ); ?>>
		<?php
		the_content();
		wp_link_pages();
		?>
	</article>
</div>
