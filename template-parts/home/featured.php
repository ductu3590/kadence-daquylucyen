<?php
/**
 * Trang chủ — khối bài nổi bật, chữ chèn lên ảnh. Mở đầu trang kiểu tạp chí.
 *
 * Một thẻ lớn bên trái (có đoạn dẫn), hai thẻ nhỏ xếp dọc bên phải; cả ba có
 * chữ nằm đè lên ảnh với lớp phủ tối chuyển dần. Nhãn chuyên mục mang huy hiệu
 * đá — người đọc nhận ra bài thuộc mảng nào trước cả khi đọc tiêu đề.
 *
 * Vì sao chữ đè lên ảnh (không phải chữ dưới ảnh): thẻ luôn cao 100% ô được
 * chia, nên hai thẻ nhỏ không bao giờ để lại khoảng trắng giữa chúng. Lớp phủ
 * là bắt buộc: ảnh đá quý thường chụp trên nền trắng, chữ trắng đặt thẳng lên
 * sẽ không đọc được.
 *
 * Chọn bài nào: mở Bài viết, "Sửa nhanh", tích "Dán bài này lên trang chủ".
 * Chưa ghim đủ ba bài thì phần còn lại lấy bài mới nhất.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_ids = dql_featured_post_ids();

if ( ! $dql_ids ) {
	return;
}

$dql_posts = array_filter( array_map( 'get_post', $dql_ids ) );

if ( ! $dql_posts ) {
	return;
}

$dql_lon = array_shift( $dql_posts );

/**
 * In một thẻ bài nổi bật.
 *
 * @param WP_Post $post  Bài viết.
 * @param string  $size  Kích thước ảnh.
 * @param string  $extra Class bổ sung.
 */
function dql_featured_card( $post, $size, $extra ) {
	$cat   = dql_post_main_category( $post );
	$ghim  = is_sticky( $post->ID );
	$lon   = 'dql-feat--lon' === $extra;
	?>
	<article class="dql-feat <?php echo esc_attr( $extra ); ?>">
		<a class="dql-feat__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
			<?php
			echo get_the_post_thumbnail(
				$post,
				$size,
				array(
					'alt'           => '',
					'class'         => 'dql-feat__img',
					// Cả ba thẻ nằm trên màn hình đầu: không tải lười, không hiện dần —
					// ảnh lớn nhất trang (LCP) phải hiện ngay khi HTML tới.
					'loading'       => 'eager',
					'decoding'      => 'async',
					'fetchpriority' => $lon ? 'high' : 'auto',
					'sizes'         => $lon
						? '(min-width: 1024px) 760px, 100vw'
						: '(min-width: 1024px) 480px, (min-width: 561px) 50vw, 100vw',
				)
			);
			?>

			<span class="dql-feat__phu"></span>

			<span class="dql-feat__body">
				<?php if ( $cat ) : ?>
					<span class="dql-cat-label dql-cat-label--tren-anh" style="--dql-gem: var(--dql-da-<?php echo esc_attr( dql_category_color_key( $cat ) ); ?>)">
						<?php dql_the_category_badge( $cat, 'nho' ); ?>
						<span class="dql-cat-label__ten"><?php echo esc_html( $cat->name ); ?></span>
						<?php if ( $ghim && $lon ) : ?>
							<span class="dql-cat-label__phu"><?php esc_html_e( 'Bài nền tảng', 'kadence-daquylucyen' ); ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>

				<span class="dql-feat__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>

				<?php if ( $lon ) : ?>
					<span class="dql-feat__trich">
						<?php echo esc_html( wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' ) ); ?>
					</span>
				<?php endif; ?>

				<span class="dql-feat__meta">
					<?php dql_the_updated( $post ); ?>
					<span class="dql-cham" aria-hidden="true"></span>
					<?php dql_the_reading_time( $post ); ?>
				</span>
			</span>
		</a>
	</article>
	<?php
}
?>

<section class="dql-section dql-featured">
	<div class="dql-container">
		<div class="dql-featured__grid">

			<?php dql_featured_card( $dql_lon, 'dql-hero', 'dql-feat--lon' ); ?>

			<?php if ( $dql_posts ) : ?>
				<div class="dql-featured__phu">
					<?php foreach ( $dql_posts as $dql_p ) : ?>
						<?php dql_featured_card( $dql_p, 'dql-card', 'dql-feat--nho' ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
