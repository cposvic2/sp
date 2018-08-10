<?php
/**
 * Template Name: Activity Sheet Template
 */
?>
<?php 
	$activity_id = $_GET["a"];
 ?>
<!doctype html>
<html <?php echo themify_get_html_schema(); ?> <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">

<title itemprop="name"><?php wp_title(); ?></title>

<!-- wp_header -->
<?php wp_head(); ?>

</head>
<body class="uc-custom">
	<div class="activity-sheet">
		<div class="print-block">
			<button class="print-button" onclick="window.print()">Print</button>
		</div>
		<div class="activity-sheet-inner">
			<h1 class="activity-title"><?php echo get_the_title( $activity_id ); ?></h1>
			<?php 
			$activity_summary = get_post_meta( $activity_id, 'activity_summary', true );
				if ( $activity_summary ) { ?>
			<div class="activity-section">
				<img class="activity-section-image" src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/activity-sheet-goal.png">
				<div class="activity-section-description">
					<p><?php echo $activity_summary ?></p>
				</div>
			</div>
			<?php }
			$required_materials = wpautop( get_post_meta( $activity_id, 'required_materials', true ) );
			if ( $required_materials ) { ?>
			<div class="activity-section">
				<img class="activity-section-image" src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/activity-sheet-materials.png">
				<div class="activity-section-description">
					<?php echo $required_materials; ?>
				</div>
			</div>
			<?php }
			$activity_competition = wpautop( get_post_meta( $activity_id, 'activity_competition', true ) );
			$score_fields = get_score_fields_by_activity( $activity_id );
			if ( isset($_SESSION['activeClass']) ) {
				$active_class = $_SESSION['activeClass'];
				$uc_groups = get_uc_groups_by_activity( $active_class, $activity_id);
				if ( $uc_groups && count($uc_groups) ) {
					$groups = array();
					$i = 0;
					while ( $i < count($uc_groups) ) {
						$students = get_post_meta( $uc_groups[$i]['value'], 'students', true );

						$students_to_display = '';
						$j = 0;
						while ( $j < count($students) ) {
							$students_to_display .= get_student_name( $students[$j] );
							$j++;
							if ( $j < count($students) )
								$students_to_display .= ', ';
						}
						$groups[] = array (
							'number' => $i+1,
							'students' => $students_to_display
						);
						$i++;
					}
				}
			}

			if (!count($groups))
				$groups = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);

			if ( $activity_competition ) { ?>
			<div class="activity-section">
				<img class="activity-section-image" src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/activity-sheet-competition.png">
				<div class="activity-section-description">
					<?php echo $activity_competition; ?>
				</div>
				<table class="uc-scoring-table" id="scoring-table">
					<thead>
						<tr>
							<th>Team Number</th>
							<th>Student Names</th>
							<?php foreach ( $score_fields as $score_field ) { ?>
							<th class="score-header"><?php echo $score_field ?></th>
							<?php } ?>
							<th class="teamwork-score-header">Teamwork Score (1-5) (5 is best)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $groups as $group ) { ?>
						<tr>
							<td><?php echo ( is_array($group) ? $group['number'] : $group ) ?></td>
							<td><?php echo ( is_array($group) ? $group['students'] : '' ) ?></td>
							<?php foreach ( $score_fields as $score_field ) { ?>
							<td></td>
							<?php } ?>
							<td></td>
						</tr>
						<?php } ?>							
					</tbody>
				</table>

				<div class="teamwork-rubric">
					<div class="teamwork-rubric-title">Teamwork Rubric</div>
					<div class="teamwork-rubric-item"><strong>Communication—</strong> All voices are heard equally, and all team members have a share in key decisions.</div>
					<div class="teamwork-rubric-item"><strong>Cooperation—</strong> Team avoids conflict by working together or resolve conflict quickly without adult intervention.</div>
					<div class="teamwork-rubric-item"><strong>Leadership—</strong> Team members consider the needs of the team and act to achieve a best effort. “Team before self.”</div>
					<div class="teamwork-rubric-item"><strong>Enthusiasm—</strong> Team displays a “CAN DO” attitude. Members listen to and encourage one another.</div>
					<div class="teamwork-rubric-item"><strong>Problem Solving—</strong> Team remains committed to the task in the face of opposition, considers new ideas, and creates solutions.</div>
				</div>
			</div>
			<?php }

			$activity_overview = wpautop( get_post_meta( $activity_id, 'activity_overview', true ) );
			$activity_background_knowledge = wpautop( get_post_meta( $activity_id, 'activity_background_knowledge', true ) );
			$activity_scientific_concepts = wpautop( get_post_meta( $activity_id, 'activity_scientific_concepts', true ) );
			$ngss_terms = get_the_terms( $activity_id, 'uc_activity_ngss' );
			$cc_terms = get_the_terms( $activity_id, 'uc_activity_cc' );
			 ?>
			<div class="activity-section">
				<img class="activity-section-image" src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/activity-sheet-supporting-instruction.png">
				<div class="activity-section-description">
					<p><strong>Activity:</strong> <?php echo get_the_title( $activity_id ); ?></p>
					<p><strong>Grade Levels:</strong> <?php echo get_the_title( $activity_id ); ?></p>
					<?php
					if ( $ngss_terms ) {
						$lastterm = count( $ngss_terms );
						$i = 0;
						echo '<p><strong>NGSC standard(s):</strong> ';
						foreach ( $ngss_terms as $ngss_term ) {
							if ( ++$i === $lastterm )
								echo $ngss_term->name;
							else
								echo $ngss_term->name . ', ';
						}
						echo '</p>';
					}
					if ( $cc_terms ) {
						$lastterm = count( $cc_terms );
						$i = 0;
						echo '<p><strong>CCSS standard(s):</strong> ';
						foreach ( $cc_terms as $cc_term ) {
							if ( ++$i === $lastterm )
								echo $cc_term->name;
							else
								echo $cc_term->name . ', ';
						}
						echo '</p>';
					}
					
					if ( $activity_overview )
						echo '<h3>Activity Overview</h3>' . $activity_overview;
					if ( $activity_background_knowledge )
						echo '<h3>Background Knowledge</h3>' . $activity_background_knowledge;
					if ( $activity_scientific_concepts )
						echo '<h3>Scientific Concepts</h3>' . $activity_scientific_concepts;
					?>
				</div>



			</div>
			<?php 
			$activity_class_discussion = wpautop( get_post_meta( $activity_id, 'activity_class_discussion', true ) );

			if ( $activity_class_discussion ) { ?>
			<div class="activity-section">
				<img class="activity-section-image" src="<?php echo UPTOWNCODE_THEME_URL ?>/assets/img/activity-sheet-class-discussion.png">
				<div class="activity-section-description">
					<?php echo $activity_class_discussion; ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</body>

