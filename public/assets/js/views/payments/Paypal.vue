<template>
  <div>
    <!-- crear un div para paypal -->
    <!-- button -->
    <!-- overload -->
    
    <base-loader v-if="loadindPayment"  />
    <div ref="paypal"></div>

  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    props:{
        formData: {
            type: Object,
            required: true
        },
        codePayment: {
            type: String,
            required: true
        },
        invoice_number: {
            type: String,
            required: true
        },
        customer:{
          type: Object,
          required: true
        }
    },
    data: () => ({
      public_key: '',
      currency: '',
      loadindPayment: false,
    }),
   async mounted() {
     await this.getPaypalKey()
      await this.loadScriptPaypal();
  },
  methods: {
    ...mapActions('paypal', [ 'getPublicKeyPaypal', 'savePaypalPayment']),
    async getPaypalKey() {
      try {
        this.loadindPayment = true
        const res = await this.getPublicKeyPaypal();
        if (res) {
          this.public_key = res.data.public_key
          this.currency = res.data.currency
        };
        
      } catch (error) {
        console.log(error)
      }finally{
        this.loadindPayment = false
      }
    },

    loadScriptPaypal() {
      const script = document.createElement("script");
      script.src = `https://www.paypal.com/sdk/js?client-id=${this.public_key}`;
      script.addEventListener("load", this.loadScriptPaypalButton);
      document.body.appendChild(script);
    },
    loadScriptPaypalButton() {
      paypal.Buttons({
          fundingSource: paypal.FUNDING.PAYPAL,
        createOrder: (data, actions) => {
            
            if(this.formData.amount > 0){
              return actions.order.create({
                payer: {
                  email_address: this.customer.email,
                  name: {
                      given_name: this.customer.name,
                  }
               },
                purchase_units: [{
                  amount: {
                    currency_code: this.currency,
                    value: this.formData.amount / 100
                  },
                  reference_id: this.codePayment,
                  custom_id: this.formData.customcode,
                  // description: this.formData.description,
                  // soft_descriptor: "Payment for " + this.formData.description,
                  invoice_id: this.invoice_number,
                }]
              });
            }
        },
        onApprove: async (data, actions) => {
          try {
             const order = await actions.order.capture();
            // transacctions id
            const dataPaypal = {
              transaction_id: order.purchase_units[0].payments.captures[0].id,
              email_address: order.payer.email_address,
              amount: order.purchase_units[0].amount.value,
              currency: order.purchase_units[0].amount.currency_code,
              country_code: order.payer.address.country_code,
              payment_status: order.status,
              // payment_id: order.id,
              create_time: order.create_time,
            }

            this.loadindPayment = false;
            const res = await this.savePaypalPayment(dataPaypal);
            this.$emit('paypalSuccess', res.id);
            
          } catch (error) {
            // mostrar error
             window.toastr['error'](error.response.data.message);
             console.log(error);
          }finally{
            this.loadindPayment = false;
          }

        },

        onError: err => {
            console.log(err);
          }
      }).render(this.$refs.paypal);
    }
  },
}
</script>

<style>
</style>