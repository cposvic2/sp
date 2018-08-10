<div class="activity-class-data">
	<h3>Results for this Activity</h3>


	<div class="activity-class-data-details">
		<?php
		$group_rankings = get_group_rankings_by_demographic( '', 'class', '', get_the_id(), $active_class );

		$expiration_length = vp_option('uc_option.activity_expiration_length');
		$activity_text = str_replace ( '!days!' , $expiration_length, vp_option('uc_option.a_complete_text') );
		echo '<p>'.$activity_text.'</p>';
									
		echo '<p><strong>Class Results</strong></p>';
		echo '<div id="chart-average-container"></div>';
		echo '
		<div class="uc-field uc-dropdown">
			<div class="label">
				<label><strong>Graph results for</strong></label>
			</div>
			<div class="field">
				<select name="activeTeam" class="uc-input select activeTeam" >
					<option value="class" selected>My Class</option>';

		foreach ( $group_rankings as $group_ranking )
			echo '<option value="'.$group_ranking['id'].'">'.get_the_title($group_ranking['id']).', '.get_group_student_names( $group_ranking['id'] ).'</option>';

		echo '
				</select>
			</div>
		</div>';

		$school_id = get_post_meta( $teacher_id, 'school', true );
		$class_grade = get_post_meta( $active_class, 'grade', true );
		$class_grade_title = ( get_term_by('slug', $class_grade, 'uc_grade') ? get_term_by('slug', $class_grade, 'uc_grade')->name : 'Multi-grade' );
		$current_school_year = get_current_school_year();

		$class_average = get_class_score_average( $active_class, get_the_id(), 'publish', $current_school_year );

		$geographic_searches = array( 
			'school' => array( 'value' => get_the_title( $school_id ), 'label' => 'school' ),
			'city' => array( 'value' => get_post_meta( $school_id, 'city', true ), 'label' => 'city' ),
			'county' => array( 'value' => get_post_meta( $school_id, 'county', true ), 'label' => 'county' ),
			'state' => array( 'value' => get_post_meta( $school_id, 'state', true ), 'label' => $administrative_area_level_1_array[get_post_meta( $school_id, 'country', true )]['name'] ),
			'country' => array( 'value' => get_post_meta( $school_id, 'country', true ), 'label' => 'country' ),
			'global' => array( 'value' => 'global', 'label' => 'Global' ),
		);

		foreach ( $geographic_searches as $key => $geographic_search ) {
			if ( $geographic_search['value'] )
				$class_results[$key] = get_class_results_by_demographic( 'standard', $key, $school_id, get_the_id(), $active_class, $class_grade, 10 , 'publish', $current_school_year);
		}

		// Make Classes datapoints
		$datapoints = array();

		$i = 0;
		$class_text = 'My Class Average: '.$class_average.'<br>';
		while ( $i < count($group_rankings) ) {
			$class_text .= get_the_title($group_rankings[$i]['id']).': '.$group_rankings[$i]['score'];
			$i++;
			if ( $i < count($group_rankings) )
				$class_text .= '<br>';
		}

		$datapoints[] = array(
			'y' => $class_average,
			'label' => "My Class",
			'text' => $class_text
		);

		foreach ( $class_results as $key => $class_result ) {

			if ( $key == 'global' ) {
				$label = $geographic_searches[$key]['label'];
				$end_text = ' globally';
			} else {
				$label = 'My ' .ucfirst( $geographic_searches[$key]['label'] );
				$end_text = ' in our '.$geographic_searches[$key]['label'];
			}

			$class_text = 'My Class Average: '.$class_average.'<br>'
				.ucfirst($label)
				.' Average: '
				.$class_result["average"]
				.'<br>We were #'
				.$class_result["place"]
				.' of '.$class_result["total"]
				.' '
				.( $class_result["total"] > 1 ? 'classes' : 'class' )
				.$end_text;

			$j = 0;
			$class_text .= '<br>The top performing classes came from the following schools:<br>';

			while ( $j < count($class_result['top_results']) ) {
				$this_school_country_value = get_post_meta( $class_result['top_results'][$j]['id'], 'country', true );
				$this_school_state = $administrative_area_level_1_array[$this_school_country_value]['values'][get_post_meta( $class_result['top_results'][$j]['id'], 'state', true )];
				$this_school_country = $countries[$this_school_country_value];
				$class_text .=  '#'.($j+1).' '.$class_result['top_results'][$j]['label'] . ( $this_school_state ? ', '.$this_school_state : '' ).', '.$this_school_country;
				$j++;
				if ( $j < count($class_result['top_results']) )
					$class_text .= '<br>';
			}

			$datapoints[] = array(
				'y' => $class_result["average"],
				'label' => $label,
				'text' => $class_text
			);
		}
		// End make class datapoints

		$class_chart_title = vp_option('uc_option.a_class_chart_title');
		$class_chart_title = str_replace ( '!grade!' , $class_grade_title, $class_chart_title );
		$class_chart_title = str_replace ( '!activitytitle!' , get_the_title(), $class_chart_title );

		$team_chart_title = vp_option('uc_option.a_team_chart_title');
		$team_chart_title = str_replace ( '!grade!' , $class_grade_title, $team_chart_title );
		$team_chart_title = str_replace ( '!activitytitle!' , get_the_title(), $team_chart_title );

		$class_chart_parts = array(
			'title' => $class_chart_title,
			'subtitle' => vp_option('uc_option.a_class_chart_subtitle'),
		);
		$team_chart_parts = array(
			'title' => $team_chart_title,
			'subtitle' => vp_option('uc_option.a_team_chart_subtitle'),
		);

		?>
		<script type="text/javascript">
			var class_datapoints = <?php echo json_encode($datapoints); ?>;
			var teams_datapoints = {};
			var class_chart_parts = <?php echo json_encode($class_chart_parts); ?>;
			var team_chart_parts = <?php echo json_encode($team_chart_parts); ?>;
			var chart_prototype = {
				title: {
					text: unescapeHTML(class_chart_parts['title']),
					fontSize: 19,
					fontFamily: "Arial",
				},
				subtitles:[{
					text: unescapeHTML(class_chart_parts['subtitle'])
				}],
				colorSet: "STEMColors",
				animationEnabled: true,
				animationDuration: 3000,
				backgroundColor: "rgba(250,251,246,1)",
				legend: {
					verticalAlign: "bottom",
					horizontalAlign: "center"
				},
				toolTip: {
					cornerRadius: 0,
					fontColor: "#000",
					contentFormatter: function(e) {
						if ( e.entries[0].dataPoint.text )
							var content = e.entries[0].dataPoint.text;

						return content;
					}
				},
				data: [{        
						type: "column",  
						showInLegend: false, 
						dataPoints: class_datapoints
					}]
			};
			window.onload = function () {
				CanvasJS.addColorSet("STEMColors", [
					"#E80303",
					"#99CC33",
					"#F60",
					"#660066",
				]);
				var chart_average = new CanvasJS.Chart("chart-average-container", chart_prototype);

				chart_average.render();
			}
		</script>
		<?php wp_nonce_field( 'update_results', 'update_results_noncename' ); ?>
		<?php 
		echo '<p><strong>Class Rankings</strong></p>';
		display_team_table( $group_rankings );

		$class_awards = get_class_awards( $active_class, get_the_id(), $class_grade );
		$awards_html = '';
		if ( count( $class_awards ) ) {
			echo '<h3>Class Awards</h3>';
			$awards_html .= '<div class="activity-awards-container">';
			foreach ( $class_awards as $class_award ) {
				switch ($class_award['award_type']) {
					case 'team-winner':
						$inner_text = 'Team '.$class_award['team-number'].' winner';
						break;
					case 'team-top-10':
						$inner_text = 'Team '.$class_award['team-number'].' top 10%';
						break;
					case 'class-top-10':
						$inner_text = 'Class top 10%';
						break;
				}
				$inner_text = "";
				$awards_html .= '<div class="award-container"><div class="award '.$class_award['geographic_type'].' '.$class_award['award_type'].' '.$class_award['demographic_type'].'">'.$inner_text.'</div></div>';
			}
			$awards_html .= '<div class="clear"></div></div>';
		}
		echo $awards_html;

		?>

	</div>
