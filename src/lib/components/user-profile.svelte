<!-- UserProfile.svelte -->
<script>
  import { onMount } from 'svelte';
  
  // User roles and their privileges
  const ROLES = {
    GUEST: { 
      id: 1, 
      name: 'guest', 
      level: 1,
      permissions: ['view_profile']
    },
    USER: { 
      id: 2, 
      name: 'user', 
      level: 2,
      permissions: ['view_profile', 'edit_own_profile', 'upload_avatar', 'view_content']
    },
    EDITOR: { 
      id: 3, 
      name: 'editor', 
      level: 3,
      permissions: ['view_profile', 'edit_own_profile', 'upload_avatar', 'view_content', 'create_content', 'edit_content', 'manage_comments']
    },
    ADMIN: { 
      id: 4, 
      name: 'admin', 
      level: 4,
      permissions: ['all']
    }
  };

  // Current user (simulating logged-in user)
  let currentUser = {
    id: 1,
    username: 'admin_user',
    email: 'admin@example.com',
    role: ROLES.ADMIN,
    isAdmin: true,
    permissions: ['all']
  };

  // Selected user for viewing/editing
  let selectedUser = null;
  
  // User list
  let users = [];
  
  // Form states
  let editMode = false;
  let deleteMode = false;
  let showUserForm = false;
  let showPrivilegeModal = false;
  
  // Form data
  let userForm = {
    id: '',
    firstName: '',
    lastName: '',
    email: '',
    username: '',
    role: 'user',
    bio: '',
    phone: '',
    location: '',
    website: '',
    avatar: '',
    isActive: true,
    joinDate: new Date().toISOString().split('T')[0]
  };
  
  // Privileges for admin to edit
  let availablePermissions = [
    { id: 'view_profile', name: 'View Profile', description: 'Can view user profiles' },
    { id: 'edit_own_profile', name: 'Edit Own Profile', description: 'Can edit their own profile' },
    { id: 'edit_all_profiles', name: 'Edit All Profiles', description: 'Can edit any user profile' },
    { id: 'upload_avatar', name: 'Upload Avatar', description: 'Can upload profile picture' },
    { id: 'view_content', name: 'View Content', description: 'Can view all content' },
    { id: 'create_content', name: 'Create Content', description: 'Can create new content' },
    { id: 'edit_content', name: 'Edit Content', description: 'Can edit existing content' },
    { id: 'delete_content', name: 'Delete Content', description: 'Can delete content' },
    { id: 'manage_comments', name: 'Manage Comments', description: 'Can approve/delete comments' },
    { id: 'manage_users', name: 'Manage Users', description: 'Can add/edit/delete users' },
    { id: 'manage_roles', name: 'Manage Roles', description: 'Can manage user roles and permissions' },
    { id: 'view_analytics', name: 'View Analytics', description: 'Can view site analytics' },
    { id: 'access_settings', name: 'Access Settings', description: 'Can access system settings' }
  ];
  
  // Filter and search
  let searchQuery = '';
  let roleFilter = 'all';
  let activeFilter = 'all';
  
  // UI States
  let isLoading = false;
  let notification = null;
  let confirmAction = null;
  
  // Mock initial users data
  const mockUsers = [
    {
      id: 1,
      username: 'admin_user',
      email: 'admin@example.com',
      firstName: 'Admin',
      lastName: 'User',
      role: ROLES.ADMIN,
      bio: 'System administrator with full access to all features.',
      phone: '+1 (555) 123-4567',
      location: 'New York, USA',
      website: 'https://admin.example.com',
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Admin',
      isActive: true,
      joinDate: '2023-01-15',
      lastLogin: '2024-01-28T10:30:00Z',
      permissions: ['all']
    },
    {
      id: 2,
      username: 'editor_john',
      email: 'john.editor@example.com',
      firstName: 'John',
      lastName: 'Editor',
      role: ROLES.EDITOR,
      bio: 'Content editor with publishing permissions.',
      phone: '+1 (555) 987-6543',
      location: 'London, UK',
      website: 'https://john.example.com',
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=John',
      isActive: true,
      joinDate: '2023-03-20',
      lastLogin: '2024-01-27T14:45:00Z',
      permissions: ROLES.EDITOR.permissions
    },
    {
      id: 3,
      username: 'regular_user',
      email: 'user@example.com',
      firstName: 'Sarah',
      lastName: 'Smith',
      role: ROLES.USER,
      bio: 'Regular user who enjoys our platform.',
      phone: '+1 (555) 456-7890',
      location: 'Toronto, Canada',
      website: '',
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah',
      isActive: true,
      joinDate: '2023-05-10',
      lastLogin: '2024-01-26T09:15:00Z',
      permissions: ROLES.USER.permissions
    },
    {
      id: 4,
      username: 'guest_viewer',
      email: 'guest@example.com',
      firstName: 'Guest',
      lastName: 'Viewer',
      role: ROLES.GUEST,
      bio: 'Guest user with limited access.',
      phone: '',
      location: '',
      website: '',
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Guest',
      isActive: true,
      joinDate: '2023-07-01',
      lastLogin: '2024-01-25T16:20:00Z',
      permissions: ROLES.GUEST.permissions
    },
    {
      id: 5,
      username: 'inactive_user',
      email: 'inactive@example.com',
      firstName: 'Michael',
      lastName: 'Brown',
      role: ROLES.USER,
      bio: 'Inactive user account.',
      phone: '+1 (555) 111-2222',
      location: 'Sydney, Australia',
      website: 'https://michael.example.com',
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Michael',
      isActive: false,
      joinDate: '2023-02-28',
      lastLogin: '2023-12-01T11:00:00Z',
      permissions: ROLES.USER.permissions
    }
  ];
  
  // Initialize
  onMount(() => {
    loadUsers();
    selectUser(mockUsers[0]);
  });
  
  // Load users
  function loadUsers() {
    isLoading = true;
    setTimeout(() => {
      users = [...mockUsers];
      isLoading = false;
      showNotification('Users loaded successfully', 'success');
    }, 500);
  }
  
  // Select a user
  function selectUser(user) {
    selectedUser = { ...user };
    editMode = false;
    deleteMode = false;
  }
  
  // Filtered users
  $: filteredUsers = users.filter(user => {
    const matchesSearch = searchQuery === '' || 
      user.username.toLowerCase().includes(searchQuery.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
      `${user.firstName} ${user.lastName}`.toLowerCase().includes(searchQuery.toLowerCase());
    
    const matchesRole = roleFilter === 'all' || user.role.name === roleFilter;
    const matchesActive = activeFilter === 'all' || 
      (activeFilter === 'active' && user.isActive) || 
      (activeFilter === 'inactive' && !user.isActive);
    
    return matchesSearch && matchesRole && matchesActive;
  });
  
  // Check if current user can edit
  $: canEdit = currentUser.isAdmin || 
    (currentUser.id === selectedUser?.id && currentUser.permissions.includes('edit_own_profile'));
  
  // Check if current user can delete
  $: canDelete = currentUser.isAdmin;
  
  // Check if current user can manage privileges
  $: canManagePrivileges = currentUser.isAdmin;
  
  // Toggle edit mode
  function toggleEditMode() {
    if (!canEdit) {
      showNotification('You do not have permission to edit this profile', 'error');
      return;
    }
    
    editMode = !editMode;
    if (editMode && selectedUser) {
      userForm = { ...selectedUser, role: selectedUser.role.name };
    }
  }
  
  // Save user changes
  async function saveUser() {
    if (!validateForm()) return;
    
    isLoading = true;
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 800));
    
    const updatedUser = {
      ...userForm,
      role: ROLES[userForm.role.toUpperCase()],
      avatar: userForm.avatar || `https://api.dicebear.com/7.x/avataaars/svg?seed=${userForm.username}`
    };
    
    // Update in users array
    const index = users.findIndex(u => u.id === updatedUser.id);
    if (index !== -1) {
      users[index] = updatedUser;
      users = users; // Trigger reactivity
    }
    
    // Update selected user
    selectedUser = updatedUser;
    editMode = false;
    isLoading = false;
    
    showNotification('Profile updated successfully', 'success');
  }
  
  // Validate form
  function validateForm() {
    if (!userForm.username.trim()) {
      showNotification('Username is required', 'error');
      return false;
    }
    
    if (!userForm.email.trim()) {
      showNotification('Email is required', 'error');
      return false;
    }
    
    // Simple email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(userForm.email)) {
      showNotification('Please enter a valid email address', 'error');
      return false;
    }
    
    return true;
  }
  
  // Delete user
  function initiateDelete() {
    if (!canDelete) {
      showNotification('You do not have permission to delete users', 'error');
      return;
    }
    
    deleteMode = true;
    confirmAction = {
      title: 'Delete User',
      message: `Are you sure you want to delete ${selectedUser.username}? This action cannot be undone.`,
      confirmText: 'Delete',
      cancelText: 'Cancel',
      type: 'danger',
      action: performDelete
    };
  }
  
  function performDelete() {
    // Remove user from array
    users = users.filter(u => u.id !== selectedUser.id);
    
    // Select another user
    if (users.length > 0) {
      selectUser(users[0]);
    } else {
      selectedUser = null;
    }
    
    deleteMode = false;
    showNotification('User deleted successfully', 'success');
  }
  
  // Create new user
  function createNewUser() {
    userForm = {
      id: generateId(),
      firstName: '',
      lastName: '',
      email: '',
      username: '',
      role: 'user',
      bio: '',
      phone: '',
      location: '',
      website: '',
      avatar: '',
      isActive: true,
      joinDate: new Date().toISOString().split('T')[0],
      permissions: []
    };
    
    showUserForm = true;
  }
  
  // Save new user
  async function saveNewUser() {
    if (!validateForm()) return;
    
    isLoading = true;
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 800));
    
    const newUser = {
      ...userForm,
      role: ROLES[userForm.role.toUpperCase()],
      avatar: userForm.avatar || `https://api.dicebear.com/7.x/avataaars/svg?seed=${userForm.username}`,
      lastLogin: null,
      permissions: ROLES[userForm.role.toUpperCase()].permissions
    };
    
    // Add to users array
    users = [...users, newUser];
    
    // Select the new user
    selectUser(newUser);
    showUserForm = false;
    isLoading = false;
    
    showNotification('User created successfully', 'success');
  }
  
  // Generate ID (simulate)
  function generateId() {
    return Math.max(...users.map(u => u.id), 0) + 1;
  }
  
  // Manage privileges
  function openPrivilegeModal() {
    if (!canManagePrivileges) {
      showNotification('Only administrators can manage privileges', 'error');
      return;
    }
    
    showPrivilegeModal = true;
  }
  
  function togglePermission(permissionId) {
    if (!selectedUser.permissions) {
      selectedUser.permissions = [];
    }
    
    const index = selectedUser.permissions.indexOf(permissionId);
    if (index === -1) {
      selectedUser.permissions.push(permissionId);
    } else {
      selectedUser.permissions.splice(index, 1);
    }
    
    selectedUser = { ...selectedUser }; // Trigger reactivity
  }
  
  function savePrivileges() {
    // In a real app, this would save to the backend
    showPrivilegeModal = false;
    showNotification('Privileges updated successfully', 'success');
  }
  
  // Toggle user active status
  function toggleUserStatus(user) {
    if (!currentUser.isAdmin) {
      showNotification('Only administrators can change user status', 'error');
      return;
    }
    
    user.isActive = !user.isActive;
    showNotification(`User ${user.isActive ? 'activated' : 'deactivated'}`, 'success');
  }
  
  // Show notification
  function showNotification(message, type = 'info') {
    notification = { message, type };
    setTimeout(() => {
      notification = null;
    }, 3000);
  }
  
  // Format date
  function formatDate(dateString) {
    if (!dateString) return 'Never';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
  
  // Cancel actions
  function cancelAction() {
    editMode = false;
    deleteMode = false;
    showUserForm = false;
    showPrivilegeModal = false;
    confirmAction = null;
  }
</script>

<svelte:head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</svelte:head>

<div class="user-profile-container">
  <!-- Header -->
  <div class="header">
    <div class="header-left">
      <h1><i class="fas fa-users-cog"></i> User Profile Management</h1>
      <p class="subtitle">Manage user accounts, roles, and permissions</p>
    </div>
    <div class="header-right">
      <div class="current-user-info">
        <img 
          src={currentUser.avatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=User'} 
          alt={currentUser.username}
          class="current-user-avatar"
        />
        <div>
          <div class="current-user-name">{currentUser.username}</div>
          <div class="current-user-role" style="color: {getRoleColor(currentUser.role)}">
            {currentUser.role.name}
          </div>
        </div>
      </div>
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

  <!-- Confirmation Modal -->
  {#if confirmAction}
    <div class="modal-overlay" on:click={cancelAction}>
      <div class="modal" on:click|stopPropagation>
        <div class="modal-header {confirmAction.type}">
          <h3>{confirmAction.title}</h3>
          <button on:click={cancelAction} class="modal-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>{confirmAction.message}</p>
        </div>
        <div class="modal-footer">
          <button on:click={cancelAction} class="btn btn-secondary">
            {confirmAction.cancelText}
          </button>
          <button on:click={confirmAction.action} class="btn {confirmAction.type === 'danger' ? 'btn-danger' : 'btn-primary'}">
            {confirmAction.confirmText}
          </button>
        </div>
      </div>
    </div>
  {/if}

  <!-- Main Content -->
  <div class="main-content">
    <!-- Left Sidebar - User List -->
    <div class="sidebar">
      <div class="sidebar-header">
        <h3><i class="fas fa-list"></i> Users ({filteredUsers.length})</h3>
        <button 
          on:click={createNewUser} 
          class="btn btn-primary btn-sm"
          disabled={!currentUser.isAdmin}
          title={currentUser.isAdmin ? 'Add new user' : 'Only admins can create users'}
        >
          <i class="fas fa-user-plus"></i> Add User
        </button>
      </div>

      <!-- Filters -->
      <div class="filters">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input 
            type="text" 
            bind:value={searchQuery}
            placeholder="Search users..."
            class="search-input"
          />
        </div>

        <div class="filter-group">
          <label>Role:</label>
          <select bind:value={roleFilter} class="filter-select">
            <option value="all">All Roles</option>
            <option value="guest">Guest</option>
            <option value="user">User</option>
            <option value="editor">Editor</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Status:</label>
          <select bind:value={activeFilter} class="filter-select">
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <!-- User List -->
      <div class="user-list">
        {#if isLoading}
          <div class="loading">Loading users...</div>
        {:else if filteredUsers.length === 0}
          <div class="empty-state">
            <i class="fas fa-user-slash"></i>
            <p>No users found</p>
          </div>
        {:else}
          {#each filteredUsers as user}
            <!-- svelte-ignore a11y_click_events_have_key_events -->
            <!-- svelte-ignore a11y_no_static_element_interactions -->
            <div 
              class="user-list-item {selectedUser?.id === user.id ? 'active' : ''} {user.isActive ? '' : 'inactive'}"
              on:click={() => selectUser(user)}
            >
              <div class="user-avatar">
                <img src={user.avatar} alt={user.username} />
                <div class="status-indicator {user.isActive ? 'active' : 'inactive'}"></div>
              </div>
              <div class="user-info">
                <div class="user-name">
                  {user.firstName} {user.lastName}
                  <!-- {user.id === currentUser.id ? <span class="you-badge">You</span>} -->
                </div>
                <div class="user-username">@{user.username}</div>
                <div class="user-role" style="color: {getRoleColor(user.role)}">
                  {user.role.name}
                </div>
              </div>
              <div class="user-actions">
                <button 
                  on:click|stopPropagation={() => toggleUserStatus(user)}
                  class="status-toggle"
                  title={user.isActive ? 'Deactivate user' : 'Activate user'}
                >
                  <i class="fas fa-power-off {user.isActive ? 'active' : 'inactive'}"></i>
                </button>
              </div>
            </div>
          {/each}
        {/if}
      </div>
    </div>

    <!-- Main Panel - User Details -->
    <div class="main-panel">
      {#if isLoading}
        <div class="loading-panel">Loading user details...</div>
      {:else if !selectedUser}
        <div class="empty-panel">
          <i class="fas fa-user-circle"></i>
          <h3>Select a user to view details</h3>
          <p>Choose a user from the list to see their profile information</p>
        </div>
      {:else}
        <!-- User Profile Header -->
        <div class="profile-header">
          <div class="profile-avatar">
            <img src={selectedUser.avatar} alt={selectedUser.username} />
            <div class="avatar-overlay">
              <button class="avatar-upload" title="Change avatar">
                <i class="fas fa-camera"></i>
              </button>
            </div>
          </div>
          <div class="profile-info">
            <h2>{selectedUser.firstName} {selectedUser.lastName}</h2>
            <div class="profile-meta">
              <span class="username">@{selectedUser.username}</span>
              <span class="role-badge" style="background-color: {getRoleColor(selectedUser.role)}">
                {selectedUser.role.name}
              </span>
              <span class="status-badge {selectedUser.isActive ? 'active' : 'inactive'}">
                {selectedUser.isActive ? 'Active' : 'Inactive'}
              </span>
              <!-- {selectedUser.id === currentUser.id  <span class="you-badge">This is you</span> -->
            </div>
            <p class="profile-bio">{selectedUser.bio || 'No bio provided'}</p>
          </div>
          <div class="profile-actions">
            {#if canEdit}
              <button on:click={toggleEditMode} class="btn {editMode ? 'btn-secondary' : 'btn-primary'}">
                <i class="fas fa-{editMode ? 'times' : 'edit'}"></i>
                {editMode ? 'Cancel Edit' : 'Edit Profile'}
              </button>
            {/if}
            
            {#if canManagePrivileges}
              <button on:click={openPrivilegeModal} class="btn btn-warning">
                <i class="fas fa-user-shield"></i> Manage Privileges
              </button>
            {/if}
            
            {#if canDelete && selectedUser.id !== currentUser.id}
              <button on:click={initiateDelete} class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete User
              </button>
            {/if}
          </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
          {#if editMode}
            <!-- Edit Form -->
            <form class="edit-form" on:submit|preventDefault={saveUser}>
              <div class="form-grid">
                <div class="form-group">
                  <label for="firstName">First Name</label>
                  <input 
                    id="firstName"
                    type="text" 
                    bind:value={userForm.firstName}
                    class="form-input"
                  />
                </div>
                
                <div class="form-group">
                  <label for="lastName">Last Name</label>
                  <input 
                    id="lastName"
                    type="text" 
                    bind:value={userForm.lastName}
                    class="form-input"
                  />
                </div>
                
                <div class="form-group">
                  <label for="username">Username *</label>
                  <input 
                    id="username"
                    type="text" 
                    bind:value={userForm.username}
                    class="form-input"
                    required
                  />
                </div>
                
                <div class="form-group">
                  <label for="email">Email *</label>
                  <input 
                    id="email"
                    type="email" 
                    bind:value={userForm.email}
                    class="form-input"
                    required
                  />
                </div>
                
                <div class="form-group">
                  <label for="role">Role</label>
                  <select 
                    id="role"
                    bind:value={userForm.role}
                    class="form-select"
                    disabled={!currentUser.isAdmin}
                  >
                    <option value="guest">Guest</option>
                    <option value="user">User</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                  </select>
                  {#if !currentUser.isAdmin}
                    <small class="form-hint">Only administrators can change roles</small>
                  {/if}
                </div>
                
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input 
                    id="phone"
                    type="tel" 
                    bind:value={userForm.phone}
                    class="form-input"
                  />
                </div>
                
                <div class="form-group">
                  <label for="location">Location</label>
                  <input 
                    id="location"
                    type="text" 
                    bind:value={userForm.location}
                    class="form-input"
                  />
                </div>
                
                <div class="form-group">
                  <label for="website">Website</label>
                  <input 
                    id="website"
                    type="url" 
                    bind:value={userForm.website}
                    class="form-input"
                  />
                </div>
                
                <div class="form-group full-width">
                  <label for="bio">Bio</label>
                  <textarea 
                    id="bio"
                    bind:value={userForm.bio}
                    class="form-textarea"
                    rows="3"
                  ></textarea>
                </div>
                
                <div class="form-group">
                  <label class="checkbox-label">
                    <input 
                      type="checkbox" 
                      bind:checked={userForm.isActive}
                      class="form-checkbox"
                      disabled={!currentUser.isAdmin}
                    />
                    <span>Active Account</span>
                  </label>
                  {#if !currentUser.isAdmin}
                    <small class="form-hint">Only administrators can change status</small>
                  {/if}
                </div>
              </div>
              
              <div class="form-actions">
                <button type="button" on:click={toggleEditMode} class="btn btn-secondary">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" disabled={isLoading}>
                  {#if isLoading}
                    <i class="fas fa-spinner fa-spin"></i> Saving...
                  {:else}
                    <i class="fas fa-save"></i> Save Changes
                  {/if}
                </button>
              </div>
            </form>
          {:else}
            <!-- View Mode -->
            <div class="view-mode">
              <div class="info-grid">
                <div class="info-card">
                  <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <div class="info-content">
                    <div class="info-label">Email</div>
                    <div class="info-value">{selectedUser.email}</div>
                  </div>
                </div>
                
                <div class="info-card">
                  <div class="info-icon">
                    <i class="fas fa-phone"></i>
                  </div>
                  <div class="info-content">
                    <div class="info-label">Phone</div>
                    <div class="info-value">{selectedUser.phone || 'Not provided'}</div>
                  </div>
                </div>
                
                <div class="info-card">
                  <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="info-content">
                    <div class="info-label">Location</div>
                    <div class="info-value">{selectedUser.location || 'Not provided'}</div>
                  </div>
                </div>
                
                <div class="info-card">
                  <div class="info-icon">
                    <i class="fas fa-globe"></i>
                  </div>
                  <div class="info-content">
                    <div class="info-label">Website</div>
                    <div class="info-value">
                      {#if selectedUser.website}
                        <a href={selectedUser.website} target="_blank">{selectedUser.website}</a>
                      {:else}
                        Not provided
                      {/if}
                    </div>
                  </div>
                </div>
                
                <div class="info-card">
                  <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                  <div class="info-content">
                    <div class="info-label">Join Date</div>
                    <div class="info-value">{formatDate(selectedUser.joinDate)}</div>
                  </div>
                </div>
                
                <div class="info-card">
                  <div class="info-icon">
                    <i class="fas fa-sign-in-alt"></i>
                  </div>
                  <div class="info-content">
                    <div class="info-label">Last Login</div>
                    <div class="info-value">{formatDate(selectedUser.lastLogin)}</div>
                  </div>
                </div>
              </div>
              
              <!-- Permissions Section -->
              <div class="permissions-section">
                <h3><i class="fas fa-shield-alt"></i> User Permissions</h3>
                <div class="permissions-grid">
                  {#each availablePermissions as permission}
                    <div class="permission-item {selectedUser.permissions?.includes(permission.id) || selectedUser.permissions?.includes('all') ? 'granted' : 'denied'}">
                      <div class="permission-checkbox">
                        <i class="fas fa-{selectedUser.permissions?.includes(permission.id) || selectedUser.permissions?.includes('all') ? 'check-circle' : 'times-circle'}"></i>
                      </div>
                      <div class="permission-info">
                        <div class="permission-name">{permission.name}</div>
                        <div class="permission-desc">{permission.description}</div>
                      </div>
                    </div>
                  {/each}
                </div>
              </div>
            </div>
          {/if}
        </div>
      {/if}
    </div>
  </div>

  <!-- New User Modal -->
  {#if showUserForm}
    <div class="modal-overlay" on:click={cancelAction}>
      <div class="modal modal-lg" on:click|stopPropagation>
        <div class="modal-header">
          <h3><i class="fas fa-user-plus"></i> Create New User</h3>
          <button on:click={cancelAction} class="modal-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form class="edit-form" on:submit|preventDefault={saveNewUser}>
            <div class="form-grid">
              <div class="form-group">
                <label for="newFirstName">First Name</label>
                <input 
                  id="newFirstName"
                  type="text" 
                  bind:value={userForm.firstName}
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="newLastName">Last Name</label>
                <input 
                  id="newLastName"
                  type="text" 
                  bind:value={userForm.lastName}
                  class="form-input"
                />
              </div>
              
              <div class="form-group">
                <label for="newUsername">Username *</label>
                <input 
                  id="newUsername"
                  type="text" 
                  bind:value={userForm.username}
                  class="form-input"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="newEmail">Email *</label>
                <input 
                  id="newEmail"
                  type="email" 
                  bind:value={userForm.email}
                  class="form-input"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="newRole">Role</label>
                <select 
                  id="newRole"
                  bind:value={userForm.role}
                  class="form-select"
                >
                  <option value="guest">Guest</option>
                  <option value="user">User</option>
                  <option value="editor">Editor</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="newPassword">Password *</label>
                <input 
                  id="newPassword"
                  type="password" 
                  value="password123" 
                  class="form-input"
                  disabled
                />
                <small class="form-hint">Default password will be sent to user's email</small>
              </div>
              
              <div class="form-group full-width">
                <label for="newBio">Bio</label>
                <textarea 
                  id="newBio"
                  bind:value={userForm.bio}
                  class="form-textarea"
                  rows="3"
                ></textarea>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="button" on:click={cancelAction} class="btn btn-secondary">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary" disabled={isLoading}>
                {#if isLoading}
                  <i class="fas fa-spinner fa-spin"></i> Creating...
                {:else}
                  <i class="fas fa-user-plus"></i> Create User
                {/if}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  {/if}

  <!-- Privilege Management Modal -->
  {#if showPrivilegeModal}
    <div class="modal-overlay" on:click={cancelAction}>
      <div class="modal modal-lg" on:click|stopPropagation>
        <div class="modal-header">
          <h3><i class="fas fa-user-shield"></i> Manage Privileges: {selectedUser?.username}</h3>
          <button on:click={cancelAction} class="modal-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="privilege-header">
            <div class="user-summary">
              <img src={selectedUser?.avatar} alt={selectedUser?.username} class="privilege-avatar" />
              <div>
                <h4>{selectedUser?.firstName} {selectedUser?.lastName}</h4>
                <div class="privilege-role" style="color: {getRoleColor(selectedUser?.role)}">
                  {selectedUser?.role?.name}
                </div>
              </div>
            </div>
            <div class="privilege-summary">
              <div class="summary-item">
                <div class="summary-value">{selectedUser?.permissions?.length || 0}</div>
                <div class="summary-label">Permissions</div>
              </div>
              <div class="summary-item">
                <div class="summary-value">{selectedUser?.role?.level}</div>
                <div class="summary-label">Role Level</div>
              </div>
            </div>
          </div>
          
          <div class="privilege-grid">
            {#each availablePermissions as permission}
              <div 
                class="privilege-item {selectedUser?.permissions?.includes(permission.id) || selectedUser?.permissions?.includes('all') ? 'active' : ''}"
                on:click={() => togglePermission(permission.id)}
              >
                <div class="privilege-checkbox">
                  <input 
                    type="checkbox" 
                    checked={selectedUser?.permissions?.includes(permission.id) || selectedUser?.permissions?.includes('all')}
                    disabled={selectedUser?.permissions?.includes('all')}
                  />
                </div>
                <div class="privilege-info">
                  <div class="privilege-name">{permission.name}</div>
                  <div class="privilege-desc">{permission.description}</div>
                </div>
                <div class="privilege-action">
                  {#if permission.id === 'all'}
                    <span class="privilege-badge">All Access</span>
                  {/if}
                </div>
              </div>
            {/each}
          </div>
          
          <div class="privilege-actions">
            <button on:click={() => selectedUser.permissions = ['all']} class="btn btn-secondary">
              <i class="fas fa-crown"></i> Grant All Permissions
            </button>
            <button on:click={() => selectedUser.permissions = []} class="btn btn-secondary">
              <i class="fas fa-ban"></i> Revoke All Permissions
            </button>
            <button on:click={() => selectedUser.permissions = selectedUser.role.permissions} class="btn btn-secondary">
              <i class="fas fa-undo"></i> Reset to Role Default
            </button>
          </div>
        </div>
        <div class="modal-footer">
          <button on:click={cancelAction} class="btn btn-secondary">
            Cancel
          </button>
          <button on:click={savePrivileges} class="btn btn-primary">
            <i class="fas fa-save"></i> Save Privileges
          </button>
        </div>
      </div>
    </div>
  {/if}
</div>

<style>
  .user-profile-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 20px;
  }

  /* Header */
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    border-radius: 15px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .header-left h1 {
    margin: 0;
    color: #2d3748;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .subtitle {
    color: #718096;
    margin: 5px 0 0 0;
    font-size: 0.95rem;
  }

  .current-user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 15px;
    background: #f8fafc;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
  }

  .current-user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid #3B82F6;
  }

  .current-user-name {
    font-weight: 600;
    color: #2d3748;
  }

  .current-user-role {
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: uppercase;
  }

  /* Notification */
  .notification {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    animation: slideIn 0.3s ease;
  }

  @keyframes slideIn {
    from {
      transform: translateY(-20px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .notification.success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
  }

  .notification.error {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
  }

  .notification.info {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
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

  /* Main Content */
  .main-content {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 2rem;
    min-height: calc(100vh - 200px);
  }

  /* Sidebar */
  .sidebar {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
  }

  .sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
  }

  .sidebar-header h3 {
    margin: 0;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .btn {
    padding: 0.6rem 1.2rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
  }

  .btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }

  .btn-primary {
    background: linear-gradient(135deg, #3B82F6 0%, #2563eb 100%);
    color: white;
  }

  .btn-secondary {
    background-color: #e2e8f0;
    color: #475569;
  }

  .btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
  }

  .btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
  }

  /* Filters */
  .filters {
    margin-bottom: 1.5rem;
  }

  .search-box {
    position: relative;
    margin-bottom: 1rem;
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
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: border-color 0.3s ease;
  }

  .search-input:focus {
    outline: none;
    border-color: #3B82F6;
  }

  .filter-group {
    margin-bottom: 0.75rem;
  }

  .filter-group label {
    display: block;
    margin-bottom: 0.3rem;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
  }

  .filter-select {
    width: 100%;
    padding: 0.6rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    background-color: white;
    color: #334155;
  }

  /* User List */
  .user-list {
    flex: 1;
    overflow-y: auto;
    max-height: 500px;
  }

  .user-list-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }

  .user-list-item:hover {
    background-color: #f8fafc;
    border-color: #e2e8f0;
  }

  .user-list-item.active {
    background-color: #e0f2fe;
    border-color: #38bdf8;
  }

  .user-list-item.inactive {
    opacity: 0.6;
  }

  .user-avatar {
    position: relative;
  }

  .user-avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
  }

  .status-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
  }

  .status-indicator.active {
    background-color: #10B981;
  }

  .status-indicator.inactive {
    background-color: #ef4444;
  }

  .user-info {
    flex: 1;
  }

  .user-name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .user-username {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 2px;
  }

  .user-role {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .you-badge {
    background-color: #3B82F6;
    color: white;
    font-size: 0.7rem;
    padding: 1px 6px;
    border-radius: 10px;
  }

  .status-toggle {
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    font-size: 1.1rem;
    transition: color 0.3s ease;
  }

  .status-toggle:hover .fa-power-off {
    color: #ef4444;
  }

  .status-toggle .fa-power-off.active {
    color: #10B981;
  }

  .status-toggle .fa-power-off.inactive {
    color: #ef4444;
  }

  /* Main Panel */
  .main-panel {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .loading-panel, .empty-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #64748b;
  }

  .empty-panel i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #cbd5e1;
  }

  /* Profile Header */
  .profile-header {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #f1f5f9;
  }

  .profile-avatar {
    position: relative;
    width: 120px;
    height: 120px;
  }

  .profile-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .avatar-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
  }

  .avatar-upload {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3B82F6 0%, #2563eb 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }

  .avatar-upload:hover {
    transform: scale(1.1);
  }

  .profile-info {
    flex: 1;
  }

  .profile-info h2 {
    margin: 0 0 0.5rem 0;
    color: #1e293b;
  }

  .profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 1rem;
  }

  .username {
    color: #64748b;
    font-weight: 500;
  }

  .role-badge {
    padding: 4px 12px;
    border-radius: 20px;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  .status-badge.active {
    background-color: #d1fae5;
    color: #065f46;
  }

  .status-badge.inactive {
    background-color: #fee2e2;
    color: #991b1b;
  }

  .profile-bio {
    color: #64748b;
    line-height: 1.6;
    margin: 0;
  }

  .profile-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 200px;
  }

  /* Forms */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #475569;
  }

  .form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background-color: white;
  }

  .form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .form-hint {
    display: block;
    margin-top: 0.3rem;
    font-size: 0.8rem;
    color: #94a3b8;
  }

  .checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
  }

  .form-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f1f5f9;
  }

  /* View Mode */
  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background-color: #f8fafc;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
  }

  .info-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
  }

  .info-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: linear-gradient(135deg, #3B82F6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
  }

  .info-content {
    flex: 1;
  }

  .info-label {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.3rem;
  }

  .info-value {
    font-weight: 600;
    color: #1e293b;
    word-break: break-all;
  }

  .info-value a {
    color: #3B82F6;
    text-decoration: none;
  }

  .info-value a:hover {
    text-decoration: underline;
  }

  /* Permissions */
  .permissions-section {
    margin-top: 2rem;
  }

  .permissions-section h3 {
    color: #1e293b;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
  }

  .permission-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1rem;
    background-color: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid;
  }

  .permission-item.granted {
    border-left-color: #10B981;
    background-color: #f0fdf4;
  }

  .permission-item.denied {
    border-left-color: #ef4444;
    background-color: #fef2f2;
  }

  .permission-checkbox {
    font-size: 1.2rem;
  }

  .permission-item.granted .permission-checkbox {
    color: #10B981;
  }

  .permission-item.denied .permission-checkbox {
    color: #ef4444;
  }

  .permission-info {
    flex: 1;
  }

  .permission-name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 2px;
  }

  .permission-desc {
    font-size: 0.85rem;
    color: #64748b;
  }

  /* Modals */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .modal {
    background: white;
    border-radius: 15px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp 0.3s ease;
  }

  @keyframes slideUp {
    from {
      transform: translateY(30px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .modal-lg {
    max-width: 800px;
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 2px solid #f1f5f9;
  }

  .modal-header h3 {
    margin: 0;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .modal-header.danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .modal-header.danger h3 {
    color: white;
  }

  .modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #64748b;
  }

  .modal-header.danger .modal-close {
    color: white;
  }

  .modal-body {
    padding: 2rem;
  }

  .modal-footer {
    padding: 1.5rem 2rem;
    border-top: 2px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
  }

  /* Privilege Modal */
  .privilege-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f1f5f9;
  }

  .user-summary {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .privilege-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
  }

  .privilege-summary {
    display: flex;
    gap: 2rem;
  }

  .summary-item {
    text-align: center;
  }

  .summary-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
  }

  .summary-label {
    font-size: 0.85rem;
    color: #64748b;
  }

  .privilege-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    max-height: 400px;
    overflow-y: auto;
    padding: 1rem;
    background-color: #f8fafc;
    border-radius: 10px;
    margin-bottom: 1.5rem;
  }

  .privilege-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
  }

  .privilege-item:hover {
    border-color: #cbd5e1;
  }

  .privilege-item.active {
    border-color: #3B82F6;
    background-color: #eff6ff;
  }

  .privilege-checkbox input {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .privilege-info {
    flex: 1;
  }

  .privilege-name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 2px;
  }

  .privilege-desc {
    font-size: 0.85rem;
    color: #64748b;
  }

  .privilege-badge {
    font-size: 0.7rem;
    padding: 3px 8px;
    background-color: #f59e0b;
    color: white;
    border-radius: 12px;
    font-weight: 600;
  }

  .privilege-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  /* Responsive */
  @media (max-width: 1200px) {
    .main-content {
      grid-template-columns: 1fr;
    }
    
    .sidebar {
      max-height: 400px;
    }
  }

  @media (max-width: 768px) {
    .header {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .profile-header {
      flex-direction: column;
      text-align: center;
    }
    
    .profile-actions {
      flex-direction: row;
      justify-content: center;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .info-grid {
      grid-template-columns: 1fr;
    }
    
    .permissions-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Loading */
  .loading, .loading-panel {
    text-align: center;
    padding: 2rem;
    color: #64748b;
  }

  .fa-spinner.fa-spin {
    animation: fa-spin 1s infinite linear;
  }

  @keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #94a3b8;
  }

  .empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
</style>

<script context="module">
  // Helper functions
  export function getRoleColor(role) {
    if (!role) return '#94a3b8';
    
    const colors = {
      'guest': '#94a3b8',
      'user': '#3B82F6',
      'editor': '#8B5CF6',
      'admin': '#EF4444'
    };
    
    return colors[role.name] || '#94a3b8';
  }
</script>