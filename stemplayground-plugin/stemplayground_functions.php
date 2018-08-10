<?php
/*
 * Various functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove Admin bar for teachers
function hide_teacher_admin_bar( $content ) {
	if ( !current_user_can( 'edit_posts' ) ) {
		return false;
	} else {
		return $content;
	}
}
add_filter( 'show_admin_bar', 'hide_teacher_admin_bar' );

// Geography
function get_states_by_country( $country ) {
	if ( $country == 'united-states' )
		return get_us_states();
	return false;
}
function get_cities_by_state( $state ) {

	$schools = get_uc_schools_by_geography( array( array( 'type' => 'state', 'value' => $state ) ) );
	if ( count($schools) ) {
		$found_cities = array();

		foreach ( $schools as $school ) {
			$city = get_post_meta( $school['value'], 'city', true );
			if ( !in_array( $city, $found_cities ) )
				$found_cities[] = $city;
		}
		foreach ( $found_cities as $found_city )
			$return_cities[] = array('value' => $found_city, 'label' => $found_city);
		return $return_cities;
	}
	return false;
}
function get_counties_by_state( $state ) {

	$schools = get_uc_schools_by_geography( array( array( 'type' => 'state', 'value' => $state ) ) );
	if ( count($schools) ) {
		$found_counties = array();

		foreach ( $schools as $school ) {
			$county = get_post_meta( $school['value'], 'county', true );
			if ( !in_array( $county, $found_counties ) )
				$found_counties[] = $county;
		}
		foreach ( $found_counties as $found_county )
			$return_counties[] = array('value' => $found_county, 'label' => $found_county);
		return $return_counties;
	}
	return false;
}

function get_league_challenge_results( $class_id ='', $league_challenge_id ='', $grade = 'all', $post_status = 'publish' ) {
	$results = array();

	$award_checks = array();

	$school_year = get_school_year_of_post( $class_id );

	$class_teacher_id = get_post_meta( $class_id, 'teacher', true );
	$class_school_id = get_post_meta( $class_teacher_id, 'school', true );

	if ( can_display_awards( $class_id ) ) {
		// Get geo/demographic categories
		array_push( $award_checks,
			array (
				'type' => 'standard',
				'geographic_type' => 'global',
				'geographic_value' => false
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'country',
				'geographic_value' => get_post_meta( $class_school_id, 'country', true )
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'state',
				'geographic_value' => get_post_meta( $class_school_id, 'state', true )
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'county',
				'geographic_value' => get_post_meta( $class_school_id, 'county', true )
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'city',
				'geographic_value' => get_post_meta( $class_school_id, 'city', true )
			)
		);

		if ( is_class_at_risk( $class_id ) ) {
			array_push( $award_checks,
				array (
					'type' => 'at-risk',
					'geographic_type' => 'global',
					'geographic_value' => false
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'country',
					'geographic_value' => get_post_meta( $class_school_id, 'country', true )
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'state',
					'geographic_value' => get_post_meta( $class_school_id, 'state', true )
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'county',
					'geographic_value' => get_post_meta( $class_school_id, 'county', true )
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'city',
					'geographic_value' => get_post_meta( $class_school_id, 'city', true )
				)
			);
		}

		if ( is_class_ell( $class_id ) ) {
			array_push( $award_checks,
				array (
					'type' => 'ell',
					'geographic_type' => 'global',
					'geographic_value' => false
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'country',
					'geographic_value' => get_post_meta( $class_school_id, 'country', true )
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'state',
					'geographic_value' => get_post_meta( $class_school_id, 'state', true )
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'county',
					'geographic_value' => get_post_meta( $class_school_id, 'county', true )
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'city',
					'geographic_value' => get_post_meta( $class_school_id, 'city', true )
				)
			);
		}
	}

	foreach ( $award_checks as $award_check ) {
		if ( is_array( $award_check ) ) {
			$results[$award_check['geographic_type']][$award_check['type']] = get_league_challenge_class_rankings_by_demographic( $award_check['type'], $award_check['geographic_type'], $class_school_id, $league_challenge_id, $grade, $post_status, $school_year );
		}
	}
	return $results;
}

// Gets league challenge awards
function get_league_challenge_awards( $rankings = array(), $geographic_type ='', $demographic_type ='', $class_id = '' ) {
	$results = array();
	$ch_min_classes = vp_option('uc_option.ch_min_classes');

	if ( !$ch_min_classes || $ch_min_classes <= count($rankings) ) {
		$i = 0;
		while ( $i < 6 ) {
			if ( $rankings[$i]['id'] == $class_id ) {
				$results[] = array( 
					'award_type' => $geographic_type.'-'.($i+1),
					'demographic_type' => $demographic_type,
					'geographic_type' => $geographic_type,
				);
			}
			$i++;
		}
	}
	return $results;
}

// Gets class awards
function get_class_awards( $class_id ='', $activity_id ='', $grade = 'all', $post_status = 'publish' ) {

	$results = array();
	$award_checks = array();

	$school_year = get_school_year_of_post( $class_id );

	$class_teacher_id = get_post_meta( $class_id, 'teacher', true );
	$class_school_id = get_post_meta( $class_teacher_id, 'school', true );

	if ( can_display_awards( $class_id ) ) {
		// Get geo/demographic categories
		array_push( $award_checks,
			array (
				'type' => 'standard',
				'geographic_type' => 'global',
				'geographic_value' => false
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'country',
				'geographic_value' => get_post_meta( $class_school_id, 'country', true )
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'state',
				'geographic_value' => get_post_meta( $class_school_id, 'state', true )
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'county',
				'geographic_value' => get_post_meta( $class_school_id, 'county', true )
			),
			array (
				'type' => 'standard',
				'geographic_type' => 'city',
				'geographic_value' => get_post_meta( $class_school_id, 'city', true )
			)
		);

		if ( is_class_at_risk( $class_id ) ) {
			array_push( $award_checks,
				array (
					'type' => 'at-risk',
					'geographic_type' => 'global',
					'geographic_value' => false
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'country',
					'geographic_value' => get_post_meta( $class_school_id, 'country', true )
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'state',
					'geographic_value' => get_post_meta( $class_school_id, 'state', true )
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'county',
					'geographic_value' => get_post_meta( $class_school_id, 'county', true )
				),
				array (
					'type' => 'at-risk',
					'geographic_type' => 'city',
					'geographic_value' => get_post_meta( $class_school_id, 'city', true )
				)
			);
		}

		if ( is_class_ell( $class_id ) ) {
			array_push( $award_checks,
				array (
					'type' => 'ell',
					'geographic_type' => 'global',
					'geographic_value' => false
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'country',
					'geographic_value' => get_post_meta( $class_school_id, 'country', true )
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'state',
					'geographic_value' => get_post_meta( $class_school_id, 'state', true )
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'county',
					'geographic_value' => get_post_meta( $class_school_id, 'county', true )
				),
				array (
					'type' => 'ell',
					'geographic_type' => 'city',
					'geographic_value' => get_post_meta( $class_school_id, 'city', true )
				)
			);
		}
	}

	// Completion award
	$results[] = array( 
		'award_type' => 'completion-award',
	);

	// Teamwork award
	$class_groups = get_uc_groups_by_activity( $class_id, $activity_id, 'all', $post_status, $school_year );
	$total_teamwork_score = 0;
	foreach ( $class_groups as $class_group ) {
		$total_teamwork_score += get_post_meta( $class_group['value'], 'teamwork_score', true );
	}

	if ( $total_teamwork_score/count($class_groups) >= 4 ) {
		$results[] = array( 
			'award_type' => 'teamwork-award',
		);
	}

	$ch_min_classes = vp_option('uc_option.ch_min_classes');

	foreach ( $award_checks as $award_check ) {
		if ( is_array( $award_check ) ) {
			
			$rankings = get_class_rankings_by_demographic( $award_check['type'], $award_check['geographic_type'], $class_school_id, $activity_id, $grade, $post_status, $school_year );

			if ( !$ch_min_classes || $ch_min_classes <= count($rankings) ) {
				$top10percent = ceil ( .1 * count($rankings) );

				// Class awards
				$i = 0;
				while ( $i < $top10percent ) {
					if ( $i == 0 && $rankings[$i]['id'] == $class_id ) {
						$results[] = array( 
							'award_type' => 'class-winner',
							'demographic_type' => $award_check['type'], 
							'geographic_type' => $award_check['geographic_type'], 
							'geographic_value' => $award_check['geographic_value'], 
						);
					}
					if ( $rankings[$i]['id'] == $class_id ) {
						$results[] = array( 
							'award_type' => 'class-top-10',
							'demographic_type' => $award_check['type'], 
							'geographic_type' => $award_check['geographic_type'], 
							'geographic_value' => $award_check['geographic_value'], 
						);
					}
					$i++;
				}
				// End Class awards

				// Group awards
				$rankings = get_group_rankings_by_demographic( $award_check['type'], $award_check['geographic_type'], $class_school_id, $activity_id, $class_id, $grade, $post_status, $school_year );
				$top10percent = ceil ( .1 * count($rankings) );

				$j = 0;
				while ( $j < count($class_groups) ) {
					if ( $class_groups[$j]['value'] == $rankings[0]['id'] && $top10percent )
						$results[] = array( 
							'award_type' => 'team-winner',
							'demographic_type' => $award_check['type'], 
							'geographic_type' => $award_check['geographic_type'], 
							'geographic_value' => $award_check['geographic_value'], 
							'group_id' => $class_groups[$j]['value']
						);

					$i = 0;
					while ( $i < $top10percent ) {
						if ( $rankings[$i]['id'] == $class_groups[$j]['value'] ) {
							$results[] = array( 
								'award_type' => 'team-top-10',
								'demographic_type' => $award_check['type'], 
								'geographic_type' => $award_check['geographic_type'], 
								'geographic_value' => $award_check['geographic_value'], 
								'group_id' => $class_groups[$j]['value']
							);
						}
						$i++;
					}
					$j++;
				}
				// End Group awards
			}
		}	
	}
	return $results;
}

function get_a_class_percentile_by_demographic( $class_id, $demographic_type = 'standard', $geographic_type = false, $activity_id = '', $post_status = 'publish', $school_year = false ) {
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$school_id = get_post_meta( $teacher_id, 'school', true );
	
	$rankings = get_class_rankings_by_demographic( $demographic_type, $geographic_type, $school_id, $activity_id, $grade, $post_status, $school_year );

	$i = 0;
	while ( $i < count($rankings) ) {
		if ( $rankings[$i]['id'] == $class_id ) {
			break;
		}
		$i++;
	}
	return floor( 100* ( $i + 1 - .5) / count( $rankings) );
}

function get_league_challenge_class_rankings_by_demographic( $demographic_type = 'standard', $geographic_type = false, $school_id = '', $league_challenge_id = '', $grade = 'all', $post_status = 'publish', $school_year = false ) {
	switch ( $demographic_type ) {
		case 'at-risk':
			$check_ell = false;
			$check_at_risk = true;
			break;
		case 'ell':
			$check_ell = true;
			$check_at_risk = false;
			break;
		default:
			$check_ell = false;
			$check_at_risk = false;
			break;
	}

	switch ( $geographic_type ) {
		case 'global':
		case 'country':
		case 'state':
		case 'county':
		case 'city':
			$classes = get_uc_classes_by_school_geography( $geographic_type, $school_id, $check_ell, $check_at_risk, $grade, $post_status, $school_year );
			break;
		case 'school':
			$classes = get_uc_classes_by_school( $school_id, $check_ell, $check_at_risk, $grade, $post_status, $school_year );
			break;
		default:
			$classes = array();
			break;
	}

	return get_league_challenge_class_rankings( $classes, $league_challenge_id, 'type1', $post_status, $school_year );
}

function get_class_rankings_by_demographic( $demographic_type = 'standard', $geographic_type = false, $school_id = '', $activity_id = '', $grade = 'all', $post_status = 'publish', $school_year = false ) {

	switch ( $demographic_type ) {
		case 'at-risk':
			$check_ell = false;
			$check_at_risk = true;
			break;	
		case 'ell':
			$check_ell = true;
			$check_at_risk = false;
			break;
		default:
			$check_ell = false;
			$check_at_risk = false;
			break;
	}

	switch ( $geographic_type ) {
		case 'global':
		case 'country':
		case 'state':
		case 'county':
		case 'city':
			$classes = get_uc_classes_by_school_geography( $geographic_type, $school_id, $check_ell, $check_at_risk, $grade, $post_status, $school_year );
			break;
		case 'school':
			$classes = get_uc_classes_by_school( $school_id, $check_ell, $check_at_risk, $grade, $post_status, $school_year );
			break;
		default:
			$classes = array();
			break;
	}

	return get_class_rankings( $classes, $activity_id, 'type1', $post_status, $school_year );
}

// Gets league challenge class rankings for an array of classes. Two formats are available for return
function get_league_challenge_class_rankings( $classes = array(), $league_challenge_id = '', $format = 'type1', $post_status = 'publish', $school_year = false ) {
	$league_challenge_scores = $rankings = array();

	$league_challenge_activities = get_post_meta( $league_challenge_id, 'activities', true );

	if ( $league_challenge_activities ) {
		foreach ( $league_challenge_activities as $activity_id ) {
			$activity_rankings[$activity_id] = get_class_rankings( $classes, $activity_id, 'type2', $post_status, $school_year );
		}


		foreach ( $classes as $class ) {
			$class_total_rank = 0;
			foreach ( $activity_rankings as $activity_ranking ) {
				$class_total_rank += $activity_ranking[$class['value']]['rank'];
			}
			$league_challenge_scores[$class['value']] = $class_total_rank/count($league_challenge_activities);
		}
		asort( $league_challenge_scores );

		foreach ( $league_challenge_scores as $key => $score ) {
			$rankings[] = array( 'id' => $key, 'score' => $score );
		}
	}


	return $rankings;
}

// Gets class rankings for an array of classes. Two formats are available for return
function get_class_rankings( $classes = array(), $activity_id = '', $format = 'type1', $post_status = 'publish', $school_year = false ) {
	$demographic_scores = array();

	foreach ( $classes as $class ) {
		if ( signup_status( $class['value'], $activity_id ) == 'completed' ) {
			$demographic_scores[$class['value']] = get_class_score_average( $class['value'], $activity_id, $post_status, $school_year );
		} else {
			$demographic_scores[$class['value']] = false;
		}
	}
	$higher_is_better = get_post_meta( $activity_id, 'score_higher', true );
	if ( $higher_is_better )
		arsort( $demographic_scores );
	else
		asort( $demographic_scores );
	$rankings = array();

	if ( $format =='type1' ) {
		foreach ( $demographic_scores as $key => $score ) {
			$rankings[] = array( 'id' => $key, 'score' => $score );
		}
	} else {
		$i = 0;
		foreach ( $demographic_scores as $key => $score ) {
			$i++;
			$rankings[$key] = array( 'rank' => ( $score !== false ? $i : count($demographic_scores) ), 'score' => $score );
		}
	}

	return $rankings;
}

function get_group_rankings_by_demographic( $demographic_type = 'standard', $geographic_type = false, $class_school_id = '', $activity_id = '', $class_id = '', $grade = 'all', $post_status = 'publish', $school_year = false ) {

	switch ( $demographic_type ) {
		case 'at-risk':
			$check_ell = false;
			$check_at_risk = true;
			break;
		case 'ell':
			$check_ell = true;
			$check_at_risk = false;
			break;
		default:
			$check_ell = false;
			$check_at_risk = false;
			break;
	}

	switch ( $geographic_type ) {
		case 'global':
		case 'country':
		case 'state':
		case 'county':
		case 'city':
			$groups = get_uc_groups_by_school_geography( $geographic_type, $class_school_id, $activity_id, $check_ell, $check_at_risk, $grade, $post_status, $school_year );
			break;
		case 'class':
			$groups = get_uc_groups_by_activity( $class_id, $activity_id, $grade, $post_status, $school_year );
			break;
		case 'school':
			$groups = get_uc_groups_by_school( $class_school_id, $activity_id, $check_ell, $check_at_risk, $grade, $post_status, $school_year );
			break;
		default:
			$groups = array();
			break;
	}

	return get_group_rankings( $groups, $activity_id, 'type1' );
}

function get_group_rankings( $groups = array(), $activity_id = '', $format = 'type1' ) {
	$demographic_scores = array();

	foreach ( $groups as $group ) {
		if ( signup_status( get_post_meta( $group['value'], 'class', true ), $activity_id ) == 'completed' ) {
			$group_score = get_post_meta( $group['value'], 'score', true );
			$teamwork_score = get_post_meta( $group['value'], 'teamwork_score', true );
			$demographic_scores[$group['value']] = calculate_final_score( $group_score, $teamwork_score, $activity_id );
		} else {
			$demographic_scores[$group['value']] = false;
		}
	}

	$higher_is_better = get_post_meta( $activity_id, 'score_higher', true );
	if ( $higher_is_better )
		arsort( $demographic_scores );
	else
		asort( $demographic_scores );

	if ( $format =='type1' ) {
		foreach ( $demographic_scores as $key => $score ) {
			$rankings[] = array( 'id' => $key, 'score' => $score );
		}
	} else {
		$i = 0;
		foreach ( $demographic_scores as $key => $score ) {
			$i++;
			$rankings[$key] = array( 'rank' => ( $score !== false ? $i : count($demographic_scores) ), 'score' => $score );
		}
	}
	return $rankings;
}

function get_class_average_by_demographic( $demographic_type = 'standard', $geographic_type = false, $school_id = '', $activity_id = '', $grade = 'all', $post_status = 'publish', $school_year = false ) {

	$rankings = get_class_rankings_by_demographic( $demographic_type, $geographic_type, $school_id, $activity_id, $grade, $post_status, $school_year );

	$rankings_sum = 0;

	if ( count($rankings) ) {
		foreach ( $rankings as $ranking ) {
			$rankings_sum += $ranking['score'];

		}
		return $rankings_sum / count($rankings);
	} else
		return false;
}

function get_class_results_by_demographic( $demographic_type = 'standard', $geographic_type = false, $school_id = '', $activity_id = '', $class_id = '', $grade = 'all', $rankings_number = 10, $post_status = 'publish', $school_year = false ) {
	$results = array();

	$rankings = get_class_rankings_by_demographic( $demographic_type, $geographic_type, $school_id, $activity_id, $grade, $post_status, $school_year );
	foreach ($rankings as $key => $ranking) {
		if ($ranking['score'] === false) {
			unset($rankings[$key]);
		}
	}
	$rankings_sum = 0;

	if ( count($rankings) ) {
		$i = 0;
		while ( $i < count($rankings) ) {
			$rankings_sum += $rankings[$i]['score'];

			if ( $rankings[$i]['id'] == $class_id )
				$results['place'] = $i + 1;
			if ( $i < $rankings_number ) {
				$class_teacher_id = get_post_meta( $rankings[$i]['id'], 'teacher', true );
				$class_school_id = get_post_meta( $class_teacher_id, 'school', true );
				$results['top_results'][] = array(
					'id' => $class_school_id,
					'label' => get_the_title( $class_school_id )
				);
			}
			$i++;
		}
		$results['total'] = count($rankings);
		$results['average'] = $rankings_sum / count($rankings);

		return $results;
	} else
		return false;
}

function get_group_results_by_demographic( $demographic_type = 'standard', $geographic_type = false, $school_id = '', $activity_id = '', $class_id = '', $group_id ='', $grade = 'all', $rankings_number = 10, $post_status = 'publish', $school_year = false ) {
	$results = array();

	$rankings = get_group_rankings_by_demographic( $demographic_type, $geographic_type, $school_id, $activity_id,  $class_id, $grade, $post_status, $school_year );

	$rankings_sum = 0;

	if ( count($rankings) ) {
		$i = 0;
		while ( $i < count($rankings) ) {
			$rankings_sum += $rankings[$i]['score'];

			if ( $rankings[$i]['id'] == $group_id )
				$results['place'] = $i + 1;
			if ( $i < $rankings_number ) {
				$group_class_id = get_post_meta( $rankings[$i]['id'], 'class', true );
				$group_teacher_id = get_post_meta( $group_class_id, 'teacher', true );
				$group_school_id = get_post_meta( $group_teacher_id, 'school', true );
				$results['top_results'][] = array(
					'id' => $group_school_id,
					'label' => get_the_title( $group_school_id )
				);
			}
			$i++;
		}
		$results['total'] = count($rankings);
		$results['average'] = $rankings_sum / count($rankings);

		return $results;
	} else
		return false;
}

// Calculates class average for activity
function get_class_score_average( $class_id = '', $activity_id = '', $post_status = 'publish', $school_year = false ) {
	$groups = get_uc_groups_by_activity( $class_id, $activity_id, $post_status, $school_year );
	if ( $groups && count($groups) ) {
		$class_score_sum = 0;
		foreach ( $groups as $group ) {
			$group_score = get_post_meta( $group['value'], 'score', true );
			$teamwork_score = get_post_meta( $group['value'], 'teamwork_score', true );
			$final_score = calculate_final_score( $group_score, $teamwork_score, $activity_id );
			$class_score_sum += $final_score;
		}
		return $class_score_sum / count($groups);
	} else
		return false;
}

// Calculate final score
function calculate_final_score( $score, $teamwork_score, $activity_id ) {
	$multiplier = vp_option('uc_option.teamwork_'.$teamwork_score);

	$higher_is_better = get_post_meta( $activity_id, 'score_higher', true );

	if ( $multiplier !== false ) {
		if ( $higher_is_better )
			return ( $score + ( $score * $multiplier / 100 ) );
		else
			return ( $score - ( $score * $multiplier / 100 ) );
	} else {
		return false;
	}
}

// Send sponsorship email and receipt
function send_sponsorship_email( $sponsorship_id ) {
	$user_id = get_post_meta( $sponsorship_id, 'user_id', true );
	if ($user_id) {
		$userdata = get_userdata( $user_id );
		$email = $userdata->user_email;
		$name = $userdata->first_name;
	} else {
		$email = get_post_meta( $sponsorship_id, 'sponsor_email', true );
		$name = get_post_meta( $sponsorship_id, 'sponsor_name', true );
	}

	$class_id = get_post_meta( $sponsorship_id, 'class', true );
	$class_name = get_the_title( $class_id );
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$teacher_name = get_post_meta( $teacher_id, 'first_name', true );
	$school_id = get_post_meta( $teacher_id, 'school', true );
	$school_name = get_the_title( $school_id );
	$price = pretty_price(vp_option('uc_option.sponsor_price'));

	$return = false;

	if ( true ) {
		$sponsor_email_body = vp_option('uc_option.sponsor_email_body');
		$sponsor_email_subj = vp_option('uc_option.sponsor_email_subj');

		// Replace name, classname and teachername
		$sponsor_email_body = str_replace ( '!name!' , $name, $sponsor_email_body );
		$sponsor_email_body = str_replace ( '!classname!' , $class_name, $sponsor_email_body );
		$sponsor_email_body = str_replace ( '!teachername!' , $teacher_name, $sponsor_email_body );
		$sponsor_email_subj = str_replace ( '!name!' , $name, $sponsor_email_subj );
		$sponsor_email_subj = str_replace ( '!classname!' , $class_name, $sponsor_email_subj );
		$sponsor_email_subj = str_replace ( '!teachername!' , $teacher_name, $sponsor_email_subj );
		$sponsor_email_subj = str_replace ( '!schoolname!' , $school_name, $sponsor_email_subj );

		include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
		$mail = new PHPMailer();
		$mail->SetFrom('no-reply@stemplayground.org', 'STEM Playground');
		$mail->AddAddress($email);
		$mail->Subject = $sponsor_email_subj;
		$mail->AltBody = wp_strip_all_tags($sponsor_email_body);
		$mail->MsgHTML(wpautop($sponsor_email_body));

		$pdf = create_sponsorship_pdf($price, $teacher_name, $class_name, $school_name);


		$doc = $pdf->Output('S');
		$mail->AddStringAttachment($doc, 'doc.pdf', 'base64', 'application/pdf');

		$return = $mail->Send();
	}
	return $return;
}

function create_sponsorship_pdf($price, $teacher_name, $class_name, $school_name) {
	$pdf = new FPDF();
	$pdf->SetMargins(20, 20);
	$pdf->AddPage();
	$pdf->Image(UPTOWNCODE_PLUGIN_PATH . 'assets/img/sponsor_logo.png',140,30,0);
	$pdf->SetFont('Arial','B',18);
	$pdf->Cell(0,10,'Your STEM Playground Sponsorship Receipt',0,1);
	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(120,5,"Thank you for sponsoring a class with STEM Playground! Your sponsorship defrays the cost of STEM Playground's materials for the class, but also provides the students with \"Mission Accomplished\" dog tags to give them recognition for their participation in STEM.",0,'L');
	$pdf->Ln();
	$pdf->MultiCell(120,5,"Your sponsorship also allows STEM Playground to continue to produce top-level content and expand our reach into under- served areas badly in need of inexpensive opportunities to engage in STEM.",0,'L');
	$pdf->Ln();
	$pdf->MultiCell(0,5,"Your sponsorship will also be featured on our website and on your sponsored class's Activity Board, where the teacher can access his or her activities. Your generosity will not go unrecognized.",0,'L');
	$pdf->Ln();
	$pdf->MultiCell(0,5,"As of early 2017, STEM Playground is a registered 501 (c)(3) organization. As such, your ".$price." sponsorship is fully tax deductible in the US.",0,'L');$pdf->Ln();
	$pdf->MultiCell(0,5,"Please see below for your sponsorship details and receipt.",0,'L');
	$pdf->Ln();
	$pdf->Cell(0,5,'Date: '.date('d/m/Y'),0,1);
	$pdf->Cell(0,5,'Sponsored Teacher: '.$teacher_name,0,1);
	$pdf->Cell(0,5,'Sponsored Class: '.$class_name,0,1);
	$pdf->Cell(0,5,'Sponsored School: '.$school_name,0,1);
	$pdf->Ln();
	$pdf->Cell(0,5,'Sponsorship Amount: '.$price,0,1);
	$pdf->Cell(0,5,'STEM Playground EIN: XX-XXXX762',0,1);
	$pdf->Ln();
	$pdf->Cell(0,5,'Thank you for your generosity in supporting your sponsored class!',0,1);
	return $pdf;
}

// Send email confirmation
function send_confirmation_email( $user_id ) {
	$userdata = get_userdata( $user_id );

	$return = false;

	if ( $userdata ) {
		$recipient = $userdata->user_email;
		$us_confirm_email = vp_option('uc_option.us_confirm_email');
		$us_confirm_subject = vp_option('uc_option.us_confirm_email_subj');
		$email_confirmation_token = get_user_meta( $user_id, 'email_confirmation_token', true );
		$confirm_url = home_url() . '/confirm/?confirmation_token='.$email_confirmation_token;

		// Replace firstname and lastname
		$us_confirm_subject = str_replace ( '!firstname!' , $userdata->first_name, $us_confirm_subject );
		$us_confirm_subject = str_replace ( '!lastname!' , $userdata->last_name, $us_confirm_subject );
		$us_confirm_email = str_replace ( '!firstname!' , $userdata->first_name, $us_confirm_email );
		$us_confirm_email = str_replace ( '!lastname!' , $userdata->last_name, $us_confirm_email );

		$us_confirm_email_html .= '<div style="text-align:center;" ><a href="'.$confirm_url.'" style="background-color:#f06544;border-radius:5px;color:white!important;display:inline-block;font-size:16px;font-family:Helvetica,Arial,sans-serif;padding:0.8em 1.5em;text-align:center;text-decoration:none" target="_blank">
			Confirm Email Address
			</a></div>';

		$return = stemplayground_send_email( $recipient, $us_confirm_subject, $us_confirm_email_html, $us_confirm_email );	
	}
	return $return;
}

// Names group
function name_group( $default_groups = true, $group_number = 0, $class_id, $activity_id ) {
	$class_name = get_the_title( $class_id );

	if ( $default_groups ) {
		$group_title = 'Default Team '. $group_number;
	} else {
		$activity_name = get_the_title( $activity_id );
		$group_title = 'Team '.$group_number;
	}
	return $group_title;
}

// Checks if post is teacher's post
function is_post_of_teacher( $post_id = '', $teacher_id = '', $input_post_type = false ) {

	$post_type = get_post_type( $post_id );

	if ( $input_post_type && $input_post_type != $post_type )
		return false;

	switch ( $post_type ) {
		case 'uc_signup':
			$class_id = get_post_meta( $post_id, 'class', true );
			$post_teacher_id = get_post_meta( $class_id, 'teacher', true );
			break;
		case 'uc_group':
		case 'uc_student':
		case 'uc_class':
			$post_teacher_id = get_post_meta( $post_id, 'teacher', true );
			break;
		case 'uc_school':
		default:
			$post_teacher_id = false;
			break;
	}

	if ( $post_teacher_id == $teacher_id )
		return true;
	else
		return false;
}

// Calculates score
function calculate_score( $activity_id = '', $scores ) {

	$score_equation = get_post_meta( $activity_id, 'score_calculation', true );
	$score_fields = get_post_meta( $activity_id, 'score_fields', true );

	if ( $score_fields ) {
		if ( $score_equation && count($score_fields) > 1 ) {
			$score_equation = strtoupper(str_replace(' ', '', $score_equation));
			for ( $i = 1; $i <= $score_fields; $i++ ) {
				if ( isset( $scores[$i - 1] ) )
					$ith_score = $scores[$i - 1];
				else
					$ith_score = 0;

				if ( strpos ( $score_equation, 'SCORE'.$i ) !== FALSE )
					$score_equation = str_replace( 'SCORE'.$i, $ith_score, $score_equation );
				else
					$score_equation .= '+'.$ith_score;
			}

			$Cal = new Field_calculate();

			$result = $Cal->calculate( $score_equation );

			return $result;
		} else {
			$total_score = 0;
			foreach ( $scores as $score ) {
				$total_score += $score;
			}
			return $total_score;
		}
	}
	return false;
}

class Field_calculate {
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';

    const PARENTHESIS_DEPTH = 10;

    public function calculate($input){
        if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);

            //  Calculate each of the parenthesis from the top
            $i = 0;
            while(strpos($input, '(') || strpos($input, ')')){
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);

                $i++;
                if($i > self::PARENTHESIS_DEPTH){
                    break;
                }
            }

            //  Calculate the result
            if(preg_match(self::PATTERN, $input, $match)){
                return $this->compute($match[0]);
            }

            return false;
        }

        return false;
    }

    private function compute($input){
        $compute = create_function('', 'return '.$input.';');

        return 0 + $compute();
    }

    private function callback($input){
        if(is_numeric($input[1])){
            return $input[1];
        }
        elseif(preg_match(self::PATTERN, $input[1], $match)){
            return $this->compute($match[0]);
        }

        return 0;
    }
}

// Determines whether awards should be displayed
function can_display_awards( $post_id = '' ) {
	$post_school_year = get_school_year_of_post( $post_id );
	$current_school_year = get_current_school_year();


	if ( $post_school_year < $current_school_year )
		return true;
	else {
		$current_date = strtotime( current_time('Y-m-d') );
		$school_year = get_school_year( $current_school_year, 'Y-m-d' );
		$school_year_start = strtotime( $school_year['start'] );

		$a_award_year_month = vp_option('uc_option.a_award_year_month');
		$a_award_year_day = vp_option('uc_option.a_award_year_day');
		$award_date = strtotime( $current_school_year.'-'.$a_award_year_month.'-'.$a_award_year_day);
		if ( $award_date < $school_year_start )
			$award_date = strtotime( ($current_school_year + 1).'-'.$a_award_year_month.'-'.$a_award_year_day);

		if ( $award_date < $current_date )
			return true;
		else
			return false;
	}
}

// Gets the school year of post
function get_school_year_of_post( $post_id ) {
	$publish_date = get_the_date( 'Y-m-d', $post_id );
	return get_school_year_of_date( $publish_date );
}

// Gets the current school year
function get_current_school_year() {
	$current_year = current_time('Y-m-d');
	return get_school_year_of_date( $current_year );
}

// Gets the school year of a date
function get_school_year_of_date( $date ) {
	$formatted_date = strtotime($date);
	$date_year = date("Y", $formatted_date);
	$date_month = date("m", $formatted_date);
	$school_year_month_start = vp_option('uc_option.school_year_month');

	if ( $date_month < $school_year_month_start )
		return strval($date_year - 1);
	elseif ( $date_month == $school_year_month_start ) {
		if ( current_time('d') < vp_option('uc_option.school_year_day') )
			return strval($date_year - 1);
	}
	return $date_year;
}

// Get list of school years
function get_school_years() {
	$school_year_first_year = vp_option('uc_option.school_year_first_year');
	$current_school_year = get_current_school_year();

	$result = array();
	for ( $i = $school_year_first_year; $i <= $current_school_year; $i++ ) {
		$result[] = array( 'value' => $i, 'label' => $i );
	}
	return $result;
}

// Gets a school year
function get_school_year( $school_year_start, $format = 'array' ) {
	$school_year_end = $school_year_start + 1;
	$school_year_month_start = vp_option('uc_option.school_year_month');
	$school_year_day_start = vp_option('uc_option.school_year_day');
	$school_year_day_start = correct_day( $school_year_start, $school_year_month_start, $school_year_day_start );

	if ( $school_year_day_start == 1 ) {
		$school_year_month_end = ( $school_year_month_start === '1' ? '12' : strval($school_year_month_start - 1) );
		$school_year_day_end = date("t", strtotime($school_year_end.'-'.$school_year_month_end.'-1'));
	} else {
		$school_year_day_end = strval($school_year_day_start - 1);
		$school_year_month_end = $school_year_month_start;
	}

	if ( $format == 'array' ) {
		return array(
			'start' => array(
				'year'  => strval($school_year_start),
				'month' => $school_year_month_start,
				'day'   => $school_year_day_start,
			),
			'end' => array(
				'year'  => strval($school_year_end),
				'month' => $school_year_month_end,
				'day'   => $school_year_day_end,
			)
		);
	} else {
		return array(
			'start' => date( $format, strtotime($school_year_start.'-'.$school_year_month_start.'-'.$school_year_day_start) ),
			'end' => date( $format, strtotime($school_year_end.'-'.$school_year_month_end.'-'.$school_year_day_end) )
		);
	}
}

// Accounts for option day being longer than month
function correct_day( $year, $month, $day ) {
	$first_day_of_month = $year.'-'.$month.'-1';
	$last_day_of_month = date("t", strtotime($first_day_of_month));
	
	if ( $day > $last_day_of_month )
		$day = $last_day_of_month;

	return $day;
}

function add_teacher_to_mailing_list( $teacher_id ) {

	$form = get_convertkit_form( $teacher_id );
	$url = 'https://api.convertkit.com/v3/forms/'.$form.'/subscribe';
	$convertkit_id = convertkit_subscribe( $url, $teacher_id );
	update_post_meta( $teacher_id, 'convertkit_id', $convertkit_id );

	$tags = get_convertkit_tags( $teacher_id );
	foreach ($tags as $tag) {
		$url = 'https://api.convertkit.com/v3/tags/'.$tag.'/subscribe';
		$convertkit_tag_id = convertkit_subscribe( $url, $teacher_id );
	}
	return $convertkit_id;
}

function convertkit_subscribe( $url, $teacher_id ) {
	global $convertkit_api_key;

	$convertkit_id = null;
	$email = get_post_meta( $teacher_id, 'email', true );
	$first_name = get_post_meta( $teacher_id, 'first_name', true );

	$args = array(
		'api_key' => $convertkit_api_key,
		'email' => $email,
		'first_name' => $first_name);

	$curl_options = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => 'Content-Type: application/json',
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POSTFIELDS => $args,
	);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_options);
	$response = json_decode(curl_exec($ch));
	curl_close($ch);
	if (isset($response->subscription->id)) $convertkit_id = $response->subscription->id;
	return $convertkit_id;
}

function get_convertkit_form( $teacher_id ) {
	$form = '235305';
	return $form;
}

function get_convertkit_tags( $teacher_id ) {
	$tags = array();

	$school_id = get_post_meta( $teacher_id, 'school', true );
	if ( get_post_meta( $school_id, 'country', true ) == 'canada' ) $tags[] = '240329'; // Canada users
	if ( get_post_meta( $school_id, 'country', true ) == 'united-states') $tags[] = '240328'; // USA users
	if ( get_post_meta( $school_id, 'non_traditional', true ) ) $tags[] = '240330'; // Homeschool users

	return $tags;
}

function stripe_save_credit_card( $token, $email ) {
	global $stripe_private_api_key;
	\Stripe\Stripe::setApiKey($stripe_private_api_key);

	try {
		$customer = \Stripe\Customer::create(array(
			"email" => $email,
			"source" => $token,
		));
	} catch (Exception $e) {
		return false;
	}

	if (!$customer)
		return false;

	return $customer->id;
}

function stripe_charge_customer( $customer_id, $amount, $currency = 'usd' ) {
	global $stripe_private_api_key;
	\Stripe\Stripe::setApiKey($stripe_private_api_key);

	try {
		$charge = \Stripe\Charge::create(array(
			"amount" => $amount,
			"currency" => $currency,
			"customer" => $customer_id
		));
	} catch (Exception $e) {
		return false;
	}

	return $charge->id;
}

function pretty_price( $price_in_cents, $currency = 'usd' ) {
	switch ($currency) {
		default:
			$symbol = '$';
			break;
	}
	$price = $price_in_cents / 100;
	return $symbol.$price;
}