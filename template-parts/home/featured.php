<?php
/**
 * Trang chủ — khối bài nổi bật, chữ chèn lên ảnh.
 *
 * Một thẻ lớn bên trái, hai thẻ nhỏ xếp dọc bên phải, cả ba đều có chữ nằm đè
 * lên ảnh với lớp phủ tối chuyển dần.
 *
 * Bản trước đặt chữ BÊN DƯỚI ảnh. Cách đó tạo ra một lỗi bố cục thật: hai thẻ
 * phải chỉ cao bằng nội dung của chúng, trong khi cột phải phải kéo dài bằng
 * thẻ trái — sinh ra khoảng trắng lớn ở giữa. Cho chữ đè lên ảnh giải quyết
 * luôn cả hai chuyện: thẻ luôn lấp đầy chiều cao được chia, và khối trông chắc
 * chắn hơn hẳn.
 *
 * Ba bài ở đây được khối "bài mới nhất" phía dưới bỏ qua, tránh lặp lại.
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
	$cats = get_the_category( $post->ID );
	?>
	<article class="dql-feat <?php echo esc_attr( $extra ); ?>" data-reveal>
		<a class="dql-feat__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
			<?php
			echo get_the_post_thumbnail(
				$post,
				$size,
				array(
					'alt'     => '',
					'class'   => 'dql-feat__img',
					'loading' => 'dql-feat--lon' === $extra ? 'eager' : 'lazy',
				)
			);
			?>

			<span class="dql-feat__phu"></span>

			<span class="dql-feat__body">
				<?php if ( $cats ) : ?>
					<span class="dql-feat__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
				<?php endif; ?>

				<span class="dql-feat__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>

				<span class="dql-feat__meta"><?php dql_the_reading_time( $post ); ?></span>
			</span>
		</a>
	</article>
	<?php
}
?>

<section class="dql-section dql-featured">
	<div class="dql-container">

		<p class="dql-eyebrow" data-reveal><?php esc_html_e( 'Đáng đọc lúc này', 'kadence-daquylucyen' ); ?></p>

		<div class="dql-featured__grid">

			<?php dql_featured_card( $dql_lon, 'dql-hero', 'dql-feat--lon' ); ?>

			<div class="dql-featured__phu">
				<?php foreach ( $dql_posts as $dql_p ) : ?>
					<?php dql_featured_card( $dql_p, 'dql-card', 'dql-feat--nho' ); ?>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
