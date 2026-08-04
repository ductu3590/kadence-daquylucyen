<?php
/**
 * Trang chủ — bố cục "Cổng kiến thức".
 *
 * Dẫn người đọc bằng chủ đề, không dẫn bằng danh sách bài mới. Lý do và các
 * phương án đã loại: xem docs/thiet-ke.md.
 *
 * Lưu ý: WordPress luôn ưu tiên file này cho trang chủ, kể cả khi bạn đặt một
 * trang tĩnh làm trang chủ trong Cài đặt → Đọc. Muốn quay lại trang chủ do
 * WordPress lo, chỉ cần đổi tên hoặc xóa file này.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( '\Kadence\kadence' ) ) {
	\Kadence\kadence()->print_styles( 'kadence-content' );
}
?>

<main id="dql-home" class="dql-home">
	<?php
	/*
	 * Thứ tự các dải và màu nền xen kẽ nhau có chủ đích:
	 *
	 *   hero       nền xám nhạt   — câu định vị, ô tìm kiếm
	 *   featured   nền trắng      — bài nổi bật, chữ đè lên ảnh
	 *   [quảng cáo]                — chỉ hiện khi vùng widget có nội dung
	 *   pillars    nền xám nhạt   — ba trụ nội dung
	 *   menh-tool  nền xanh đậm   — ĐIỂM NHẤN, cắt nhịp trang
	 *   latest     nền trắng      — bài mới nhất
	 *   newsletter nền xám nhạt   — đăng ký nhận bài qua email
	 *
	 * Không xen kẽ nền thì các khối trôi liền một mạch và mắt không nhận ra
	 * ranh giới — đúng cảm giác "rời rạc" dù nội dung vẫn đủ.
	 */
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/featured' );

	dql_ad_slot( 'dql-ad-home', 'dql-ad--trang-chu' );

	get_template_part( 'template-parts/home/pillars' );
	get_template_part( 'template-parts/home/menh-tool' );
	get_template_part( 'template-parts/home/latest' );
	get_template_part( 'template-parts/home/newsletter' );
	?>
</main>

<?php
get_footer();
