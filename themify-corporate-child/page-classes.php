<?php
/**
 * Template Name: Classes Template
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
				<img src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/The-Class-Icon.png">

				<?php the_content(); ?>

				<?php
				$user_id = get_current_user_id();
				$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
				$current_school_year = get_current_school_year();
				$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );

				// Errors
				$uc_errors = array();
				$uc_errors['class_no_name_error'] = vp_option('uc_option.class_no_name_error');
				$uc_errors['class_verify_various'] = vp_option('uc_option.class_verify_various');
				$uc_errors['remove_warning'] = vp_option('uc_option.class_remove_warning');

				echo '<script type="text/javascript">';
					foreach ( $uc_errors as $key => $uc_error ) {
						echo 'var ' . $key . '="' . $uc_error . '";';
					}
				echo '</script>';
				// end Errors

				?>
				<div class="uc-table-container">
					<table class="uc-table" id="class-table">
						<thead>
							<tr>
								<th class="textleft">Class Name</th>
								<th>Grade</th>
								<th>Edit</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $uc_classes as $uc_class ) {
								$uc_class_post = get_post( $uc_class['value'] );
								$uc_class_name =  $uc_class_post->post_title;
								$uc_class_grade = ( get_post_meta( $uc_class['value'], 'grade', true ) ? get_post_meta( $uc_class['value'], 'grade', true ) : 'Various' );
								echo '
								<tr data-ref="'. $uc_class['value'] .'">
									<td class="textleft"><span class="classname">'.$uc_class_name.'</span></td>
									<td><span class="grade">'.$uc_class_grade.'</span></td>
									<td><input type="submit" class="class-edit" value="Edit Row"></td>
								</tr>
								';
							} ?>
							
						</tbody>
					</table>
				</div>
				<div class="class-new-container">
					<h3 class="form-title">Add New Class</h3>
					<div class="uc-metabox">
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Class Name</label>
							</div>
							<div class="field">
								<div class="input">
									<input type="text" name="uc-class-title-new" id="uc-class-title-new" class="uc-input input-large" value="">
								</div>
							</div>
						</div>
						<div class="uc-field uc-textbox uc-meta-single" id="">
							<div class="label">
								<label>Grade</label>
							</div>
								<div class="field">
									<div class="input">
										<select name="uc-class-grade-new" id="uc-class-grade-new" class="uc-input select">
										<option value="">Various</option>
										<?php 
										$grades = get_uc_grades();
										foreach ( $grades as $grade ) {
											echo '<option value="'. $grade['value'] .'">'. $grade['label'] .'</option>';
										}
										?>
										</select>
									</div>
								</div>
						</div>
					</div>
					<?php wp_nonce_field( 'update_classes', 'update_classes_noncename' ); ?>
					<?php wp_nonce_field( 'remove_post', 'remove_post_noncename' ); ?>
					<div class="alert-container"></div>
					<input type="submit" id="class-add" class="button" value="Add Class" tabindex="18">
					<div class="proceed-container"></div>
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
var grades = <?php echo json_encode($grades); ?>;

</script>
<?php get_footer(); ?>