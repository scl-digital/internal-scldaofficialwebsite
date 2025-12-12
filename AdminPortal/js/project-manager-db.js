/**
 * Enhanced Project Management System - Database Integrated
 * Real-time CRUD operations with MySQL database persistence
 * Includes error handling, input validation, and activity logging
 */

class ProjectManagerDB {
  constructor(containerSelector = '#projectManagerContainer', apiBase = '/api/admin') {
    this.container = document.querySelector(containerSelector);
    this.apiBase = apiBase;
    this.projects = [];
    this.currentView = 'table';
    this.viewArchived = false;
    this.toastNotifications = [];
    this.isLoading = false;
    
    this.init();
  }

  async init() {
    this.setupEventListeners();
    await this.loadProjects();
    this.render();
  }

  /**
   * API Communication Methods
   */
  
  async apiCall(endpoint, method = 'GET', data = null) {
    try {
      const options = {
        method,
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      };

      if (data) {
        options.body = JSON.stringify(data);
      }

      const response = await fetch(`${this.apiBase}/${endpoint}`, options);
      const contentType = response.headers.get('content-type') || '';
      let result;

      if (contentType.includes('application/json')) {
        result = await response.json();
      } else {
        const text = await response.text();
        throw new Error(`Invalid JSON response (content-type: ${contentType}). Response starts with: ${text.substring(0,200)}`);
      }

      if (!response.ok && !result.success) {
        throw new Error(result.message || `API Error: ${response.status}`);
      }

      return result;
    } catch (error) {
      console.error('API Call Error:', error);
      this.showNotification(error.message, 'error');
      throw error;
    }
  }

  /**
   * Project Operations
   */
  
  async loadProjects() {
    try {
      this.isLoading = true;
      const result = await this.apiCall('projects_api.php');
      
      if (result.success && Array.isArray(result.data)) {
        this.projects = result.data;
        console.log(`Loaded ${this.projects.length} projects from database`);
      }
    } catch (error) {
      console.error('Failed to load projects:', error);
      this.showNotification('Failed to load projects', 'error');
    } finally {
      this.isLoading = false;
    }
  }

  async createProject(projectData) {
    try {
      this.isLoading = true;
      this.showNotification('Creating project...', 'info');
      
      const result = await this.apiCall('projects_api.php', 'POST', projectData);
      
      if (result.success) {
        this.showNotification('Project created successfully!', 'success');
        await this.loadProjects();
        this.render();
        return result.id;
      }
    } catch (error) {
      console.error('Failed to create project:', error);
      this.showNotification(`Failed to create project: ${error.message}`, 'error');
      throw error;
    } finally {
      this.isLoading = false;
    }
  }

  async updateProject(projectId, updates) {
    try {
      this.isLoading = true;
      this.showNotification('Updating project...', 'info');
      
      const result = await this.apiCall('projects_api.php', 'PUT', {
        id: projectId,
        ...updates
      });
      
      if (result.success) {
        this.showNotification('Project updated successfully!', 'success');
        await this.loadProjects();
        this.render();
      }
    } catch (error) {
      console.error('Failed to update project:', error);
      this.showNotification(`Failed to update project: ${error.message}`, 'error');
      throw error;
    } finally {
      this.isLoading = false;
    }
  }

  async deleteProject(projectId) {
    try {
      if (!confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        return;
      }
      
      this.isLoading = true;
      this.showNotification('Deleting project...', 'info');
      
      const result = await this.apiCall('projects_api.php', 'DELETE', { id: projectId });
      
      if (result.success) {
        this.showNotification('Project deleted successfully!', 'success');
        await this.loadProjects();
        this.render();
      }
    } catch (error) {
      console.error('Failed to delete project:', error);
      this.showNotification(`Failed to delete project: ${error.message}`, 'error');
      throw error;
    } finally {
      this.isLoading = false;
    }
  }

  /**
   * Milestone Operations
   */
  
  async loadMilestones(projectId) {
    try {
      const result = await this.apiCall(`tasks_api.php?action=milestones&project_id=${projectId}`);
      return result.success ? result.data : [];
    } catch (error) {
      console.error('Failed to load milestones:', error);
      return [];
    }
  }

