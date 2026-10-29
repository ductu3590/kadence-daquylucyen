<?php
/**
 * Trang kết quả tìm kiếm.
 *
 * Không dùng truy vấn chính. Truy vấn tìm kiếm mặc định của WordPress trộn mọi
 * loại nội dung vào một danh sách xếp theo độ liên quan, nên bài viết và mục từ
 * điển nằm xen kẽ nhau — người đọc không biết mình đang nhìn cái gì.
 *
 * Thay vào đó chạy hai truy vấn riêng và bày thành hai nhóm. Từ điển đứng trước:
 * khi ai đó gõ một thuật ngữ, thứ họ cần là định nghĩa ngay, không phải danh
 * sách bài có nhắc tới nó.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( function_exists( '\Kadence\kadence' ) ) {
	\Kadence\kadence()->print_styles( 'kadence-content' );
}

$dql_s     = get_search_query();
$dql_trang = max( 1, (int) get_query_var( 'paged' ) );

// Từ điển chỉ hiện ở trang đầu — sang trang 2 là người đọc đang duyệt bài viết.
$dql_tu_dien = null;

if ( 1 === $dql_trang && post_type_exists( 'dql_thuat_ngu' ) && $dql_s ) {
	$dql_tu_dien = new WP_Query(
		array(
			'post_type'      => 'dql_thuat_ngu',
			'post_status'    => 'publish',
			's'              => $dql_s,
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		)
	);
}

$dql_bai = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		's'              => $dql_s,
		'posts_per_page' => 10,
		'paged'          => $dql_trang,
	)
);

$dql_co_tu_dien = $dql_tu_dien && $dql_tu_dien->have_posts();
$dql_co_bai     = $dql_bai->have_posts();
?>

<?php
/*
 * Thẻ <div>, không phải <main>.
 *
 * header.php của Kadence đã mở sẵn <main id="inner-wrap">, và HTML chỉ cho phép
 * một <main> hiển thị trên một trang. Lồng thêm một cái nữa làm trình đọc màn
 * hình báo hai vùng nội dung chính và người dùng phải chọn giữa hai thứ giống hệt.
 *
 * id="main": đích của liên kết "Skip to content" mà Kadence gắn cứng href="#main".
 */