</div><!-- /.activity-class-data -->

<div class="entry-content" itemprop="articleBody">
	<h3>Standards Covered</h3>
	<div class="activity-standards">
		<?php 
		$terms = get_the_terms( get_the_id(), 'uc_activity_branch' );
		if ( $terms ) {
			$lastterm = count( $terms );
			$i = 0;
			echo '<p><strong>Branch(es) of Science:</strong> ';
			foreach ( $terms as $term ) {
				if ( ++$i === $lastterm )
					echo $term->name;
				else
					echo $term->name . ', ';
			}
			echo '</p>';
		} 
		$terms = get_the_terms( get_the_id(), 'uc_activity_ngss' );
		if ( $terms ) {
			$lastterm = count( $terms );
			$i = 0;
			echo '<p><strong>NGSS Standard(s):</strong> ';
			foreach ( $terms as $term ) {
				if ( ++$i === $lastterm )
					echo $term->name;
				else
					echo $term->name . ', ';
			}
			echo '</p>';
		}
		$terms = get_the_terms( get_the_id(), 'uc_activity_cc' );
		if ( $terms ) {
			$lastterm = count( $terms );
			$i = 0;
			echo '<p><strong>CCSS Standard(s):</strong> ';
			foreach ( $terms as $term ) {
				if ( ++$i === $lastterm )
					echo $term->name;
				else
					echo $term->name . ', ';
			}
			echo '</p>';
		} ?>
	</div>
</div><!-- /.entry-content -->