  async createMilestone(projectId, milestoneData) {
    try {
      this.showNotification('Creating milestone...', 'info');
      
      const result = await this.apiCall('tasks_api.php?action=milestones', 'POST', {
        project_id: projectId,
        ...milestoneData
      });
      
      if (result.success) {
        this.showNotification('Milestone created successfully!', 'success');
        return result.id;
      }
    } catch (error) {
      console.error('Failed to create milestone:', error);
      this.showNotification(`Failed to create milestone: ${error.message}`, 'error');
      throw error;
    }
  }

  async updateMilestone(milestoneData) {
    try {
      this.showNotification('Updating milestone...', 'info');
      
      const result = await this.apiCall('tasks_api.php?action=milestones', 'PUT', milestoneData);
      
      if (result.success) {
        this.showNotification('Milestone updated successfully!', 'success');
      }
    } catch (error) {
      console.error('Failed to update milestone:', error);
      this.showNotification(`Failed to update milestone: ${error.message}`, 'error');
      throw error;
    }
  }

  async deleteMilestone(milestoneId) {
    try {
      if (!confirm('Delete this milestone? All tasks will be removed.')) {
        return;
      }
      
      this.showNotification('Deleting milestone...', 'info');
      
      const result = await this.apiCall('tasks_api.php?action=milestones', 'DELETE', { id: milestoneId });
      
      if (result.success) {
        this.showNotification('Milestone deleted successfully!', 'success');
      }
    } catch (error) {
      console.error('Failed to delete milestone:', error);
      this.showNotification(`Failed to delete milestone: ${error.message}`, 'error');
      throw error;
    }
  }

  /**
   * Task Operations
   */
  
  async loadTasks(projectId, milestoneId = null) {
    try {
      let url = `tasks_api.php?action=tasks&project_id=${projectId}`;
      if (milestoneId) {
        url += `&milestone_id=${milestoneId}`;
      }
      const result = await this.apiCall(url);
      return result.success ? result.data : [];
    } catch (error) {
      console.error('Failed to load tasks:', error);
      return [];
    }
  }

  async createTask(projectId, milestoneId, taskData) {
    try {
      this.showNotification('Creating task...', 'info');
      
      const result = await this.apiCall('tasks_api.php?action=tasks', 'POST', {
        project_id: projectId,
        milestone_id: milestoneId,
        ...taskData
      });
      
      if (result.success) {
        this.showNotification('Task created successfully!', 'success');
        return result.id;
      }
    } catch (error) {
      console.error('Failed to create task:', error);
      this.showNotification(`Failed to create task: ${error.message}`, 'error');
      throw error;
    }
  }

  async updateTask(taskData) {
    try {
      this.showNotification('Updating task...', 'info');
      
      const result = await this.apiCall('tasks_api.php?action=tasks', 'PUT', taskData);
      
      if (result.success) {
        this.showNotification('Task updated successfully!', 'success');
      }
    } catch (error) {
      console.error('Failed to update task:', error);
      this.showNotification(`Failed to update task: ${error.message}`, 'error');
      throw error;
    }
  }

  async deleteTask(taskId) {
    try {
      if (!confirm('Delete this task?')) {
        return;
      }
      
      this.showNotification('Deleting task...', 'info');
      
      const result = await this.apiCall('tasks_api.php?action=tasks', 'DELETE', { id: taskId });
      
      if (result.success) {
        this.showNotification('Task deleted successfully!', 'success');
      }
    } catch (error) {
      console.error('Failed to delete task:', error);
      this.showNotification(`Failed to delete task: ${error.message}`, 'error');
      throw error;
    }
  }

  /**
   * Activity Log
   */
  
  async loadActivityLog(projectId) {
    try {
      const result = await this.apiCall(`activity_log_api.php?project_id=${projectId}&limit=20`);
      return result.success ? result.data : [];
    } catch (error) {
      console.error('Failed to load activity log:', error);
      return [];
    }
  }

  /**
   * UI Methods
   */

  showNotification(message, type = 'info') {
    const notification = {
      id: Date.now(),
      message,
      type,
      timestamp: new Date()
    };

    this.toastNotifications.push(notification);
    this.renderNotifications();

    // Auto-remove after 5 seconds
    setTimeout(() => {
      this.toastNotifications = this.toastNotifications.filter(n => n.id !== notification.id);
      this.renderNotifications();
    }, 5000);
  }

