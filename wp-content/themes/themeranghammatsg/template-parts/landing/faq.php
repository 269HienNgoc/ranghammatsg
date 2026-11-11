<?php
/**
 * Landing – Câu hỏi thường gặp: <details> mở/đóng không cần JS (cùng giao diện FAQ cuối bài viết).
 * Field: inc/landing/layouts/faq.php. Không có câu hỏi hợp lệ (đủ hỏi + đáp) → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_items = array();
foreach ( (array) ( $rhm_data['items'] ?? array() ) as $rhm_row ) {
	$rhm_row      = (array) $rhm_row;
	$rhm_question = rhm_landing_text( $rhm_row, 'question' );
	$rhm_answer   = trim( wp_kses( (string) ( $rhm_row['answer'] ?? '' ), RHM_FAQ_ALLOWED_HTML ) );
	if ( '' !== $rhm_question && '' !== $rhm_answer ) {
		$rhm_items[] = array( $rhm_question, rhm_external_links( wpautop( $rhm_answer ) ) );
	}
}
if ( ! $rhm_items ) {
	return;
}

$rhm_head_id = $args['id'] . '-title';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container max-w-[868px]">
		<?php rhm_landing_heading( $rhm_head_id, rhm_landing_text( $rhm_data, 'title' ) ?: __( 'Câu hỏi thường gặp', 'themeranghammatsg' ), '', '', 'center' ); ?>
		<div class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-white shadow-card">
			<?php foreach ( $rhm_items as $rhm_index => [ $rhm_question, $rhm_answer ] ) : ?>
				<details id="<?php echo esc_attr( $args['id'] . '-q' . ( $rhm_index + 1 ) ); ?>" class="group">
					<summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-4 text-lg leading-snug font-semibold text-dark transition-colors hover:text-primary sm:px-5 [&::-webkit-details-marker]:hidden">
						<span class="min-w-0 [overflow-wrap:anywhere]"><?php echo esc_html( $rhm_question ); ?></span>
						<?php echo rhm_get_icon( 'chevron', 'size-5 shrink-0 text-primary transition-transform group-open:rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</summary>
					<div class="entry-content px-4 pb-5 sm:px-5"><?php echo $rhm_answer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses ở trên, chỉ thêm target/rel cho link external. ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
