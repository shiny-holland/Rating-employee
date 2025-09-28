import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/departments',
        name: 'Departments',
        component: () => import('./Pages/Departments/Manage.vue')
    },
    {
        path: '/departments/manage',
        name: 'DepartmentManage',
        component: () => import('./Pages/Departments/Manage.vue')
    },
    {
        path: '/employees',
        name: 'Employees',
        component: () => import('./Pages/Employees/Manage.vue')
    },
    {
        path: '/employees/manage',
        name: 'EmployeeManage',
        component: () => import('./Pages/Employees/Manage.vue')
    },
    {
        path: '/rating',
        name: 'Rating',
        component: () => import('./Pages/Rating/Manage.vue')
    },
    {
        path: '/rating/manage',
        name: 'RatingManage',
        component: () => import('./Pages/Rating/Manage.vue')
    },
    {
        path: '/',
        redirect: '/departments'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
