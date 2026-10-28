<?php
/**
 * Trang chủ — chuyên mục tiêu điểm.
 *
 * Bốn bài mới nhất (có ảnh) của MỘT chuyên mục, bày thành hàng thẻ. Dùng để
 * đưa một mảng mới của site ra trang chủ — ví dụ Văn hóa & con người Lục Yên —
 * khi mảng đó chưa đủ nổi trong khối bài mới nhất.
 *
 * Chọn ở Customizer → Trang chủ Đá Quý → "Chuyên mục tiêu điểm". Chưa chọn
 * thì khối không hiện. Chuyên mục có dưới 2 bài có ảnh cũng không hiện với
 * khách (một hàng thẻ chỉ có một thẻ trông như hỏng); quản trị viên thấy lời
 * nhắc.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

$dql_cat_id = absint( get_theme_mod( 'dql_spotlight_cat', 0 ) );

if ( ! $dql_cat_id ) {
	return;
}

$dql_cat = get_term( $dql_cat_id, 'category' );

if ( ! $dql_cat || is_wp_error( $dql_cat ) ) {
	return;
}

$dql_q = new WP_Query(
	array(
		'post_type'           => 'post',
		'cat'                 => $dql_cat->term_id, // Gồm cả chuyên mục con.
		'posts_per_page'      => 4,
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

$dql_is_admin = current_user_can( 'manage_options' );

if ( $dql_q->post_count < 2 && ! $dql_is_admin ) {
	wp_reset_postdata();
	return;
}

$dql_kicker = dql_home_option( 'dql_spotlight_title' );
$dql_mau    = dql_category_color_key( $dql_cat );
?>

<section class="dql-section dql-spotlight" style="--dql-gem: var(--dql-da-<?php echo esc_attr( $dql_mau ); ?>)">
	<div class="dql-container">

		<div class="dql-khoi-dau dql-khoi-dau--gem" data-reveal>
			<div class="dql-khoi-dau__gem-wrap">
				<?php dql_the_category_badge( $dql_cat, 'vua' ); ?>
				<div>
					<?php if ( $dql_kicker ) : ?>
						<p class="dql-kicker dql-kicker--gem"><?php echo esc_html( $dql_kicker ); ?></p>
					<?php endif; ?>
					<h2 class="dql-khoi-dau__title"><?php echo esc_html( $dql_cat->name ); ?></h2>
				</div>
			</div>

			<a class="dql-nut dql-nut--vien" href="<?php echo esc_url( get_category_link( $dql_cat->term_id ) ); ?>">
				<?php esc_html_e( 'Xem chuyên mục', 'kadence-daquylucyen' ); ?>
			</a>
		</div>

		<?php if ( $dql_q->post_count < 2 ) : ?>
			<p class="dql-cho-admin">
				<?php esc_html_e( 'Chuyên mục tiêu điểm cần ít nhất 2 bài có ảnh đại diện mới hiện với khách. Ô này chỉ quản trị viên thấy.', 'kadence-daquylucyen' ); ?>
			</p>
		<?php else : ?>
			<div class="dql-cards dql-cards--4">
				<?php
				while ( $dql_q->have_posts() ) :
					$dql_q->the_post();
					?>
					<article <?php post_class( 'dql-card' ); ?> data-reveal>
						<a class="dql-card__link" href="<?php the_permalink(); ?>">
							<span class="dql-card__media">
								<?php the_post_thumbnail( 'dql-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
							</span>
							<span class="dql-card__body">
								<span class="dql-card__title"><?php the_title(); ?></span>
								<span class="dql-card__meta">
									<?php dql_the_updated(); ?>
									<span class="dql-cham" aria-hidden="true"></span>
									<?php dql_the_reading_time(); ?>
								</span>
							</span>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php
wp_reset_postdata();
