<?php
/**
 * Khai báo năng lực của theme: bản dịch, kích thước ảnh, hỗ trợ trình soạn thảo.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nạp file dịch của child theme.
 *
 * Kadence tự lo phần dịch của nó. Đây là chỗ cho chuỗi riêng của site,
 * ví dụ "Thời gian đọc", "Mục lục bài viết".
 */
function dql_load_textdomain() {
	load_child_theme_textdomain( 'kadence-daquylucyen', DQL_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'dql_load_textdomain' );

/**
 * Đăng ký kích thước ảnh riêng.
 *
 * Kadence đã có sẵn vài kích thước, nhưng chúng thiên về bố cục thương mại.
 * Blog đá quý cần ảnh macro cắt đúng tỉ lệ để lưới bài trông đều nhau.
 */
function dql_image_sizes() {
	// Ảnh thẻ bài viết trong lưới. Tỉ lệ 3:2, cắt cứng để lưới không bị so le.
	add_image_size( 'dql-card', 720, 480, true );

	// Ảnh bìa bài viết đơn, đủ sắc nét trên màn hình lớn mà không quá nặng.
	add_image_size( 'dql-hero', 1440, 810, true );

	// Ảnh vuông cho bộ sưu tập và ảnh mẫu đá.
	add_image_size( 'dql-square', 600, 600, true );
}
add_action( 'after_setup_theme', 'dql_image_sizes' );

/**
 * Đặt tên tiếng Việt cho các kích thước ảnh để hiện trong trình soạn thảo.
 *
 * @param array $sizes Danh sách kích thước đang có.
 * @return array
 */
function dql_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'dql-card'   => __( 'Thẻ bài viết (720×480)', 'kadence-daquylucyen' ),
			'dql-hero'   => __( 'Ảnh bìa (1440×810)', 'kadence-daquylucyen' ),
			'dql-square' => __( 'Ảnh vuông (600×600)', 'kadence-daquylucyen' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'dql_image_size_names' );

/**
 * Bổ sung hỗ trợ cho trình soạn thảo khối.
 */
function dql_editor_support() {
	// Dùng CSS riêng cho trình soạn thảo để nội dung trong admin trông giống ngoài trang.
	add_theme_support( 'editor-styles' );

	// blocks.css nạp ở cả hai nơi — cùng một file, không có bản sao nào phải
	// giữ đồng bộ bằng tay. Người viết bài thấy đúng thứ độc giả sẽ thấy.
	add_editor_style(
		array(
			'assets/css/blocks.css',
			'assets/css/editor.css',
		)
	);

	// Không đăng ký editor-color-palette ở đây.
	// Kadence đã trỏ bảng màu của trình soạn thảo về các biến --global-palette
	// và đã tắt bảng màu mặc định của WordPress trong theme.json của nó.
	// Ghi đè ở đây sẽ cắt đứt liên kết đó.
}
add_action( 'after_setup_theme', 'dql_editor_support', 20 );
