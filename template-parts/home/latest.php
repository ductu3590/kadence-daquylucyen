<?php
/**
 * Trang chủ — bài mới nhất, hai cột kèm cột bên.
 *
 * Danh sách bài bên trái dạng ngang (ảnh trái, chữ phải), cột bên bên phải do
 * dql_sidebar_home() dựng — xem inc/sidebar.php.
 *
 * Cột bên này KHÔNG dùng chung với trang bài viết nữa. Lý do: dùng chung thì
 * widget "Bài viết mới nhất" đặt trong đó sẽ in ngay cạnh danh sách bài mới bên
 * trái, cùng một nội dung hai lần, cách nhau vài centimet.
 *
 * Cột bên trống thì phần này tự chuyển về một cột, không để lại khoảng hở.
 *
 * Đặt cuối cùng có chủ đích: với người lần đầu vào site, "bài nào mới" là thông
 * tin ít giá trị nhất. Người đọc quay lại thường xuyên mới quan tâm khối này,
 * và họ sẽ cuộn xuống tìm.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_count = absint( get_theme_mod( 'dql_latest_count', 6 ) );
$dql_count = $dql_count ? $dql_count : 6;

/*
 * Chỉ lấy bài CÓ ảnh đại diện.
 *
 * Đây là lưới dẫn bằng hình. Một thẻ thiếu ảnh để lại ô xám trống giữa các thẻ
 * khác và làm cả khối trông như đang hỏng — đã xảy ra thật với bài mẫu
 * "Chào tất cả mọi người!" của WordPress.
 */
$dql_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => $dql_count,
		// Loại trừ theo ID những bài đã nằm ở khối nổi bật phía trên.
		'post__not_in'        => dql_featured_post_ids(),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		),
	)
);

if ( ! $dql_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

$dql_title    = dql_home_option( 'dql_latest_title' );
$dql_co_cot   = dql_sidebar_home_active();
$dql_blog     = get_option( 'page_for_posts' );
?>

<section class="dql-section dql-latest<?php echo $dql_co_cot ? ' dql-latest--co-cot' : ''; ?>">
	<div class="dql-container">
		<div class="dql-latest__bo-cuc">

			<div class="dql-latest__chinh">
				<div class="dql-latest__head" data-reveal>
					<h2 class="dql-latest__title"><?php echo esc_html( $dql_title ); ?></h2>

					<?php
					// Chỉ hiện "Xem tất cả" khi thật sự có trang danh sách bài để dẫn tới.
					if ( $dql_blog ) :
						?>
						<a class="dql-latest__all" href="<?php echo esc_url( get_permalink( $dql_blog ) ); ?>">
							<?php esc_html_e( 'Xem tất cả', 'kadence-daquylucyen' ); ?>
							<?php dql_the_icon( 'tiep' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<ul class="dql-latest__ds">
					<?php
					while ( $dql_query->have_posts() ) :
						$dql_query->the_post();
						$dql_cat = dql_post_main_category();
						?>
						<li class="dql-tin" data-reveal>
							<a class="dql-tin__link" href="<?php the_permalink(); ?>">
								<span class="dql-tin__anh dql-hat">
									<?php
									// sizes khớp cột ảnh 168px / 104px (≤ 560px) trong home.css,
									// để trình duyệt chọn bản nhỏ trong srcset thay vì bản 720px.
									the_post_thumbnail(
										'dql-card',
										array(
											'alt'     => '',
											'loading' => 'lazy',
											'sizes'   => '(max-width: 560px) 104px, 168px',
										)
									);
									?>
								</span>

								<span class="dql-tin__chu">
									<?php if ( $dql_cat ) : ?>
										<?php echo dql_category_label( $dql_cat, false, 'dql-tin__cat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Đã thoát trong dql_category_label(). ?>
									<?php endif; ?>

									<span class="dql-tin__ten"><?php the_title(); ?></span>

									<span class="dql-tin__trich">
										<?php echo esc_html( wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 22, '…' ) ); ?>
									</span>

									<span class="dql-tin__meta">
										<?php dql_the_updated(); ?>
										<span class="dql-cham" aria-hidden="true"></span>
										<?php dql_the_reading_time(); ?>
									</span>
								</span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			</div>

			<?php if ( $dql_co_cot ) : ?>
				<aside class="dql-sidebar dql-latest__cot" aria-label="<?php esc_attr_e( 'Nội dung bên lề', 'kadence-daquylucyen' ); ?>">
					<?php dql_sidebar_home(); ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>
</section>

<?php
wp_reset_postdata();
