<?php
/**
 * Landing – Dải điểm nổi bật: ô trắng icon nét + nhãn (2 cột mobile, 1 hàng desktop). Field: inc/landing/layouts/highlights.php.
 * Ngay sau Banner → nổi lên đè mép dưới banner (lề âm). Không có ô hợp lệ → không hiện.
 * Tiêu đề H2 + mô tả (TinyMCE) tùy chọn (2026-11-26): có tiêu đề / mô tả → KHÔNG đè banner (chữ sẽ nằm trên ảnh banner);
 * có tiêu đề → section aria-labelledby tiêu đề; không có cả hai → giữ nguyên như cũ (aria-label "Điểm nổi bật").
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_items = array();
foreach ( rhm_landing_rows( $args['data'] ?? array(), 'items' ) as $rhm_row ) {
	$rhm_label = rhm_landing_text( $rhm_row, 'label' );
	if ( '' !== $rhm_label ) {
		$rhm_items[] = array( rhm_landing_icon( $rhm_row, 'icon' ), $rhm_label );
	}
}
if ( ! $rhm_items ) {
	return;
}

$rhm_title   = rhm_landing_text( $args['data'] ?? array(), 'title' );
$rhm_desc    = rhm_landing_rich( $args['data'] ?? array(), 'description' );
$rhm_head_id = $args['id'] . '-title';
$rhm_overlap = 'hero' === $args['prev'] && '' === $rhm_title && '' === $rhm_desc;

$rhm_cols = match ( count( $rhm_items ) ) {
	1, 2    => 'grid grid-cols-2 gap-3 lg:gap-5',
	3       => 'grid grid-cols-2 gap-3 sm:grid-cols-3 lg:gap-5',
	4       => 'grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-5',
	5       => 'grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 lg:gap-5',
	default => 'grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 lg:gap-5',
};
?>
<?php if ( '' !== $rhm_title ) : ?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', $rhm_desc, 'center', rhm_landing_color( $args['data'] ?? array(), 'title_color' ) ); ?>
	</div>
<?php elseif ( '' !== $rhm_desc ) : ?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-label="<?php esc_attr_e( 'Điểm nổi bật', 'themeranghammatsg' ); ?>">
	<div class="site-container">
		<div class="rhm-rich mx-auto mb-6 max-w-3xl text-center leading-relaxed text-body/85 lg:mb-8"><?php echo $rhm_desc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
	</div>
<?php else : ?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $rhm_overlap ? 'relative z-10 pb-6 lg:-mt-8 lg:pb-8' : 'py-6 lg:py-8' ); ?>" aria-label="<?php esc_attr_e( 'Điểm nổi bật', 'themeranghammatsg' ); ?>">
<?php endif; ?>
	<ul class="site-container <?php echo esc_attr( $rhm_cols ); ?>">
		<?php foreach ( $rhm_items as [ $rhm_icon, $rhm_label ] ) : ?>
			<li class="flex min-h-16 items-center justify-center gap-3 rounded-lg border border-line bg-white px-3 py-3 text-center shadow-card lg:min-h-20 lg:gap-4 lg:px-5">
				<?php if ( $rhm_icon ) : ?>
					<?php echo rhm_icon_svg( 'landing', $rhm_icon, 'size-8 shrink-0 text-primary lg:size-10' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
				<?php endif; ?>
				<span class="text-left text-sm leading-snug font-semibold text-dark lg:text-base"><?php echo esc_html( $rhm_label ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
