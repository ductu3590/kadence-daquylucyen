<?php
/**
 * Trang chủ — khối đăng ký nhận bài mới qua email.
 *
 * Theme chỉ dựng phần khung: tiêu đề, mô tả, và chỗ đặt form. Bản thân form do
 * plugin lo — theme không nên đụng vào việc lưu địa chỉ email của người đọc,
 * vì đó là dữ liệu phải sống sót khi đổi theme.
 *
 * Dán mã nhúng của form vào Customizer → Trang chủ Đá Quý → "Mã form đăng ký
 * email". Site đã có sẵn WPForms Lite, mã sẽ dạng [wpforms id="123"].
 *
 * Chưa dán mã thì khối này KHÔNG hiện với khách, chỉ quản trị viên thấy chỗ
 * dành sẵn — giống cách xử lý khối tra mệnh khi chưa có plugin.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_shortcode = trim( dql_home_option( 'dql_newsletter_form' ) );
$dql_is_admin  = current_user_can( 'manage_options' );

if ( ! $dql_shortcode && ! $dql_is_admin ) {
	return;
}

$dql_title = dql_home_option( 'dql_newsletter_title' );
$dql_desc  = dql_home_option( 'dql_newsletter_desc' );
?>

<section class="dql-section dql-newsletter<?php echo $dql_shortcode ? '' : ' dql-newsletter--placeholder'; ?>">
	<div class="dql-container">
		<div class="dql-newsletter__box" data-reveal>

			<div class="dql-newsletter__text">
				<h2 class="dql-newsletter__title"><?php echo esc_html( $dql_title ); ?></h2>

				<?php if ( $dql_desc ) : ?>
					<p class="dql-newsletter__desc"><?php echo esc_html( $dql_desc ); ?></p>
				<?php endif; ?>
			</div>

			<div class="dql-newsletter__form">
				<?php if ( $dql_shortcode ) : ?>
					<?php echo do_shortcode( $dql_shortcode ); ?>
				<?php else : ?>
					<p class="dql-newsletter__notice">
						<?php esc_html_e( 'Chỗ dành cho form đăng ký email. Tạo form bằng WPForms rồi dán mã nhúng vào Customizer → Trang chủ Đá Quý. Khối này chỉ hiện với quản trị viên cho tới khi có mã.', 'kadence-daquylucyen' ); ?>
					</p>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
