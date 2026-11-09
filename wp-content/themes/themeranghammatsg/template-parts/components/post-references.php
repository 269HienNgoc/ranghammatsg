<?php
/**
 * Tài liệu tham khảo cuối bài (ACF rhm_post_references, inc/post-extras.php) — khớp Article.citation trong inc/schema.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_references = rhm_post_section_visible( 'references', get_the_ID() ) ? rhm_get_post_references( get_the_ID() ) : array();
if ( ! $rhm_references ) {
	return;
}
?>
<section class="mt-10" aria-labelledby="rhm-references-title">
	<h2 id="rhm-references-title" class="text-xl leading-snug font-bold text-teal-dark"><?php esc_html_e( 'Tài liệu tham khảo', 'themeranghammatsg' ); ?></h2>
	<ol class="mt-3 list-decimal space-y-2 pl-6 text-[15px] leading-relaxed text-muted [overflow-wrap:anywhere]">
		<?php foreach ( $rhm_references as $rhm_item ) : ?>
			<li>
				<?php if ( $rhm_item['url'] ) : ?>
					<a class="text-teal-dark underline underline-offset-2 hover:text-primary" href="<?php echo esc_url( $rhm_item['url'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_item['url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>><?php echo esc_html( $rhm_item['title'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $rhm_item['title'] ); ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
