<?php
/**
 * Template Name: Sponsorship Template
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

				<?php 
				$class_id = intval($_GET['class_id']);
				if ($class_id) :
				$class_name = get_the_title( $class_id );

				$teacher_id = get_post_meta( $class_id, 'teacher', true );
				$teacher_name = get_post_meta( $teacher_id, 'first_name', true );
				$school_id = get_post_meta( $teacher_id, 'school', true );
				$school_name = get_the_title( $school_id );

				$price = pretty_price(vp_option('uc_option.sponsor_price'));

				$the_content = get_the_content();
				$the_content = str_replace('!teachername!', $teacher_name, $the_content);
				$the_content = str_replace('!classname!', $class_name, $the_content);
				$the_content = str_replace('!schoolname!', $school_name, $the_content);
				$the_content = str_replace('!price!', $price, $the_content);
				echo $the_content; ?>
				
				<?php
					if ( isset($response['status']) && $response['status'] !== 'OK' )
						echo '<div class="alert alert-caution">'.$response['reason'].'</div>';
				?>

				<form id="sponsorship" action="<?php echo add_query_arg( 'class_id', $class_id, the_permalink() ); ?>" method="post">
					<div class="uc-metabox">
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Name/Business Name</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="name" id="name" class="uc-input input-large" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>" data-rule-required="true">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Email</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="email" name="email" id="email" class="uc-input input-large" value="<?php echo get_post_or_saved_postdata('email', ''); ?>" data-rule-required="true" data-rule-email="true">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Twitter Handle</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="twitter" id="twitter" class="uc-input input-large" value="<?php echo isset($_POST['twitter']) ? $_POST['twitter'] : '' ?>" data-rule-required="false">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Password (if you would like to make an account)</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="password" name="upass" id="upass" class="uc-input input-large" value="<?php echo isset($_POST['upass']) ? $_POST['upass'] : '' ?>" data-rule-required="false" data-rule-minlength="6">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Confirm Password</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="password" name="upassconf" id="upassconf" class="uc-input input-large" value="<?php echo isset($_POST['upass']) ? $_POST['upass'] : '' ?>" data-rule-required="false" data-rule-minlength="6" data-rule-equalto="#upass">
								</div>
							</div>
						</div>
					</div>
					<?php include(locate_template('template-parts/stripe.php') ); ?>
					<?php wp_nonce_field( 'sponsorship', 'sponsorship_noncename' ); ?>
					<input type="hidden" name="action" value="sponsorship">
					<input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
					<div class="alert-container main-alert"></div>
					<input type="submit" id="sponsorship-submit" class="button" value="Sponsor Class" disabled>
				</form>

				<?php endif; ?>

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