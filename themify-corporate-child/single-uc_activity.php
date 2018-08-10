<?php
/**
 * Template for single activity view
 */
?>

<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

<!-- layout-container -->
<div id="layout" class="pagewidth clearfix uc-custom">

	<?php themify_content_before(); // hook ?>
	<!-- content -->
	<div id="content" class="list-post">
		<?php themify_content_start(); // hook ?>

		<?php 
		/** Themify Default Variables
		 *  @var object */
		global $themify;

		?>

		<?php themify_post_before(); // hook ?>

		<article itemscope itemtype="http://schema.org/Article" id="post-<?php the_id(); ?>" <?php post_class( 'post clearfix' ); ?>>
			
			<?php themify_post_start(); // hook ?>

			<div class="post-content">
				<?php 
				$banner_image = get_post_meta( get_the_ID(), 'activity_banner', true );
				if (!empty($banner_image) && ( strpos($banner_image, '.jpg') !== false || strpos($banner_image, '.png') !== false || strpos($banner_image, '.gif') !== false ) ) : ?>
				<div class="activity-banner" style="background-image: url(<?php echo $banner_image; ?>);"></div>
				<?php endif; ?>
				<h1 class="activity-title" itemprop="headline"><?php the_title(); ?></h1>
				<?php if ( is_user_logged_in() ) : ?>
				<div class="uc-field uc-dropdown activity-class">
					<?php 
						$user_id = get_current_user_id();
						$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
						$current_school_year = get_current_school_year();
						$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );
						
						if ( isset($_SESSION['activeClass']) ) {
							$active_class = $_SESSION['activeClass'];
						} else {
							$active_class = $uc_classes[0]['value'];
							$_SESSION['activeClass'] = $active_class;
						}
						$signup_status = signup_status( $active_class, get_the_id() );
					?>
					<form method="post">
						<input type="hidden" id="action" name="action" value="activeClass">
						<?php wp_nonce_field( 'active_class', 'active_class_noncename' ); ?>
						<div class="uc-field uc-dropdown activity-class">
							<div class="label">
								<label><strong>Active Class</strong></label>
							</div>
							<div class="field">
								<select name="activeClass" class="uc-input select activity-class-select" onchange="this.form.submit()">
									<?php foreach ( $uc_classes as $uc_class )
										echo '<option value="'. $uc_class['value'] .'" '. selected( $active_class, $uc_class['value'], false) .'>'. $uc_class['label'] .'</option>';
									?>
								</select>
							</div>
						</div>
					</form>
					<?php include(locate_template('template-parts/sponsorship-banner.php') ); ?>
				</div>
				<?php endif; ?>
				<?php 
				if ( !is_user_logged_in() )
					include(locate_template('template-parts/activity-page-not-logged-in.php') );
				elseif ( $signup_status == 'active' )
					include(locate_template('template-parts/activity-page-active.php') );
				elseif ( $signup_status == 'completed' )
					include(locate_template('template-parts/activity-page-completed.php') );
				elseif ( $signup_status == 'expired' )
					include(locate_template('template-parts/activity-page-expired.php') );
				elseif ( is_class_at_maximum_signups( $active_class ) )
					include(locate_template('template-parts/activity-page-maximum.php') );
				elseif ( !$signup_status )
					include(locate_template('template-parts/activity-page-available.php') );
				?>

				<?php if ( is_user_logged_in() ) : ?>
				<br><a class="button" href="<?php echo get_permalink_from_template('page-activity-board.php'); ?>"><button><?php echo vp_option('uc_option.a_activity_board_text'); ?></button></a>

				<p><?php echo str_replace('!email!', '<a href="mailto:'.vp_option('uc_option.a_activity_page_problems_email').'">'.vp_option('uc_option.a_activity_page_problems_email').'</a>', vp_option('uc_option.a_activity_page_problems_text')); ?></p>
				<?php endif; ?>
			</div>
			<!-- /.post-content -->
			<?php themify_post_end(); // hook ?>
			
		</article>
		<!-- /.post -->

		<?php themify_post_after(); // hook ?>
				
		<?php themify_content_end(); // hook ?>	
	</div>
	<!-- /content -->
	<?php themify_content_after(); // hook ?>

<?php endwhile; ?>

<?php 
/////////////////////////////////////////////
// Sidebar							
/////////////////////////////////////////////
if ($themify->layout != 'sidebar-none'): get_sidebar(); endif; ?>

</div>
<!-- /layout-container -->
	
<?php get_footer(); ?>