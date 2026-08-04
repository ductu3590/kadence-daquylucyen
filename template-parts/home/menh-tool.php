<?php
/**
 * Trang chủ — ô tra đá hợp mệnh.
 *
 * Công cụ thật nằm ở plugin dql-core (giai đoạn 4), không nằm trong theme —
 * dữ liệu phải sống sót khi đổi theme.
 *
 * Khi chưa có plugin, khối này KHÔNG hiển thị cho khách. Một ô nhập liệu bấm
 * vào không có gì xảy ra còn tệ hơn là không có ô nào. Chỉ quản trị viên thấy
 * chỗ dành sẵn, kèm lời nhắc.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_has_tool = function_exists( 'dql_core_menh_form' );
$dql_is_admin = current_user_can( 'manage_options' );

if ( ! $dql_has_tool && ! $dql_is_admin ) {
	return;
}

$dql_title = dql_home_option( 'dql_menh_title' );
$dql_desc  = dql_home_option( 'dql_menh_desc' );
?>

<?php // id="dql-menh" là đích của liên kết "Tra ngay" ở cột bên bài viết. ?>
<section id="dql-menh" class="dql-section dql-menh<?php echo $dql_has_tool ? '' : ' dql-menh--placeholder'; ?>">
	<div class="dql-container">
		<div class="dql-menh__box" data-reveal>
			<div class="dql-menh__text">
				<h2 class="dql-menh__title"><?php echo esc_html( $dql_title ); ?></h2>
				<?php if ( $dql_desc ) : ?>
					<p class="dql-menh__desc"><?php echo esc_html( $dql_desc ); ?></p>
				<?php endif; ?>
			</div>

			<div class="dql-menh__form">
				<?php if ( $dql_has_tool ) : ?>
					<?php dql_core_menh_form(); ?>
				<?php else : ?>
					<p class="dql-menh__notice">
						<?php esc_html_e( 'Chỗ dành cho công cụ tra mệnh. Khối này chỉ hiện với quản trị viên cho tới khi plugin dql-core được cài và kích hoạt.', 'kadence-daquylucyen' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
