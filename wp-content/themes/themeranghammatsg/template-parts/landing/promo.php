<?php
/**
 * Landing – Ô banner nổi bật (dạng ô): nền gradient mint, ảnh 45% trái + logo / tiêu đề / dòng phụ phải.
 * Có link → link nằm ở tiêu đề, phủ cả ô bằng ::after (1 link duy nhất, bấm đâu trong ô cũng được).
 * Field: inc/landing/layouts/promo.php. Thiếu tiêu đề → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
if ( '' === $rhm_title ) {
	return;
}

$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
$rhm_logo    = absint( $rhm_data['logo'] ?? 0 );
$rhm_logo    = $rhm_logo && wp_get_attachment_url( $rhm_logo ) ? $rhm_logo : 0; // Cho phép SVG (không phải "image" với WP).
$rhm_url     = rhm_landing_url( $rhm_data, 'link' );
$rhm_head_id = $args['id'] . '-title';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="relative h-full overflow-hidden rounded-2xl border border-line bg-linear-to-br from-mint via-white to-mint p-5 shadow-card transition-shadow hover:shadow-lg lg:p-6" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="<?php echo esc_attr( $rhm_image ? 'grid h-full grid-cols-[45%_minmax(0,1fr)] items-center gap-4 lg:gap-6' : 'flex h-full flex-col justify-center' ); ?>">
		<?php if ( $rhm_image ) : ?>
			<div>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, rhm_landing_box_sizes( $args['span'] ?? 10, 0.45, 0.45, 8 ), 'block h-auto w-full', (bool) $args['is_first'] );
				?>
			</div>
		<?php endif; ?>
		<div class="min-w-0">
			<?php
			if ( $rhm_logo ) {
				$rhm_logo_src = wp_get_attachment_image_src( $rhm_logo, 'full' );
				$rhm_logo_w   = ( $rhm_logo_src && $rhm_logo_src[2] > 0 ) ? (int) round( 28 * $rhm_logo_src[1] / $rhm_logo_src[2] ) : 112;
				// Cao 28px cố định; width tính theo tỉ lệ file → không CLS. Ảnh raster: srcset để màn hình 2x đủ nét.
				echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress escape thuộc tính.
					$rhm_logo,
					'full',
					false,
					array(
						'class'    => 'mb-3 h-7 w-auto',
						'width'    => $rhm_logo_w,
						'height'   => 28,
						'sizes'    => $rhm_logo_w . 'px',
						'alt'      => rhm_landing_image_alt( $rhm_logo ),
						'loading'  => 'lazy',
						'decoding' => 'async',
					)
				);
			}
			?>
			<h2 id="<?php echo esc_attr( $rhm_head_id ); ?>" class="text-lg leading-snug font-bold text-primary lg:text-xl">
				<?php if ( '' !== $rhm_url ) : ?>
					<a class="after:absolute after:inset-0 hover:text-primary-dark" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo rhm_external_link_attrs( $rhm_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>><?php echo esc_html( $rhm_title ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $rhm_title ); ?>
				<?php endif; ?>
			</h2>
			<?php foreach ( rhm_landing_lines( $rhm_data, 'subtitle' ) as $rhm_line ) : ?>
				<p class="mt-2 text-sm leading-snug text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
			<?php endforeach; ?>
		</div>
	</div>
</section>
