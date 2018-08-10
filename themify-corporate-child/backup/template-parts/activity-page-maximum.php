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
	<?php } ?>
	<h3>Activity Materials</h3>
	<div class="activity-materials">
		<?php
		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-activity-sheet.php'
		));
		if ( $pages ) {
			echo '<a href="'.get_permalink ($pages[0]).'?a='.get_the_id().'" target="_blank" />Activity Sheet</a>';
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
			$max_signups = vp_option('uc_option.signup_maximum');
			$activity_text = str_replace ( '!signups!' , $max_signups, vp_option('uc_option.a_max_text') );
			echo '<p>'. $activity_text .'</p>';
		?>

	</div>
</div><!-- /.activity-class-data -->