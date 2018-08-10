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
									<input type="text" name="uname" id="uname" class="uc-input input-large" value="<?php echo isset($_POST['uname']) ? $_POST['uname'] : '' ?>" data-rule-required="true" data-rule-minlength="6">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Password</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="password" name="upass" id="upass" class="uc-input input-large" value="<?php echo isset($_POST['upass']) ? $_POST['upass'] : '' ?>" data-rule-required="true" data-rule-minlength="6">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Confirm Password</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="password" name="upassconf" id="upassconf" class="uc-input input-large" value="<?php echo isset($_POST['upass']) ? $_POST['upass'] : '' ?>" data-rule-required="true" data-rule-minlength="6" data-rule-equalto="#upass">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>First Name</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="first_name" id="first_name" class="uc-input input-large" value="<?php echo get_post_or_saved_postdata('first_name', ''); ?>" data-rule-required="true">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Last Name</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="last_name" id="last_name" class="uc-input input-large" value="<?php echo get_post_or_saved_postdata('last_name', ''); ?>" data-rule-required="true">
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
								<label>Birthday</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="date" name="birthdate" id="birthdate" class="uc-input input-large" value="<?php echo get_post_or_saved_postdata('birthdate', ''); ?>" data-rule-required="false">
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
										<input class="uc-input" type="radio" name="gender" id="m_gender" value="m" <?php checked( "m", get_post_or_saved_postdata('gender', '') ); ?> data-rule-required="false">
										Male
									</label>
									<label>
										<input class="uc-input" type="radio" name="gender" id="f_gender" value="f" <?php checked( "f", get_post_or_saved_postdata('gender', '') ); ?> data-rule-required="false">
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
										<input class="uc-input" type="radio" name="stem_proficient" id="y_stem_proficient" value="1" <?php checked( "1", get_post_or_saved_postdata('stem_proficient', '2') ); ?> data-rule-required="false">
										Yes
									</label>
									<label>
										<input class="uc-input" type="radio" name="stem_proficient" d="n_stem_proficient" value="0" <?php checked( "0", get_post_or_saved_postdata('stem_proficient', '2') ); ?> data-rule-required="false">
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
										<input class="uc-input" type="radio" name="college_science" id="n_college_science" value="1" <?php checked( "1", get_post_or_saved_postdata('college_science', '2') ); ?> data-rule-required="false">
										Yes
									</label>
									<label>
										<input class="uc-input" type="radio" name="college_science" id=="y_college_science" value="0" <?php checked( "0", get_post_or_saved_postdata('college_science', '2') ); ?> data-rule-required="false">
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
									<label><input class="uc-input" type="radio" name="school_at_risk" id="y_school_at_risk" value="1" <?php checked( "1", get_post_or_saved_postdata('school_at_risk', '0') ); ?> data-rule-required="false">Yes</label>
									<label><input class="uc-input" type="radio" name="school_at_risk" id="n_school_at_risk" value="2" <?php checked( "2", get_post_or_saved_postdata('school_at_risk', '0') ); ?> data-rule-required="false">No</label>
									<label><input class="uc-input" type="radio" name="school_at_risk" id="na_school_at_risk" value="0" <?php checked( "0", get_post_or_saved_postdata('school_at_risk', '0') ); ?> data-rule-required="false">No opinion</label>
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label>Does your district participate in Middle School- or High School-level STEM competitions?</label>
							</div>
							<div class="field">
								<div class="input">
									<label><input class="uc-input" type="radio" name="stem_competitions" id="y_stem_competitions" value="1" <?php checked( "1", get_post_or_saved_postdata('stem_competitions', '0') ); ?> data-rule-required="false">Yes</label>
									<label><input class="uc-input" type="radio" name="stem_competitions" id="n_stem_competitions" value="2" <?php checked( "2", get_post_or_saved_postdata('stem_competitions', '0') ); ?> data-rule-required="false">No</label>
									<label><input class="uc-input" type="radio" name="stem_competitions" id="na_stem_competitions" value="0" <?php checked( "0", get_post_or_saved_postdata('stem_competitions', '0') ); ?> data-rule-required="false">I don't know</label>
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label><?php echo vp_option('uc_option.convertkit_question'); ?></label>
							</div>
							<div class="field">
								<div class="input">
									<label><input class="uc-input" type="checkbox" name="mailing_list" id="mailing_list" value="1" <?php checked( "1", get_post_or_saved_postdata('mailing_list', '1') ); ?> data-rule-required="false">Yes</label>
								</div>
							</div>
						</div>
						<div class="uc-field uc-radio uc-meta-single" id="">
							<div class="label">
								<label><?php echo vp_option('uc_option.notify_question'); ?></label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="notify_question_1" id="notify_question_1" class="uc-input input-large" placeholder="ex. someone@stemplayground.org" value="<?php echo get_post_or_saved_postdata('notify_question_1', ''); ?>" data-rule-required="false">
								</div>
							</div>
						</div>
					<?php wp_nonce_field( 'update_user', 'update_user_noncename' ); ?>
					<input type="hidden" name="action" value="signupSubmit">
					<input type="hidden" name="notification_emails" value="1">
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
			first_name: "<?php echo vp_option('uc_option.us_ufirst_req'); ?>",
			last_name: "<?php echo vp_option('uc_option.us_ulast_req'); ?>",
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