<!-- UsersList.svelte -->
<script>
  import { onMount } from 'svelte';
  import * as XLSX from 'xlsx'; // For Excel export
  import html2canvas from 'html2canvas'; // For image export
  import jsPDF from 'jspdf'; // For PDF export
  import 'jspdf-autotable'; // For PDF table formatting
	import { browser } from '$app/environment';
	import { get_users } from '$lib/methods/methods';
  
  // Users data
  let users = [];
  
  // UI States
  let isLoading = false;
  let sortBy = 'id';
  let sortDirection = 'asc';
  let editRowId = null;
  let editForm = {};
  let showFilters = false;
  let exportFormat = 'json';
  let notification = null;
  let selectedUsers = new Set();
  let selectAll = false;
  
  // Filters
  let filters = {
    search: '',
    role: 'all',
    status: 'all',
    dateFrom: '',
    dateTo: '',
    verified: 'all'
  };
  
  // Pagination
  let currentPage = 1;
  let itemsPerPage = 10;
  let pageOptions = [5, 10, 25, 50, 100];
  
  // Mock data
  const mockUsers = [
    { id: 1, name: 'John Doe', email: 'john@example.com', role: 'admin', status: 'active', joinDate: '2024-01-15', lastLogin: '2024-01-28', verified: true, phone: '+1-555-0123', location: 'New York' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'user', status: 'active', joinDate: '2024-01-10', lastLogin: '2024-01-27', verified: true, phone: '+1-555-0124', location: 'Los Angeles' },
    { id: 3, name: 'Bob Johnson', email: 'bob@example.com', role: 'editor', status: 'inactive', joinDate: '2024-01-05', lastLogin: '2024-01-20', verified: false, phone: '+1-555-0125', location: 'Chicago' },
    { id: 4, name: 'Alice Brown', email: 'alice@example.com', role: 'user', status: 'active', joinDate: '2023-12-20', lastLogin: '2024-01-28', verified: true, phone: '+1-555-0126', location: 'Miami' },
    { id: 5, name: 'Charlie Wilson', email: 'charlie@example.com', role: 'guest', status: 'active', joinDate: '2023-12-15', lastLogin: '2024-01-25', verified: false, phone: '+1-555-0127', location: 'Seattle' },
    { id: 6, name: 'Diana Prince', email: 'diana@example.com', role: 'admin', status: 'active', joinDate: '2023-12-10', lastLogin: '2024-01-28', verified: true, phone: '+1-555-0128', location: 'Boston' },
    { id: 7, name: 'Edward Norton', email: 'edward@example.com', role: 'user', status: 'suspended', joinDate: '2023-11-30', lastLogin: '2024-01-15', verified: true, phone: '+1-555-0129', location: 'Austin' },
    { id: 8, name: 'Fiona Gallagher', email: 'fiona@example.com', role: 'editor', status: 'active', joinDate: '2023-11-25', lastLogin: '2024-01-26', verified: false, phone: '+1-555-0130', location: 'Denver' },
    { id: 9, name: 'George Miller', email: 'george@example.com', role: 'user', status: 'active', joinDate: '2023-11-20', lastLogin: '2024-01-27', verified: true, phone: '+1-555-0131', location: 'Phoenix' },
    { id: 10, name: 'Hannah Davis', email: 'hannah@example.com', role: 'guest', status: 'inactive', joinDate: '2023-11-15', lastLogin: '2023-12-30', verified: false, phone: '+1-555-0132', location: 'Atlanta' },
    { id: 11, name: 'Ian Curtis', email: 'ian@example.com', role: 'user', status: 'active', joinDate: '2023-11-10', lastLogin: '2024-01-28', verified: true, phone: '+1-555-0133', location: 'Portland' },
    { id: 12, name: 'Julia Roberts', email: 'julia@example.com', role: 'admin', status: 'active', joinDate: '2023-11-05', lastLogin: '2024-01-28', verified: true, phone: '+1-555-0134', location: 'San Diego' },
    { id: 13, name: 'Kevin Spacey', email: 'kevin@example.com', role: 'user', status: 'suspended', joinDate: '2023-10-30', lastLogin: '2024-01-10', verified: false, phone: '+1-555-0135', location: 'Dallas' },
    { id: 14, name: 'Laura Palmer', email: 'laura@example.com', role: 'editor', status: 'active', joinDate: '2023-10-25', lastLogin: '2024-01-26', verified: true, phone: '+1-555-0136', location: 'Houston' },
    { id: 15, name: 'Michael Scott', email: 'michael@example.com', role: 'user', status: 'active', joinDate: '2023-10-20', lastLogin: '2024-01-28', verified: true, phone: '+1-555-0137', location: 'Philadelphia' }
  ];
  
  // Roles and statuses for filters
  const roles = ['all', 'admin', 'user', 'editor', 'guest'];
  const statuses = ['all', 'active', 'inactive', 'suspended'];
  
  // Initialize
  onMount(async() => {
     await loadUsers();

      // Make sure dropdowns work on mobile
      if(browser){
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
      });
    }
  });
      }

  });
  
  // Load users
  async function loadUsers() {
    isLoading = true;
    await new Promise(resolve => setTimeout(resolve, 800));
    // users = mockUsers.map(user => ({
    //   ...user,
    //   selected: false
    // }));

    const users_=await get_users();

    users=makeUsers(users_);

    isLoading = false;
    showNotification('Users loaded successfully', 'success');
  }
  
  // Sort users
  function sortUsers(column) {
    if (sortBy === column) {
      sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      sortBy = column;
      sortDirection = 'asc';
    }
    
    users = [...users].sort((a, b) => {
      let aVal = a[column];
      let bVal = b[column];
      
      // Handle different data types
      if (column === 'joinDate' || column === 'lastLogin') {
        aVal = new Date(aVal);
        bVal = new Date(bVal);
      }
      
      if (typeof aVal === 'string') {
        aVal = aVal.toLowerCase();
        bVal = bVal.toLowerCase();
      }
      
      if (aVal < bVal) return sortDirection === 'asc' ? -1 : 1;
      if (aVal > bVal) return sortDirection === 'asc' ? 1 : -1;
      return 0;
    });
  }
  
  // Get sort icon
  function getSortIcon(column) {
    if (sortBy !== column) return 'sort';
    return sortDirection === 'asc' ? 'sort-up' : 'sort-down';
  }
  
  // Filter users
  $: filteredUsers = users.filter(user => {
    // Search filter
    const searchMatch = filters.search === '' || 
      user.name.toLowerCase().includes(filters.search.toLowerCase()) ||
      user.email.toLowerCase().includes(filters.search.toLowerCase()) ||
      user.phone?.toLowerCase().includes(filters.search.toLowerCase()) ||
      user.location?.toLowerCase().includes(filters.search.toLowerCase());
    
    // Role filter
    const roleMatch = filters.role === 'all' || user.role === filters.role;
    
    // Status filter
    const statusMatch = filters.status === 'all' || user.status === filters.status;
    
    // Date filters
    const joinDate = new Date(user.joinDate);
    const fromMatch = !filters.dateFrom || joinDate >= new Date(filters.dateFrom);
    const toMatch = !filters.dateTo || joinDate <= new Date(filters.dateTo + 'T23:59:59');
    
    // Verified filter
    const verifiedMatch = filters.verified === 'all' || 
      (filters.verified === 'verified' && user.verified) ||
      (filters.verified === 'unverified' && !user.verified);
    
    return searchMatch && roleMatch && statusMatch && fromMatch && toMatch && verifiedMatch;
  });
  
  // Pagination
  $: totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
  $: paginatedUsers = filteredUsers.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );
  $: startItem = (currentPage - 1) * itemsPerPage + 1;
  $: endItem = Math.min(currentPage * itemsPerPage, filteredUsers.length);
  
  // Toggle select all
  $: {
    if (selectAll) {
      selectedUsers = new Set(paginatedUsers.map(user => user.id));
    } else {
      selectedUsers.clear();
    }
  }
  
  // Toggle individual selection
  function toggleSelectUser(userId) {
    if (selectedUsers.has(userId)) {
      selectedUsers.delete(userId);
    } else {
      selectedUsers.add(userId);
    }
    selectedUsers = new Set(selectedUsers);
  }
  
  // Start editing
  function startEditing(user) {
    editRowId = user.id;
    editForm = { ...user };
  }
  
  // Save edit
  function saveEdit() {
    const index = users.findIndex(u => u.id === editRowId);
    if (index !== -1) {
      users[index] = { ...editForm };
      users = users;
      editRowId = null;
      showNotification('User updated successfully', 'success');
    }
  }
  
  // Cancel edit
  function cancelEdit() {
    editRowId = null;
    editForm = {};
  }
  
  // Delete user
  function deleteUser(id) {
    users = users.filter(user => user.id !== id);
    selectedUsers.delete(id);
    showNotification('User deleted successfully', 'success');
  }
  
  // Bulk actions
  function bulkDelete() {
    if (selectedUsers.size === 0) {
      showNotification('No users selected', 'warning');
      return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedUsers.size} user(s)?`)) {
      users = users.filter(user => !selectedUsers.has(user.id));
      selectedUsers.clear();
      showNotification(`${selectedUsers.size} users deleted successfully`, 'success');
    }
  }
  
  function bulkUpdateStatus(status) {
    if (selectedUsers.size === 0) {
      showNotification('No users selected', 'warning');
      return;
    }
    
    users = users.map(user => {
      if (selectedUsers.has(user.id)) {
        return { ...user, status };
      }
      return user;
    });
    
    showNotification(`${selectedUsers.size} users updated to ${status}`, 'success');
  }
  
  // Clear filters
  function clearFilters() {
    filters = {
      search: '',
      role: 'all',
      status: 'all',
      dateFrom: '',
      dateTo: '',
      verified: 'all'
    };
  }
  
  // Show notification
  function showNotification(message, type = 'info') {
    notification = { message, type };
    setTimeout(() => {
      notification = null;
    }, 3000);
  }
  
  // Export functions
  async function exportData(format) {
    try {
      const data = selectedUsers.size > 0 
        ? users.filter(user => selectedUsers.has(user.id))
        : filteredUsers;
      
      switch (format) {
        case 'json':
          exportJSON(data);
          break;
        case 'csv':
          exportCSV(data);
          break;
        case 'excel':
          exportExcel(data);
          break;
        case 'pdf':
          await exportPDF(data);
          break;
        case 'image':
          await exportImage();
          break;
        default:
          showNotification('Invalid export format', 'error');
      }
    } catch (error) {
      console.error('Export error:', error);
      showNotification('Export failed: ' + error.message, 'error');
    }
  }
  
  // JSON Export
  function exportJSON(data) {
    const jsonString = JSON.stringify(data, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `users_${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showNotification('JSON exported successfully', 'success');
  }
  
  // CSV Export
  function exportCSV(data) {
    const headers = ['ID', 'Name', 'Email', 'Role', 'Status', 'Join Date', 'Last Login', 'Verified', 'Phone', 'Location'];
    const csvRows = [
      headers.join(','),
      ...data.map(user => [
        user.id,
        `"${user.name.replace(/"/g, '""')}"`,
        user.email,
        user.role,
        user.status,
        user.joinDate,
        user.lastLogin,
        user.verified ? 'Yes' : 'No',
        user.phone || '',
        user.location || ''
      ].join(','))
    ];
    
    const csvString = csvRows.join('\n');
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `users_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showNotification('CSV exported successfully', 'success');
  }
  
  // Excel Export
  function exportExcel(data) {
    const worksheet = XLSX.utils.json_to_sheet(data.map(user => ({
      ID: user.id,
      Name: user.name,
      Email: user.email,
      Role: user.role,
      Status: user.status,
      'Join Date': user.joinDate,
      'Last Login': user.lastLogin,
      Verified: user.verified ? 'Yes' : 'No',
      Phone: user.phone || '',
      Location: user.location || ''
    })));
    
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');
    
    // Auto-size columns
    const maxWidth = Object.keys(data[0] || {}).reduce((acc, key) => {
      const maxLength = Math.max(
        key.length,
        ...data.map(d => String(d[key] || '').length)
      );
      return { ...acc, [key]: { wch: Math.min(maxLength, 50) } };
    }, {});
    
    worksheet['!cols'] = Object.values(maxWidth);
    
    XLSX.writeFile(workbook, `users_${new Date().toISOString().split('T')[0]}.xlsx`);
    showNotification('Excel file exported successfully', 'success');
  }
  
  // PDF Export
  async function exportPDF(data) {
    const doc = new jsPDF();
    
    // Add title
    doc.setFontSize(18);
    doc.text('Users List', 14, 22);
    
    // Add export date
    doc.setFontSize(10);
    doc.text(`Exported: ${new Date().toLocaleDateString()}`, 14, 30);
    
    // Add summary
    doc.setFontSize(11);
    doc.text(`Total Users: ${data.length}`, 14, 40);
    
    // Prepare table data
    const tableData = data.map(user => [
      user.id,
      user.name,
      user.email,
      user.role,
      user.status,
      user.joinDate,
      user.lastLogin,
      user.verified ? 'Yes' : 'No'
    ]);
    
    // Create table
    doc.autoTable({
      startY: 45,
      head: [['ID', 'Name', 'Email', 'Role', 'Status', 'Join Date', 'Last Login', 'Verified']],
      body: tableData,
      theme: 'striped',
      headStyles: { fillColor: [41, 128, 185] },
      styles: { fontSize: 9 },
      columnStyles: {
        0: { cellWidth: 15 },
        1: { cellWidth: 30 },
        2: { cellWidth: 40 },
        3: { cellWidth: 20 },
        4: { cellWidth: 25 },
        5: { cellWidth: 25 },
        6: { cellWidth: 25 },
        7: { cellWidth: 20 }
      }
    });
    
    // Save PDF
    doc.save(`users_${new Date().toISOString().split('T')[0]}.pdf`);
    showNotification('PDF exported successfully', 'success');
  }
  
  // Image Export
  async function exportImage() {
    const tableElement = document.querySelector('.users-table-container');
    if (!tableElement) {
      showNotification('Table element not found', 'error');
      return;
    }
    
    // Add export info
    const originalDisplay = tableElement.style.display;
    tableElement.style.display = 'block';
    
    // Capture the table
    const canvas = await html2canvas(tableElement, {
      scale: 2,
      backgroundColor: '#ffffff',
      useCORS: true,
      logging: false
    });
    
    // Convert to image and download
    const image = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.href = image;
    link.download = `users_${new Date().toISOString().split('T')[0]}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    tableElement.style.display = originalDisplay;
    showNotification('Image exported successfully', 'success');
  }
  
  // Format date
  function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  }
  
  // Format phone
  function formatPhone(phone) {
    if (!phone) return '-';
    return phone.replace(/(\d{3})-(\d{3})-(\d{4})/, '($1) $2-$3');
  }
  
  // Get status color
  function getStatusColor(status) {
    const colors = {
      active: '#10b981',
      inactive: '#6b7280',
      suspended: '#ef4444',
      pending: '#f59e0b'
    };
    return colors[status] || '#6b7280';
  }
  
  // Get role color
  function getRoleColor(role) {
    const colors = {
      admin: '#ef4444',
      user: '#3b82f6',
      editor: '#8b5cf6',
      guest: '#6b7280'
    };
    return colors[role] || '#6b7280';
  }

  const makeUsers=(users)=>{

    const usersList=users.map((u,i)=>{
      return {
        id:u.id,
        name:`${u.title} ${u.firstname} ${u.lastname}`,
        email:u.email,
        role:u.user_role,
        status:u.status,
        joinDate:u.created_at,
        verified:true,
        phone:u.phone,
        location:"",
        selected:false
      }
    })

    return usersList;
  }


