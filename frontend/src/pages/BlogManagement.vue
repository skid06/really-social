<template>
  <q-layout view="hHh Lpr lFf">
    <q-page-container>
      <q-page padding>
        <div class="q-pa-md">
          <div class="row q-mb-md items-center">
            <div class="col-grow">
              <q-input
                v-model="search"
                debounce="300"
                filled
                placeholder="Search by title"
                @update:model-value="fetchBlogs"
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>
            <div class="col-auto q-ml-md">
              <q-btn color="primary" icon="add" label="Add Blog" @click="openAddDialog" />
            </div>
          </div>

          <!-- Blog Table -->
          <q-table
            :rows="blogs"
            :columns="columns"
            row-key="id"
            :loading="loading"
            v-model:pagination="pagination"
            @request="onRequest"
          >
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                <q-td key="title" :props="props">{{ props.row.title }}</q-td>
                <q-td key="author" :props="props">
                  {{ props.row.user?.name || 'N/A' }}
                </q-td>
                <q-td key="status" :props="props">
                  <q-badge :color="props.row.status === 'published' ? 'green' : 'grey'">
                    {{ props.row.status }}
                  </q-badge>
                </q-td>
                <q-td key="created_at" :props="props">
                  {{ new Date(props.row.created_at).toLocaleString() }}
                </q-td>
                <q-td key="actions" :props="props">
                  <q-btn-dropdown flat color="primary" icon="more_vert">
                    <q-list>
                      <q-item clickable v-close-popup @click="openEditDialog(props.row)">
                        <q-item-section avatar>
                          <q-icon name="edit" />
                        </q-item-section>
                        <q-item-section>Edit</q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="openPreviewDialog(props.row)">
                        <q-item-section avatar>
                          <q-icon name="visibility" />
                        </q-item-section>
                        <q-item-section>Preview</q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="changeStatus(props.row)">
                        <q-item-section avatar>
                          <q-icon
                            :name="
                              props.row.status === 'published' ? 'visibility_off' : 'visibility'
                            "
                          />
                        </q-item-section>
                        <q-item-section>
                          {{ props.row.status === 'published' ? 'Hide' : 'Publish' }}
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="archiveBlog(props.row)">
                        <q-item-section avatar>
                          <q-icon name="archive" />
                        </q-item-section>
                        <q-item-section>Archive</q-item-section>
                      </q-item>
                    </q-list>
                  </q-btn-dropdown>
                </q-td>
              </q-tr>
            </template>
          </q-table>
        </div>
      </q-page>
    </q-page-container>

    <add-blog-dialog v-model="showAddDialog" @blog-added="fetchBlogs" />

    <edit-blog-dialog v-model="showEditDialog" :blog="selectedBlog" @blog-updated="fetchBlogs" />

    <preview-blog-dialog v-model="showPreviewDialog" :blog="selectedBlog" />
  </q-layout>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AddBlogDialog from 'components/AddBlogDialog.vue'
import EditBlogDialog from 'components/EditBlogDialog.vue'
import PreviewBlogDialog from 'components/PreviewBlogDialog.vue'

export default defineComponent({
  name: 'BlogManagementPage',

  components: {
    AddBlogDialog,
    EditBlogDialog,
    PreviewBlogDialog,
  },

  setup() {
    const $q = useQuasar()
    const router = useRouter()

    const blogs = ref([])
    const loading = ref(false)
    const search = ref('')
    const selectedBlog = ref({})

    const showAddDialog = ref(false)
    const showEditDialog = ref(false)
    const showPreviewDialog = ref(false)

    const pagination = ref({
      sortBy: 'created_at',
      descending: true,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
    })

    const columns = [
      { name: 'id', align: 'left', label: 'ID', field: 'id', sortable: true },
      { name: 'title', align: 'left', label: 'Title', field: 'title', sortable: true },
      {
        name: 'author',
        align: 'left',
        label: 'Author',
        field: (row) => row.user?.name || 'N/A',
        sortable: true,
      },
      { name: 'status', align: 'left', label: 'Status', field: 'status', sortable: true },
      {
        name: 'created_at',
        align: 'left',
        label: 'Created At',
        field: 'created_at',
        sortable: true,
      },
      { name: 'actions', align: 'right', label: 'Actions', field: 'actions', sortable: false },
    ]

    // Fetch blogs with pagination and search
    const fetchBlogs = async () => {
      loading.value = true

      try {
        const params = {
          page: pagination.value.page,
          per_page: pagination.value.rowsPerPage,
          title: search.value,
        }

        const response = await axios.get('/api/blogs', { params })
        blogs.value = response.data.data
        pagination.value.rowsNumber = response.data.total
      } catch (error) {
        console.error('Error fetching blogs:', error)
        $q.notify({
          color: 'negative',
          message: 'Failed to fetch blogs',
          icon: 'error',
        })
      } finally {
        loading.value = false
      }
    }

    // Handle pagination
    const onRequest = async (props) => {
      const { page, rowsPerPage, sortBy, descending } = props.pagination
      pagination.value.page = page
      pagination.value.rowsPerPage = rowsPerPage
      pagination.value.sortBy = sortBy
      pagination.value.descending = descending

      await fetchBlogs()
    }

    // Dialog handlers
    const openAddDialog = () => {
      showAddDialog.value = true
    }

    const openEditDialog = (blog) => {
      selectedBlog.value = { ...blog }
      showEditDialog.value = true
    }

    const openPreviewDialog = (blog) => {
      selectedBlog.value = { ...blog }
      showPreviewDialog.value = true
    }

    // Change blog status (publish/hide)
    const changeStatus = async (blog) => {
      try {
        const newStatus = blog.status === 'published' ? 'hidden' : 'published'

        await axios.patch(`/api/blogs/${blog.id}/status`, {
          status: newStatus,
        })

        $q.notify({
          color: 'positive',
          message: `Blog ${newStatus === 'published' ? 'published' : 'hidden'} successfully`,
          icon: 'check',
        })

        fetchBlogs()
      } catch (error) {
        $q.notify({
          color: 'negative',
          message: error,
          icon: 'error',
        })
      }
    }

    // Archive blog (soft delete)
    const archiveBlog = async (blog) => {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to archive this blog?',
        cancel: true,
        persistent: true,
      }).onOk(async () => {
        try {
          await axios.delete(`/api/blogs/${blog.id}`)

          $q.notify({
            color: 'positive',
            message: 'Blog archived successfully',
            icon: 'check',
          })

          fetchBlogs()
        } catch (error) {
          $q.notify({
            color: 'negative',
            message: error,
            icon: 'error',
          })
        }
      })
    }

    // Logout
    const logout = async () => {
      try {
        await axios.post('/api/logout')

        // Clear token
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']

        $q.notify({
          color: 'info',
          message: 'Logged out successfully',
          icon: 'logout',
        })

        // Redirect to login
        router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
      }
    }

    onMounted(() => {
      // Check if token exists
      const token = localStorage.getItem('token')

      if (!token) {
        router.push('/login')
        return
      }

      // Set the authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

      // Initial fetch
      fetchBlogs()
    })

    return {
      blogs,
      loading,
      columns,
      pagination,
      search,
      selectedBlog,
      showAddDialog,
      showEditDialog,
      showPreviewDialog,
      fetchBlogs,
      onRequest,
      openAddDialog,
      openEditDialog,
      openPreviewDialog,
      changeStatus,
      archiveBlog,
      logout,
    }
  },
})
</script>
