<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-page class="flex flex-center">
        <q-card style="width: 350px">
          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Really Social - Login</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="onSubmit" class="q-gutter-md">
              <q-input
                filled
                v-model="email"
                label="Email"
                type="email"
                lazy-rules
                :rules="[
                  (val) => (val && val.length > 0) || 'Please enter your email',
                  (val) =>
                    /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(val) ||
                    'Please enter a valid email',
                ]"
              />

              <q-input
                filled
                v-model="password"
                label="Password"
                :type="isPwd ? 'password' : 'text'"
                lazy-rules
                :rules="[(val) => (val && val.length > 0) || 'Please enter your password']"
              >
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
              </q-input>

              <div>
                <q-btn
                  label="Login"
                  type="submit"
                  color="primary"
                  class="full-width"
                  :loading="loading"
                />
              </div>
              <div>
                <q-btn flat label="Don't have an account? Register" @click="goToRegistration" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { useUserStore } from 'src/stores/user'
import axios from 'axios'

export default defineComponent({
  name: 'LoginPage',

  setup() {
    const $q = useQuasar()
    const router = useRouter()

    const email = ref('')
    const password = ref('')
    const isPwd = ref(true)
    const loading = ref(false)

    const onSubmit = async () => {
      loading.value = true

      try {
        const response = await axios.post('/api/login', {
          email: email.value,
          password: password.value,
        })

        localStorage.setItem('token', response.data.token)
        const userStore = useUserStore()
        userStore.setUser({
          user: response.data.user,
        })

        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

        $q.notify({
          color: 'positive',
          message: 'Login successful',
          icon: 'check',
        })

        router.push('/blogs')
      } catch (error) {
        let message = 'Login failed'

        if (error.response && error.response.data && error.response.data.message) {
          message = error.response.data.message
        }

        $q.notify({
          color: 'negative',
          message: message,
          icon: 'error',
        })
      } finally {
        loading.value = false
      }
    }

    const goToRegistration = () => {
      router.push('/registration')
    }

    return {
      email,
      password,
      isPwd,
      loading,
      onSubmit,
      goToRegistration,
    }
  },
})
</script>
