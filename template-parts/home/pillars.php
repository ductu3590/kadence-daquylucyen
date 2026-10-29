<?php
/**
 * Trang chủ — trụ nội dung: "Lục Yên qua từng góc nhìn".
 *
 * Khối quan trọng nhất của trang chủ: dẫn người đọc bằng CHỦ ĐỀ thay vì bắt
 * họ dò trong danh sách bài. Mỗi trụ là một chuyên mục, chọn trong Customizer
 * → Trang chủ Đá Quý (tối đa sáu — site giờ có cả văn hóa, du lịch, ẩm thực).
 *
 * Mỗi thẻ: ảnh của bài mới nhất trong chuyên mục, huy hiệu đá lớn đè lên mép
 * dưới ảnh (icon và màu của chuyên mục), tên, mô tả, số bài. Ảnh cho thẻ sức
 * nặng thị giác mà không bắt chủ site viết thêm gì; huy hiệu cho thẻ bản sắc.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_pillars = dql_home_pillars();

if ( ! $dql_pillars ) {
	return;
}

$dql_title = dql_home_option( 'dql_pillars_title' );
$dql_blog  = get_option( 'page_for_posts' );
?>

<section class="dql-section dql-pillars">
	<div class="dql-container">

		<div class="dql-khoi-dau" data-reveal>
			<div>
				<p class="dql-kicker"><?php esc_html_e( 'Bắt đầu từ đâu', 'kadence-daquylucyen' ); ?></p>
				<?php if ( $dql_title ) : ?>
					<h2 class="dql-khoi-dau__title"><?php echo esc_html( $dql_title ); ?></h2>
				<?php endif; ?>
			</div>

			<?php if ( $dql_blog ) : ?>
				<a class="dql-khoi-dau__link" href="<?php echo esc_url( get_permalink( $dql_blog ) ); ?>">
					<?php esc_html_e( 'Tất cả bài viết', 'kadence-daquylucyen' ); ?>
					<?php dql_the_icon( 'tiep' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="dql-pillars__grid dql-pillars__grid--<?php echo (int) count( $dql_pillars ); ?>">
			<?php
			foreach ( $dql_pillars as $dql_pillar ) :
				$dql_img   = dql_category_image( $dql_pillar );
				$dql_total = dql_category_deep_count( $dql_pillar );
				$dql_mau   = dql_category_color_key( $dql_pillar );
				?>
				<a class="dql-pillar" href="<?php echo esc_url( get_category_link( $dql_pillar->term_id ) ); ?>" data-reveal style="--dql-gem: var(--dql-da-<?php echo esc_attr( $dql_mau ); ?>)">

					<span class="dql-pillar__media dql-hat">
						<?php if ( $dql_img ) : ?>
							<?php
							echo wp_get_attachment_image(
								$dql_img,
								'dql-card',
								false,
								array(
									'alt'     => '',
									'loading' => 'lazy',
									// Ba cột ~400px từ 1024px; hai cột ở khổ vừa. Dưới 600px ảnh bị ẩn.
									'sizes'   => '(min-width: 1024px) 400px, 50vw',
								)
							);
							?>
						<?php endif; ?>

						<?php dql_the_category_badge( $dql_pillar, 'lon', 'dql-pillar__gem' ); ?>
					</span>

					<span class="dql-pillar__body">
						<h3 class="dql-pillar__name"><?php echo esc_html( $dql_pillar->name ); ?></h3>

						<?php if ( $dql_pillar->description ) : ?>
							<span class="dql-pillar__desc"><?php echo esc_html( wp_trim_words( $dql_pillar->description, 22 ) ); ?></span>
						<?php endif; ?>

						<?php if ( $dql_total ) : ?>
							<span class="dql-pillar__count">
								<?php
								printf(
									/* translators: %s: số bài viết trong chuyên mục. */
									esc_html( _n( '%s bài viết', '%s bài viết', $dql_total, 'kadence-daquylucyen' ) ),
									esc_html( number_format_i18n( $dql_total ) )
								);
								?>
							</span>
						<?php endif; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
