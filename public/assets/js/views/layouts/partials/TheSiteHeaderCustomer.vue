<template>
  <header
    class="
      fixed
      top-0
      left-0
      z-40
      flex
      items-center
      justify-between
      w-full
      px-4
      py-3
      md:h-16 md:px-8
      bg-gradient-to-r
      from-primary-500
      to-primary-400
    "
  >
    <a
      href="/admin/dashboard"
      class="
        float-none
        text-lg
        not-italic
        font-black
        tracking-wider
        text-white
        brand-main
        md:float-left
        font-base
      "
    >
      <img
        v-if="previewLogo"
        id="logo-white"
        :src="previewLogo"
        class="hidden h-6 md:block"
      />
   <!--     <img
        v-else
        id="logo-white"
        src="/assets/img/logo-corebill.png"
        alt="Crater Logo"
        class="hidden h-6 md:block"
      />-->
      <img
        v-if="previewLogo"
        id="logo-mobile"
        :src="previewLogo"
        class="block h-8 md:hidden"
      />
  <!--      <img
        v-else
        id="logo-mobile"
        src="/assets/img/logo-corebill.png"
        alt="Crater Logo"
        class="block h-8 md:hidden"
      />-->
    </a>

    <ul class="float-right h-8 m-0 list-none md:h-9">
      <a
        :class="{ 'is-active': isSidebarOpen }"
        href="#"
        class="
          flex
          float-left
          p-1
          ml-3
          overflow-visible
          text-sm text-black
          ease-linear
          bg-white
          border-0
          rounded
          cursor-pointer
          md:hidden md:ml-0
          hamburger hamburger--arrowturn
        "
        @click="toggleSidebar"
      >
        <div class="relative inline-block w-6 h-6">
          <div class="block hamburger-inner top-1/2" />
        </div>
      </a>

      <li class="relative block float-left ml-2">
        <sw-dropdown>
          <a
            slot="activator"
            href="#"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            class="inline-block text-sm text-black bg-white rounded-sm avatar"
          >
            <img
              :src="profilePicture"
              alt="Avatar"
              class="w-8 h-8 rounded-sm md:h-9 md:w-9"
            />
          </a>

          <sw-dropdown-item
            tag-name="router-link"
            to="/customer/payment-accounts"
          >
            <credit-card-icon class="w-4 h-4 mr-2 text-gray-600" />
            {{ $t('navigation.payment_accounts') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            tag-name="router-link"
            to="/customer/tickets"
          >
            <document-text-icon class="w-4 h-4 mr-2 text-gray-600" />
            {{ $t('navigation.tickets') }}
          </sw-dropdown-item>

          <sw-dropdown-item @click="logout">
            <logout-icon class="w-4 h-4 mr-2 text-gray-600" />
            {{ $t('navigation.logout') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </li>
    </ul>
  </header>
</template>

<script type="text/babel">
import { mapGetters, mapActions } from 'vuex'
import {
  PlusIcon,
  DocumentTextIcon,
  DocumentIcon,
  UserIcon,
  CogIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'

import { LogoutIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    PlusIcon,
    DocumentTextIcon,
    DocumentIcon,
    UserIcon,
    CogIcon,
    LogoutIcon,
    CreditCardIcon,
  },
  data() {
    return {
      previewLogo: null,
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters(['isSidebarOpen']),
    profilePicture() {
      if (
        this.currentUser &&
        this.currentUser.avatar !== null &&
        this.currentUser.avatar !== 0
      ) {
        return this.currentUser.avatar
      } else {
        return '/images/default-avatar.jpg'
      }
    },
  },
  created() {
    //this.fetchCurrentUser()
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('user', ['fetchCurrentUser']),
    ...mapActions('auth', ['logout']),
    ...mapActions('modal', ['openModal']),
    ...mapActions(['toggleSidebar']),
    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()
      if (response.data.user) {
        this.previewLogo = response.data.user.company.logo
      }
      this.isRequestOnGoing = false
    },
  },
}
</script>
<style lang="scss">
.hamburger {
  transition-property: opacity, filter;
  transition-duration: 0.15s;
}
.hamburger-inner {
  top: 50%;
  left: 4.5px;
  right: 4.5px;
}
.hamburger-inner,
.hamburger-inner::before,
.hamburger-inner::after {
  height: 2px;
  background-color: black;
  border-radius: 2px;
  position: absolute;
  transition-property: transform;
  transition-duration: 0.15s;
  transition-timing-function: ease;
}

.hamburger-inner::before,
.hamburger-inner::after {
  content: '';
  display: block;
  width: 100%;
}

.hamburger-inner::before {
  top: -5px;
}

.hamburger-inner::after {
  bottom: -5px;
}

.hamburger--arrowturn.is-active .hamburger-inner {
  transform: rotate(-180deg);
}

.hamburger--arrowturn.is-active .hamburger-inner::before {
  transform: translate3d(5px, 3px, 0) rotate(45deg) scale(0.5, 1);
}

.hamburger--arrowturn.is-active .hamburger-inner::after {
  transform: translate3d(5px, -3px, 0) rotate(-45deg) scale(0.5, 1);
}
</style>
