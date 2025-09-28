<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="sidebar-header">
        <h4 class="company-name">Zigma Global Environ Solution</h4>
      </div>
      <ul class="nav-menu">
        <li class="nav-item">
          <router-link to="/departments" class="nav-link" :class="{ active: $route.path.includes('/departments') }">
            <i class="nav-icon">🏢</i>
            <span>Add Department</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/employees" class="nav-link" :class="{ active: $route.path.includes('/employees') }">
            <i class="nav-icon">👥</i>
            <span>Add Employee</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/rating" class="nav-link" :class="{ active: $route.path.includes('/rating') }">
            <i class="nav-icon">⭐</i>
            <span>Department Rating</span>
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <header class="page-header">
        <h1 class="page-title">{{ pageTitle }}</h1>
      </header>
      <div class="content-wrapper">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'Layout',
  computed: {
    pageTitle() {
      if (this.$route.path.includes('/departments')) {
        return 'Department Management';
      } else if (this.$route.path.includes('/employees')) {
        return 'Employee Management';
      } else if (this.$route.path.includes('/rating')) {
        return 'Department Rating System';
      }
      return 'Dashboard';
    }
  }
}
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.sidebar {
  width: 250px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  position: fixed;
  height: 100vh;
  overflow-y: auto;
  z-index: 1000;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  text-align: center;
}

.company-name {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: white;
  line-height: 1.3;
}

.nav-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
}

.nav-link:hover {
  background-color: rgba(255,255,255,0.1);
  color: white;
  border-left-color: #ffd700;
}

.nav-link.active {
  background-color: rgba(255,255,255,0.15);
  color: white;
  border-left-color: #ffd700;
  font-weight: 600;
}

.nav-icon {
  margin-right: 12px;
  font-size: 18px;
  width: 20px;
  text-align: center;
}

.main-content {
  flex: 1;
  margin-left: 250px;
  display: flex;
  flex-direction: column;
}

.page-header {
  background: white;
  padding: 20px 30px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  border-bottom: 1px solid #e9ecef;
}

.page-title {
  margin: 0;
  font-size: 28px;
  font-weight: 600;
  color: #2c3e50;
}

.content-wrapper {
  flex: 1;
  padding: 30px;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  
  .main-content {
    margin-left: 0;
  }
  
  .content-wrapper {
    padding: 20px;
  }
}
</style>
