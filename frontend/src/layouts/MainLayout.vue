<template>
  <q-layout view="hHh lpR lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title> Really Social Blog App </q-toolbar-title>

        <q-space />

        <q-btn flat dense round icon="logout" @click="handleLogout">
          <q-tooltip>Logout</q-tooltip>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item-label header> Hi, {{ userStore.user?.user.name }} </q-item-label>

        <EssentialLink v-for="link in linksList" :key="link.title" v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from 'src/stores/user'
import EssentialLink from 'components/EssentialLink.vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import axios from 'axios'

const userStore = useUserStore()
const router = useRouter()
const $q = useQuasar()

const linksList = [
  {
    title: 'Blogs',
    icon: 'pages',
    link: '/blogs',
  },
]

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

async function handleLogout() {
  try {
    await axios.post('/api/logout')

    userStore.clearUser()

    $q.notify({
      type: 'info',
      message: 'Logged out successfully',
      icon: 'logout',
    })

    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    $q.notify({
      type: 'negative',
      message: 'Failed to logout. Please try again.',
      icon: 'error',
    })
  }
}

onMounted(() => {
  userStore.loadUserFromLocalStorage()
})
</script>
