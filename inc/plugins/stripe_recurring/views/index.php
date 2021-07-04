<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
var stripe = Stripe("<?php _e( get_option("stripe_publishable_key", "") )?>");
stripe.redirectToCheckout({
  // Make the id field from the Checkout Session creation API response
  // available to this file, so you can provide it as parameter here
  // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
  sessionId: '<?php _e( $checkout_session_id )?>'
}).then(function (result) {
  // If `redirectToCheckout` fails due to a browser or network
  // error, display the localized error message to your customer
  // using `result.error.message`.
});
</script>