  renderNotifications() {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
      `;
      document.body.appendChild(container);
    }

    container.innerHTML = this.toastNotifications.map(notif => `
      <div class="alert alert-${notif.type} alert-dismissible fade show" role="alert" style="margin-bottom: 10px;">
        ${notif.message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `).join('');
  }

  setupEventListeners() {
    document.addEventListener('click', (e) => {
      if (e.target.closest('[data-view-toggle]')) {
        this.currentView = e.target.closest('[data-view-toggle]').dataset.viewToggle;
        this.render();
      }

      if (e.target.closest('[data-archive-toggle]')) {
        this.viewArchived = !this.viewArchived;
        this.render();
      }
    });
  }

  getStatusColor(status) {
    const colors = {
      'pending': '#FCD34D',
      'in_progress': '#3B82F6',
      'in-progress': '#3B82F6',
      'review': '#A78BFA',
      'completed': '#10B981',
      'cancelled': '#EF4444',
      'on_hold': '#FBBF24'
    };
    return colors[status] || '#E5E7EB';
  }

  getStatusBadgeClass(status) {
    const classes = {
      'pending': 'badge-warning',
      'in_progress': 'badge-info',
      'in-progress': 'badge-info',
      'review': 'badge-purple',
      'completed': 'badge-success',
      'cancelled': 'badge-danger',
      'on_hold': 'badge-secondary'
    };
    return classes[status] || 'badge-secondary';
  }

  formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }

  formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'ZAR'
    }).format(amount || 0);
  }

  daysUntilDeadline(deadline) {
    const now = new Date();
    const deadlineDate = new Date(deadline);
    const days = Math.ceil((deadlineDate - now) / (1000 * 60 * 60 * 24));
    return days;
  }

  render() {
    if (!this.container) return;

    if (this.isLoading) {
      this.container.innerHTML = `
        <div class="text-center p-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading projects...</p>
        </div>
      `;
      return;
    }

    const projectsToShow = this.projects;

    let html = `
      <div class="project-manager">
        <div class="project-header">
          <h3>Project Management (Database Integrated)</h3>
          <div class="project-controls">
            <button class="btn btn-primary btn-sm" onclick="projectManagerDB.openCreateProjectModal()">
              <i class="bi bi-plus-circle"></i> New Project
            </button>
            <div class="btn-group btn-group-sm ms-2" role="group">
              <button type="button" class="btn btn-outline-secondary" data-view-toggle="table" 
                ${this.currentView === 'table' ? 'active' : ''}>
                <i class="bi bi-table"></i> Table
              </button>
              <button type="button" class="btn btn-outline-secondary" data-view-toggle="kanban"
                ${this.currentView === 'kanban' ? 'active' : ''}>
                <i class="bi bi-kanban"></i> Kanban
              </button>
            </div>
          </div>
        </div>

        ${this.currentView === 'table' ? this.renderTableView(projectsToShow) : this.renderKanbanView(projectsToShow)}
      </div>
    `;

    this.container.innerHTML = html;
    this.attachTableEventListeners();
  }

  renderTableView(projectsToShow) {
    return `
      <div class="table-responsive">
        <table class="table table-hover project-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Client</th>
              <th>Status</th>
              <th>Progress</th>
              <th>Amount</th>
              <th>Deadline</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${projectsToShow.length === 0 ? `<tr><td colspan="7" class="text-center text-muted">No projects found</td></tr>` : ''}
            ${projectsToShow.map(project => `
              <tr>
                <td><strong>${project.title}</strong></td>
                <td>
                  <div>${project.client_name || 'N/A'}</div>
                  <small class="text-muted">${project.client_email || ''}</small>
                </td>
                <td>
                  <span class="badge ${this.getStatusBadgeClass(project.status)}" style="background-color: ${this.getStatusColor(project.status)}; color: #000;">
                    ${project.status.replace('-', ' ').replace('_', ' ')}
                  </span>
                </td>
                <td>
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: ${project.progress}%; background-color: ${this.getStatusColor(project.status)};" 
                      aria-valuenow="${project.progress}" aria-valuemin="0" aria-valuemax="100">
                      ${project.progress}%
                    </div>
                  </div>
                </td>
                <td><strong>${this.formatCurrency(project.amount)}</strong></td>
                <td>
                  ${this.formatDate(project.deadline)}
                  <br><small class="text-muted">${project.deadline ? this.daysUntilDeadline(project.deadline) + ' days left' : 'N/A'}</small>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-primary" onclick="projectManagerDB.openViewProjectModal(${project.id})">
                      <i class="bi bi-eye"></i> View
                    </button>
                    <button class="btn btn-outline-secondary" onclick="projectManagerDB.openEditProjectModal(${project.id})">
                      <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-outline-danger" onclick="projectManagerDB.deleteProject(${project.id})">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </div>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  renderKanbanView(projectsToShow) {
    const statuses = ['pending', 'in_progress', 'review', 'completed'];
    const statusLabels = {
      'pending': 'Pending',
      'in_progress': 'In Progress',
      'review': 'Review',
      'completed': 'Completed'
    };

    return `
      <div class="row g-3">
        ${statuses.map(status => `
          <div class="col-md-3">
            <div class="card bg-light">
              <div class="card-header bg-primary text-white">
                <h6 class="mb-0">${statusLabels[status]}</h6>
              </div>
              <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                ${projectsToShow
                  .filter(p => p.status === status)
                  .map(project => `
                    <div class="card mb-2 cursor-pointer" style="cursor: pointer;" onclick="projectManagerDB.openViewProjectModal(${project.id})">
                      <div class="card-body p-2">
                        <h6 class="card-title text-truncate" title="${project.title}">${project.title}</h6>
                        <small class="text-muted d-block text-truncate">${project.client_name || 'N/A'}</small>
                        <div class="progress mt-2" style="height: 15px;">
                          <div class="progress-bar" style="width: ${project.progress}%;">${project.progress}%</div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                          <small>${this.formatCurrency(project.amount)}</small>
                          <small class="text-muted">${this.daysUntilDeadline(project.deadline)} days</small>
                        </div>
                      </div>
                    </div>
                  `).join('')}
              </div>
            </div>
          </div>
        `).join('')}
      </div>
    `;
  }

  attachTableEventListeners() {
    // Event listeners are attached via onclick attributes in render
  }

  async openViewProjectModal(projectId) {
    const project = this.projects.find(p => p.id === projectId);
    if (!project) return;

    const logs = await this.loadActivityLog(projectId);

    const html = `
      <div class="modal fade" id="viewProjectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">${project.title}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <h6 class="text-muted">Client</h6>
                  <p><strong>${project.client_name}</strong><br><small>${project.client_email}</small></p>
                </div>
                <div class="col-md-6">
                  <h6 class="text-muted">Status</h6>
                  <span class="badge" style="background-color: ${this.getStatusColor(project.status)}; color: #000; padding: 8px 12px; font-size: 13px;">
                    ${project.status.replace('-', ' ').replace('_', ' ')}
                  </span>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <h6 class="text-muted">Amount</h6>
                  <p><strong>${this.formatCurrency(project.amount)}</strong></p>
                </div>
                <div class="col-md-6">
                  <h6 class="text-muted">Progress</h6>
                  <p><strong>${project.progress}%</strong></p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <h6 class="text-muted">Start Date</h6>
                  <p>${this.formatDate(project.start_date)}</p>
                </div>
                <div class="col-md-6">
                  <h6 class="text-muted">Deadline</h6>
                  <p>${this.formatDate(project.deadline)}</p>
                </div>
              </div>

              <div class="mb-3">
                <h6 class="text-muted">Description</h6>
                <p>${project.description || '<em>No description</em>'}</p>
              </div>

              <hr>
              <h6>Recent Activity</h6>
              <div class="activity-log" style="max-height: 300px; overflow-y: auto;">
                ${logs.length === 0 ? '<p class="text-muted">No activity yet</p>' : ''}
                ${logs.slice(0, 10).map(log => `
                  <div class="activity-item" style="padding: 8px; border-left: 3px solid #3B82F6; margin-bottom: 8px;">
                    <strong>${log.action}</strong><br>
                    <small>${log.description}</small><br>
                    <small class="text-muted">${new Date(log.created_at).toLocaleString()}</small>
                  </div>
                `).join('')}
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="projectManagerDB.openEditProjectModal(${projectId})">Edit</button>
            </div>
          </div>
        </div>
      </div>
    `;

    let container = document.getElementById('viewProjectModalContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'viewProjectModalContainer';
      document.body.appendChild(container);
    }
    container.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('viewProjectModal'));
    modal.show();
  }

  openEditProjectModal(projectId) {
    const project = this.projects.find(p => p.id === projectId);
    if (!project) return;

    const html = `
      <div class="modal fade" id="editProjectModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Project</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" id="ep_title" value="${project.title}" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="ep_description" rows="3">${project.description || ''}</textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Amount (ZAR)</label>
                <input type="number" class="form-control" id="ep_amount" value="${project.amount}" min="0" step="100">
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="ep_status">
                  <option value="pending" ${project.status === 'pending' ? 'selected' : ''}>Pending</option>
                  <option value="in_progress" ${project.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                  <option value="review" ${project.status === 'review' ? 'selected' : ''}>Review</option>
                  <option value="completed" ${project.status === 'completed' ? 'selected' : ''}>Completed</option>
                  <option value="cancelled" ${project.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                  <option value="on_hold" ${project.status === 'on_hold' ? 'selected' : ''}>On Hold</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Progress (%)</label>
                <input type="number" class="form-control" id="ep_progress" value="${project.progress}" min="0" max="100">
              </div>
              <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" id="ep_start" value="${project.start_date || ''}">
              </div>
              <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" id="ep_deadline" value="${project.deadline || ''}">
              </div>
              <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="ep_notes" rows="2">${project.notes || ''}</textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="projectManagerDB.saveProjectEdit(${projectId})">Save Changes</button>
            </div>
          </div>
        </div>
      </div>
    `;

    let container = document.getElementById('editProjectModalContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'editProjectModalContainer';
      document.body.appendChild(container);
    }
    container.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('editProjectModal'));
    modal.show();
  }

  async saveProjectEdit(projectId) {
    try {
      const updates = {
        title: document.getElementById('ep_title').value.trim(),
        description: document.getElementById('ep_description').value.trim(),
        amount: parseFloat(document.getElementById('ep_amount').value) || 0,
        status: document.getElementById('ep_status').value,
        progress: parseInt(document.getElementById('ep_progress').value) || 0,
        start_date: document.getElementById('ep_start').value || null,
        deadline: document.getElementById('ep_deadline').value || null,
        notes: document.getElementById('ep_notes').value.trim()
      };

      // Validation
      if (!updates.title) {
        this.showNotification('Title is required', 'error');
        return;
      }

      if (updates.start_date && updates.deadline && updates.deadline < updates.start_date) {
        this.showNotification('Deadline cannot be before start date', 'error');
        return;
      }

      await this.updateProject(projectId, updates);
      bootstrap.Modal.getInstance(document.getElementById('editProjectModal')).hide();
    } catch (error) {
      console.error('Error saving project:', error);
    }
  }

  openCreateProjectModal() {
    const html = `
      <div class="modal fade" id="createProjectModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">New Project</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="cp_title" placeholder="Project title" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Client <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="cp_client" placeholder="Client ID or name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="cp_description" rows="2" placeholder="Project description"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Amount (ZAR)</label>
                <input type="number" class="form-control" id="cp_amount" placeholder="0" min="0" step="100">
              </div>
              <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" id="cp_start">
              </div>
              <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" id="cp_deadline">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="projectManagerDB.createNewProject()">Create Project</button>
            </div>
          </div>
        </div>
      </div>
    `;

    let container = document.getElementById('createProjectModalContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'createProjectModalContainer';
      document.body.appendChild(container);
    }
    container.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('createProjectModal'));
    modal.show();
  }

  async createNewProject() {
    try {
      const title = document.getElementById('cp_title').value.trim();
      const client_id = parseInt(document.getElementById('cp_client').value) || 1; // Default to client 1
      const description = document.getElementById('cp_description').value.trim();
      const amount = parseFloat(document.getElementById('cp_amount').value) || 0;
      const start_date = document.getElementById('cp_start').value || null;
      const deadline = document.getElementById('cp_deadline').value || null;

      if (!title) {
        this.showNotification('Title is required', 'error');
        return;
      }

      if (start_date && deadline && deadline < start_date) {
        this.showNotification('Deadline cannot be before start date', 'error');
        return;
      }

      await this.createProject({
        client_id,
        title,
        description,
        amount,
        start_date,
        deadline,
        status: 'pending',
        progress: 0
      });

      bootstrap.Modal.getInstance(document.getElementById('createProjectModal')).hide();
    } catch (error) {
      console.error('Error creating project:', error);
    }
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('projectManagerContainer');
  if (container) {
    window.projectManagerDB = new ProjectManagerDB('#projectManagerContainer', '/api/admin');
  }
});
