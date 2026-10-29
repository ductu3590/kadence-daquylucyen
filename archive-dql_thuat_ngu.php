<?php
/**
 * Trang từ điển thuật ngữ: danh sách A–Z.
 *
 * Không dùng lưới thẻ như trang chuyên mục. Từ điển là thứ người ta TRA, không
 * phải thứ người ta lướt — nên bày dạng danh sách gọn, nhóm theo chữ cái đầu,
 * kèm chỉ mục nhảy nhanh ở trên.
 *
 * Trang này chỉ hiển thị. Loại nội dung do plugin dql-core đăng ký; tắt plugin
 * là trang này không còn được gọi tới nữa.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( '\Kadence\kadence' ) ) {
	\Kadence\kadence()->print_styles( 'kadence-content' );
}

// Lấy toàn bộ thuật ngữ một lần. Từ điển vài trăm mục vẫn nhẹ hơn nhiều so với
// phân trang rồi bắt người tra bấm qua lại.
$dql_terms = get_posts(
	array(
		'post_type'      => 'dql_thuat_ngu',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

/**
 * Lấy chữ cái đầu để nhóm, bỏ dấu tiếng Việt.
 *
 * remove_accents() biến "Độ cứng" thành "Do cung", nhờ vậy Đ và D nằm chung
 * một nhóm — đúng với cách người Việt tra từ điển.
 *
 * @param string $title Tên thuật ngữ.
 * @return string
 */
function dql_chu_dau( $title ) {
	$s = remove_accents( $title );
	$s = mb_strtoupper( mb_substr( trim( $s ), 0, 1, 'UTF-8' ), 'UTF-8' );

	return preg_match( '/[A-Z]/', $s ) ? $s : '#';
}

$dql_nhom = array();

foreach ( $dql_terms as $dql_term ) {
	$dql_nhom[ dql_chu_dau( $dql_term->post_title ) ][] = $dql_term;
}

ksort( $dql_nhom );

$dql_bang_chu = range( 'A', 'Z' );
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
<div id="main" class="dql-tudien">
	<section class="dql-section">
		<div class="dql-container dql-container--narrow">

			<h1 class="dql-tudien__tieude"><?php post_type_archive_title(); ?></h1>

			<p class="dql-tudien__mota">
				<?php esc_html_e( 'Giải nghĩa ngắn gọn các thuật ngữ thường gặp khi tìm hiểu và mua đá quý.', 'kadence-daquylucyen' ); ?>
			</p>

			<?php if ( ! $dql_terms ) : ?>

				<p><?php esc_html_e( 'Từ điển chưa có thuật ngữ nào.', 'kadence-daquylucyen' ); ?></p>

			<?php else : ?>

				<ul class="dql-tudien__chumuc">
					<?php
					foreach ( $dql_bang_chu as $dql_chu ) :
						if ( isset( $dql_nhom[ $dql_chu ] ) ) :
							?>
							<li><a href="#chu-<?php echo esc_attr( $dql_chu ); ?>"><?php echo esc_html( $dql_chu ); ?></a></li>
						<?php else : ?>
							<li><span aria-hidden="true"><?php echo esc_html( $dql_chu ); ?></span></li>
							<?php
						endif;
					endforeach;
					?>
				</ul>

				<?php foreach ( $dql_nhom as $dql_chu => $dql_muc ) : ?>
					<section class="dql-tudien__nhom" id="chu-<?php echo esc_attr( $dql_chu ); ?>">
						<h2 class="dql-tudien__chu"><?php echo esc_html( $dql_chu ); ?></h2>

						<?php foreach ( $dql_muc as $dql_item ) : ?>
							<div class="dql-tudien__muc">
								<h3 class="dql-tudien__tu">
									<a href="<?php echo esc_url( get_permalink( $dql_item ) ); ?>">
										<?php echo esc_html( $dql_item->post_title ); ?>
									</a>
								</h3>
								<p class="dql-tudien__nghia">
									<?php echo esc_html( wp_trim_words( wp_strip_all_tags( $dql_item->post_content ), 40, '…' ) ); ?>
								</p>
							</div>
						<?php endforeach; ?>
					</section>
				<?php endforeach; ?>

			<?php endif; ?>

		</div>
	</section>
</div>

<?php
get_footer();
