<template>
  <div id="stripe-buy-button-container">
    <button class="primary-button">Pay now</button>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js';
import { mapActions, mapGetters } from 'vuex'
export default {
  created() {
    this.getStripeId()
  },
  methods: {
    // ...mapActions('paypal', ['getPublicKeyPaypal', 'savePaypalPayment']),
    ...mapActions('stripes', ['getStripeSessionId']),

    async getStripeId() {
      console.log("joining here")
      try {
        this.loadindPayment = true
        const res = await this.getStripeSessionId()

        if (res) {
          console.log("STRIPE SESSION ID", res);
          const stripePromise = loadStripe('pk_test_51PGlSmKXS6iA6Rk6dkrhos0H1yzM6N99c2F5Kn5rWEpNyqBfUfM9KLqdidelfpzoCeioPpvHcC3hKGFYqgNqVJda00AaeF6pFh'); // Replace with your publishable key
          console.log("stripe promise", stripePromise)
          stripePromise.then((stripe) => {
            console.log("stripe", res.request_id)
            const redirectPromise = stripe.redirectToCheckout({
              // Replace these with your actual payment details
              sessionId: res.request_id, // Replace with your Stripe Session ID
            });

            redirectPromise.then((result) => {
              // Handle successful redirection (optional)
              console.log('Successfully redirected to checkout:', result);
            }).catch((error) => {
              // Handle redirection errors (optional)
              console.error('Error during redirection:', error);
            });
          });
          this.public_key = res.data.public_key
          this.currency = res.data.currency
        }
      } catch (error) {
      } finally {
        this.loadindPayment = false
      }
    },
  },
};
</script>

<style scoped>
/* Optional styling for your Buy Button container */
</style>
