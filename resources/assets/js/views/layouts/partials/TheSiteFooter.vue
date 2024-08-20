<template>
  <footer
    class="fixed bottom-0 flex items-center justify-end w-full h-10 pt-2 pr-8 text-sm font-normal text-gray-700 bg-white">
      <div v-if="footer.footer_text_value">
        <p class="mb-1 ml-12">{{ footer.footer_text_value }}
          <a target="_blank" :href="footer.footer_url_value"> {{ footer.footer_url_name }} </a>
          <span v-if="(footer.current_year == 1)">
            © {{ footer_year }}
          </span>
        </p>
      </div>
      <p class="mb-1 ml-12" v-else>{{ $t('layout_login.copyright_crater') }}</p>
  </footer>
</template>
<!-- props footer -->
<script>
export default {
  data(){
    return {
      footer_year: new Date().getFullYear()
    }
  },
  name: 'footer',
  props: {
    footer: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
