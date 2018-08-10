<?php
/**
 * Template Name: Students Template
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
				<img src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/The-Student-Icon.png">

				<?php the_content(); ?>

				<?php
				$user_id = get_current_user_id();
				$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
				$current_school_year = get_current_school_year();
				$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );
				$genders = get_genders();
				$yesnos = get_yesno();

				// Errors
				$uc_errors = array();
				$uc_errors['st_verify_errors'] = vp_option('uc_option.st_verify_errors');
				$uc_errors['st_verify_error'] = vp_option('uc_option.st_verify_error');
				$uc_errors['remove_warning'] = vp_option('uc_option.st_remove_warning');
				$uc_errors['st_min_banner'] = vp_option('uc_option.st_min_banner');
				$group_size = vp_option('uc_option.group_size');

				echo '<script type="text/javascript">';
					foreach ( $uc_errors as $key => $uc_error ) {
						echo 'var ' . $key . '="' . $uc_error . '";';
					}
				echo '</script>';
				// end Errors
				
				foreach ( $uc_classes as $uc_class ) {
					$uc_students = get_uc_students_by_class( $uc_class['value'], 'publish', $current_school_year );
					$uc_class_grade = ( get_post_meta( $uc_class['value'], 'grade', true ) ? get_post_meta( $uc_class['value'], 'grade', true ) : '' );

				 ?>
					<div class="uc-collapsible collapsed" id="class-<?php echo $uc_class['value'] ?>"><?php echo $uc_class['label'] ?><span></span></div>
					<div class="uc-collapsible-content collapsed" data-ref="<?php echo $uc_class['value'] ?>">
						<div class="student-alert-container" style="display: block;">
							<?php if (count($uc_students) < $group_size) { ?>
								<div class="alert alert-caution"><?php echo $uc_errors['st_min_banner']; ?></div><?php
							} ?>
						</div>
						<div class="uc-table-container">
							<table class="uc-table student-table" id="student-table-<?php echo $uc_class['value'] ?>">
								<thead>
									<tr>
										<th class="textleft">Student Name</th>
										<th>Grade</th>
										<th>Gender</th>
										<th>ESL</th>
										<th>Edit</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach( $uc_students as $uc_student ) {
										$uc_student_id = $uc_student['value'];
										$uc_student_firstname = get_post_meta( $uc_student_id, 'firstname', true );
										$uc_student_lastinitial = get_post_meta( $uc_student_id, 'lastinitial', true );

										$uc_student_grade = ( get_post_meta( $uc_student_id, 'grade', true ) ? get_post_meta( $uc_student_id, 'grade', true ) : '' );

										$uc_student_class = ( get_post_meta( $uc_student_id, 'class', true ) ? get_post_meta( $uc_student_id, 'class', true ) : '' );
										$uc_class_post = get_post( $uc_student_class );

										$uc_student_gender = '';
										$saved_gender = get_post_meta( $uc_student_id, 'gender', true );
										if ( $saved_gender ) {
											foreach ( $genders as $gender ) {
												if ( $gender['value'] == $saved_gender )
													$uc_student_gender = $gender['label'];
											}
										}

										$uc_student_ell = '';
										$saved_ell = get_post_meta( $uc_student_id, 'ell', true );
										if ( $saved_ell ) {
											foreach ( $yesnos as $yesno ) {
												if ( $yesno['value'] == $saved_ell )
													$uc_student_ell = $yesno['label'];
											}
										}
										
										echo '
										<tr data-ref="'. $uc_student_id .'">
											<td class="textleft"><span class="firstname">'.$uc_student_firstname.'</span> <span class="lastinitial">'.$uc_student_lastinitial.'</span></td>
											<td><span class="grade">'.$uc_student_grade.'</span></td>
											<td><span class="gender">'.$uc_student_gender.'</span></td>
											<td><span class="ell">'.$uc_student_ell.'</span></td>
											<td><input type="submit" class="student-edit" value="Edit Row"></td>
										</tr>
										';
									} ?>
									
								</tbody>
							</table>
						</div>
						<p class="form-title">Add new student for <?php echo $uc_class['label'] ?></h3>
						<form class="student-add-form" id="student-add-form-<?php echo $uc_class['value'] ?>" method="get" action >
							<div class="uc-metabox">
								<div class="uc-field uc-textbox uc-meta-single">
									<div class="label">
										<label>First Name</label>
									</div>
									<div class="field">
										<div class="input">
											<input type="text" name="stfirstname" class="uc-input input-large uc-student-firstname-new" value="">
										</div>
									</div>
								</div>
								<div class="uc-field uc-textbox uc-meta-single">
									<div class="label">
										<label>Last Initial</label>
									</div>
									<div class="field">
										<div class="input">
											<input type="text" name="stlastinitial" class="uc-input input-large uc-student-lastinitial-new" value="" maxlength="1">
										</div>
									</div>
								</div>
								<div class="uc-field uc-textbox uc-meta-single">
									<div class="label">
										<label>Grade</label>
									</div>
										<div class="field">
											<div class="input">
												<select name="stgrade" class="uc-input select uc-student-grade-new">
												<?php 
												$grades = get_uc_grades();
												foreach ( $grades as $grade ) {
													echo '<option value="'. $grade['value'] .'" '.selected( $grade['value'], $uc_class_grade, false).'>'. $grade['label'] .'</option>';
												}
												?>
												</select>
											</div>
										</div>
								</div>
								<div class="uc-field uc-radio uc-meta-single">
									<div class="label">
										<label>Gender</label>
									</div>
									<div class="field">
										<div class="input">
											<?php 
											foreach ( $genders as $gender ) { ?>
												<label>
													<input class="uc-input" type="radio" name="stgender" value="<?php echo $gender['value'] ?>">
													<?php echo $gender['label'] ?>
												</label>
											<?php } ?>


										</div>
									</div>
								</div>
								<div class="uc-field uc-radio uc-meta-single">
									<div class="label">
										<label><?php echo vp_option('uc_option.st_ell_question'); ?></label>
									</div>
									<div class="field">
										<div class="input">
											<?php 
											foreach ( $yesnos as $yesno ) { ?>
												<label>
													<input class="uc-input" type="radio" name="stell" value="<?php echo $yesno['value'] ?>">
													<?php echo $yesno['label'] ?>
												</label>
											<?php } ?>
											<label>
												<input class="uc-input" type="radio" name="stell" value="">
												No answer
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="alert-container"></div>
							<input type="hidden" name="action" value="update_student">
							<input type="hidden" name="stclass" value="<?php echo $uc_class['value'] ?>">
							<input type="submit" class="button student-add" value="Add Student">
						</form>
					</div>

				<?php
				} ?>

				<?php wp_nonce_field( 'update_students', 'update_students_noncename' ); ?>
				<?php wp_nonce_field( 'remove_post', 'remove_post_noncename' ); ?>

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
var grades = <?php echo json_encode($grades); ?>;
var genders = <?php echo json_encode($genders); ?>;
var yesnos = <?php echo json_encode($yesnos); ?>;

jQuery( document ).ready(function() { 
	jQuery.validator.setDefaults({
		rules: {
			stfirstname: { required: true },
			stlastinitial: { required: true, minlength: 1, maxlength: 1 },
			stgrade: { required: true, },
			stgender: { required: true, },
			stell: { required: true, },
		},
		messages: {
			stfirstname: "<?php echo vp_option('uc_option.st_stfirstname_req') ?>",
			stlastinitial: {
				required: "<?php echo vp_option('uc_option.st_stlastinitial_req') ?>",
				minlength: "<?php echo vp_option('uc_option.sc_stlastinitial_min_max') ?>",
				maxlength: "<?php echo vp_option('uc_option.sc_stlastinitial_min_max') ?>",
			},
			stgrade: "<?php echo vp_option('uc_option.st_stgrade_req') ?>",
			stgender: "<?php echo vp_option('uc_option.st_stgender_req') ?>",
			stell: "<?php echo vp_option('uc_option.st_stell_req') ?>",
		},
		wrapper: 'div',
		errorClass: "alert alert-warning",
		errorPlacement: function(error, element) {
				error.appendTo(element.closest('.input')).hide().slideDown('normal');
		}
	});

	jQuery('.student-add-form').each(function() {
		var thismetabox = jQuery(this).closest('.uc-collapsible-content');
		var thisform = jQuery(this).parent('.uc-collapsible-content');
		jQuery(this).validate({
			invalidHandler: function(event, validator) {
				var errors = validator.numberOfInvalids();
				if ( errors ) {
					var message = errors == 1
					? st_verify_error
					: st_verify_errors.replace('!fields!', errors );
					display_alert( message, 'warning', thisform );
				}
			},
			submitHandler: function( form ) {
				var wpnonce = jQuery('#update_students_noncename').val();

				var data = jQuery(form).serialize() + '&_wpnonce=' + wpnonce;

				jQuery.post(ajaxurl, data, function(response) {
					var parsed_response = jQuery.parseJSON( response );

					if ( parsed_response['status'] == 'OK' ) {

						if ( parsed_response['meta']['ell'] == null )
							ellDisplay = '';
						else
							ellDisplay = parsed_response['meta']['ell'];

						var newfirstname = parsed_response['meta']['firstname'].replace(/\\/g, "");
						var newlastinitial = parsed_response['meta']['lastinitial'].replace(/\\/g, "");

						var newstudentrow = jQuery('<tr class="new-row" data-ref="'+parsed_response['new']+'"><td class="textleft"><span class="firstname">'+newfirstname+'</span> <span class="lastinitial">'+newlastinitial+'</span></td><td><span class="grade">'+parsed_response['meta']['grade']+'</span></td><td><span class="gender">'+parsed_response['meta']['gender']+'</span></td><td><span class="ell">'+ellDisplay+'</span></td><td><input type="submit" class="student-remove" value="Edit Row"></td></tr>');
						var tablebody = jQuery(thisform).find('.student-table tbody');
						jQuery(newstudentrow).find('.student-remove').bind("click", edit_student_post);
						jQuery(newstudentrow)
							.prependTo( tablebody )
							.find('td')
							.animate({ paddingTop: 8, paddingBottom: 8 } )
							.wrapInner('<div style="display: none;" />')
							.parent()
							.find('td > div')
							.slideDown(400, function(){
								var $set = jQuery(this);
								$set.replaceWith($set.contents());
						});

						jQuery(thisform).find('.uc-student-firstname-new').val('');
						jQuery(thisform).find('.uc-student-lastinitial-new').val('');
						jQuery(thisform).find('input:radio[name=stgender]').prop('checked', false);
						jQuery(thisform).find('input:radio[name=stell]:checked').prop('checked', false);
						display_alert( 'Student added', 'success', thisform );
						console.log(parsed_response);
						if (parsed_response['above_min'])
							jQuery('.student-alert-container').html('');
						else
							display_alert( st_min_banner, 'caution', thisform, 'student-alert-container' );

						if ( parsed_response.hasOwnProperty('proceed') ) {
							jQuery(thisform).find('.student-add-form').after('<div class="proceed-container"><a href="'+parsed_response['proceed']['url']+'" ><button>'+parsed_response['proceed']['text']+'</button></a></div>');
						}

					} else if ( parsed_response['status'] == 'REQUEST_DENIED' ) {
						display_alert( 'Error: ' + parsed_response['reason'], 'caution', thisform );
					} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
						display_alert( 'Error: Invalid request', 'caution', thisform );
					} else {
						display_alert( 'Error: Unknown error', 'caution', thisform );
					}
				});
			},
		});
	});
});

</script>
<?php get_footer(); ?>