<?php
/**
 * Landing – Danh sách có icon + ảnh. Field: inc/landing/layouts/icon-list.php.
 * Thiếu tiêu đề hoặc không có dòng nào → không hiện.
 *
 * Vị trí ảnh (image_position):
 * - left / right: desktop 2 cột (ảnh | chữ), canh giữa theo chiều dọc; mobile xếp dọc theo thứ tự DOM:
 *   left → ảnh trên danh sách, right → ảnh dưới danh sách. Tiêu đề kiểu "bar" (vạch dọc) trong cột chữ.
 * - top / bottom (hoặc không có ảnh): tiêu đề canh giữa, danh sách rộng hết khung chia 2 cột từ lg, ảnh rộng hết khung.
 * Ảnh giữ nguyên tỉ lệ file (h-auto w-full → CLS 0). Cột chữ chạm mép phải khung (ảnh trái / trên / dưới / không ảnh) chừa
 * lg:pr-24: cột nút liên hệ nhanh (fixed, ~88px từ mép phải màn hình) không đè lên cuối dòng.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
$rhm_icon  = rhm_landing_icon( $rhm_data, 'icon' ) ?: 'tich';

$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_lines = rhm_landing_lines( $rhm_row, 'text' );
	if ( $rhm_lines ) {
		$rhm_items[] = array( rhm_landing_icon( $rhm_row, 'icon' ) ?: $rhm_icon, $rhm_lines );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_image    = rhm_landing_image( $rhm_data, 'image' );
$rhm_position = (string) ( $rhm_data['image_position'] ?? 'right' );
$rhm_position = in_array( $rhm_position, array( 'left', 'right', 'top', 'bottom' ), true ) ? $rhm_position : 'right';
$rhm_side     = $rhm_image && in_array( $rhm_position, array( 'left', 'right' ), true );
$rhm_head_id  = $args['id'] . '-title';

// sizes: cạnh nhau = 1 cột của lưới 2 cột cách 32px → (1408 − 32) / 2 = 688px | (100vw − 32 − 32) / 2; dưới 1024px rộng hết khung.
// Trên / dưới: rộng hết khung 1408px | 100vw − 32px.
$rhm_sizes = $rhm_side
	? '(min-width: 1440px) 688px, (min-width: 1024px) calc(50vw - 32px), calc(100vw - 32px)'
	: '(min-width: 1440px) 1408px, calc(100vw - 32px)';

// Section đứng đầu trang → ảnh là LCP (fetchpriority=high), còn lại lazy. rhm_html_attrs escape từng thuộc tính.
$rhm_image_html = $rhm_image
	? '<div class="overflow-hidden rounded-2xl bg-soft shadow-card">' . rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full', (bool) $args['is_first'] ) . '</div>'
	: '';

ob_start();
?>
<ul class="<?php echo esc_attr( $rhm_side ? 'space-y-4' : 'grid gap-x-10 gap-y-4 lg:grid-cols-2' ); ?>">
	<?php foreach ( $rhm_items as [ $rhm_item_icon, $rhm_lines ] ) : ?>
		<li class="flex items-start gap-3 lg:gap-4">
			<span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-mint text-primary lg:size-10" aria-hidden="true">
				<?php echo rhm_icon_svg( 'landing', $rhm_item_icon, 'size-5 lg:size-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
			</span>
			<span class="min-w-0 pt-1.5 leading-relaxed text-body lg:pt-2"><?php echo implode( '<br>', array_map( 'esc_html', $rhm_lines ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng dòng đã esc_html. ?></span>
		</li>
	<?php endforeach; ?>
</ul>
<?php
$rhm_list_html = (string) ob_get_clean();
$rhm_desc      = rhm_landing_lines( $rhm_data, 'description' );
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php if ( $rhm_side ) : ?>
			<div class="grid items-center gap-6 lg:grid-cols-2 lg:gap-8">
				<?php if ( 'left' === $rhm_position ) : ?>
					<?php echo $rhm_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML dựng ở trên. ?>
				<?php endif; ?>
				<div class="<?php echo esc_attr( 'left' === $rhm_position ? 'min-w-0 lg:pr-24' : 'min-w-0' ); ?>">
					<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'bar' ); ?>
					<?php foreach ( $rhm_desc as $rhm_line ) : ?>
						<p class="mb-4 leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
					<?php endforeach; ?>
					<?php echo $rhm_list_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML dựng ở trên. ?>
				</div>
				<?php if ( 'right' === $rhm_position ) : ?>
					<?php echo $rhm_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML dựng ở trên. ?>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center' ); ?>
			<?php foreach ( $rhm_desc as $rhm_line ) : ?>
				<p class="mx-auto mb-3 max-w-4xl text-center leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
			<?php endforeach; ?>
			<?php if ( $rhm_image && 'top' === $rhm_position ) : ?>
				<div class="mt-6 lg:mt-8"><?php echo $rhm_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML dựng ở trên. ?></div>
			<?php endif; ?>
			<div class="mt-6 lg:mt-8 lg:pr-24"><?php echo $rhm_list_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML dựng ở trên. ?></div>
			<?php if ( $rhm_image && 'bottom' === $rhm_position ) : ?>
				<div class="mt-6 lg:mt-8"><?php echo $rhm_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML dựng ở trên. ?></div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