?>
<div id="main" class="dql-search-page">
	<section class="dql-section">
		<div class="dql-container dql-container--narrow">

			<header class="dql-kq__dau">
				<h1 class="dql-kq__tieude">
					<?php
					printf(
						/* translators: %s: từ khóa tìm kiếm. */
						esc_html__( 'Kết quả cho “%s”', 'kadence-daquylucyen' ),
						'<span class="dql-kq__tu">' . esc_html( $dql_s ) . '</span>'
					);
					?>
				</h1>

				<p class="dql-kq__dem">
					<?php
					printf(
						/* translators: %s: số bài viết tìm thấy. */
						esc_html( _n( '%s bài viết', '%s bài viết', (int) $dql_bai->found_posts, 'kadence-daquylucyen' ) ),
						esc_html( number_format_i18n( $dql_bai->found_posts ) )
					);

					if ( $dql_co_tu_dien ) {
						echo ' · ';
						printf(
							/* translators: %s: số thuật ngữ tìm thấy. */
							esc_html( _n( '%s thuật ngữ', '%s thuật ngữ', (int) $dql_tu_dien->post_count, 'kadence-daquylucyen' ) ),
							esc_html( number_format_i18n( $dql_tu_dien->post_count ) )
						);
					}
					?>
				</p>

				<form role="search" method="get" class="dql-kq__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="dql-kq-field">
						<?php esc_html_e( 'Tìm lại', 'kadence-daquylucyen' ); ?>
					</label>
					<input type="search" id="dql-kq-field" name="s" value="<?php echo esc_attr( $dql_s ); ?>" placeholder="<?php esc_attr_e( 'Tìm loại đá, mệnh, thuật ngữ…', 'kadence-daquylucyen' ); ?>" />
					<button type="submit"><?php esc_html_e( 'Tìm', 'kadence-daquylucyen' ); ?></button>
				</form>
			</header>

			<?php if ( $dql_co_tu_dien ) : ?>
				<section class="dql-kq__nhom dql-kq__nhom--tu-dien">
					<h2 class="dql-eyebrow"><?php esc_html_e( 'Trong từ điển', 'kadence-daquylucyen' ); ?></h2>

					<ul class="dql-kq__tudien">
						<?php
						while ( $dql_tu_dien->have_posts() ) :
							$dql_tu_dien->the_post();
							?>
							<li>
								<a href="<?php the_permalink(); ?>">
									<span class="dql-kq__tudien-tu"><?php the_title(); ?></span>
									<span class="dql-kq__tudien-nghia">
										<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 18, '…' ) ); ?>
									</span>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>
					<?php wp_reset_postdata(); ?>
				</section>
			<?php endif; ?>

			<?php if ( $dql_co_bai ) : ?>
				<section class="dql-kq__nhom">
					<h2 class="dql-eyebrow"><?php esc_html_e( 'Trong bài viết', 'kadence-daquylucyen' ); ?></h2>

					<ul class="dql-kq__ds">
						<?php
						while ( $dql_bai->have_posts() ) :
							$dql_bai->the_post();
							$dql_cats = get_the_category();
							?>
							<li class="dql-kq__item">
								<a class="dql-kq__link" href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<span class="dql-kq__anh">
											<?php the_post_thumbnail( 'dql-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
										</span>
									<?php endif; ?>

									<span class="dql-kq__chu">
										<?php if ( $dql_cats ) : ?>
											<span class="dql-kq__cat"><?php echo esc_html( $dql_cats[0]->name ); ?></span>
										<?php endif; ?>

										<span class="dql-kq__ten"><?php the_title(); ?></span>

										<span class="dql-kq__trich">
											<?php
											// 24 từ: đủ để biết bài nói gì, không đủ để thành một đoạn
											// văn khiến danh sách kết quả dài lê thê.
											echo esc_html( wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 24, '…' ) );
											?>
										</span>

										<span class="dql-kq__meta"><?php dql_the_reading_time(); ?></span>
									</span>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>

					<?php
					$dql_phan_trang = paginate_links(
						array(
							'total'     => (int) $dql_bai->max_num_pages,
							'current'   => $dql_trang,
							'prev_text' => esc_html__( 'Trước', 'kadence-daquylucyen' ),
							'next_text' => esc_html__( 'Sau', 'kadence-daquylucyen' ),
							'type'      => 'list',
						)
					);

					if ( $dql_phan_trang ) {
						echo '<nav class="dql-kq__phantrang" aria-label="' . esc_attr__( 'Phân trang kết quả', 'kadence-daquylucyen' ) . '">';
						echo wp_kses_post( $dql_phan_trang );
						echo '</nav>';
					}

					wp_reset_postdata();
					?>
				</section>
			<?php endif; ?>

			<?php if ( ! $dql_co_bai && ! $dql_co_tu_dien ) : ?>
				<div class="dql-kq__trong">
					<p><?php esc_html_e( 'Không tìm thấy nội dung nào khớp với từ khóa này.', 'kadence-daquylucyen' ); ?></p>

					<p class="dql-kq__goi-y">
						<?php esc_html_e( 'Thử tìm bằng tên loại đá, ví dụ:', 'kadence-daquylucyen' ); ?>
					</p>

					<div class="dql-kq__tu-goi-y">
						<?php
						foreach ( array( 'ruby', 'sapphire', 'thạch anh', 'ngọc bích', 'mệnh Kim' ) as $dql_vd ) :
							?>
							<a class="dql-chip" href="<?php echo esc_url( add_query_arg( 's', rawurlencode( $dql_vd ), home_url( '/' ) ) ); ?>">
								<?php echo esc_html( $dql_vd ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</section>
</div>

<?php
get_footer();
