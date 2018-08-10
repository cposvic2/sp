<?php
/**
 * Template Name: Assign Groups Template
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
				<img src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/The-Team-Icon.png">

				<?php the_content(); ?>

				<?php
				$user_id = get_current_user_id();
				$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
				$current_school_year = get_current_school_year();
				$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );

				$default_group_text = vp_option('uc_option.default_group_text');

				// Errors
				$uc_errors = array();
				$uc_errors['group_missing_student_error'] = vp_option('uc_option.group_missing_student_error');

				echo '<script type="text/javascript">';
					foreach ( $uc_errors as $key => $uc_error ) {
						echo 'var ' . $key . '="' . $uc_error . '";';
					}
				echo '</script>';
				// end Errors
				
				foreach ( $uc_classes as $uc_class ) {
					$class_id = $uc_class['value'];
					$class_name = $uc_class['label'];

				 ?>
					<div class="uc-collapsible collapsed" id="class-<?php echo $class_id ?>"><?php echo $class_name ?><span></span></div>
					<div class="uc-collapsible-content collapsed">
						<p><?php echo $default_group_text ?></p>
						<div class="group-modify-container" data-ref="<?php echo $class_id ?>">
							<?php display_groups( $class_id ); ?>
							<div class="alert-container"></div>
							<input type="submit" class="group-save button" value="Save Teams">
						</div>
					</div>
				<?php
				} ?>
				<?php wp_nonce_field( 'update_groups', 'update_groups_noncename' ); ?>

				<?php wp_link_pages(array('before' => '<p class="post-pagination"><strong>'.__('Pages:','themify').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

				<?php edit_post_link(__('Edit','themify'), '[', ']'); ?>

				<!-- comments -->
				<?php if(!themify_check('setting-comments_pages') && $themify->query_category == ""): ?>
					<?php comments_template(); ?>
				<?php endif; ?>
				<!-- /comments -->

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
	
<?php get_footer(); ?>