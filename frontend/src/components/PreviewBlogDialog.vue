<template>
  <q-dialog v-model="showDialog" persistent>
    <q-card style="width: 700px; max-width: 80vw">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Blog Preview</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div class="text-h5 q-mb-md">{{ blog.title }}</div>

        <div class="q-mb-md">
          <q-badge :color="blog.status === 'published' ? 'green' : 'grey'">
            {{ blog.status }}
          </q-badge>
          <span class="q-ml-sm text-grey">
            {{ blog.created_at ? new Date(blog.created_at).toLocaleString() : '' }}
          </span>
        </div>

        <div class="blog-content" v-html="blog.content"></div>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="Close" color="primary" v-close-popup />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
  name: 'PreviewBlogDialog',

  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
    blog: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },

  emits: ['update:modelValue'],

  setup(props, { emit }) {
    const showDialog = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value),
    })

    return {
      showDialog,
    }
  },
})
</script>

<style>
.blog-content img {
  max-width: 100%;
}
</style>
