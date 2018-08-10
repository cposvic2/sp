<?php

?><script src="https://js.stripe.com/v3/"></script>
<style>
.StripeElement {
	background-color: white;
	padding: 8px 12px;
	border-radius: 4px;
	border: 1px solid transparent;
	box-shadow: 0 1px 3px 0 #e6ebf1;
	-webkit-transition: box-shadow 150ms ease;
	transition: box-shadow 150ms ease;
}

.StripeElement--focus {
	box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
	border-color: #fa755a;
}

.StripeElement--webkit-autofill {
	background-color: #fefde5 !important;
}
</style>
<p>
	<div class="form-row">
		<label for="card-element"><h3>Credit or debit card</h3>This step is for card verification only. You are not charged until you hit "Sponsor Class".</label>
		<div id="card-element"></div>
	</div>
	<div class="alert-container stripe-alert-container" role="alert"></div>
	<input type="hidden" name="stripe_token" id="stripe_token" value="<?php echo $stripe_token; ?>" data-required="true">
</p>
<p>
	<button type=button id="credit-card-verify">Verify Credit Card</button>
</p>
<script type="text/javascript">
(function($) {
	<?php global $stripe_publishable_api_key; ?>
	var stripe = Stripe('<?php echo $stripe_publishable_api_key; ?>');

	var elements = stripe.elements();

	var style = {
		base: {
			color: '#32325d',
			lineHeight: '24px',
			fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
			fontSmoothing: 'antialiased',
			fontSize: '16px',
			'::placeholder': {
				color: '#aab7c4'
			}
		},
		invalid: {
			color: '#fa755a',
			iconColor: '#fa755a'
		}
	};

	var card = elements.create('card', {style: style});
	var alertContainer = jQuery('alert-container');

	card.mount('#card-element');

	card.addEventListener('change', function(event) {
		if (event.error) {
			$(alertContainer).html('<div class="alert alert-error">'+event.error.message+'</div>').slideDown();
		} else {
			$(alertContainer).html('');
		}
	});

	$('#credit-card-verify').click(function(event) {
		event.preventDefault();
		stripe.createToken(card).then(function(result) {
			if (result.error) {
				$(alertContainer).html('<div class="alert alert-success">'+result.error.message+'</div>');
			} else {
				$('input[name=stripe_token]').val(result.token.id);
				var worked = display_alert( 'Your card information was verified successfully', 'success', null, 'stripe-alert-container' );
				console.log(worked);				
				$('input[type=submit]').prop( "disabled", false );
			}
		});
	});
})(jQuery);

</script>