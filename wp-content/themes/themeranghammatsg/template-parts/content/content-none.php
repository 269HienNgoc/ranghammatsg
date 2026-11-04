<?php
/**
 * Không có nội dung. CSS: archive.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="rounded-lg bg-soft p-8 text-center">
	<?php if ( is_search() ) : ?>
		<p class="mb-4"><?php esc_html_e( 'Không tìm thấy kết quả phù hợp. Vui lòng thử từ khóa khác.', 'themeranghammatsg' ); ?></p>
		<div class="mx-auto max-w-xl"><?php get_search_form(); ?></div>
	<?php else : ?>
		<p><?php esc_html_e( 'Chưa có nội dung.', 'themeranghammatsg' ); ?></p>
	<?php endif; ?>
</div>
