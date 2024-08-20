<template>
  <sw-card v-if="getStickyNotes.length > 0" class="flex flex-col mt-8">
    <div class="col-span-12">
      <p
        class="text-gray-500 uppercase sw-section-title"
      >
        {{ $t('customers.sticky_notes') }}
      </p>

      <div
        class="grid grid-cols-1 gap-3 mt-5"
      >
        <div v-for="(note, index) in getStickyNotes" :key="index" class="flex">
          <p
            class="mb-1 text-sm font-normal leading-5 non-italic text-primary-800"
          >
            <router-link
              :to="''"
              class="font-medium text-primary-500"
            >
              {{ note.summary }}
            </router-link>
          </p>
        </div>
      </div>
  </div>
  </sw-card>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  data() {
    return {
      notes: [],
    }
  },
  computed: {
    ...mapGetters('customerProfile', ['loggedInCustomer']),

    getStickyNotes() {
      if (this.notes) {
        let notes = this.notes
        return notes
          .filter((note) => note.stiky === 1)
          .sort((a, b) => b.id - a.id)
          .slice(0, 10)
      }
      return []
    }

  },
  created() {
    this.notes = this.loggedInCustomer.customer.notes.map((note) => {
      return {
        id: note.id,
        summary: note.summary,
        stiky: note.stiky,
        user_id: note.user_id,
      }
    })
  },
}
</script>

<style scoped>

</style>