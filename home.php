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

				<?php dql_archive_loop( true ); ?>

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
