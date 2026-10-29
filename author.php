<?php
/**
 * Trang tác giả.
 *
 * Không đi qua khung nội dung của Kadence, cùng cách làm với category.php.
 *
 * Thứ tự khối, từ trên xuống:
 *
 *   1. Đầu trang  — đường dẫn, chân dung, tên, chức danh, tiểu sử, link mạng xã
 *                   hội, ba con số, hộp "Nguyên tắc khi viết", dải chip lọc chủ đề.
 *   2. Đọc nhiều nhất — ba thẻ, chỉ ở trang 1 khi chưa lọc.
 *   3. Danh sách  — toàn hàng ngang, kèm nhãn chuyên mục (dql_archive_loop).
 *   4. Phân trang.
 *   5. Dải mời mở Từ điển.
 *
 * Các khối do inc/author.php dựng; danh sách và phân trang dùng chung với trang
 * chuyên mục ở inc/archive.php.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( '\Kadence\kadence' ) ) {
	\Kadence\kadence()->print_styles( 'kadence-content' );
}

$dql_user = get_queried_object();
$dql_dang = dql_author_chu_de();
?>

<?php
/*
 * Thẻ <div>, không phải <main>, và id="main" — cùng lý do với category.php.
 */
?>
<div id="main" class="dql-archive dql-tacgia">

	<?php dql_author_head( $dql_user, $dql_dang ); ?>

	<div class="dql-archive__than">
		<div class="dql-container">

			<?php dql_author_top_cards( $dql_user->ID, $dql_dang ); ?>

			<div class="dql-tglist__dau">
				<h2 class="dql-tglist__title">
					<?php
					if ( $dql_dang ) {
						printf(
							/* translators: %s: tên chuyên mục. */
							esc_html__( 'Bài về %s', 'kadence-daquylucyen' ),
							esc_html( $dql_dang->name )
						);
					} else {
						esc_html_e( 'Tất cả bài viết', 'kadence-daquylucyen' );
					}
					?>
				</h2>
				<span class="dql-tglist__so">
					<?php
					printf(
						/* translators: %s: số bài viết. */
						esc_html( _n( '%s bài', '%s bài', (int) $wp_query->found_posts, 'kadence-daquylucyen' ) ),
						esc_html( number_format_i18n( (int) $wp_query->found_posts ) )
					);
					?>
				</span>
			</div>

			<?php if ( have_posts() ) : ?>

				<?php
				// Đang lọc một chủ đề thì nhãn chuyên mục trên từng dòng trùng đúng
				// tiêu đề danh sách — bỏ đi, cùng quy tắc với trang chuyên mục.
				dql_archive_loop( ! $dql_dang, false );
				?>

				<?php dql_archive_pagination(); ?>

			<?php else : ?>

				<div class="dql-ar-trong">
					<p><?php esc_html_e( 'Tác giả này chưa có bài viết nào ở đây.', 'kadence-daquylucyen' ); ?></p>
				</div>

			<?php endif; ?>

			<?php dql_author_tu_dien(); ?>

		</div>
	</div>

</div>

<?php
get_footer();
