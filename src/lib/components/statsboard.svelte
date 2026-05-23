<!-- StatsDashboard.svelte -->
<script>
  import { onMount } from 'svelte';
  
  // Initial statistics data
  let stats = [
    { title: 'users', value: 0, desc: 'Active users on our platform', icon: 'users', color: '#3B82F6', maxValue: 10000 },
    { title: 'activities', value: 0, desc: 'Completed activities this month', icon: 'running', color: '#10B981', maxValue: 5000 },
    { title: 'events', value: 0, desc: 'Upcoming events scheduled', icon: 'calendar-alt', color: '#8B5CF6', maxValue: 200 },
    { title: 'articles', value: 0, desc: 'Published articles this year', icon: 'newspaper', color: '#F59E0B', maxValue: 500 },
    { title: 'gallery', value: 0, desc: 'Images in our gallery', icon: 'images', color: '#EF4444', maxValue: 1000 }
  ];
  
  let activeTab = 'dashboard';
  let isLoading = true;
  let totalValue = 0;
  
  // Mock API data for demonstration
  const mockData = {
    users: 8567,
    activities: 3421,
    events: 148,
    articles: 387,
    gallery: 892
  };
  
  // Function to fetch/update stats
  async function fetchStats() {
    isLoading = true;
    
    // Simulate API call delay
    await new Promise(resolve => setTimeout(resolve, 800));
    
    // Update stats with mock data
    stats = stats.map(stat => ({
      ...stat,
      value: mockData[stat.title] || 0
    }));
    
    calculateTotal();
    isLoading = false;
  }
  
  // Function to calculate total
  function calculateTotal() {
    totalValue = stats.reduce((sum, stat) => sum + stat.value, 0);
  }
  
  // Function to update a specific stat
  function updateStat(title, newValue) {
    const index = stats.findIndex(stat => stat.title === title);
    if (index !== -1) {
      stats[index].value = newValue;
      stats = stats; // Trigger reactivity
      calculateTotal();
    }
  }
  
  // Function to increment all stats
  function incrementAll() {
    stats = stats.map(stat => ({
      ...stat,
      value: Math.min(stat.value + Math.floor(Math.random() * 100) + 1, stat.maxValue)
    }));
    calculateTotal();
  }
  
  // Function to reset all stats
  function resetAll() {
    stats = stats.map(stat => ({
      ...stat,
      value: 0
    }));
    calculateTotal();
  }
  
  // Initialize on mount
  onMount(() => {
    fetchStats();
  });
</script>

<svelte:head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</svelte:head>

