<?php
/**
 * Trang chủ — bố cục tạp chí "Đá Thô" (từ bản 2.0).
 *
 * Giữ nguyên tắc "cổng kiến thức" của bản 1.x (dẫn bằng chủ đề, không dẫn bằng
 * danh sách bài mới) nhưng mở đầu bằng khối bài nổi bật cỡ lớn như một tạp chí
 * — vì site giờ viết về cả Lục Yên, ảnh phong cảnh và con người đáng được đặt
 * lên đầu. Lý do và các phương án đã loại: xem docs/thiet-ke.md.
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

<?php
/*
 * Thẻ <div>, không phải <main>.
 *
 * header.php của Kadence đã mở sẵn <main id="inner-wrap">, và HTML chỉ cho phép
 * một <main> hiển thị trên một trang.
 */
?>
<div id="dql-home" class="dql-home">
	<?php
	/*
	 * Thứ tự các dải và nền xen kẽ nhau có chủ đích:
	 *
	 *   featured   nền đá hoa     — ba bài nổi bật, chữ đè lên ảnh (mở đầu kiểu tạp chí)
	 *   hero       nền trắng      — câu định vị + ô tìm kiếm, hai cột
	 *   pillars    nền đá hoa     — trụ nội dung (3–6 chuyên mục có huy hiệu đá)
	 *   [quảng cáo]
	 *   menh-tool  nền than đá    — ĐIỂM NHẤN, cắt nhịp trang
	 *   spotlight  nền đá hoa     — chuyên mục tiêu điểm (chọn trong Customizer)
	 *   latest     nền trắng      — bài mới nhất + cột bên
	 *   newsletter nền đá hoa     — đăng ký nhận bài
	 *
	 * Không xen kẽ nền thì các khối trôi liền một mạch và mắt không nhận ra
	 * ranh giới.
	 */
	get_template_part( 'template-parts/home/featured' );
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/pillars' );

	dql_ad_slot( 'dql-ad-home', 'dql-ad--trang-chu' );

	get_template_part( 'template-parts/home/menh-tool' );
	get_template_part( 'template-parts/home/spotlight' );
	get_template_part( 'template-parts/home/latest' );
	get_template_part( 'template-parts/home/newsletter' );
	?>
</div>

<?php
get_footer();
