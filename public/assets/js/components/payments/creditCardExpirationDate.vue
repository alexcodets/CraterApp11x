<template>
    <sw-input-group
      :label="$t('authorize.date')"
      required
    >
      <div class="flex w-full">
        <sw-select
            placeholder="MM"
            class="mr-1"
            :searchable="true"
            :allow-empty="false"
            v-model="dateExpirationMonth"
            :options="monthsOptions"
        />

        <sw-select
            placeholder="YYYY"
            class="ml-1"
            :searchable="true"
            :allow-empty="false"
            v-model="dateExpirationYear"
            :options="yearsOptions"
        />
      </div>
    </sw-input-group>
</template>

<script>
export default {
    // props de value
    props: {
        value: { required: true },
    },
  computed: {
    dateExpirationYear: {
      get() {
        //   si es typeof  fecha retorna el año
        if (typeof this.value === 'object') {
          return this.value.getFullYear();
        }else{
          return this.value.split('-')[0]
        }        
      },
      set(year) {
        const currentYear = new Date().getFullYear()        
        if (currentYear == year) {
            this.$emit('input', `${year}-${this.monthsOptions[0]}`)
        }else{
            this.$emit('input', `${year}-${ this.dateExpirationMonth || this.monthsOptions[0]}`)
        }
      },
    },
    dateExpirationMonth: {
      get() {
        //   si es typeof fecha retorna el mes
        if (typeof this.value === 'object') {
          return this.value.getMonth() + 1;
        }else{
          return this.value.split('-')[1]
        }        
      },
      set(month) {
        this.$emit('input', `${this.dateExpirationYear}-${month}`)
      },
    },
    // generador de los 15 años para el select de fecha de expiración de la tarjeta de crédito
    yearsOptions() {
      const years = []
      const currentYear = new Date().getFullYear()
      for (let i = currentYear; i < currentYear + 16; i++) {
        years.push(`${i}`)
      }
      return years
    },
    // generador de los 12 meses del año formato MM
    monthsOptions() {
      const months = []
      const yearSelect = typeof this.value === 'object' ? this.value.getFullYear() : this.value.split('-')[0]
      const currentMonth =
        yearSelect == new Date().getFullYear() ? new Date().getMonth() + 1 : 1
      for (let i = currentMonth; i <= 12; i++) {
        months.push(i < 10 ? `0${i}` : `${i}`)
      }
      return months
    },
  },
}
</script>

<style>
</style>