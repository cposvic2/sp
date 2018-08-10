<?php 
$sponsorship = get_uc_sponsorship_for_class( $active_class );
?>
<?php if (!!$sponsorship): 
	$sponsor_user_id = get_post_meta( $sponsorship['value'], 'user_id', true );
	if (!!$sponsor_user_id) {
		$sponsor = get_userdata( $sponsor_user_id );
		$first_name = $sponsor->first_name; 
	} else {
		$first_name = get_post_meta( $sponsorship['value'], 'sponsor_name', true );
	}
?>
<div class="sponsorship-banner">Your class is sponsored through a generous donation from <?php echo $first_name; ?></div>
<?php else: ?>
<?php endif; ?>