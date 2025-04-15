<template>
  <q-dialog v-model="showDialog" persistent maximized>
    <q-card>
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Add New Blog</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="blog.title"
            filled
            label="Title *"
            :rules="[(val) => !!val || 'Title is required']"
          />

          <q-editor
            v-model="blog.content"
            min-height="300px"
            placeholder="Blog content..."
            :rules="[(val) => !!val || 'Content is required']"
          />

          <!-- Fixed status selection -->
          <q-select
            v-model="blog.status"
            :options="statusOptions"
            option-value="value"
            option-label="label"
            map-options
            emit-value
            filled
            label="Status *"
            :rules="[(val) => !!val || 'Status is required']"
          />

          <div class="flex justify-end q-mt-md">
            <q-btn label="Cancel" color="negative" flat v-close-popup />
            <q-btn
              label="Submit"
              type="submit"
              color="primary"
              class="q-ml-sm"
              :loading="loading"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import { defineComponent, ref, watch, computed } from 'vue'
import { useQuasar } from 'quasar'
import axios from 'axios'

export default defineComponent({
  name: 'AddBlogDialog',

  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
  },

  emits: ['update:modelValue', 'blog-added'],

  setup(props, { emit }) {
    const $q = useQuasar()

    const showDialog = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value),
    })

    const loading = ref(false)
    const blog = ref({
      title: '',
      content: '',
      status: 'hidden',
    })

    const statusOptions = [
      { label: 'Published', value: 'published' },
      { label: 'Hidden', value: 'hidden' },
    ]

    // Reset form when dialog is opened
    watch(
      () => props.modelValue,
      (newVal) => {
        if (newVal) {
          blog.value = {
            title: '',
            content: '',
            status: 'hidden',
          }
        }
      },
    )

    const onSubmit = async () => {
      loading.value = true

      try {
        console.log('Submitting new blog with status:', blog.value.status)

        await axios.post('/api/blogs', blog.value)

        $q.notify({
          color: 'positive',
          message: 'Blog created successfully',
          icon: 'check',
        })

        showDialog.value = false
        emit('blog-added')
      } catch (error) {
        let message = 'Failed to create blog'

        if (error.response && error.response.data && error.response.data.message) {
          message = error.response.data.message
        }

        console.error('Create error:', error)

        $q.notify({
          color: 'negative',
          message: message,
          icon: 'error',
        })
      } finally {
        loading.value = false
      }
    }

    return {
      showDialog,
      blog,
      loading,
      statusOptions,
      onSubmit,
    }
  },
})
</script>
