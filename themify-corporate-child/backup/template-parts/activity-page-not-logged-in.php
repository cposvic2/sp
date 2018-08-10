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
		<div class="required-materials"><?php echo preg_replace('#<a.*?>(.*?)</a>#i', '\1', $required_materials) ?></div>
	<?php } ?>
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
	<?php 
	$a_activity_page_signup_link = vp_option('uc_option.a_activity_page_signup_link');
	$signup_pages = get_pages( array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-signup.php'
		));
		if ( $signup_pages )
			$signup_page = get_permalink( $signup_pages[0] );
		else
			$signup_page = home_url(); ?>

	<p><a href="<?php echo $signup_page; ?>"><button><?php echo $a_activity_page_signup_link; ?></button></a></p>
</div><!-- /.entry-content -->