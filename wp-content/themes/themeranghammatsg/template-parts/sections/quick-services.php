<?php
/**
 * Section: dịch vụ nổi bật (hàng icon dưới hero). Dữ liệu: inc/quick-services.php. CSS: page-home.css
 * Desktop 1 hàng (tối đa 8 mục); tablet 4 mục / hàng; mobile 3 mục / hàng (chữ 16px vẫn đủ rộng).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_items = rhm_get_quick_services();

if ( ! $rhm_items ) {
	return;
}
?>
<section class="py-6 lg:py-10" aria-label="<?php esc_attr_e( 'Dịch vụ nổi bật', 'themeranghammatsg' ); ?>">
	<div class="site-container">
		<ul class="flex flex-wrap justify-center gap-y-5 rounded-2xl border border-[#edf1f5] bg-linear-to-b from-white to-[#f4f7fc] px-2 py-6 shadow-[0_8px_30px_rgb(16_42_67/0.06)] lg:flex-nowrap lg:px-4 lg:py-4">
			<?php foreach ( $rhm_items as $rhm_item ) : ?>
				<?php
				$rhm_tag  = $rhm_item['link'] ? 'a' : 'div';
				$rhm_href = $rhm_item['link'] ? ' href="' . esc_url( $rhm_item['link'] ) . '"' : '';
				?>
				<li class="basis-1/3 sm:basis-1/4 lg:flex-1">
					<<?php echo esc_html( $rhm_tag ); ?> class="group flex h-full flex-col items-center gap-3 px-1 text-center"<?php echo $rhm_href; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
						<span class="flex size-16 items-center justify-center rounded-full bg-[#eef9f8] shadow-[inset_0_0_0_1px_#dcefea] transition duration-300 group-hover:-translate-y-1 group-hover:bg-white group-hover:shadow-[0_6px_16px_rgb(11_122_85/0.15)] lg:size-20">
							<?php if ( $rhm_item['icon'] ) : ?>
								<img class="size-8 lg:size-10" src="<?php echo esc_url( $rhm_item['icon'] ); ?>" alt="" width="40" height="40" loading="lazy" decoding="async">
							<?php endif; ?>
						</span>
						<span class="text-base leading-snug text-body transition-colors group-hover:text-primary"><?php echo nl2br( esc_html( $rhm_item['title'] ), false ); ?></span>
					</<?php echo esc_html( $rhm_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
