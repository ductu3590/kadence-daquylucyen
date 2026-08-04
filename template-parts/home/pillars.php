<?php
/**
 * Trang chủ — ba trụ nội dung.
 *
 * Đây là khối quan trọng nhất của trang chủ: nó dẫn người đọc bằng chủ đề thay
 * vì bắt họ dò trong danh sách bài. Mỗi trụ là một chuyên mục, chọn trong
 * Customizer → Trang chủ Đá Quý.
 *
 * Mỗi thẻ mượn ảnh của bài mới nhất trong chuyên mục. Bản đầu tiên chỉ có biểu
 * tượng và chữ, và trông rỗng tuếch khi chuyên mục chưa có mô tả — ảnh là thứ
 * cho thẻ sức nặng thị giác mà không bắt chủ site phải viết thêm gì.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_pillars = dql_home_pillars();

if ( ! $dql_pillars ) {
	return;
}
?>

<section class="dql-section dql-pillars">
	<div class="dql-container">

		<p class="dql-eyebrow" data-reveal><?php esc_html_e( 'Bắt đầu từ đâu', 'kadence-daquylucyen' ); ?></p>

		<div class="dql-pillars__grid">
			<?php
			foreach ( $dql_pillars as $dql_index => $dql_pillar ) :
				$dql_img   = dql_category_image( $dql_pillar );
				$dql_total = dql_category_deep_count( $dql_pillar );
				?>
				<a class="dql-pillar" href="<?php echo esc_url( get_category_link( $dql_pillar->term_id ) ); ?>" data-reveal>

					<span class="dql-pillar__media">
						<?php if ( $dql_img ) : ?>
							<?php
							echo wp_get_attachment_image(
								$dql_img,
								'dql-card',
								false,
								array(
									'alt'     => '',
									'loading' => 'lazy',
								)
							);
							?>
						<?php endif; ?>

						<span class="dql-pillar__icon-wrap">
							<?php dql_pillar_icon( $dql_index ); ?>
						</span>
					</span>

					<span class="dql-pillar__body">
						<h2 class="dql-pillar__name"><?php echo esc_html( $dql_pillar->name ); ?></h2>

						<?php if ( $dql_pillar->description ) : ?>
							<span class="dql-pillar__desc"><?php echo esc_html( wp_trim_words( $dql_pillar->description, 20 ) ); ?></span>
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
