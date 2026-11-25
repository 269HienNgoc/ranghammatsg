<?php
/**
 * Thẻ 1 khách hàng "Ảnh trước – sau", dùng chung:
 * - trang chủ: template-parts/sections/before-after.php (dữ liệu Tùy biến, inc/home-before-after.php);
 * - landing: template-parts/landing/before-after.php (dữ liệu ACF của section).
 *
 * Bố cục: ảnh chân dung (trái, cao bằng cột phải) | ảnh "Trước" + ảnh "Sau" xếp dọc (phải, 3:2)
 * → tên khách (H3) + tỉnh/thành → dòng dịch vụ + dòng loại implant/vật liệu (có icon, trống thì không in).
 *
 * $args:
 * - item:     array{portrait:int, before:int, after:int, name:string, location:string, service:string, product:string} (đã kiểm tra hợp lệ).
 * - sizes:    sizes của 1 cột ảnh (chân dung / trước / sau cùng độ rộng cột).
 * - priority: true → ảnh chân dung là LCP (eager + fetchpriority="high"); còn lại lazy.
 * - h3_attr:  ' style="color: …"' (màu tiêu đề con của landing) hoặc ''.
 * - card / media / label: class thẻ (độ rộng slide, bo góc, đệm) / bo góc khung ảnh / bo góc nhãn "Trước", "Sau".
 *   Chuỗi class viết NGUYÊN VĂN ở nơi gọi (Tailwind quét file đó + file này: @source trong page-home.css, page-about.css).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_item = $args['item'] ?? array();
if ( ! $rhm_item ) {
	return;
}

$rhm_sizes    = (string) ( $args['sizes'] ?? '' );
$rhm_priority = ! empty( $args['priority'] );
$rhm_h3_attr  = (string) ( $args['h3_attr'] ?? '' );
$rhm_media    = (string) ( $args['media'] ?? 'rounded-2xl' );
$rhm_label    = 'absolute bottom-0 left-0 ' . ( $args['label'] ?? 'rounded-tr-xl' ) . ' bg-deep px-3 py-1 text-sm font-bold text-white lg:px-4 lg:text-base';

$rhm_image = static function ( int $id, string $alt, string $class, bool $priority = false ) use ( $rhm_sizes ): string {
	$attrs = array(
		'class' => $class,
		'alt'   => $alt,
		'sizes' => $rhm_sizes,
	);
	$attrs += $priority
		? array(
			'loading'       => 'eager',
			'fetchpriority' => 'high',
		)
		: array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		);
	return wp_get_attachment_image( $id, 'full', false, $attrs );
};
?>
<article class="<?php echo esc_attr( (string) ( $args['card'] ?? '' ) ); ?>">
	<div class="grid grid-cols-2 gap-2">
		<div class="relative row-span-2 overflow-hidden <?php echo esc_attr( $rhm_media ); ?> bg-soft">
			<?php echo $rhm_image( $rhm_item['portrait'], $rhm_item['name'], 'absolute inset-0 size-full object-cover', $rhm_priority ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
		</div>
		<div class="relative aspect-[3/2] overflow-hidden <?php echo esc_attr( $rhm_media ); ?> bg-soft">
			<?php
			/* translators: %s: tên khách hàng. */
			echo $rhm_image( $rhm_item['before'], sprintf( __( 'Trước điều trị – %s', 'themeranghammatsg' ), $rhm_item['name'] ), 'size-full object-cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image().
			?>
			<span class="<?php echo esc_attr( $rhm_label ); ?>" aria-hidden="true"><?php esc_html_e( 'Trước', 'themeranghammatsg' ); ?></span>
		</div>
		<div class="relative aspect-[3/2] overflow-hidden <?php echo esc_attr( $rhm_media ); ?> bg-soft">
			<?php
			/* translators: %s: tên khách hàng. */
			echo $rhm_image( $rhm_item['after'], sprintf( __( 'Sau điều trị – %s', 'themeranghammatsg' ), $rhm_item['name'] ), 'size-full object-cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image().
			?>
			<span class="<?php echo esc_attr( $rhm_label ); ?>" aria-hidden="true"><?php esc_html_e( 'Sau', 'themeranghammatsg' ); ?></span>
		</div>
	</div>

	<div class="mt-4 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 px-1 wrap-anywhere">
		<h3 class="text-base font-bold text-primary uppercase lg:text-lg"<?php echo $rhm_h3_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_color_attr() đã escape. ?>><?php echo esc_html( $rhm_item['name'] ); ?></h3>
		<?php if ( '' !== $rhm_item['location'] ) : ?>
			<p class="text-sm font-bold text-muted uppercase lg:text-base"><?php echo esc_html( $rhm_item['location'] ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( '' !== $rhm_item['service'] || '' !== $rhm_item['product'] ) : ?>
		<ul class="mt-2 space-y-1.5 px-1 text-[15px] leading-snug text-body lg:text-base">
			<?php if ( '' !== $rhm_item['service'] ) : ?>
				<li class="flex gap-2">
					<?php echo rhm_icon_svg( 'menu', 'tooth', 'mt-px size-5 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
					<?php echo esc_html( $rhm_item['service'] ); ?>
				</li>
			<?php endif; ?>
			<?php if ( '' !== $rhm_item['product'] ) : ?>
				<li class="flex gap-2">
					<?php echo rhm_icon_svg( 'menu', 'implant', 'mt-px size-5 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
					<?php echo esc_html( $rhm_item['product'] ); ?>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</article>
