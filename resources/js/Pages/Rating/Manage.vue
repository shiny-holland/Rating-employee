<template>
  <Layout>
    <div v-if="show" class="overlay">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    
    <!-- Rating Form -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
          <i class="fas fa-star me-2"></i>
          Department Rating System
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="validationForm">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label required">Select Department</label>
                <select
                    v-model="selectedDeptId"
                    class="form-control form-control-lg"
                    :class="{ 'is-invalid': !selectedDeptId && submitted }"
                    @change="loadEmployees"
                    required
                >
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.dept_name }}
                  </option>
                </select>
                <small v-if="!selectedDeptId && submitted" class="text-danger">Department is required</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label required">Department Head Name</label>
                <input
                    v-model="departmentHeadName"
                    type="text"
                    class="form-control form-control-lg"
                    :class="{ 'is-invalid': !departmentHeadName && submitted }"
                    placeholder="Enter department head name"
                    required
                />
                <small v-if="!departmentHeadName && submitted" class="text-danger">Department head name is required</small>
              </div>
            </div>
          </div>

          <!-- Employee Ratings Section - Only show if not already rated -->
          <div v-if="employees.length > 0 && !hasExistingRating" class="mt-4">
            <h6 class="mb-3">
              <i class="fas fa-users me-2"></i>
              Employee Ratings (1(Low)-5(High) scale)
            </h6>
            <div class="row">
              <div class="col-md-8">
                <div v-for="(employee, index) in employees" :key="employee.id" class="mb-3">
                  <div class="card border-primary">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">{{ index + 1 }}</span>
                            <span class="fw-bold">{{ employee.employee_name }}</span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input
                                v-model.number="employee.rating"
                                type="number"
                                class="form-control text-center"
                                step="1"
                                maxlength="1"
                                placeholder="0"
                                @blur="validateRating(employee, $event)"
                                @input="handleInput(employee, $event)"
                                @keydown="preventInvalidInput"
                                @paste="preventPaste"
                            />
                            <span class="input-group-text">/5</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Show completion message if department is already rated -->
          <div v-if="employees.length > 0 && hasExistingRating" class="mt-4">
            <div class="alert alert-success">
              <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div>
                  <h6 class="mb-1">Department Rating Completed</h6>
                  <p class="mb-0">This department has been successfully rated and the rating is now locked.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Average Rating Display - Only show when rating -->
          <div v-if="employees.length > 0 && !hasExistingRating" class="mt-4">
            <div class="alert alert-info">
              <h6 class="mb-2">
                <i class="fas fa-calculator me-2"></i>
                Average Rating: <span class="fw-bold text-primary">{{ averageRating.toFixed(2) }}</span>
              </h6>
            </div>
          </div>

          <!-- Action Buttons -->
          <div v-if="!hasExistingRating" class="row mt-4">
            <div class="col">
              <div class="d-flex gap-3">
                <button
                    type="button"
                    @click="resetForm"
                    class="btn btn-outline-secondary btn-lg px-4"
                >
                  <i class="fas fa-times me-2"></i>
                  Reset
                </button>
                <button
                    type="submit"
                    class="btn btn-success btn-lg px-4"
                    :disabled="employees.length === 0"
                >
                  <i class="fas fa-save me-2"></i>
                  Save Rating
                </button>
              </div>
            </div>
          </div>

        </form>
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
      selectedDeptId: '',
      departmentHeadName: '',
      departments: [],
      employees: [],
      show: false,
      submitted: false,
      hasExistingRating: false,
    }
  },
  computed: {
    averageRating() {
      if (this.employees.length === 0) return 0;
      const total = this.employees.reduce((sum, emp) => sum + (emp.rating || 0), 0);
      return total / this.employees.length;
    }
  },
  async created() {
    this.loadDepartments();
  },
  methods: {
    async loadDepartments() {
      const data_send = await this.callAxios('/getDepartments', {}, 'post');
      this.departments = data_send.data;
    },
    async loadEmployees() {
      if (!this.selectedDeptId) {
        this.employees = [];
        this.hasExistingRating = false;
        return;
      }
      
      this.show = true;
      
      // Load employees
      const emp_data = await this.callAxios('/getEmployeesByDepartment', { dept_id: this.selectedDeptId }, 'post');
      this.employees = emp_data.data.map(emp => ({
        ...emp,
        rating: emp.rating || 0
      }));
      
      // Check if department already has rating
      const rating_check = await this.callAxios('/checkDepartmentRating', { dept_id: this.selectedDeptId }, 'post');
      this.hasExistingRating = rating_check.data.has_rating;
      
      this.show = false;
    },
    preventInvalidInput(event) {
      // Allow: backspace, delete, tab, escape, enter, arrows
      if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(event.keyCode) !== -1 ||
          // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
          (event.keyCode === 65 && event.ctrlKey === true) ||
          (event.keyCode === 67 && event.ctrlKey === true) ||
          (event.keyCode === 86 && event.ctrlKey === true) ||
          (event.keyCode === 88 && event.ctrlKey === true)) {
        return;
      }
      
      // Only allow number keys (0-9)
      if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
        event.preventDefault();
        return;
      }
      
      // Get the new character being typed
      const newChar = String.fromCharCode(event.keyCode);
      const numValue = parseInt(newChar);
      
      // Only allow single digits 1-5
      if (numValue < 1 || numValue > 5) {
        event.preventDefault();
      }
    },
    preventPaste(event) {
      event.preventDefault();
      // Get pasted text
      const pastedText = (event.clipboardData || window.clipboardData).getData('text');
      const numValue = parseInt(pastedText);
      
      // Only allow single digit 1-5
      if (!isNaN(numValue) && numValue >= 1 && numValue <= 5 && pastedText.length === 1) {
        event.target.value = numValue;
        // Trigger input event to update v-model
        event.target.dispatchEvent(new Event('input', { bubbles: true }));
      }
    },
    handleInput(employee, event) {
      const value = event.target.value;
      
      // If empty, allow it
      if (value === '' || value === null || value === undefined) {
        employee.rating = '';
        return;
      }
      
      // Convert to number
      const numValue = parseInt(value);
      
      // Only allow single digit 1-5
      if (!isNaN(numValue) && numValue >= 1 && numValue <= 5 && value.length === 1) {
        employee.rating = numValue;
      } else {
        // Clear invalid input immediately
        employee.rating = '';
        event.target.value = '';
        // Force the input to be empty
        this.$nextTick(() => {
          event.target.value = '';
        });
      }
    },
    validateRating(employee, event) {
      // Only validate if rating is not empty and is a valid number
      if (employee.rating !== null && employee.rating !== undefined && employee.rating !== '') {
        const rating = parseInt(employee.rating);
        if (!isNaN(rating)) {
          // Only allow integers between 1 and 5
          if (rating < 1 || rating > 5) {
            // Clear immediately for any invalid input
            employee.rating = '';
          } else {
            // Ensure it's an integer and limit to single digit
            employee.rating = Math.floor(rating);
            // If somehow multiple digits got through, take only the first digit
            if (employee.rating.toString().length > 1) {
              employee.rating = parseInt(employee.rating.toString().charAt(0));
            }
          }
        } else {
          // Clear non-numeric input
          employee.rating = '';
        }
      }
    },
    resetForm() {
      this.selectedDeptId = '';
      this.departmentHeadName = '';
      this.employees = [];
      this.hasExistingRating = false;
      this.submitted = false;
    },
    async validationForm() {
      this.submitted = true;
      
      if (!this.selectedDeptId || !this.departmentHeadName) {
        return;
      }
      
      if (this.employees.length === 0) {
        this.sweetAlertmethod('Error', 'No employees found for this department.', 'error', 'btn-danger');
        return;
      }
      
      // Validate all employee ratings - only integers 1-5 allowed
      const invalidRatings = this.employees.filter(emp => {
        if (!emp.rating) return true;
        const rating = parseInt(emp.rating);
        return isNaN(rating) || rating < 1 || rating > 5 || !Number.isInteger(parseFloat(emp.rating));
      });
      if (invalidRatings.length > 0) {
        this.sweetAlertmethod('Error', 'All employee ratings must be whole numbers between 1 and 5.', 'error', 'btn-danger');
        return;
      }
      
      this.show = true;
      
      const data = {
        dept_id: this.selectedDeptId,
        department_head_name: this.departmentHeadName,
        employee_ratings: this.employees.map(emp => ({
          employee_id: emp.id,
          rating: emp.rating
        }))
      };
      
      const data_send = await this.callAxios('/saveDepartmentRating', data, 'post');
      
      if (data_send.status === 200) {
        this.show = false;
        this.sweetAlertmethod(data_send.data.button, data_send.data.message, data_send.data.notify, 'btn-success');
        this.resetForm();
      } else {
        this.show = false;
        this.sweetAlertmethod(data_send.data.button, data_send.data.message, data_send.data.notify, 'btn-danger');
      }
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

.card.border-primary {
  border-color: #0d6efd !important;
}

.badge.fs-6 {
  font-size: 1rem !important;
}
</style>
