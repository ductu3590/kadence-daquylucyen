<?php
/**
 * Đăng ký nhóm mẫu khối (block pattern) riêng của site.
 *
 * Bản thân các mẫu nằm trong thư mục patterns/. WordPress từ 6.0 tự quét thư
 * mục đó và đăng ký mọi file .php có phần chú thích đầu file — không cần gọi
 * register_block_pattern() cho từng mẫu.
 *
 * File này chỉ tạo nhóm để các mẫu có chỗ đứng riêng trong bảng chọn, không lẫn
 * vào hàng trăm mẫu mặc định của WordPress và Kadence.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tạo nhóm mẫu khối cho site.
 */
function dql_register_pattern_category() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'dql',
		array(
			'label'       => __( 'Đá Quý Lục Yên', 'kadence-daquylucyen' ),
			'description' => __( 'Các khối nội dung dùng lặp lại trong bài viết về đá quý.', 'kadence-daquylucyen' ),
		)
	);
}
add_action( 'init', 'dql_register_pattern_category' );
