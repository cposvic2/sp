<?php
/**
 * Template Name: Activity Board Template
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
				<?php the_content();
				$user_id = get_current_user_id();
				$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
				$current_school_year = get_current_school_year();
				$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );
				$current_league_challenge = get_current_uc_league_challenge();			

				if(isset($_SESSION['activeClass'])) {
					$active_class = $_SESSION['activeClass'];
				} else {
					$active_class = $uc_classes[0]['value'];
					$_SESSION['activeClass'] = $active_class;
				}
				if ( $current_league_challenge ) {
					$current_league_challenge_statuses = get_uc_challenge_activities_status( $active_class, $current_league_challenge['value'] );
					$league_challenge_activities = get_post_meta( $current_league_challenge['value'], 'activities', true );

					$ch_post_id = vp_option('uc_option.ch_post_id');
					if ( $current_league_challenge_statuses['eligible'] === false )
						$ch_act_brd_text = vp_option('uc_option.ch_act_brd_x_text');
					elseif ( $current_league_challenge_statuses['uncompleted'] == 0 )
						$ch_act_brd_text = vp_option('uc_option.ch_act_brd_zero_text');
					elseif ( $current_league_challenge_statuses['uncompleted'] == 1 )
						$ch_act_brd_text = vp_option('uc_option.ch_act_brd_single_text');
					else
						$ch_act_brd_text = str_replace('!activities!', $current_league_challenge_statuses['uncompleted'], vp_option('uc_option.ch_act_brd_text'));

					$ch_act_brd_link = vp_option('uc_option.ch_act_brd_link');
					echo '<div class="activity-board-league-challenge-header"><h3>'.$ch_act_brd_text . '</h3> (<a href="'. get_permalink ( $ch_post_id ) .'" >'. $ch_act_brd_link .'</a>)</div>';

					if ( can_display_awards( $active_class ) ) {
						$ch_view_results_text = vp_option('uc_option.ch_view_results_text');

						$pages = get_pages( array(
							'meta_key' => '_wp_page_template',
							'meta_value' => 'page-league-challenge.php'
						));
						if ( $pages )
							$ch_view_results_url = get_permalink( $pages[0] );
						else
							$ch_view_results_url = home_url();

						echo '<div class="activity-board-league-challenge-results-header"><a href="'. $ch_view_results_url .'" >'. $ch_view_results_text .'</a></div>';
					}
				}
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
				<?php 
				$activities = get_available_uc_activities( $active_class ); ?>
				<h3>Available Activities</h3>
				<div class="activities-container">
					<?php
					if ( is_class_at_maximum_signups( $active_class ) ) {
						$max_signups = vp_option('uc_option.signup_maximum');
						$activity_text = str_replace ( '!signups!' , $max_signups, vp_option('uc_option.a_a_board_max_signup') );
						echo '<p>'.$activity_text.'</p>';
					}
					foreach ($activities as $activity) {
						display_activity_card( $activity['value'], isset($league_challenge_activities) && in_array( $activity['value'], $league_challenge_activities ) );
					}
					?>
				</div>
				<?php $activities = get_active_uc_activities( $active_class ); ?>
				<h3>Active Activities</h3>
				<div class="activities-container">
					<?php
					foreach ($activities as $activity) {
						display_activity_card( $activity['value'], isset($league_challenge_activities) && in_array( $activity['value'], $league_challenge_activities ) );
					}
					?>
				</div>
				<?php $activities = get_completed_uc_activities( $active_class ); ?>
				<h3>Completed Activities</h3>
				<div class="activities-container">
					<?php
					foreach ($activities as $activity) {
						display_activity_card( $activity['value'], isset($league_challenge_activities) && in_array( $activity['value'], $league_challenge_activities ) );
					}
					?>
				</div>
				<?php $activities = get_expired_uc_activities( $active_class ); ?>
				<h3>Expired Activities</h3>
				<div class="activities-container">
					<?php
					foreach ($activities as $activity) {
						display_activity_card( $activity['value'], isset($league_challenge_activities) && in_array( $activity['value'], $league_challenge_activities ) );
					}
					?>
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
	
<?php get_footer(); ?>