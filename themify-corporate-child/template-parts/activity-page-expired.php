<div class="entry-content" itemprop="articleBody">
	<?php 

	$activity_summary = get_post_meta( get_the_ID(), 'activity_summary', true );
	if ( $activity_summary ) { ?>
		<h3>Activity Summary</h3>
		<p><?php echo $activity_summary ?></p>
	<?php }
	$video_links = get_post_meta( get_the_ID(), 'video_engagement_links', true );
	if ( !empty($video_links) ) {
		?><h3>Video Engagement Links</h3><?php
		$video_engagement_text = get_post_meta( get_the_ID(), 'video_engagement_text', true );
		if ( !empty($video_engagement_text) ) {
			?><p><?php echo $video_engagement_text; ?></p><?php
		}
		$video_links = explode(';', $video_links);
		foreach ($video_links as $video_link) {

			$video_link = trim(str_replace('watch?v=', 'embed/', $video_link));
			if (!empty($video_link)) {
				?>
				<iframe title="YouTube video player" class="youtube-player" type="text/html" width="640" height="390" src="<?php echo $video_link ?>" frameborder="0" allowFullScreen></iframe>
				<?php 
			}
		}
	}
	$required_materials = get_post_meta( get_the_ID(), 'required_materials', true );
	if ( $required_materials ) { ?>
		<h3>Required Materials</h3>
		<div class="required-materials"><?php echo $required_materials ?></div>
	<?php }
	$activity_handout = get_post_meta( get_the_ID(), 'activity_handout', true );
	$activity_exam = get_post_meta( get_the_ID(), 'activity_exam', true );
	$activity_answer_key = get_post_meta( get_the_ID(), 'activity_answer_key', true );
	$activity_additional = get_post_meta( get_the_ID(), 'activity_additional', true );
	 ?>
	<h3>Activity Materials</h3>
	<div class="activity-materials">
		<?php
		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-activity-sheet.php'
		));
		if ( $pages ) {
			echo '<p><a href="'.get_permalink ($pages[0]).'?a='.get_the_id().'" target="_blank" />Activity Sheet</a></p>';
		}
		if ( $activity_handout ) {
			echo '<p><a href="'.$activity_handout.'" target="_blank" />Activity Handout</a></p>';
		}
		if ( $activity_exam ) {
			echo '<p><a href="'.$activity_exam.'" target="_blank" />Activity Exam</a></p>';
		}
		if ( $activity_answer_key ) {
			echo '<p><a href="'.$activity_answer_key.'" target="_blank" />Activity Answer Key</a></p>';
		}
		if ( $activity_additional ) {
			echo '<p><a href="'.$activity_additional.'" target="_blank" />Additional Activity Material</a></p>';
		}
		?>
	</div>
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

<div class="activity-class-data">
	<h3>Class Signup Details</h3>


	<div class="activity-class-data-details">
		<?php
		$signup_id = get_signup( $active_class, get_the_id() );
		$signup_date = DateTime::createFromFormat( 'm-d-Y', get_post_meta( $signup_id, 'signup_date', true ) );
		$expiration_length = vp_option('uc_option.activity_expiration_length');
		$activity_text = str_replace ( '!days!' , $expiration_length, vp_option('uc_option.a_expired_text') );
		$expiration_length = new DateInterval('P'.$expiration_length.'D');
		$expiration_length = new DateInterval('P'.vp_option('uc_option.activity_expiration_length').'D');
		$expiration_date = clone $signup_date;
		$expiration_date->add( $expiration_length );
		echo '
			<p>'.$activity_text.'</p>
			<p><strong>Signup Date:</strong> '.$signup_date->format('M j Y').'<br>
			<strong>Expiration Date:</strong> '.$expiration_date->format('M j Y').'</p>';
		?>

	</div>
</div><!-- /.activity-class-data -->