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
 *   2. Danh sách   — hàng ngang, ảnh nhỏ bên trái.
 *   3. Dải tra mệnh — chèn sau bài thứ tư, chỉ ở trang 1.
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

$dql_term  = get_queried_object();
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
<div id="dql-chuyen-muc" class="dql-archive">

	<?php dql_archive_head( $dql_term ); ?>

	<div class="dql-archive__than">
		<div class="dql-container">

			<?php if ( have_posts() ) : ?>

				<div class="dql-rows">
					<?php
					while ( have_posts() ) :
						the_post();
						$dql_dem++;

						dql_archive_row();

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
								'exclude' => array( (int) get_option( 'default_category' ) ),
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
