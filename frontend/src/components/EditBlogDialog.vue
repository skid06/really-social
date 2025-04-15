<template>
  <q-dialog v-model="showDialog" persistent maximized>
    <q-card>
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Edit Blog</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="editedBlog.title"
            filled
            label="Title *"
            :rules="[(val) => !!val || 'Title is required']"
          />

          <q-editor
            v-model="editedBlog.content"
            min-height="300px"
            placeholder="Blog content..."
            :rules="[(val) => !!val || 'Content is required']"
          />

          <!-- Fixed status selection -->
          <q-select
            v-model="editedBlog.status"
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
            <q-btn label="Save" type="submit" color="primary" class="q-ml-sm" :loading="loading" />
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
  name: 'EditBlogDialog',

  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
    blog: {
      type: Object,
      required: true,
    },
  },

  emits: ['update:modelValue', 'blog-updated'],

  setup(props, { emit }) {
    const $q = useQuasar()

    const showDialog = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value),
    })

    const loading = ref(false)
    const editedBlog = ref({
      id: '',
      title: '',
      content: '',
      status: '',
    })

    const statusOptions = [
      { label: 'Published', value: 'published' },
      { label: 'Hidden', value: 'hidden' },
    ]

    // Fixed watch to properly handle status
    watch(
      () => props.blog,
      (newVal) => {
        if (newVal && newVal.id) {
          // Create a new object to avoid reference issues
          editedBlog.value = {
            id: newVal.id,
            title: newVal.title,
            content: newVal.content,
            status: newVal.status, // Make sure this is just the string value
          }

          console.log('Current status:', editedBlog.value.status)
        }
      },
      { deep: true },
    )

    const onSubmit = async () => {
      loading.value = true

      try {
        console.log('Submitting with status:', editedBlog.value.status)

        await axios.put(`/api/blogs/${editedBlog.value.id}`, {
          title: editedBlog.value.title,
          content: editedBlog.value.content,
          status: editedBlog.value.status,
        })

        $q.notify({
          color: 'positive',
          message: 'Blog updated successfully',
          icon: 'check',
        })

        showDialog.value = false
        emit('blog-updated')
      } catch (error) {
        let message = 'Failed to update blog'

        if (error.response && error.response.data && error.response.data.message) {
          message = error.response.data.message
        }

        console.error('Update error:', error)

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
      editedBlog,
      loading,
      statusOptions,
      onSubmit,
    }
  },
})
</script>
