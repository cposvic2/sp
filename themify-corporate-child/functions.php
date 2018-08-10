<?php 

define('UPTOWNCODE_THEME_NAME', 'stemplayground_theme' );
define('UPTOWNCODE_THEME_PATH', get_stylesheet_directory() );
define('UPTOWNCODE_THEME_URL',  get_stylesheet_directory_uri() );

/**
 * Setup Stemplayground Theme
 */
include( UPTOWNCODE_THEME_PATH . '/uc-functions/ajax-submit.php');
include( UPTOWNCODE_THEME_PATH . '/uc-functions/uc-theme-functions.php');

// Register scripts
add_action( 'wp_enqueue_scripts', 'enqueue_scripts_and_styles' );
function enqueue_scripts_and_styles() {
	wp_register_script( 'jquery-validate', UPTOWNCODE_THEME_URL . '/assets/js/jquery.validate.min.js', array( 'jquery' ), '1.0.0' );
	wp_register_script( 'canvasjs', 'https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js', array( 'jquery' ), '1.0.0' );
	wp_register_script( 'stemplayground', UPTOWNCODE_THEME_URL . '/assets/js/stemplayground.js', array( 'jquery', 'jquery-validate', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable' ), filemtime(UPTOWNCODE_THEME_PATH . '/assets/js/stemplayground.js') );
	wp_register_script( 'clipboard', UPTOWNCODE_THEME_URL . '/assets/js/clipboard.js', array( 'jquery' ), '1.0.0' );
	
	// Enqueue parent style
	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );

	wp_enqueue_script( 'jquery-validate' );
	wp_enqueue_script( 'canvasjs' );
	wp_enqueue_script( 'stemplayground' );

	if ( is_page_template( 'page-sponsorship-info.php' ) ) {
		wp_enqueue_script( 'clipboard' );
	}
}

add_action( 'after_setup_theme', 'uc_theme_setup' );
function uc_theme_setup() {
	add_image_size( 'activity-board', 260, 100, false );
}

//Stop compressing images
add_filter('jpeg_quality', function($arg){return 100;});

// Customize login logo
function uc_login_logo() { ?>
	<style type="text/css">
		#login h1 a {
			background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/login-logo.png);
			width: 300px;
			height: 244px;
			-webkit-background-size: auto;
			background-size: auto;
			margin: 0 auto;
		}
	</style>
<?php }
add_action( 'login_enqueue_scripts', 'uc_login_logo' );




 ?>