<div class="stats-dashboard">
  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <h2><i class="fas fa-chart-line"></i> Statistics Dashboard</h2>
    <div class="header-actions">
      <button class="btn btn-primary" on:click={fetchStats} disabled={isLoading}>
        <i class="fas fa-sync-alt {isLoading ? 'fa-spin' : ''}"></i>
        {isLoading ? 'Loading...' : 'Refresh Data'}
      </button>
      <button class="btn btn-secondary" on:click={incrementAll}>
        <i class="fas fa-plus"></i> Increment All
      </button>
      <button class="btn btn-danger" on:click={resetAll}>
        <i class="fas fa-undo"></i> Reset All
      </button>
    </div>
  </div>
  
  <!-- Tabs Navigation -->
  <div class="tabs-nav">
    <button 
      class:active={activeTab === 'dashboard'}
      on:click={() => activeTab = 'dashboard'}
    >
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </button>
    <button 
      class:active={activeTab === 'stats'}
      on:click={() => activeTab = 'stats'}
    >
      <i class="fas fa-chart-bar"></i> Statistics Table
    </button>
    <button 
      class:active={activeTab === 'edit'}
      on:click={() => activeTab = 'edit'}
    >
      <i class="fas fa-edit"></i> Edit Stats
    </button>
  </div>
  
  <!-- Dashboard Tab -->
  {#if activeTab === 'dashboard'}
    <div class="tab-content">
      <!-- Total Stats Card -->
      <div class="total-card">
        <div class="total-content">
          <h3><i class="fas fa-chart-pie"></i> Total Count</h3>
          <div class="total-value">{totalValue.toLocaleString()}</div>
          <p>Combined total of all statistics</p>
        </div>
        <div class="total-icon">
          <i class="fas fa-database"></i>
        </div>
      </div>
      
      <!-- Stats Cards Grid -->
      <div class="stats-grid">
        {#each stats as stat, i}
          <div class="stat-card" style="border-left-color: {stat.color}">
            <div class="stat-header">
              <div class="stat-icon" style="background-color: {stat.color}">
                <i class="fas fa-{stat.icon}"></i>
              </div>
              <div class="stat-title">
                <h3>{stat.title.charAt(0).toUpperCase() + stat.title.slice(1)}</h3>
                <p>{stat.desc}</p>
              </div>
            </div>
            
            <div class="stat-value">
              <span class="value">{stat.value.toLocaleString()}</span>
              <span class="max">/ {stat.maxValue.toLocaleString()}</span>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-container">
              <div 
                class="progress-bar" 
                style="width: {Math.min((stat.value / stat.maxValue) * 100, 100)}%; background-color: {stat.color}"
              ></div>
            </div>
            
            <div class="stat-footer">
              <span class="percentage">{Math.round((stat.value / stat.maxValue) * 100)}%</span>
              <div class="stat-actions">
                <button 
                  class="btn-small" 
                  on:click={() => updateStat(stat.title, Math.min(stat.value + 100, stat.maxValue))}
                  style="background-color: {stat.color}"
                >
                  <i class="fas fa-plus"></i> 100
                </button>
                <button 
                  class="btn-small btn-danger" 
                  on:click={() => updateStat(stat.title, Math.max(stat.value - 100, 0))}
                >
                  <i class="fas fa-minus"></i> 100
                </button>
              </div>
            </div>
          </div>
        {/each}
      </div>
      
      <!-- Charts Section -->
      <div class="charts-section">
        <div class="chart-card">
          <h3><i class="fas fa-chart-bar"></i> Distribution Chart</h3>
          <div class="chart-container">
            {#each stats as stat}
              <div class="chart-bar" title="{stat.title}: {stat.value}">
                <div 
                  class="bar-fill" 
                  style="height: {(stat.value / Math.max(...stats.map(s => s.value))) * 100}%; background-color: {stat.color}"
                ></div>
                <span class="bar-label">{stat.title.charAt(0).toUpperCase()}</span>
              </div>
            {/each}
          </div>
        </div>
        
        <div class="chart-card">
          <h3><i class="fas fa-chart-pie"></i> Percentage Breakdown</h3>
          <div class="pie-chart">
            <svg viewBox="0 0 100 100" class="pie-svg">
              {#each stats as stat, i}
                <circle 
                  cx="50" 
                  cy="50" 
                  r="40" 
                  fill="transparent" 
                  stroke={stat.color}
                  stroke-width="20"
                  stroke-dasharray="{(stat.value / totalValue) * 251.2} 251.2"
                  stroke-dashoffset={getStrokeOffset(i)}
                  class="pie-slice"
                />
              {/each}
            </svg>
            <div class="pie-center">
              <div class="pie-total">{totalValue.toLocaleString()}</div>
              <div class="pie-label">Total</div>
            </div>
          </div>
          <div class="pie-legend">
            {#each stats as stat}
              <div class="legend-item">
                <span class="legend-color" style="background-color: {stat.color}"></span>
                <span class="legend-label">{stat.title}</span>
                <span class="legend-value">{(stat.value / totalValue * 100).toFixed(1)}%</span>
              </div>
            {/each}
          </div>
        </div>
      </div>
    </div>
  {/if}
  
  <!-- Statistics Table Tab -->
  {#if activeTab === 'stats'}
    <div class="tab-content">
      <div class="table-container">
        <h3><i class="fas fa-table"></i> Statistics Data Table</h3>
        <table class="stats-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Value</th>
              <th>Description</th>
              <th>Max Value</th>
              <th>Percentage</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {#each stats as stat}
              <tr>
                <td class="title-cell">
                  <div class="title-content">
                    <div class="title-icon" style="background-color: {stat.color}">
                      <i class="fas fa-{stat.icon}"></i>
                    </div>
                    <span>{stat.title.toUpperCase()}</span>
                  </div>
                </td>
                <td class="value-cell">
                  <strong style="color: {stat.color}">{stat.value.toLocaleString()}</strong>
                </td>
                <td class="desc-cell">{stat.desc}</td>
                <td class="max-cell">{stat.maxValue.toLocaleString()}</td>
                <td class="percentage-cell">
                  <div class="percentage-display">
                    <span>{Math.round((stat.value / stat.maxValue) * 100)}%</span>
                    <div class="mini-progress">
                      <div 
                        class="mini-progress-bar" 
                        style="width: {Math.min((stat.value / stat.maxValue) * 100, 100)}%; background-color: {stat.color}"
                      ></div>
                    </div>
                  </div>
                </td>
                <td class="actions-cell">
                  <button 
                    class="table-btn" 
                    on:click={() => updateStat(stat.title, Math.min(stat.value + 50, stat.maxValue))}
                    style="background-color: {stat.color}"
                  >
                    <i class="fas fa-plus"></i>
                  </button>
                  <button 
                    class="table-btn btn-danger" 
                    on:click={() => updateStat(stat.title, Math.max(stat.value - 50, 0))}
                  >
                    <i class="fas fa-minus"></i>
                  </button>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    </div>
  {/if}
  
  <!-- Edit Stats Tab -->
  {#if activeTab === 'edit'}
    <div class="tab-content">
      <div class="edit-container">
        <h3><i class="fas fa-edit"></i> Edit Statistics Values</h3>
        <p class="edit-description">Update the values for each statistic. Changes are reflected immediately.</p>
        
        <div class="edit-grid">
          {#each stats as stat, i}
            <div class="edit-card">
              <div class="edit-header" style="border-bottom-color: {stat.color}">
                <h4>{stat.title.charAt(0).toUpperCase() + stat.title.slice(1)}</h4>
                <div class="edit-icon" style="background-color: {stat.color}">
                  <i class="fas fa-{stat.icon}"></i>
                </div>
              </div>
              
              <div class="edit-body">
                <div class="edit-field">
                  <label for="{stat.title}-value">Current Value:</label>
                  <div class="value-display" style="color: {stat.color}">
                    {stat.value.toLocaleString()}
                  </div>
                </div>
                
                <div class="edit-field">
                  <label for="{stat.title}-new">New Value:</label>
                  <input 
                    type="number" 
                    id="{stat.title}-new"
                    bind:value={stat.value}
                    min="0"
                    max={stat.maxValue}
                    class="edit-input"
                  />
                </div>
                
                <div class="edit-field">
                  <label>Range:</label>
                  <input 
                    type="range" 
                    min="0" 
                    max={stat.maxValue}
                    bind:value={stat.value}
                    class="edit-slider"
                    style="--track-color: {stat.color}"
                  />
                </div>
                
                <div class="edit-actions">
                  <button 
                    class="edit-btn" 
                    on:click={() => stat.value = Math.min(stat.value + 100, stat.maxValue)}
                    style="background-color: {stat.color}"
                  >
                    <i class="fas fa-plus"></i> Add 100
                  </button>
                  <button 
                    class="edit-btn btn-danger" 
                    on:click={() => stat.value = 0}
                  >
                    <i class="fas fa-trash"></i> Reset
                  </button>
                </div>
              </div>
            </div>
          {/each}
        </div>
      </div>
    </div>
  {/if}
  

</div>

<style>
  .stats-dashboard {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 1400px;
    margin: 2rem auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  /* Header Styles */
  .dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e1e8ed;
  }
  
  .dashboard-header h2 {
    margin: 0;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .header-actions {
    display: flex;
    gap: 10px;
  }
  
  /* Button Styles */
  .btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }
  
  .btn-primary {
    background-color: #3B82F6;
    color: white;
  }
  
  .btn-secondary {
    background-color: #10B981;
    color: white;
  }
  
  .btn-danger {
    background-color: #EF4444;
    color: white;
  }
  
  .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
  
  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  .btn-small {
    padding: 0.4rem 0.8rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  
  /* Tabs Navigation */
  .tabs-nav {
    display: flex;
    gap: 5px;
    margin-bottom: 2rem;
    background: white;
    border-radius: 10px;
    padding: 5px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  }
  
  .tabs-nav button {
    flex: 1;
    padding: 1rem;
    border: none;
    background: transparent;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
  }
  
  .tabs-nav button.active {
    background-color: #3B82F6;
    color: white;
    box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
  }
  
  .tabs-nav button:hover:not(.active) {
    background-color: #f1f5f9;
  }
  
  /* Tab Content */
  .tab-content {
    animation: fadeIn 0.5s ease;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  /* Total Card */
  .total-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
  }
  
  .total-content h3 {
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .total-value {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  
  .total-icon {
    font-size: 4rem;
    opacity: 0.7;
  }
  
  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    border-left: 5px solid;
    transition: transform 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }
  
  .stat-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
  }
  
  .stat-title h3 {
    margin: 0;
    color: #2d3748;
    font-size: 1.2rem;
  }
  
  .stat-title p {
    margin: 0.3rem 0 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }
  
  .stat-value {
    margin-bottom: 1rem;
  }
  
  .stat-value .value {
    font-size: 2.5rem;
    font-weight: 700;
  }
  
  .stat-value .max {
    font-size: 1.2rem;
    color: #94a3b8;
  }
  
  /* Progress Bar */
  .progress-container {
    height: 10px;
    background-color: #e2e8f0;
    border-radius: 5px;
    margin-bottom: 1rem;
    overflow: hidden;
  }
  
  .progress-bar {
    height: 100%;
    border-radius: 5px;
    transition: width 0.5s ease;
  }
  
  .stat-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .percentage {
    font-weight: 600;
    color: #2d3748;
    font-size: 1.1rem;
  }
  
  .stat-actions {
    display: flex;
    gap: 8px;
  }
  
  /* Charts Section */
  .charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }
  
  .chart-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }
  
  .chart-card h3 {
    margin: 0 0 1.5rem 0;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .chart-container {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    height: 200px;
    padding: 1rem;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 1rem;
  }
  
  .chart-bar {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    width: 40px;
  }
  
  .bar-fill {
    width: 100%;
    border-radius: 5px 5px 0 0;
    transition: height 0.5s ease;
  }
  
  .bar-label {
    font-weight: 600;
    color: #64748b;
  }
  
  /* Pie Chart */
  .pie-chart {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto 1.5rem;
  }
  
  .pie-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
  }
  
  .pie-slice {
    transition: stroke-dasharray 0.5s ease;
  }
  
  .pie-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
  }
  
  .pie-total {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2d3748;
  }
  
  .pie-label {
    color: #64748b;
    font-size: 0.9rem;
  }
  
  .pie-legend {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
  
  .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
  }
  
  .legend-label {
    font-weight: 500;
    color: #2d3748;
  }
  
  .legend-value {
    margin-left: auto;
    font-weight: 600;
    color: #64748b;
  }
  
  /* Table Styles */
  .table-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }
  
  .stats-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
  }
  
  .stats-table th {
    background-color: #f8fafc;
    padding: 1rem;
    text-align: left;
    color: #64748b;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
  }
  
  .stats-table td {
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .title-cell .title-content {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .title-icon {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
  }
  
  .table-btn {
    width: 35px;
    height: 35px;
    border-radius: 6px;
    border: none;
    color: white;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
  }
  
  .mini-progress {
    height: 6px;
    background-color: #e2e8f0;
    border-radius: 3px;
    margin-top: 5px;
  }
  
  .mini-progress-bar {
    height: 100%;
    border-radius: 3px;
  }
  
  /* Edit Section */
  .edit-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }
  
  .edit-description {
    color: #64748b;
    margin-bottom: 2rem;
  }
  
  .edit-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
  }
  
  .edit-card {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  }
  
  .edit-header {
    background-color: #f8fafc;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid;
  }
  
  .edit-header h4 {
    margin: 0;
    color: #2d3748;
  }
  
  .edit-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
  }
  
  .edit-body {
    padding: 1rem;
  }
  
  .edit-field {
    margin-bottom: 1rem;
  }
  
  .edit-field label {
    display: block;
    margin-bottom: 5px;
    color: #64748b;
    font-weight: 500;
  }
  
  .value-display {
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .edit-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 1rem;
  }
  
  .edit-input:focus {
    outline: none;
    border-color: #3B82F6;
  }
  
  .edit-slider {
    width: 100%;
    height: 8px;
    border-radius: 4px;
    background: linear-gradient(to right, #e2e8f0, var(--track-color));
    outline: none;
    -webkit-appearance: none;
  }
  
  .edit-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--track-color);
    cursor: pointer;
  }
  
  .edit-actions {
    display: flex;
    gap: 10px;
    margin-top: 1.5rem;
  }
  
  .edit-btn {
    flex: 1;
    padding: 0.6rem;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
  }
  
  /* Export Section */
  .export-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }
  
  .export-options {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .export-btn {
    padding: 0.75rem 1.5rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }
  
  .export-btn:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
  }
  
  .data-preview {
    background-color: #1e293b;
    border-radius: 8px;
    padding: 1rem;
  }
  
  .data-preview h4 {
    color: #cbd5e1;
    margin-top: 0;
  }
  
  .data-preview pre {
    color: #94a3b8;
    margin: 0;
    font-size: 0.9rem;
    white-space: pre-wrap;
    word-wrap: break-word;
  }

</style>

  
  /* Helper function for pie chart */
  <script context="module">
    function getStrokeOffset(index) {
      const offsets = [0, 62.8, 125.6, 188.4, 251.2];
      return offsets[index] || 0;
    }
    
    function exportData(format) {
      // In a real app, this would trigger a file download
      alert(`Data exported as ${format.toUpperCase()} format!`);
    }
    
    function copyToClipboard() {
      const dataStr = JSON.stringify(stats, null, 2);
      navigator.clipboard.writeText(dataStr).then(() => {
        alert('Data copied to clipboard!');
      });
    }
  </script>