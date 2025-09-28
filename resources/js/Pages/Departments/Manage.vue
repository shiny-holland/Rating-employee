<template>
  <Layout>
    <div v-if="show" class="overlay">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="fas fa-plus-circle me-2"></i>
          {{ EditID !== '' ? 'Edit Department' : 'Add New Department' }}
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="validationForm">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group mb-3">
                <label class="form-label required">Department Name</label>
                <input
                    v-model="dept_name"
                    type="text"
                    class="form-control form-control-lg"
                    :class="{ 'is-invalid': !dept_name && submitted }"
                    placeholder="Enter department name"
                    required
                />
                <small v-if="!dept_name && submitted" class="text-danger">Department name is required</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="d-flex gap-3">
                <button
                    type="button"
                    @click="resetData"
                    class="btn btn-outline-secondary btn-lg px-4"
                >
                  <i class="fas fa-times me-2"></i>
                  Cancel
                </button>
                <button
                    type="submit"
                    class="btn btn-primary btn-lg px-4"
                >
                  <i class="fas fa-save me-2"></i>
                  {{ EditID !== '' ? 'Update Department' : 'Save Department' }}
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">
          <i class="fas fa-list me-2"></i>
          Department List
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-striped">
            <thead class="table-dark">
              <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Department Name</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in rows" :key="data.id">
                <td class="text-center fw-bold">{{ data.id }}</td>
                <td class="text-center">{{ data.dept_name }}</td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="change_edit(data.id)"
                        title="Edit Department"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        @click="sweetAlertDeleteMethod('/deleteDepartment', {'id': data.id, table: 'department'}, 'post')"
                        title="Delete Department"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import axios from 'axios';
import Layout from '../../Components/Layout.vue';

export default {
  components: {
    Layout
  },
  data() {
    return {
      dept_name: '',
      EditID: '',
      rows: [],
      show: false,
      submitted: false,
    }
  },
  async created() {
    this.DepartmentLoad();
  },
  methods: {
    async DepartmentLoad() {
      const data_send = await this.callAxios('/getDepartments', {}, 'post');
      console.log('API Response:', data_send);
      console.log('Data:', data_send.data);
      this.rows = data_send.data;
      console.log('Rows after assignment:', this.rows);
    },
    resetData() {
      this.dept_name = this.EditID = '';
      this.submitted = false;
    },
    async validationForm() {
      this.submitted = true;
      if (!this.dept_name) {
        return;
      }
      
      this.show = true;
      const data = new FormData();
      data.append('dept_name', this.dept_name);

      if (this.EditID) {
        data.append('id', this.EditID);
      }
      data.append('username', 'admin');
      
      const data_send = await this.callAxios('/saveDepartment', data, 'post');
      if (data_send.status === 200) {
        this.show = false;
        this.sweetAlertmethod(data_send.data.button, data_send.data.message, data_send.data.notify, 'btn-primary');
        this.dept_name = '';
        this.EditID = '';
        this.submitted = false;
        this.DepartmentLoad();
      } else {
        this.show = false;
        this.sweetAlertmethod(data_send.data.button, data_send.data.message, data_send.data.notify, 'btn-danger');
      }
    },
    async change_edit(id) {
      this.show = true;
      this.EditID = id;
      const data_send = await this.callAxios('/getSingleDepartment', { 'id': id }, 'post');
      this.dept_name = data_send.data.dept_name;
      this.DepartmentLoad();
      this.show = false;
    },
    async callAxios(url, data, method) {
      try {
        const response = await axios({
          method: method,
          url: url,
          data: data,
          headers: {
            'Content-Type': 'application/json',
          }
        });
        return response.data;
      } catch (error) {
        console.error('API Error:', error);
        return {
          status: 500,
          data: {
            button: 'Error',
            message: 'Network error occurred',
            notify: 'error'
          }
        };
      }
    },
    sweetAlertmethod(title, text, icon, button) {
      Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'OK'
      });
    },
    sweetAlertDeleteMethod(url, data, method) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          const data_send = await this.callAxios(url, data, method);
          if (data_send.status === 200) {
            this.sweetAlertmethod(data_send.data.button, data_send.data.message, data_send.data.notify, 'btn-success');
            this.DepartmentLoad();
          } else {
            this.sweetAlertmethod(data_send.data.button, data_send.data.message, data_send.data.notify, 'btn-danger');
          }
        }
      });
    }
  }
}
</script>

<style>
.required::after {
  content: " *";
  color: red;
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.spinner-border {
  color: white;
}
</style>