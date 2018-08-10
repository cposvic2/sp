<?php
/**
 * Template Name: Signup Template
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
					if ( isset($response['status']) && $response['status'] !== 'OK' )
						echo '<div class="alert alert-caution">'.$response['reason'].'</div>';
				?>

				<form id="signup-form" action="<?php the_permalink() ?>" method="post">
					<div class="uc-metabox">
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Username</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="uname" id="uname" class="uc-input input-large" value="<?php echo isset($_POST['uname']) ? $_POST['uname'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Password</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="password" name="upass" id="upass" class="uc-input input-large" value="<?php echo isset($_POST['upass']) ? $_POST['upass'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Confirm Password</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="password" name="upassconf" id="upassconf" class="uc-input input-large" value="<?php echo isset($_POST['upass']) ? $_POST['upass'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>First Name</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="ufirst" id="ufirst" class="uc-input input-large" value="<?php echo isset($_POST['ufirst']) ? $_POST['ufirst'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Last Name</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="ulast" id="ulast" class="uc-input input-large" value="<?php echo isset($_POST['ulast']) ? $_POST['ulast'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Email</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="email" name="uemail" id="uemail" class="uc-input input-large" value="<?php echo isset($_POST['uemail']) ? $_POST['uemail'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Birthday</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="date" name="ubd" id="ubd" class="uc-input input-large" value="<?php echo isset($_POST['ubd']) ? $_POST['ubd'] : '' ?>">
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label>Gender</label>
							</div>
							<div class="field">
								<div class="input">
									<label>
										<input class="uc-input" type="radio" name="ugen" id="ugenm" value="m" <?php checked( "m", $_POST['ugen'] ); ?>>
										Male
									</label>
									<label>
										<input class="uc-input" type="radio" name="ugen" id="ugenf" value="f" <?php checked( "f", $_POST['ugen'] ); ?>>
										Female
									</label>
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label>Are you proficient in STEM?</label>
							</div>
							<div class="field">
								<div class="input">
									<label>
										<input class="uc-input" type="radio" name="ustem" id="ustemy" value="1" <?php checked( "1", $_POST['ustem'] ); ?>>
										Yes
									</label>
									<label>
										<input class="uc-input" type="radio" name="ustem" d="ustemn" value="0" <?php checked( "0", $_POST['ustem'] ); ?>>
										No
									</label>
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label><?php echo vp_option('uc_option.us_stem_question'); ?></label>
							</div>
							<div class="field">
								<div class="input">
									<label>
										<input class="uc-input" type="radio" name="usci" id="usciy" value="1" <?php checked( "1", $_POST['usci'] ); ?>>
										Yes
									</label>
									<label>
										<input class="uc-input" type="radio" name="usci" id=="uscin" value="0" <?php checked( "0", $_POST['usci'] ); ?>>
										No
									</label>
								</div>
							</div>
						</div>
				<!--	<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label>Would you like to be a STEM Playground Ambassador?</label>
								<div class="uc-field-description">
								</div>
							</div>
							<div class="field">
								<div class="input">
									<label>
										<input class="uc-input" type="radio" name="uamb" id="uamby" value="1">
										Yes
									</label>
									<label>
										<input class="uc-input" type="radio" name="uamb" id="uambn" value="0">
										No
									</label>
								</div>
							</div>
						</div> -->
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label>Would you consider the majority of your students "at-risk"?</label>
							</div>
							<div class="field">
								<div class="input">
									<label><input class="uc-input" type="radio" name="uatrisk" id="uatrisk" value="1" <?php checked( "1", $_POST['uatrisk'] ); ?>>Yes</label>
									<label><input class="uc-input" type="radio" name="uatrisk" id="uatrisk" value="2" <?php checked( "2", $_POST['uatrisk'] ); ?>>No</label>
									<label><input class="uc-input" type="radio" name="uatrisk" id="uatrisk" value="0" <?php checked( "0", $_POST['uatrisk'] ); ?>>No opinion</label>
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label>Does your district participate in Middle School- or High School-level STEM competitions?</label>
							</div>
							<div class="field">
								<div class="input">
									<label><input class="uc-input" type="radio" name="ucomp" id="ucomp" value="1" <?php checked( "1", $_POST['ucomp'] ); ?>>Yes</label>
									<label><input class="uc-input" type="radio" name="ucomp" id="ucomp" value="2" <?php checked( "2", $_POST['ucomp'] ); ?>>No</label>
									<label><input class="uc-input" type="radio" name="ucomp" id="ucomp" value="0" <?php checked( "0", $_POST['ucomp'] ); ?>>I don't know</label>
								</div>
							</div>
						</div>
					<?php wp_nonce_field( 'update_user', 'update_user_noncename' ); ?>
					<input type="hidden" name="action" value="signupSubmit">
					<div class="alert-container"></div>
					<input type="submit" id="signup-submit" class="button" value="Sign Up">
				</form>

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
	jQuery('#signup-form').validate({
		rules: {
			uname: {
				required: true,
				minlength: 6
			},
			upass: {
				required: true,
				minlength: 6
			},
			upassconf: {
				required: true,
				minlength: 6,
				equalTo: "#upass"
			},
			ufirst: "required",
			ulast: "required",
			uemail: {
				required: true,
				email: true
			},
			ubd: {
				required: false,
			},
			ugen: {
				required: false,
			},
			ustem: {
				required: false,
			},
			usci: {
				required: false,
			},
			uamb: {
				required: false,

			},
		},
		messages: {
			uname: {
				required: "<?php echo vp_option('uc_option.us_uname_req'); ?>",
				minlength: "<?php echo vp_option('uc_option.us_uname_min'); ?>",
			},
			upass: {
				required: "<?php echo vp_option('uc_option.us_upass_req'); ?>",
				minlength: "<?php echo vp_option('uc_option.us_upass_min'); ?>",
			},
			upassconf: {
				required: "<?php echo vp_option('uc_option.us_upass_req'); ?>",
				minlength: "<?php echo vp_option('uc_option.us_upass_min'); ?>",
				equalTo: "<?php echo vp_option('uc_option.us_upass_equal'); ?>",
			},
			ufirst: "<?php echo vp_option('uc_option.us_ufirst_req'); ?>",
			ulast: "<?php echo vp_option('uc_option.us_ulast_req'); ?>",
			uemail: {
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
				display_alert( message, 'warning' );
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