<template>
  <sw-input-group :label="$t('authorize.date')" required>
    <div class="flex w-full">
      <sw-select
        placeholder="MM"
        class="mr-1"
        :tabindex="1"
        :searchable="true"
        :allow-empty="false"
        v-model="dateExpirationMonth"
        :options="monthsOptions"
        :invalid="invalid"
      />

      <sw-select
        placeholder="YYYY"
        class="ml-1"
        :tabindex="1"
        :searchable="true"
        :allow-empty="false"
        v-model="dateExpirationYear"
        :options="yearsOptions"
        :invalid="invalid"
      />
    </div>
  </sw-input-group>
</template>

<script>
export default {
  // props de value
  props: {
    value: {
      required: true,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  computed: {
    dateExpirationYear: {
      get() {
        //   si es typeof  fecha retorna el año
        if (typeof this.value === 'object') {
          return this.value.getFullYear()
        } else {
          return this.value.split('-')[0]
        }
      },
      set(year) {
        const currentYear = new Date().getFullYear()
        if (currentYear == year) {
          this.$emit('input', `${year}-${this.monthsOptions[0]}`)
        } else {
          this.$emit(
            'input',
            `${year}-${this.dateExpirationMonth || this.monthsOptions[0]}`
          )
        }
      },
    },
    dateExpirationMonth: {
      get() {
        //   si es typeof fecha retorna el mes
        if (typeof this.value === 'object') {
          return this.value.getMonth() + 1 + 1
        } else {
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
      const yearSelect =
        typeof this.value === 'object'
          ? this.value.getFullYear()
          : this.value.split('-')[0]
      const currentMonth =
        yearSelect == new Date().getFullYear()
          ? new Date().getMonth() + 1 + 1
          : 1

      for (let i = 1; i <= 12; i++) {
        months.push(i < 10 ? `0${i}` : `${i}`)
      }

      // Agregar console.log para verificar los meses
     // console.log('Meses disponibles:', months)

      return months
    },
  },
}
</script>

<style>
</style>