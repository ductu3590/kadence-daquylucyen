<?php
/**
 * Trang danh sách bài — trang được chọn ở Cài đặt → Đọc → "Trang bài viết".
 *
 * Dùng chung toàn bộ khối với trang chuyên mục (category.php). Khác đúng một
 * chỗ: ở đây không có chuyên mục nào, nên mọi hàm nhận $term = null và tự bỏ
 * phần phụ thuộc chuyên mục — hộp "Bắt đầu từ đâu" không hiện, dải chip đổi
 * thành ba chuyên mục cha, số liệu tính trên toàn bộ bài viết của site.
 *
 * Vì sao có file này: trước đây trang danh sách bài vẫn đi qua khung Kadence
 * trong khi trang chuyên mục đã dựng lại. Hai trang cùng là danh sách bài mà
 * một bên lưới thẻ có cột bên, một bên danh sách hàng ngang không cột bên —
 * người đọc đi từ trang này sang trang kia tưởng lạc sang site khác.
 *
 * Các khối do inc/archive.php dựng.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( '\Kadence\kadence' ) ) {
	\Kadence\kadence()->print_styles( 'kadence-content' );
}

$dql_trang = max( 1, (int) get_query_var( 'paged' ) );

// Vị trí chèn dải tra mệnh. Chỉ ở trang 1: người đã lật sang trang 2 là người
// đang tìm một bài cụ thể, chắn ngang danh sách của họ là quấy rầy.
$dql_chen = ( 1 === $dql_trang ) ? 4 : 0;
$dql_dem  = 0;
?>

<?php
/*
 * Thẻ <div>, không phải <main>.
 *
 * header.php của Kadence đã mở sẵn <main id="inner-wrap">, và HTML chỉ cho phép
 * một <main> hiển thị trên một trang. Lồng thêm một cái nữa làm trình đọc màn
 * hình báo hai vùng nội dung chính và người dùng phải chọn giữa hai thứ giống hệt.
 */
?>
<div id="dql-danh-sach-bai" class="dql-archive">

	<?php dql_archive_head(); ?>

	<div class="dql-archive__than">
		<div class="dql-container">

			<?php if ( have_posts() ) : ?>

				<div class="dql-rows">
					<?php
					while ( have_posts() ) :
						the_post();
						$dql_dem++;

						dql_archive_row( true );

						/*
						 * Đóng danh sách lại, chèn dải, rồi mở danh sách mới.
						 *
						 * Không đặt dải vào bên trong .dql-rows: các dòng dùng
						 * đường kẻ dưới để ngăn nhau, một khối lạ nằm giữa sẽ
						 * thừa ra một đường kẻ ngay trên nó.
						 */
						if ( $dql_chen && $dql_dem === $dql_chen && $dql_dem < (int) $GLOBALS['wp_query']->post_count ) {
							echo '</div>';
							dql_archive_cta_menh();
							echo '<div class="dql-rows">';
						}
					endwhile;
					?>
				</div>

				<?php dql_archive_pagination(); ?>

			<?php else : ?>

				<div class="dql-ar-trong">
					<p><?php esc_html_e( 'Chưa có bài viết nào được đăng.', 'kadence-daquylucyen' ); ?></p>
				</div>

			<?php endif; ?>

		</div>
	</div>

	<?php dql_archive_top_strip(); ?>

</div>

<?php
get_footer();
