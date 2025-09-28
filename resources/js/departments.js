// Department Management Component
const DepartmentManage = {
    template: `
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">Department Management</h2>
                    
                    <!-- Loading Overlay -->
                    <div v-if="show" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <!-- Add Department Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">Add Department</h4>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="validationForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="required">Department Name</label>
                                            <input 
                                                type="text" 
                                                v-model="dept_name"
                                                :class="['form-control', errors.dept_name ? 'is-invalid' : '']"
                                                placeholder="Add Department Name"
                                                required
                                            />
                                            <div v-if="errors.dept_name" class="invalid-feedback">
                                                {{ errors.dept_name[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-1">
                                    <div class="col">
                                        <button 
                                            type="button"
                                            @click="resetData"
                                            class="btn btn-outline-secondary"
                                        >
                                            Cancel
                                        </button>
                                        <button 
                                            type="submit"
                                            class="btn btn-primary float-right"
                                        >
                                            {{ EditID !== '' ? 'Update' : 'Save' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Departments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Departments</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">Department Name</th>
                                            <th class="text-center">Created Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in rows" :key="data.id">
                                            <td class="text-center">{{ data.id }}</td>
                                            <td class="text-center">{{ data.dept_name }}</td>
                                            <td class="text-center">{{ data.cdate ? new Date(data.cdate).toLocaleDateString() : 'N/A' }}</td>
                                            <td class="text-center">
                                                <button 
                                                    @click="change_edit(data.id)"
                                                    class="btn btn-primary btn-sm me-2"
                                                >
                                                    Edit
                                                </button>
                                                <button 
                                                    @click="deleteDepartment(data.id)"
                                                    class="btn btn-danger btn-sm"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    data() {
        return {
            dept_name: '',
            EditID: '',
            show: false,
            rows: [],
            errors: {},
        }
    },
    created() {
        this.loadDepartments();
    },
    methods: {
        async loadDepartments() {
            try {
                const data_send = await this.callAxios('/getDepartments', {}, 'post');
                this.rows = data_send.data;
            } catch (error) {
                console.error('Error loading departments:', error);
                this.rows = [];
            }
        },
        resetData() {
            this.dept_name = '';
            this.EditID = '';
            this.errors = {};
        },
        async validationForm() {
            // Clear previous errors
            this.errors = {};
            
            // Basic client-side validation
            if (!this.dept_name.trim()) {
                this.errors.dept_name = ['Department name is required'];
                return;
            }
            
            this.show = true;
            
            const data = new FormData();
            data.append('dept_name', this.dept_name);
            data.append('username', 'admin');
            
            if (this.EditID) {
                data.append('id', this.EditID);
            }
            
            try {
                let data_send = await this.callAxios('/saveDepartment', data, 'post');
                
                if (data_send.status === 200) {
                    this.show = false;
                    alert(data_send.data.data.message);
                    this.resetData();
                    this.loadDepartments();
                }
                
            } catch (error) {
                this.show = false;
                
                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.errors = error.response.data.data.errors;
                    alert('Please fix the validation errors');
                } else {
                    console.error('Error saving department:', error);
                    alert('An error occurred while saving the department');
                }
            }
        },
        async change_edit(id) {
            this.show = true;
            this.EditID = id;
            
            try {
                const data_send = await this.callAxios('/getSingleDepartment', { 'id': id }, 'post');
                this.dept_name = data_send.data.dept_name;
                this.loadDepartments();
            } catch (error) {
                console.error('Error loading department:', error);
            }
            
            this.show = false;
        },
        async deleteDepartment(id) {
            if (confirm('Are you sure you want to delete this department?')) {
                this.show = true;
                try {
                    const response = await this.callAxios('/deleteDepartment', {'id': id, table: 'department'}, 'post');
                    if (response.status === 200) {
                        alert(response.data.data.message);
                        this.loadDepartments();
                    }
                } catch (error) {
                    console.error('Error deleting department:', error);
                }
                this.show = false;
            }
        },
        async callAxios(url, data, method = 'post') {
            const response = await axios({
                method: method,
                url: url,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            return response;
        }
    }
};

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DepartmentManage;
}
