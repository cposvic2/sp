<?php
/**
 * Template Name: League Challenge Template
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
				$current_league_challenge = get_current_uc_league_challenge();

				if(isset($_SESSION['activeClass'])) {
					$active_class = $_SESSION['activeClass'];
				} else {
					$active_class = $uc_classes[0]['value'];
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
				$school_id = get_post_meta( $teacher_id, 'school', true );
				$class_grade = get_post_meta( $active_class, 'grade', true );
				$class_grade_title = ( get_term_by('slug', $class_grade, 'uc_grade') ? get_term_by('slug', $class_grade, 'uc_grade')->name : 'Multi-grade' );
				$current_school_year = get_current_school_year();
				$league_challenge_results = get_league_challenge_results( $active_class, $current_league_challenge['value'], $class_grade, 'publish' );

				// Awards
				$display_awards_section = false;
				$display_awards_content = '';
				foreach($league_challenge_results as $geographic_type => $league_challenge_geographic) {
					foreach( $league_challenge_geographic as $demographic_type => $league_challenge_result ) {
						$these_awards = get_league_challenge_awards( $league_challenge_result, $geographic_type, $demographic_type, $active_class );
						if ( count($these_awards) ) {
							$display_awards_section = true;
							$display_awards_content .= '<strong>'.ucfirst($geographic_type).' Awards</strong><br>';
							$display_awards_content .= '<div class="activity-awards-container">';
							foreach ( $these_awards as $class_award ) {
								$display_awards_content .= '<div class="award-container"><div class="award league-winner '.$class_award['award_type'].' '.$class_award['demographic_type'].'">'.$inner_text.'</div></div>';
							}
							$display_awards_content .= '<div class="clear"></div></div>';
						}
					}
				}
				if ( $display_awards_section ) {
					echo '<h3>League Challenge Awards</h3>'.$display_awards_content;

				}

				?>
				<h3>League Challenge Results</h3>
				<?php
					foreach($league_challenge_results as $key => $league_challenge_geographic) {
						$league_challenge_result = $league_challenge_geographic['standard'];

						
						echo '<div class="league-challenge-result-container"><strong>'.ucfirst($key).' Results</strong><br>';
						$results_display = '<ul>';

						$i = 0;
						while ( $i < count($league_challenge_result) ) {
							if( $league_challenge_result[$i]['id'] == $active_class )
								$class_rank = $i + 1;
							if( $i < 10 ) {
								$this_class_id = $league_challenge_result[$i]['id'];
								$this_teacher_id = get_post_meta( $this_class_id, 'teacher', true );
								$this_school_id = get_post_meta( $this_teacher_id, 'school', true );
								$this_school_country_value = get_post_meta( $this_school_id, 'country', true );
								$this_school_state = $administrative_area_level_1_array[$this_school_country_value]['values'][get_post_meta( $this_school_id, 'state', true )];
								$this_school_country = $countries[$this_school_country_value];
								$class_text =  '#'.($i+1).' '. get_the_title($this_school_id) . ( $this_school_state ? ', '.$this_school_state : '' ).', '.$this_school_country;

								$results_display .= '<li>'.$class_text.'</li>';
							}
							$i++;
						}

						echo 'Your class placed '.$class_rank.' out of '.count($league_challenge_result).' classes<br>'.$results_display.'</ul></div>';
					}

				?>



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