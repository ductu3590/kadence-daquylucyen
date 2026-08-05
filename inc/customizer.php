<?php
/**
 * Tùy chọn Customizer riêng cho trang chủ.
 *
 * Dùng API Customizer chuẩn của WordPress, không dùng hệ control riêng của
 * Kadence. Lý do: các tùy chọn ở đây đơn giản (chữ và chọn chuyên mục), dùng
 * API chuẩn thì không phụ thuộc vào thay đổi nội bộ của Kadence khi nó cập nhật.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Danh sách tùy chọn trang chủ.
 *
 * Gom vào một hàm để đăng ký Customizer và hàm đọc giá trị dùng chung một
 * nguồn — tránh cảnh sửa mặc định ở một chỗ mà quên chỗ kia.
 *
 * @return array
 */
function dql_home_settings() {
	return array(
		'dql_hero_title'   => array(
			'default' => __( 'Hiểu đúng về đá quý trước khi mua', 'kadence-daquylucyen' ),
			'label'   => __( 'Câu định vị', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_hero_sub'     => array(
			'default' => __( 'Kiến thức, phong thủy và kiểm định đá quý Lục Yên. Viết cho người mua, không viết cho người bán.', 'kadence-daquylucyen' ),
			'label'   => __( 'Câu mô tả dưới', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
		'dql_menh_title'   => array(
			'default' => __( 'Bạn sinh năm nào?', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề ô tra mệnh', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_menh_desc'    => array(
			'default' => __( 'Nhập năm sinh để biết bạn thuộc mệnh gì và những loại đá nào hợp với mệnh đó.', 'kadence-daquylucyen' ),
			'label'   => __( 'Mô tả ô tra mệnh', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
		'dql_latest_title' => array(
			'default' => __( 'Bài mới nhất', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề khối bài mới', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_newsletter_title' => array(
			'default' => __( 'Nhận bài mới qua email', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề khối đăng ký email', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_newsletter_desc'  => array(
			'default' => __( 'Mỗi khi có bài kiến thức mới về đá quý, bạn sẽ nhận được một email. Không quảng cáo, không spam.', 'kadence-daquylucyen' ),
			'label'   => __( 'Mô tả khối đăng ký email', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
		'dql_newsletter_form'  => array(
			'default' => '',
			'label'   => __( 'Mã form đăng ký email', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
	);
}

/**
 * Lấy giá trị một tùy chọn trang chủ, tự động rơi về mặc định.
 *
 * @param string $key Khóa tùy chọn.
 * @return string
 */
function dql_home_option( $key ) {
	$settings = dql_home_settings();
	$default  = isset( $settings[ $key ]['default'] ) ? $settings[ $key ]['default'] : '';

	return (string) get_theme_mod( $key, $default );
}

/**
 * Đăng ký mục Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Đối tượng Customizer.
 */
function dql_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'dql_home',
		array(
			'title'       => __( 'Trang chủ Đá Quý', 'kadence-daquylucyen' ),
			'priority'    => 25,
			'description' => __( 'Nội dung các khối trên trang chủ. Bố cục do template quyết định, không đổi được ở đây.', 'kadence-daquylucyen' ),
		)
	);

	// Các ô chữ.
	$priority = 10;
	foreach ( dql_home_settings() as $key => $config ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'textarea' === $config['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'    => $config['label'],
				'section'  => 'dql_home',
				'type'     => $config['type'],
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	// Ba trụ nội dung. Mỗi trụ là một chuyên mục.
	$pillar_labels = array(
		1 => __( 'Trụ 1 — chuyên mục', 'kadence-daquylucyen' ),
		2 => __( 'Trụ 2 — chuyên mục', 'kadence-daquylucyen' ),
		3 => __( 'Trụ 3 — chuyên mục', 'kadence-daquylucyen' ),
	);

	foreach ( $pillar_labels as $index => $label ) {
		$wp_customize->add_setting(
			'dql_pillar_' . $index,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'dql_pillar_' . $index,
			array(
				'label'       => $label,
				'section'     => 'dql_home',
				'type'        => 'select',
				'choices'     => dql_category_choices(),
				'priority'    => 60 + $index,
				'description' => 1 === $index
					? __( 'Bỏ trống cả ba thì trang chủ tự lấy ba chuyên mục có nhiều bài nhất.', 'kadence-daquylucyen' )
					: '',
			)
		);
	}

	// Số bài trong khối "Bài mới nhất".
	$wp_customize->add_setting(
		'dql_latest_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_latest_count',
		array(
			'label'       => __( 'Số bài mới nhất hiển thị', 'kadence-daquylucyen' ),
			'section'     => 'dql_home',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 3,
				'max'  => 12,
				'step' => 3,
			),
			'priority'    => 70,
		)
	);

	// Logo chân trang.
	//
	// inc/footer.php vẫn luôn đọc theme mod này, nhưng trước đây không có ô nào
	// đặt nó — chỉ gán được bằng code. Hậu quả: chân trang nền xanh đậm hiện tên
	// site dạng chữ thay vì logo, và chủ site không có cách nào sửa.
	//
	// Đặt cạnh logo header trong "Cài đặt site" chứ không tạo mục riêng, vì đây
	// là chuyện nhận diện thương hiệu — người đi tìm nó sẽ tìm ở chỗ có logo.
	$wp_customize->add_setting(
		'dql_footer_logo',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'dql_footer_logo',
			array(
				'label'       => __( 'Logo chân trang', 'kadence-daquylucyen' ),
				'description' => __( 'Bản chữ trắng. Chân trang nền xanh đậm nên logo chữ navy đặt lên đó sẽ gần như biến mất. Bỏ trống thì chân trang hiện tên site dạng chữ.', 'kadence-daquylucyen' ),
				'section'     => 'title_tagline',
				'mime_type'   => 'image',
				'priority'    => 9,
			)
		)
	);
}
add_action( 'customize_register', 'dql_customize_register' );

/**
 * Danh sách chuyên mục cho ô chọn trong Customizer.
 *
 * @return array
 */
function dql_category_choices() {
	$choices = array( 0 => __( '— Chưa chọn —', 'kadence-daquylucyen' ) );

	$categories = get_categories(
		array(
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);

	foreach ( $categories as $category ) {
		$choices[ $category->term_id ] = $category->name;
	}

	return $choices;
}