</script>

<svelte:head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</svelte:head>

<div class="users-list-container">
  <!-- Header -->
  <div class="header">
    <div class="header-left">
      <h1><i class="fas fa-users"></i> Users Management</h1>
      <p class="subtitle">Manage your users with advanced filtering and export options</p>
    </div>
    <div class="header-right">
      <button class="ui blue mini icon button" on:click={() => loadUsers()} disabled={isLoading}>
        <i class="fas fa-sync-alt {isLoading ? 'fa-spin' : ''}"></i>
        Refresh
      </button>

      <a href="users/create">
          <button class="ui purple icon mini button">
          <i class="user icon"></i>
          New User
        </button>
      </a>
    </div>
  </div>

  <!-- Notification -->
  {#if notification}
    <div class="notification {notification.type}">
      <i class="fas fa-{notification.type === 'success' ? 'check-circle' : notification.type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>{notification.message}</span>
      <button on:click={() => notification = null} class="close-notification">
        <i class="fas fa-times"></i>
      </button>
    </div>
  {/if}

  <!-- Stats Cards -->
  <div class="stats-cards">
    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-content">
        <div class="stat-value">{users.length}</div>
        <div class="stat-label">Total Users</div>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
        <i class="fas fa-user-check"></i>
      </div>
      <div class="stat-content">
        <div class="stat-value">{users.filter(u => u.status === 'active').length}</div>
        <div class="stat-label">Active Users</div>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="stat-content">
        <div class="stat-value">{users.filter(u => u.role === 'admin').length}</div>
        <div class="stat-label">Admins</div>
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
        <i class="fas fa-clock"></i>
      </div>
      <div class="stat-content">
        <div class="stat-value">{users.filter(u => !u.verified).length}</div>
        <div class="stat-label">Unverified</div>
      </div>
    </div>
  </div>

  <!-- Controls Section -->
  <div class="controls-section">
    <div class="controls-left">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
          type="text" 
          bind:value={filters.search}
          placeholder="Search users..."
          class="search-input"
        />
        {#if filters.search}
          <button on:click={() => filters.search = ''} class="clear-search">
            <i class="fas fa-times"></i>
          </button>
        {/if}
      </div>
      
      <button 
        class="btn {showFilters ? 'btn-primary' : 'btn-secondary'}" 
        on:click={() => showFilters = !showFilters}
      >
        <i class="fas fa-filter"></i>
        {showFilters ? 'Hide Filters' : 'Show Filters'}
      </button>
      
      {#if selectedUsers.size > 0}
        <div class="selected-count">
          <span class="count-badge">{selectedUsers.size} selected</span>
        </div>
      {/if}
    </div>
    
    <div class="controls-right">
      <!-- Bulk Actions -->
      {#if selectedUsers.size > 0}
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle">
            <i class="fas fa-bars"></i> Bulk Actions
          </button>
          <div class="dropdown-menu">
            <button class="dropdown-item" on:click={bulkDelete}>
              <i class="fas fa-trash"></i> Delete Selected
            </button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item" on:click={() => bulkUpdateStatus('active')}>
              <i class="fas fa-check-circle"></i> Set as Active
            </button>
            <button class="dropdown-item" on:click={() => bulkUpdateStatus('inactive')}>
              <i class="fas fa-times-circle"></i> Set as Inactive
            </button>
            <button class="dropdown-item" on:click={() => bulkUpdateStatus('suspended')}>
              <i class="fas fa-ban"></i> Set as Suspended
            </button>
          </div>
        </div>
      {/if}
      
      <!-- Export Dropdown -->
      <div class="dropdown">
        <button class="btn btn-success dropdown-toggle">
          <i class="fas fa-download"></i> Export
        </button>
        <div class="dropdown-menu">
          <div class="dropdown-header">Export Format</div>
          <div class="export-options">
            <button class="export-option {exportFormat === 'json' ? 'active' : ''}" on:click={() => { exportFormat = 'json'; exportData('json'); }}>
              <i class="fas fa-file-code"></i> JSON
            </button>
            <button class="export-option {exportFormat === 'csv' ? 'active' : ''}" on:click={() => { exportFormat = 'csv'; exportData('csv'); }}>
              <i class="fas fa-file-csv"></i> CSV
            </button>
            <button class="export-option {exportFormat === 'excel' ? 'active' : ''}" on:click={() => { exportFormat = 'excel'; exportData('excel'); }}>
              <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="export-option {exportFormat === 'pdf' ? 'active' : ''}" on:click={() => { exportFormat = 'pdf'; exportData('pdf'); }}>
              <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button class="export-option {exportFormat === 'image' ? 'active' : ''}" on:click={() => { exportFormat = 'image'; exportData('image'); }}>
              <i class="fas fa-file-image"></i> Image
            </button>
          </div>
          <div class="dropdown-footer">
            {selectedUsers.size > 0 ? 'Exporting selected users' : 'Exporting all filtered users'}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters Panel -->
  {#if showFilters}
    <div class="filters-panel">
      <div class="filters-header">
        <h3><i class="fas fa-sliders-h"></i> Advanced Filters</h3>
        <button class="btn btn-text" on:click={clearFilters}>
          <i class="fas fa-undo"></i> Clear All
        </button>
      </div>
      
      <div class="filters-grid">
        <div class="filter-group">
          <label>Role</label>
          <select bind:value={filters.role} class="filter-select">
            {#each roles as role}
              <option value={role}>{role.charAt(0).toUpperCase() + role.slice(1)}</option>
            {/each}
          </select>
        </div>
        
        <div class="filter-group">
          <label>Status</label>
          <select bind:value={filters.status} class="filter-select">
            {#each statuses as status}
              <option value={status}>{status.charAt(0).toUpperCase() + status.slice(1)}</option>
            {/each}
          </select>
        </div>
        
        <div class="filter-group">
          <label>Verified</label>
          <select bind:value={filters.verified} class="filter-select">
            <option value="all">All</option>
            <option value="verified">Verified</option>
            <option value="unverified">Unverified</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Join Date From</label>
          <input 
            type="date" 
            bind:value={filters.dateFrom}
            class="filter-input"
          />
        </div>
        
        <div class="filter-group">
          <label>Join Date To</label>
          <input 
            type="date" 
            bind:value={filters.dateTo}
            class="filter-input"
          />
        </div>
      </div>
    </div>
  {/if}

  <!-- Users Table -->
  <div class="table-container users-table-container">
    {#if isLoading}
      <div class="loading-state">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
        <p>Loading users...</p>
      </div>
    {:else if filteredUsers.length === 0}
      <div class="empty-state">
        <i class="fas fa-user-slash fa-3x"></i>
        <h3>No users found</h3>
        <p>Try adjusting your search or filters</p>
        <button class="btn btn-primary" on:click={clearFilters}>
          <i class="fas fa-undo"></i> Clear Filters
        </button>
      </div>
    {:else}
      <div class="table-responsive">
        <table class="users-table">
          <thead>
            <tr >
              <th class="checkbox-cell">
                <label class="checkbox-wrapper">
                  <input 
                    type="checkbox" 
                    bind:checked={selectAll}
                    class="checkbox-input"
                  />
                  <span class="checkbox-custom"></span>
                </label>
              </th>
              <!-- <th on:click={() => sortUsers('id')}>
                <div class="sortable-header">
                  ID
                  <i class="fas fa-{getSortIcon('id')}"></i>
                </div>
              </th> -->
              <th on:click={() => sortUsers('name')}>
                <div class="sortable-header">
                  Name
                  <i class="fas fa-{getSortIcon('name')}"></i>
                </div>
              </th>
              <th on:click={() => sortUsers('email')}>
                <div class="sortable-header">
                  Email
                  <i class="fas fa-{getSortIcon('email')}"></i>
                </div>
              </th>
              <th on:click={() => sortUsers('role')}>
                <div class="sortable-header">
                  Role
                  <i class="fas fa-{getSortIcon('role')}"></i>
                </div>
              </th>
              <th on:click={() => sortUsers('status')}>
                <div class="sortable-header">
                  Status
                  <i class="fas fa-{getSortIcon('status')}"></i>
                </div>
              </th>
              <th on:click={() => sortUsers('joinDate')}>
                <div class="sortable-header">
                  Join Date
                  <i class="fas fa-{getSortIcon('joinDate')}"></i>
                </div>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {#each paginatedUsers as user (user.id)}
              <tr class="{selectedUsers.has(user.id) ? 'selected' : ''}">
                <!-- Checkbox -->
                <td class="checkbox-cell">
                  <label class="checkbox-wrapper">
                    <input 
                      type="checkbox" 
                      checked={selectedUsers.has(user.id)}
                      on:change={() => toggleSelectUser(user.id)}
                      class="checkbox-input"
                    />
                    <span class="checkbox-custom"></span>
                  </label>
                </td>
                
                <!-- ID -->
                <!-- <td class="id-cell">#{user.id}</td> -->
                
                <!-- Name & Info -->
                <td class="name-cell">
                  {#if editRowId === user.id}
                    <input 
                      type="text" 
                      bind:value={editForm.name}
                      class="edit-input"
                    />
                  {:else}
                    <div class="user-info">
                      <div class="user-name">{user.name}</div>
                    </div>
                  {/if}
                </td>
                
                <!-- Email -->
                <td class="email-cell">
                  {#if editRowId === user.id}
                    <input 
                      type="email" 
                      bind:value={editForm.email}
                      class="edit-input"
                    />
                  {:else}
                    <div class="email-wrapper">
                      <i class="fas fa-envelope"></i>
                      <a href="mailto:{user.email}" class="email-link">{user.email}</a>
                    </div>
                  {/if}
                </td>
                
                <!-- Role -->
                <td class="role-cell">
                  {#if editRowId === user.id}
                    <select bind:value={editForm.role} class="edit-select">
                      <option value="admin">Admin</option>
                      <option value="user">User</option>
                      <option value="editor">Editor</option>
                      <option value="guest">Guest</option>
                    </select>
                  {:else}
                    <span class="role-badge" style="background-color: {getRoleColor(user.role)}20; color: {getRoleColor(user.role)};">
                      <i class="fas fa-user-{user.role === 'admin' ? 'shield' : user.role === 'editor' ? 'edit' : 'circle'}"></i>
                      {user.role}
                    </span>
                  {/if}
                </td>
                
                <!-- Status -->
                <td class="status-cell">
                  {#if editRowId === user.id}
                    <select bind:value={editForm.status} class="edit-select">
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                      <option value="suspended">Suspended</option>
                    </select>
                  {:else}
                    <span class="status-badge" style="background-color: {getStatusColor(user.status)}20; color: {getStatusColor(user.status)};">
                      <i class="fas fa-circle"></i>
                      {user.status}
                    </span>
                  {/if}
                </td>
                
                <!-- Join Date -->
                <td class="date-cell">
                  {#if editRowId === user.id}
                    <input 
                      type="date" 
                      bind:value={editForm.joinDate}
                      class="edit-input"
                    />
                  {:else}
                    {formatDate(user.joinDate)}
                  {/if}
                </td>
                
                
                <!-- Actions -->
                <td class="actions-cell">
                  {#if editRowId === user.id}
                    <div class="edit-actions">
                      <button on:click={saveEdit} class="btn-icon btn-success" title="Save">
                        <i class="fas fa-check"></i>
                      </button>
                      <button on:click={cancelEdit} class="btn-icon btn-secondary" title="Cancel">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  {:else}
                    <div class="action-buttons">
                      <button on:click={() => startEditing(user)} class="btn-icon btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button on:click={() => deleteUser(user.id)} class=" btn-icon btn-danger" title="Delete">
                        <i class="trash icon"></i>
                      </button>
                      <div class="dropdown actions-dropdown">
                        <button class="btn-icon btn-secondary" title="More actions">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                          <button class="dropdown-item">
                            <i class="fas fa-eye"></i> View Profile
                          </button>
                          <button class="dropdown-item">
                            <i class="fas fa-envelope"></i> Send Email
                          </button>
                          <button class="dropdown-item">
                            <i class="fas fa-key"></i> Reset Password
                          </button>
                          <div class="dropdown-divider"></div>
                          <button class="dropdown-item">
                            <i class="fas fa-file-export"></i> Export User
                          </button>
                        </div>
                      </div>
                    </div>
                  {/if}
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="pagination-container">
        <div class="pagination-info">
          Showing {startItem} to {endItem} of {filteredUsers.length} entries
          {#if filters.search || filters.role !== 'all' || filters.status !== 'all'}
            <span class="filtered-info">
              (filtered from {users.length} total users)
            </span>
          {/if}
        </div>
        
        <div class="pagination-controls">
          <div class="pagination-size">
            <label>Show:</label>
            <select bind:value={itemsPerPage} class="page-size-select">
              {#each pageOptions as option}
                <option value={option}>{option}</option>
              {/each}
            </select>
          </div>
          
          <div class="pagination-buttons">
            <button 
              on:click={() => currentPage = 1}
              disabled={currentPage === 1}
              class="pagination-btn"
            >
              <i class="fas fa-angle-double-left"></i>
            </button>
            
            <button 
              on:click={() => currentPage--}
              disabled={currentPage === 1}
              class="pagination-btn"
            >
              <i class="fas fa-angle-left"></i>
            </button>
            
            {#each Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
              let pageNum;
              if (totalPages <= 5) {
                pageNum = i + 1;
              } else if (currentPage <= 3) {
                pageNum = i + 1;
              } else if (currentPage >= totalPages - 2) {
                pageNum = totalPages - 4 + i;
              } else {
                pageNum = currentPage - 2 + i;
              }
              return pageNum;
            }) as pageNum}
              <button 
                on:click={() => currentPage = pageNum}
                class="pagination-btn {currentPage === pageNum ? 'active' : ''}"
              >
                {pageNum}
              </button>
            {/each}
            
            <button 
              on:click={() => currentPage++}
              disabled={currentPage === totalPages}
              class="pagination-btn"
            >
              <i class="fas fa-angle-right"></i>
            </button>
            
            <button 
              on:click={() => currentPage = totalPages}
              disabled={currentPage === totalPages}
              class="pagination-btn"
            >
              <i class="fas fa-angle-double-right"></i>
            </button>
          </div>
        </div>
      </div>
    {/if}
  </div>
</div>

<style>
  .users-list-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8fafc;
    min-height: 100vh;
    padding: 20px;
  }

  /* Header */
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .header-left h1 {
    margin: 0;
    color: #1e293b;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .subtitle {
    color: #64748b;
    margin: 5px 0 0 0;
    font-size: 0.95rem;
  }

  /* Notification */
  .notification {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    animation: slideIn 0.3s ease;
    border-left: 4px solid;
  }

  @keyframes slideIn {
    from {
      transform: translateY(-10px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .notification.success {
    background-color: #f0fdf4;
    color: #166534;
    border-left-color: #22c55e;
  }

  .notification.error {
    background-color: #fef2f2;
    color: #991b1b;
    border-left-color: #ef4444;
  }

  .notification.warning {
    background-color: #fefce8;
    color: #854d0e;
    border-left-color: #f59e0b;
  }

  .close-notification {
    margin-left: auto;
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    opacity: 0.7;
  }

  .close-notification:hover {
    opacity: 1;
  }

  /* Stats Cards */
  .stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
  }

  .stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
  }

  .stat-label {
    color: #64748b;
    font-size: 0.9rem;
    margin-top: 4px;
  }

  /* Controls Section */
  .controls-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 16px;
  }

  .controls-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .search-box {
    position: relative;
    min-width: 300px;
  }

  .search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
  }

  .search-input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: white;
  }

  .search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .clear-search {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
  }

  .clear-search:hover {
    color: #64748b;
  }

  .selected-count {
    padding: 6px 12px;
    background: #eff6ff;
    border-radius: 6px;
  }

  .count-badge {
    font-size: 0.85rem;
    font-weight: 600;
    color: #1d4ed8;
  }

  .controls-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  /* Buttons */
  .btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }

  .btn-primary {
    background: #3b82f6;
    color: white;
  }

  .btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .btn-secondary {
    background: #e2e8f0;
    color: #475569;
  }

  .btn-secondary:hover {
    background: #cbd5e1;
  }

  .btn-success {
    background: #10b981;
    color: white;
  }

  .btn-success:hover {
    background: #059669;
  }

  .btn-danger {
    background: #ef4444;
    color: white;
  }

  .btn-danger:hover {
    background: #dc2626;
  }

  .btn-text {
    background: none;
    color: #3b82f6;
    padding: 8px;
  }

  .btn-text:hover {
    background: #eff6ff;
  }

  .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
  }

  .btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  /* Dropdown */
  .dropdown {
    position: relative;
    display: inline-block;
  }

  .dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 4px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    min-width: 200px;
    z-index: 1000;
    display: none;
    animation: fadeIn 0.2s ease;
  }

  .dropdown:hover .dropdown-menu {
    display: block;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .dropdown-header {
    padding: 12px 16px;
    font-weight: 600;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
  }

  .dropdown-item {
    width: 100%;
    padding: 10px 16px;
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s ease;
  }

  .dropdown-item:hover {
    background: #f1f5f9;
  }

  .dropdown-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 4px 0;
  }

  .dropdown-footer {
    padding: 8px 16px;
    font-size: 0.8rem;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
  }

  /* Export Options */
  .export-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    padding: 12px;
  }

  .export-option {
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }

  .export-option:hover {
    border-color: #3b82f6;
    background: #eff6ff;
  }

  .export-option.active {
    border-color: #3b82f6;
    background: #eff6ff;
  }

  .export-option i {
    font-size: 1.2rem;
    color: #3b82f6;
  }

  /* Filters Panel */
  .filters-panel {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .filters-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }

  .filter-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #475569;
    font-size: 0.9rem;
  }

  .filter-select, .filter-input {
    width: 100%;
    padding: 8px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
  }

  .filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: #3b82f6;
  }

  /* Table Container */
  .table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .loading-state, .empty-state {
    padding: 60px 20px;
    text-align: center;
    color: #64748b;
  }

  .empty-state i {
    margin-bottom: 16px;
    color: #cbd5e1;
  }

  .empty-state h3 {
    margin: 0 0 8px 0;
    color: #475569;
  }

  .empty-state p {
    margin: 0 0 20px 0;
  }

  /* Table */
  .table-responsive {
    overflow-x: auto;
  }

  .users-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
  }

  .users-table thead {
    background: #f8fafc;
  }

  .users-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
    user-select: none;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .users-table th:hover {
    background: #f1f5f9;
  }

  .sortable-header {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .sortable-header i {
    color: #94a3b8;
    font-size: 0.8rem;
  }

  .users-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s ease;
  }

  .users-table tbody tr:hover {
    background: #f8fafc;
  }

  .users-table tbody tr.selected {
    background: #eff6ff;
  }

  .users-table td {
    padding: 16px;
    color: #475569;
  }

  /* Checkbox */
  .checkbox-cell {
    width: 48px;
  }

  .checkbox-wrapper {
    display: inline-block;
    position: relative;
    cursor: pointer;
  }

  .checkbox-input {
    opacity: 0;
    position: absolute;
  }

  .checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #cbd5e1;
    border-radius: 4px;
    display: inline-block;
    position: relative;
    background: white;
    transition: all 0.2s ease;
  }

  .checkbox-input:checked + .checkbox-custom {
    background: #3b82f6;
    border-color: #3b82f6;
  }

  .checkbox-input:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
  }

  /* User Info Cells */
  .id-cell {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #1e293b;
  }

  .user-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .user-name {
    font-weight: 600;
    color: #1e293b;
  }

  .user-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .meta-item {
    font-size: 0.8rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .email-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .email-link {
    color: #3b82f6;
    text-decoration: none;
  }

  .email-link:hover {
    text-decoration: underline;
  }

  /* Badges */
  .role-badge, .status-badge, .verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
  }

  .verified-badge.verified {
    background-color: #d1fae5;
    color: #065f46;
  }

  .verified-badge.unverified {
    background-color: #fef2f2;
    color: #991b1b;
  }

  /* Edit Inputs */
  .edit-input, .edit-select {
    width: 100%;
    padding: 8px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
  }

  .edit-input:focus, .edit-select:focus {
    outline: none;
    border-color: #3b82f6;
  }

  /* Actions */
  .actions-cell {
    width: 150px;
  }

  .action-buttons, .edit-actions {
    display: flex;
    gap: 8px;
  }

  .actions-dropdown .dropdown-menu {
    right: 0;
    left: auto;
  }

  /* Pagination */
  .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: 16px;
  }

  .pagination-info {
    color: #64748b;
    font-size: 0.9rem;
  }

  .filtered-info {
    color: #94a3b8;
    font-size: 0.85rem;
  }

  .pagination-controls {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .pagination-size {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .page-size-select {
    padding: 6px 10px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
  }

  .pagination-buttons {
    display: flex;
    gap: 4px;
  }

  .pagination-btn {
    min-width: 36px;
    height: 36px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  .pagination-btn:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #cbd5e1;
  }

  .pagination-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
  }

  .pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .controls-section {
      flex-direction: column;
      align-items: stretch;
    }
    
    .controls-left, .controls-right {
      width: 100%;
    }
    
    .search-box {
      min-width: 100%;
    }
  }

  @media (max-width: 768px) {
    .header {
      flex-direction: column;
      gap: 16px;
      text-align: center;
    }
    
    .stats-cards {
      grid-template-columns: repeat(2, 1fr);
    }
    
    .pagination-container {
      flex-direction: column;
      align-items: stretch;
      text-align: center;
    }
    
    .pagination-controls {
      justify-content: center;
    }
  }

  @media (max-width: 480px) {
    .stats-cards {
      grid-template-columns: 1fr;
    }
    
    .controls-left {
      flex-direction: column;
      align-items: stretch;
    }
    
    .filters-grid {
      grid-template-columns: 1fr;
    }
    
    .export-options {
      grid-template-columns: 1fr;
    }
  }

  /* Spinner animation */
  .fa-spinner.fa-spin {
    animation: fa-spin 1s infinite linear;
  }

  @keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>

