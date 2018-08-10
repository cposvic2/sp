<?php
/**
 * Template Name: School Template
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
				<img src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/TheSchoolIcon.png">

				<?php the_content(); ?>

				<?php
				$user_id = get_current_user_id();
				$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
				$school_id = get_post_meta( $teacher_id, 'school', true );

				// Errors
				$uc_errors = array();
				$uc_errors['sc_verify_errors'] = vp_option('uc_option.sc_verify_errors');
				$uc_errors['sc_verify_error'] = vp_option('uc_option.sc_verify_error');

				$uc_errors['sc_sctitle_req'] = vp_option('uc_option.sc_sctitle_req');
				$uc_errors['sc_scaddress1_req'] = vp_option('uc_option.sc_scaddress1_req');
				$uc_errors['sc_sccity_req'] = vp_option('uc_option.sc_sccity_req');
				$uc_errors['sc_sccounty_req'] = vp_option('uc_option.sc_sccounty_req');
				$uc_errors['sc_scstate_req'] = vp_option('uc_option.sc_scstate_req');
				$uc_errors['sc_sczip_req'] = vp_option('uc_option.sc_sczip_req');
				$uc_errors['sc_sczip_min_max'] = vp_option('uc_option.sc_sczip_min_max');
				$uc_errors['sc_sccountry_req'] = vp_option('uc_option.sc_sccountry_req');

				echo '<script type="text/javascript">';
					foreach ( $uc_errors as $key => $uc_error ) {
						echo 'var ' . $key . '="' . $uc_error . '";';
					}
				echo '</script>';
				// end Errors

				if ( $_GET['r'] == 'e' ) {
					$sc_submit_error = vp_option('uc_option.sc_submit_error');
					echo '<p>'.$sc_submit_error.'</p>';
				}

				if ( !$school_id ) {
					$sc_what_country = vp_option('uc_option.sc_what_country');
					?>
					<div class="school-search-container">
						<div class="uc-field uc-dropdown uc-meta-single school-country-select" id="">
							<div class="label"><label><?php echo $sc_what_country; ?></label></div>
							<div class="field">
								<div class="input">
									<select name="sccountry" id="sccountry" class="uc-input select">
										<option value="" disabled selected></option>
										<?php 
										$countries = get_countries();
										foreach ( $countries as $country ) {
											echo '<option value="'. $country['value'] .'">'. $country['label'] .'</option>';
										} ?>
									</select>
								</div>
							</div>
						</div>

						<?php 
						$sc_search_intro = vp_option('uc_option.sc_search_intro');
						?>
						<div class="school-search">
							<div class="uc-field uc-dropdown uc-meta-single school-country-select" id="">
								<div class="label"><label><?php echo $sc_search_intro; ?></label></div>
								<div class="field">
									<div class="input">
										<input type="text" id="school-search-zip" class="uc-input input-large button-right" value="" placeholder="Search...">
									</div>
								</div>
							</div>
							<input type="submit" id="school-search-submit" class="button" value="Search">
							<div class="school-search-results-container">
								<form id="school-search-results-form" action="<?php the_permalink() ?>" method="post">
									<div class="school-search-results uc-radio">
									</div>
									<div class="school-search-save">
										<input type="hidden" name="action" value="saveSchool">
										<?php wp_nonce_field( 'save_school', 'save_school_noncename' ); ?>
										<div class="alert-container"></div>
										<input type="submit" id="school-search-accept" class="button" value="Save School">
										<a href="#" class="school-not-listed">My school is not listed</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php }

				$school_name = ( $school_id ? get_the_title ( $school_id ) : '' );
				$school_non_traditional = ( get_post_meta( $school_id, 'non_traditional', true ) ? 'Yes' : 'No' );
				$school_street_address = ( get_post_meta( $school_id, 'streetaddress', true ) ? get_post_meta( $school_id, 'streetaddress', true ) : '' );
				$school_street_address2 = ( get_post_meta( $school_id, 'streetaddress2', true ) ? get_post_meta( $school_id, 'streetaddress2', true ) : '' );
				$school_city = ( get_post_meta( $school_id, 'city', true ) ? get_post_meta( $school_id, 'city', true ) : '' ); 
				$school_county = ( get_post_meta( $school_id, 'county', true ) ? get_post_meta( $school_id, 'county', true ) : '' );
				$school_state = ( get_post_meta( $school_id, 'state', true ) ? get_post_meta( $school_id, 'state', true ) : '' );
				$school_zip = ( get_post_meta( $school_id, 'zip', true ) ? get_post_meta( $school_id, 'zip', true ) : '' );
				$school_country = ( get_post_meta( $school_id, 'country', true ) ? get_post_meta( $school_id, 'country', true ) : '' );

				?>
				<div class="school-info<?php echo ( $school_id ? '' : ' hidden'); ?>">
					<form id="school-form" action="<?php the_permalink() ?>" method="post">
						<div class="uc-metabox">
							<div class="uc-field uc-textbox uc-meta-single" id="">
								<div class="label">
									<label>School Name</label>
								</div>
								<?php if ( !$school_id ) { ?>
								<div class="field"><div class="input">
										<input type="text" name="sctitle" id="sctitle" class="uc-input input-large" value="">
								</div></div>
								<?php } else { ?> <div><div class="label"><?php echo $school_name ?></div></div> <?php } ?>
							</div>
							<div class="uc-field uc-radio uc-meta-single" id="">
								<div class="label">
									<label><?php echo vp_option('uc_option.sc_non_trad_q'); ?></label>
								</div>
								<?php if ( !$school_id ) { ?>
								<div class="field">
									<div class="input">
										<label><input class="uc-input" type="radio" name="scnontrad" id="scnontradyes" value="1">Yes</label>
										<label><input class="uc-input" type="radio" name="scnontrad" id="scnontradno" value="0" checked>No</label>
									</div>
								</div>
								<?php } else { ?> <div><div class="label"><?php echo $school_non_traditional ?></div></div> <?php } ?>
							</div>
							<div class="uc-field uc-textbox uc-meta-single" id="">
								<div class="label">
									<label>Street Address</label>
								</div>
								<?php if ( !$school_id ) { ?>
								<div class="field"><div class="input">
										<input type="text" name="scaddress1" id="scaddress1" class="uc-input input-large" value="">
								</div></div>
								<?php } else { ?> <div><div class="label"><?php echo $school_street_address ?></div></div> <?php } ?>
							</div>
							<div class="uc-field uc-textbox uc-meta-single" id="">
								<div class="label">
									<label>Street Address 2</label>
								</div>
								<?php if ( !$school_id ) { ?>
								<div class="field"><div class="input">
										<input type="text" name="scaddress2" id="scaddress2" class="uc-input input-large" value="">
								</div></div>
								<?php } else { ?> <div><div class="label"><?php echo $school_street_address2 ?></div></div> <?php } ?>
							</div>
							<div class="uc-field uc-textbox uc-meta-single" id="">
								<div class="label">
									<label>City</label>
								</div>
								<?php if ( !$school_id ) { ?>
								<div class="field"><div class="input">
										<input type="text" name="sccity" id="sccity" class="uc-input input-large" value="">
								</div></div>
								<?php } else { ?> <div><div class="label"><?php echo $school_city ?></div></div> <?php } ?>
							</div>
							<div class="uc-field uc-textbox uc-meta-single" id="">
								<div class="label">
									<label>County</label>
								</div>
								<?php if ( !$school_id ) { ?>
								<div class="field"><div class="input">
										<input type="text" name="sccounty" id="sccounty" class="uc-input input-large" value="">
								</div></div>
								<?php } else { ?> <div><div class="label"><?php echo $school_county ?></div></div> <?php } ?>
							</div>
							<div class="state-placeholder">


								<?php if ( $school_id && $school_state ) { ?>
									<div class="uc-field uc-dropdown uc-meta-single" id="">
										<div class="label">
											<label>State</label>
										</div>
										<div><div class="label"><?php echo $school_state ?></div></div>
									</div>
								<?php } ?>
							</div>
							<?php if ( $school_id ) { ?>
							<div class="uc-field uc-textbox uc-meta-single" id="">
								<div class="label">
									<label>Zip/Mailing Code</label>
								</div>
								<div><div class="label"><?php echo $school_zip ?></div></div>
							</div>
							<?php } ?>
							<?php if ( $school_id ) { ?>
							<div class="uc-field uc-dropdown uc-meta-single" id="">
								<div class="label">
									<label>Country</label>
								</div>
								<div><div class="label"><?php echo $countries[$school_country] ?></div></div>
							</div>
							<?php } ?>
						</div>
						<?php wp_nonce_field( 'update_school', 'update_school_noncename' ); ?>
						<input type="hidden" name="sccountry2" id="sccountry2" value="">
						<input type="hidden" name="action" value="updateSchool">
						<div class="alert-container"></div>
						<?php if ( !$school_id ) { ?>
						<input type="submit" id="school_submit" class="button" value="Submit School Info">
						<?php } ?>
					</form>
				</div>

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
	jQuery('#school-form').validate({
		rules: {
			sctitle: {
				required: true,
			},
			scnontrad: {
				required: true,
			},
			scaddress1: {
				required: true,
			},
			scaddress2: {
				required: false,
			},
			sccity: {
				required: true,
			},
			sccounty: {
				required: true,
			},
			scstate: {
				required: true,
			},
			sccountry: {
				required: true,
			},
		},
		messages: {
			sctitle: sc_sctitle_req,
			scaddress1: sc_scaddress1_req,
			sccity: sc_sccity_req,
			sccounty: sc_sccounty_req,
			scstate: sc_scstate_req,
			sccountry: sc_sccountry_req,
		},
		invalidHandler: function(event, validator) {
			var errors = validator.numberOfInvalids();
			if ( errors ) {
				var message = errors == 1
				? sc_verify_error
				: sc_verify_errors.replace('!fields!', errors );
				display_alert( message, 'warning', jQuery('#school-form') );
			}
		},

		wrapper: 'div',
		errorClass: "alert alert-warning",
		errorPlacement: function(error, element) {
				error.appendTo(element.closest('.input')).hide().slideDown('normal');
		}
	});
</script>
<body><p style="padding-left: 400px; padding-right: 400px;"><span style="color: #ff0000;"><strong>Note: </strong><span style="color: #000000;">Some users have reported that they select their country, but see no options beyond that.  If this is the case for you, please visit <a href="https://www.whatismybrowser.com/guides/how-to-enable-javascript/">this link</a> to learn how to enable javascript in your browser.  If you are unable to do that, please e-mail <a href="mailto:steve@stemplayground.org">steve@stemplayground.org</a></p></body>
<?php get_footer(); ?>