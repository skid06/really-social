<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-page class="flex flex-center">
        <q-card style="width: 350px">
          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Really Social - Register</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="onSubmit" class="q-gutter-md">
              <q-input
                filled
                v-model="name"
                label="Full Name"
                lazy-rules
                :rules="[(val) => !!val || 'Please enter your name']"
              />

              <q-input
                filled
                v-model="email"
                label="Email"
                type="email"
                lazy-rules
                :rules="[
                  (val) => !!val || 'Please enter your email',
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
                :rules="[(val) => !!val || 'Please enter your password']"
              >
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
              </q-input>
              <q-input
                filled
                v-model="confirmPassword"
                label="Confirm Password"
                :type="isPwd ? 'password' : 'text'"
                lazy-rules
                :rules="[
                  (val) => !!val || 'Please confirm your password',
                  (val) => val === password || 'Passwords do not match',
                ]"
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
                  label="Register"
                  type="submit"
                  color="primary"
                  class="full-width"
                  :loading="loading"
                />
              </div>
              <div>
                <q-btn flat label="Already signup? Login" @click="goToLogin" />
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
  name: 'RegisterPage',

  setup() {
    const $q = useQuasar()
    const router = useRouter()

    const name = ref('')
    const email = ref('')
    const password = ref('')
    const confirmPassword = ref('')
    const isPwd = ref(true)
    const loading = ref(false)

    const onSubmit = async () => {
      loading.value = true

      try {
        const response = await axios.post('/api/register', {
          name: name.value,
          email: email.value,
          password: password.value,
          password_confirmation: confirmPassword.value,
        })

        localStorage.setItem('token', response.data.token)
        const userStore = useUserStore()
        userStore.setUser({ user: response.data.user })

        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

        $q.notify({
          color: 'positive',
          message: 'Registration successful',
          icon: 'check',
        })

        router.push('/blogs')
      } catch (error) {
        let message = 'Registration failed'

        if (error.response?.data?.message) {
          message = error.response.data.message
        }

        $q.notify({
          color: 'negative',
          message,
          icon: 'error',
        })
      } finally {
        loading.value = false
      }
    }
    const goToLogin = () => {
      router.push('/login')
    }
    return {
      name,
      email,
      password,
      confirmPassword,
      isPwd,
      loading,
      onSubmit,
      goToLogin,
    }
  },
})
</script>
