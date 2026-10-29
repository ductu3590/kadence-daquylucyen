<?php
/**
 * Trang chuyên mục — phương án "Cổng chủ đề".
 *
 * Không đi qua khung nội dung của Kadence. Cùng cách làm với search.php, và cùng
 * lý do: bố cục ở đây là danh sách hàng ngang không có cột bên, còn Kadence chỉ
 * dựng lưới thẻ có cột bên. Uốn cái này thành cái kia bằng CSS thì mỗi lần
 * Kadence đổi markup là một lần trang gãy.
 *
 * Thứ tự khối, từ trên xuống:
 *
 *   1. Đầu trang   — đường dẫn, tên, mô tả, ba con số, hộp "Bắt đầu từ đâu", chip.
 *   2. Danh sách   — ba bài mới nhất dạng thẻ, còn lại hàng ngang (dql_archive_loop).
 *   3. Dải tra mệnh — chèn sau ba hàng đầu, chỉ ở trang 1.
 *   4. Phân trang  — kèm dòng cho biết đang ở đâu trong tổng số bài.
 *   5. Đọc nhiều nhất — dải cuối trang, thay cho khối cũ ở cột bên.
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

$dql_term = get_queried_object();
?>

<?php
/*
 * Thẻ <div>, không phải <main>.
 *
 * header.php của Kadence đã mở sẵn <main id="inner-wrap">, và HTML chỉ cho phép
 * một <main> hiển thị trên một trang. Lồng thêm một cái nữa làm trình đọc màn
 * hình báo hai vùng nội dung chính và người dùng phải chọn giữa hai thứ giống hệt.
 *
 * id="main": đích của liên kết "Skip to content" mà Kadence gắn cứng href="#main".
 */
?>
<div id="main" class="dql-archive">

	<?php dql_archive_head( $dql_term ); ?>

	<div class="dql-archive__than">
		<div class="dql-container">

			<?php if ( have_posts() ) : ?>

				<?php dql_archive_loop( false ); ?>

				<?php dql_archive_pagination(); ?>

			<?php else : ?>

				<div class="dql-ar-trong">
					<p><?php esc_html_e( 'Chuyên mục này chưa có bài viết nào.', 'kadence-daquylucyen' ); ?></p>

					<p class="dql-ar-trong__goi-y">
						<?php esc_html_e( 'Thử một trong các chuyên mục khác:', 'kadence-daquylucyen' ); ?>
					</p>

					<div class="dql-ar-trong__muc">
						<?php
						$dql_khac = get_categories(
							array(
								'parent'  => 0,
								'number'  => 4,
								'orderby' => 'count',
								'order'   => 'DESC',
								'exclude' => array( dql_uncategorized_id() ),
							)
						);

						foreach ( $dql_khac as $dql_cat ) :
							?>
							<a class="dql-chip" href="<?php echo esc_url( get_category_link( $dql_cat->term_id ) ); ?>">
								<?php echo esc_html( $dql_cat->name ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

			<?php endif; ?>

		</div>
	</div>

	<?php dql_archive_top_strip( $dql_term ); ?>

</div>

<?php
get_footer();
