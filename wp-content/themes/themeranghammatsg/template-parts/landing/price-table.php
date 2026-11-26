<?php
/**
 * Landing – Bảng giá: tiêu đề (H2, canh giữa) + mô tả → bảng → ghi chú.
 * Bảng = cột tên dịch vụ (th scope=row) + 1–6 cột giá + cột nút Tư vấn (mở popup đặt lịch). Ô giá: giá gốc gạch ngang (tùy chọn)
 * + giá ưu đãi đỏ, hoặc chỉ giá (xanh thương hiệu). Field: inc/landing/layouts/price-table.php.
 *
 * Mobile (< 768px):
 * - Thẻ (mobile_cards bật VÀ bảng ≥ 4 cột tính cả cột tên + cột nút): CÙNG 1 <table>, CSS .price-table--cards (page-about.css,
 *   ngoài @layer) đổi mỗi <tr> thành thẻ: thanh đầu = tên, mỗi ô = "nhãn cột (data-label) — giá", ô trống ẩn, thanh cuối = nút.
 * - Còn lại: bảng cuộn ngang trong khung (tabindex + role=region cho vùng cuộn), bảng ≥ 4 cột có min-w 720px.
 * Không JS, không ảnh → CLS 0. Khung có class bg-white → "Chữ sáng" của Nền section giữ chữ tối trong bảng.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );

// Cột giá có tiêu đề, giữ chỉ số gốc để khớp ô giá của từng dòng.
$rhm_columns = array();
foreach ( rhm_landing_rows( $rhm_data, 'columns' ) as $rhm_i => $rhm_column ) {
	$rhm_label = rhm_landing_text( $rhm_column, 'title' );
	if ( '' !== $rhm_label ) {
		$rhm_columns[ $rhm_i ] = array( $rhm_label, rhm_landing_text( $rhm_column, 'note' ) );
	}
}

$rhm_rows = array();
// Tên dịch vụ = ô chữ có định dạng (HTML đã lọc, rhm_landing_rich()) + bản chữ thuần (chữ ẩn của nút Tư vấn). Tên rỗng → bỏ dòng.
foreach ( rhm_landing_rows( $rhm_data, 'rows' ) as $rhm_row ) {
	$rhm_name = rhm_landing_rich( $rhm_row, 'name' );
	if ( '' !== $rhm_name ) {
		$rhm_rows[] = array( $rhm_name, rhm_landing_rows( $rhm_row, 'cells' ), trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $rhm_name ) ) ) ) );
	}
}
if ( '' === $rhm_title || ! $rhm_columns || ! $rhm_rows ) {
	return;
}

$rhm_id      = $args['id'];
$rhm_head_id = $rhm_id . '-title';
$rhm_button  = ! isset( $rhm_data['button'] ) || ! empty( $rhm_data['button'] );
$rhm_btn_txt = rhm_landing_text( $rhm_data, 'button_text' );
$rhm_btn_txt = '' !== $rhm_btn_txt ? $rhm_btn_txt : __( 'Tư vấn', 'themeranghammatsg' );
$rhm_url     = rhm_get_booking_url();
$rhm_popup   = rhm_booking_popup_enabled() ? ' data-rhm-popup="booking"' : '';
$rhm_total   = 1 + count( $rhm_columns ) + ( $rhm_button ? 1 : 0 );
$rhm_cards   = ( ! isset( $rhm_data['mobile_cards'] ) || ! empty( $rhm_data['mobile_cards'] ) ) && $rhm_total >= 4;
$rhm_first   = rhm_landing_text( $rhm_data, 'first_column' );
$rhm_first   = '' !== $rhm_first ? $rhm_first : __( 'Dịch vụ', 'themeranghammatsg' ); // Ô tiêu đề cột không được rỗng (a11y).
$rhm_note    = rhm_landing_rich( $rhm_data, 'note' );

// Nền hàng tiêu đề cột (head_bg_*): desktop = <thead><tr> (style inline thắng bg-primary); thẻ mobile = thanh đầu (tên) + thanh cuối
// (nút) đọc biến --rhm-head-bg / --rhm-head-fg trên <table> (page-about.css). "Chữ tối" → chữ màu dark thay vì trắng.
$rhm_head      = rhm_landing_background( $rhm_data, 'head_bg' );
$rhm_head_dark = '' !== $rhm_head['css'] && $rhm_head['dark'];
$rhm_head_tr   = $rhm_head_dark ? 'bg-primary text-dark' : 'bg-primary text-white';
$rhm_head_vars = '' !== $rhm_head['css'] ? '--rhm-head-bg: ' . $rhm_head['css'] . ';' . ( $rhm_head_dark ? ' --rhm-head-fg: var(--color-dark);' : '' ) : '';

// Cột chữ: cột có ít nhất 1 ô dài > 40 ký tự (mô tả, ưu điểm…) HOẶC nhiều dòng / danh sách (ô giá là trình soạn thảo) → mọi ô
// của cột canh trái, chữ thường (đồng bộ cả cột); cột còn lại giữ kiểu giá (đậm, canh giữa). Khóa = chỉ số gốc của cột.
$rhm_text_cols = array();
foreach ( $rhm_rows as [ , $rhm_cells ] ) {
	foreach ( array_keys( $rhm_columns ) as $rhm_i ) {
		$rhm_cell = $rhm_cells[ $rhm_i ] ?? array();
		$rhm_html = rhm_landing_rich( $rhm_cell, 'price' );
		$rhm_multi = preg_match( '#<(ul|ol|br)\b#i', $rhm_html ) || substr_count( strtolower( $rhm_html ), '<p' ) > 1;
		if ( $rhm_multi || mb_strlen( trim( wp_strip_all_tags( $rhm_html ) ) ) > 40 || mb_strlen( rhm_landing_text( $rhm_cell, 'old_price' ) ) > 40 ) {
			$rhm_text_cols[ $rhm_i ] = true;
		}
	}
}

// Khung: thẻ mobile → khung bảng chỉ từ 768px (dưới đó mỗi dòng tự là thẻ); cuộn ngang → khung + vùng cuộn mọi màn hình.
// Ô tự xuống dòng nên bảng thường vừa khung; quá nhiều cột chữ dài thì cuộn ngang thay vì bị cắt (overflow-x-auto, không hidden).
$rhm_wrap  = $rhm_cards
	? 'md:overflow-x-auto md:rounded-xl md:border md:border-line md:bg-white md:shadow-card'
	: 'overflow-x-auto rounded-xl border border-line bg-white shadow-card';
$rhm_table = $rhm_cards
	? 'price-table--cards w-full border-collapse text-sm lg:text-[15px]'
	: ( $rhm_total >= 4 ? 'w-full min-w-[720px] border-collapse text-sm md:min-w-0 lg:text-[15px]' : 'w-full border-collapse text-sm lg:text-[15px]' );
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), rhm_landing_rich( $rhm_data, 'description' ), 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>

		<div class="<?php echo esc_attr( $rhm_wrap ); ?>"<?php echo $rhm_cards ? '' : ' tabindex="0" role="region" aria-labelledby="' . esc_attr( $rhm_head_id ) . '"'; ?>>
			<table class="<?php echo esc_attr( $rhm_table ); ?>" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>"<?php echo '' !== $rhm_head_vars ? ' style="' . esc_attr( $rhm_head_vars ) . '"' : ''; ?>>
				<thead>
					<tr class="<?php echo esc_attr( $rhm_head_tr ); ?>"<?php echo '' !== $rhm_head['css'] ? ' style="' . esc_attr( 'background: ' . $rhm_head['css'] . ';' ) . '"' : ''; ?>>
						<th scope="col" class="min-w-36 px-4 py-4 text-center leading-snug font-bold uppercase lg:min-w-44 lg:px-5"><?php echo esc_html( $rhm_first ); ?></th>
						<?php foreach ( $rhm_columns as [ $rhm_label, $rhm_sub ] ) : ?>
							<th scope="col" class="px-4 py-4 text-center leading-snug font-bold uppercase lg:px-5">
								<?php echo esc_html( $rhm_label ); ?>
								<?php if ( '' !== $rhm_sub ) : ?>
									<span class="mt-1 block text-[13px] font-medium"><?php echo esc_html( $rhm_sub ); ?></span>
								<?php endif; ?>
							</th>
						<?php endforeach; ?>
						<?php if ( $rhm_button ) : ?>
							<th scope="col" class="px-4 py-4"><span class="sr-only"><?php esc_html_e( 'Đăng ký tư vấn', 'themeranghammatsg' ); ?></span></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rhm_rows as [ $rhm_name, $rhm_cells, $rhm_name_text ] ) : ?>
						<tr class="even:bg-mint/60">
							<th scope="row" class="min-w-36 px-4 py-4 text-left leading-snug font-medium text-primary lg:min-w-44 lg:px-5"><div class="rhm-rich"><?php echo $rhm_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div></th>
							<?php foreach ( $rhm_columns as $rhm_i => [ $rhm_label ] ) : ?>
								<?php
								// Giá = HTML đã lọc (trình soạn thảo, rhm_landing_rich()); giá gốc = chữ thuần.
								$rhm_price = rhm_landing_rich( $rhm_cells[ $rhm_i ] ?? array(), 'price' );
								$rhm_old   = rhm_landing_text( $rhm_cells[ $rhm_i ] ?? array(), 'old_price' );
								if ( '' === $rhm_price ) {
									// Chỉ nhập giá gốc → coi là giá.
									[ $rhm_price, $rhm_old ] = array( esc_html( $rhm_old ), '' );
								}
								// Cột chữ ($rhm_text_cols) → canh trái, chữ thường; cột giá → số đậm canh giữa.
								// Không nowrap: số tiền không có dấu cách nên không bị ngắt, chữ dài xuống dòng → bảng vừa khung.
								$rhm_long = isset( $rhm_text_cols[ $rhm_i ] );
								$rhm_td   = match ( true ) {
									'' === $rhm_price => 'is-empty px-4 py-4 text-center',
									$rhm_long         => 'px-4 py-4 text-left wrap-break-word lg:px-5',
									default           => 'px-4 py-4 text-center wrap-break-word lg:px-5',
								};
								?>
								<td data-label="<?php echo esc_attr( $rhm_label ); ?>" class="<?php echo esc_attr( $rhm_td ); ?>">
									<?php if ( '' !== $rhm_price ) : ?>
										<?php // div (không span): giá là HTML của trình soạn thảo (<p>, <ul>…) — khối không được nằm trong span. ?>
										<div class="<?php echo esc_attr( $rhm_long ? 'price-table__value rhm-rich inline-block text-sm leading-relaxed font-medium lg:text-[15px]' : 'price-table__value rhm-rich inline-block text-base leading-snug font-bold lg:text-[17px]' ); ?>">
											<?php if ( '' !== $rhm_old ) : ?>
												<del class="block text-muted"><span class="sr-only"><?php esc_html_e( 'Giá gốc:', 'themeranghammatsg' ); ?> </span><?php echo esc_html( $rhm_old ); ?></del>
												<div class="mt-1 text-[#c62828]"><span class="sr-only"><?php esc_html_e( 'Giá ưu đãi:', 'themeranghammatsg' ); ?> </span><?php echo $rhm_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich() / esc_html(). ?></div>
											<?php else : ?>
												<div class="text-primary"><?php echo $rhm_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich() / esc_html(). ?></div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
							<?php if ( $rhm_button ) : ?>
								<td class="price-table__action px-4 py-4 text-center">
									<a class="inline-flex h-9 items-center rounded-full bg-footer-accent px-5 font-bold whitespace-nowrap text-dark shadow-card transition hover:brightness-95" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo $rhm_popup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute. ?>>
										<?php echo esc_html( $rhm_btn_txt ); ?>
										<span class="sr-only">
											<?php
											/* translators: %s: tên dịch vụ */
											echo esc_html( sprintf( __( 'về %s', 'themeranghammatsg' ), $rhm_name_text ) );
											?>
										</span>
									</a>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if ( '' !== $rhm_note ) : ?>
			<div class="rhm-rich mt-4 text-sm leading-relaxed text-body/85 lg:mt-5"><?php echo $rhm_note; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
		<?php endif; ?>
	</div>
</section>
