<?php
/**
 * Đá Quý Lục Yên — child theme của Kadence.
 *
 * File này chỉ làm một việc: nạp các module trong inc/.
 * Không viết logic trực tiếp ở đây — mỗi tính năng thuộc về một file riêng.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/*
 * Số này phải khớp với Version trong style.css.
 *
 * Không chỉ để cho đẹp: WordPress nhớ tạm danh sách file trong thư mục patterns/
 * và chỉ quét lại khi phiên bản theme đổi. Thêm một mẫu khối mới mà quên tăng
 * phiên bản thì mẫu đó sẽ không xuất hiện trong trình soạn thảo, và không có
 * thông báo lỗi nào cả.
 */
define( 'DQL_VERSION', '2.0.0' );
define( 'DQL_DIR', get_stylesheet_directory() );
define( 'DQL_URI', get_stylesheet_directory_uri() );

require_once DQL_DIR . '/inc/setup.php';
require_once DQL_DIR . '/inc/brand.php';
require_once DQL_DIR . '/inc/icons.php';
require_once DQL_DIR . '/inc/theme-mode.php';
require_once DQL_DIR . '/inc/enqueue.php';
require_once DQL_DIR . '/inc/template-tags.php';
require_once DQL_DIR . '/inc/customizer.php';
require_once DQL_DIR . '/inc/header.php';
require_once DQL_DIR . '/inc/toc.php';
require_once DQL_DIR . '/inc/single.php';
require_once DQL_DIR . '/inc/archive.php';
require_once DQL_DIR . '/inc/author.php';
require_once DQL_DIR . '/inc/patterns.php';
require_once DQL_DIR . '/inc/footer.php';
require_once DQL_DIR . '/inc/sidebar.php';
require_once DQL_DIR . '/inc/search.php';
