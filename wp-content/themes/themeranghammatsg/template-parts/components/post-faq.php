<?php
/**
 * Câu hỏi thường gặp cuối bài (ACF rhm_post_faq, inc/post-extras.php). <details> mở/đóng không cần JS.
 * id rhm-faq-N khớp @id Question trong schema (inc/schema.php). CSS: utility của single-post.css.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_faq = rhm_get_post_faq( get_the_ID() );
if ( ! $rhm_faq ) {
	return;
}
?>
<section class="mt-10 lg:mt-12" aria-labelledby="rhm-faq-title">
	<h2 id="rhm-faq-title" class="text-2xl leading-snug font-bold text-pretty text-teal-dark lg:text-[2rem]"><?php esc_html_e( 'Câu hỏi thường gặp', 'themeranghammatsg' ); ?></h2>
	<div class="mt-4 divide-y divide-line overflow-hidden rounded-2xl border border-line bg-white shadow-card">
		<?php foreach ( $rhm_faq as $rhm_index => $rhm_item ) : ?>
			<details id="rhm-faq-<?php echo esc_attr( $rhm_index + 1 ); ?>" class="group">
				<summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-4 text-lg leading-snug font-semibold text-dark transition-colors hover:text-primary sm:px-5 [&::-webkit-details-marker]:hidden">
					<span class="min-w-0 [overflow-wrap:anywhere]"><?php echo esc_html( $rhm_item['question'] ); ?></span>
					<?php echo rhm_get_icon( 'chevron', 'size-5 shrink-0 text-primary transition-transform group-open:rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</summary>
				<div class="entry-content px-4 pb-5 sm:px-5"><?php echo wp_kses( $rhm_item['answer'], RHM_FAQ_ALLOWED_HTML ); ?></div>
			</details>
		<?php endforeach; ?>
	</div>
</section>
