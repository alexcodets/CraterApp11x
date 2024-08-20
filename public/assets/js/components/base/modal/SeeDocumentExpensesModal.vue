<template>
  <div class="relative" >
    <div class="flex flex-col h-full">
      <iframe
        style="min-height:500px"
        v-if="props.media.typeFile == 'pdf'"
        :src="props.media.base64"
        width="100%"
        height="100%"
      >
      </iframe>
      <img
        v-if="ifImage"
        :src="props.media.base64"
        class="m-2 rounded-md"
        style="max-height: 80%; animation: fadeIn 2s ease"
      />
      <div v-if="!ifImage && props.media.typeFile != 'pdf'" class="flex flex-wrap justify-center items-center text-center p-3">
        
        <img :src="imageExt(props.media.typeFile)" alt="ext">
        <h2 class="w-full text-xl font-bold text-primary mt-3">{{ $t('expenses.no_preview')}}</h2>
      </div>

    </div>

  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  computed: {
    ...mapGetters('modal', ['refreshData', 'props']),
    ifImage(){
      return ['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(this.props.media.typeFile)
    },
  },

  methods: {
    ...mapActions('modal', ['closeModal']),

    getCustomLabel({ driver, name }) {
      return `${name} — [${driver}]`
    },

    imageExt(ext){
      return `/images/icon-ext/${ext}.png`;
    },
  },
}
</script>
