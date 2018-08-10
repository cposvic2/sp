<?php
/**
 * Template Name: Sponsorship Information
 */
?>

<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<!-- layout-container -->
<div id="layout" class="pagewidth clearfix uc-custom">

	<?php themify_content_before(); // hook ?>
	<!-- content -->
	<div id="content" class="clearfix">
    	<?php themify_content_start(); // hook ?>

		<?php
		/////////////////////////////////////////////
		// 404
		/////////////////////////////////////////////
		if(is_404()): ?>
			<h1 class="page-title" itemprop="headline"><?php _e('404','themify'); ?></h1>
			<p><?php _e( 'Page not found.', 'themify' ); ?></p>
		<?php endif; ?>

		<?php
		/////////////////////////////////////////////
		// PAGE
		/////////////////////////////////////////////
		?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="page-<?php the_ID(); ?>" class="type-page" itemscope itemtype="http://schema.org/Article">

			<!-- page-title -->
			<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
			<!-- /page-title -->

			<div class="page-content entry-content" itemprop="articleBody">

				<?php the_content(); ?>

				<?php
				$user_id = get_current_user_id();
				$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
				$current_school_year = get_current_school_year();
				$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );

				if (count($uc_classes)) :
					foreach ($uc_classes as $uc_class) : ?>
				<h4><?php echo $uc_class['label']; ?></h4>
				<p>
					<input type="text" id="link-class-<?php echo $uc_class['value']; ?>" class="uc-input input-large link-input" value="https://stemplayground.org/sign-up-as-a-sponsor/?class_id=<?php echo $uc_class['value']; ?>">
					<button class="link-copy" data-clipboard-target="#link-class-<?php echo $uc_class['value']; ?>"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
				</p>
				<?php endforeach; ?>
				<?php else: ?>
				<p>You have no classes. </p>
				<?php endif; ?>

				<script>
				var clipboard = new Clipboard('.link-copy');
				</script>
			
				<?php edit_post_link(__('Edit','themify'), '[', ']'); ?>

			</div>
			<!-- /.post-content -->

			</div><!-- /.type-page -->
		<?php endwhile; endif; ?>

		<?php themify_content_end(); // hook ?>
	</div>
	<!-- /content -->
    <?php themify_content_after(); // hook ?>

	<?php
	/////////////////////////////////////////////
	// Sidebar
	/////////////////////////////////////////////
	if ($themify->layout != 'sidebar-none'): get_sidebar(); endif; ?>

</div>
<!-- /layout-container -->
<script type="text/javascript">
	jQuery('#sponsorship').validate({
		messages: {
			uname: {
				minlength: "<?php echo vp_option('uc_option.us_uname_min'); ?>",
			},
			upass: {
				minlength: "<?php echo vp_option('uc_option.us_upass_min'); ?>",
			},
			upassconf: {
				minlength: "<?php echo vp_option('uc_option.us_upass_min'); ?>",
				equalTo: "<?php echo vp_option('uc_option.us_upass_equal'); ?>",
			},
			email: {
				required: "<?php echo vp_option('uc_option.us_uemail_req'); ?>",
				minlength: "<?php echo vp_option('uc_option.us_uemail_email'); ?>",
			},
		},
		invalidHandler: function(event, validator) {
			var errors = validator.numberOfInvalids();
			if ( errors ) {
				var message = errors == 1
				? "<?php echo vp_option('uc_option.us_verify_error'); ?>"
				: "<?php echo vp_option('uc_option.us_verify_errors'); ?>".replace('!fields!', errors );
				display_alert( message, 'warning', null, 'main-alert' );
			}
		},
		wrapper: 'div',
		errorClass: "alert alert-warning",
		errorPlacement: function(error, element) {
				error.appendTo(element.closest('.input')).hide().slideDown('normal');
		}
	});
</script>
<?php get_footer(); ?>