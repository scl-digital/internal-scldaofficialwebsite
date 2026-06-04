// Project Management System
// Modular, scalable project management with Kanban, archiving, PDF export, and activity logs

class ProjectManager {
  constructor(containerSelector = '#projectManagerContainer') {
    this.container = document.querySelector(containerSelector);
    this.projects = [];
    this.archivedProjects = [];
    this.currentView = 'table'; // 'table' or 'kanban'
    this.viewArchived = false;
    this.activityLogs = {};
    
    this.init();
  }

  init() {
    this.loadProjects();
    this.setupEventListeners();
    this.render();
  }

  loadProjects() {
    const stored = localStorage.getItem('scl_projects');
    const archived = localStorage.getItem('scl_projects_archived');
    
    if (stored) {
      this.projects = JSON.parse(stored);
    } else {
      // Default sample data with Amount field
      this.projects = [
        {
          id: 1,
          title: 'Tongil Medicare Flyers',
          client: 'Jean Pierre Lomboto Lyonga',
          clientEmail: 'jlomboto@gmail.com',
          status: 'pending',
          progress: 0,
          startDate: '2025-11-01',
          deadline: '2025-11-30',
          amount: 2500,
          description: 'Design and print flyers for Tongil Medicare clinic.',
          notes: '',
          attachments: [],
          createdAt: new Date().toISOString()
        },
        {
          id: 2,
          title: 'Nexa International - Website Redesign',
          client: 'Shinsa Lyonga Lomboto',
          clientEmail: 'shinsalomboto45@gmail.com',
          status: 'pending',
          progress: 0,
          startDate: '2025-11-05',
          deadline: '2025-12-15',
          amount: 5000,
          description: 'Complete website redesign for Nexa International School.',
          notes: '',
          attachments: [],
          createdAt: new Date().toISOString()
        },
        {
          id: 3,
          title: 'Kietu Lakes UI Design',
          client: 'Shinsa Lyonga Lomboto',
          clientEmail: 'shinsalomboto45@gmail.com',
          status: 'pending',
          progress: 0,
          startDate: '2025-11-10',
          deadline: '2025-12-01',
          amount: 3500,
          description: 'UI/UX design for Kietu Lakes booking application.',
          notes: '',
          attachments: [],
          createdAt: new Date().toISOString()
        },
        {
          id: 4,
          title: 'Renne Auto Report',
          client: 'Shinsa Lyonga Lomboto',
          clientEmail: 'shinsalomboto45@gmail.com',
          status: 'pending',
          progress: 0,
          startDate: '2025-11-15',
          deadline: '2025-11-25',
          amount: 1500,
          description: 'Generate and format quarterly financial report.',
          notes: '',
          attachments: [],
          createdAt: new Date().toISOString()
        }
      ];
      this.saveProjects();
    }

    if (archived) {
      this.archivedProjects = JSON.parse(archived);
    }

    // Load activity logs
    const logsStored = localStorage.getItem('scl_activity_logs');
    if (logsStored) {
      this.activityLogs = JSON.parse(logsStored);
    } else {
      this.projects.forEach(p => {
        this.activityLogs[p.id] = [
          { action: 'created', message: 'Project created', timestamp: p.createdAt }
        ];
      });
      this.saveActivityLogs();
    }
  }

  saveProjects() {
    localStorage.setItem('scl_projects', JSON.stringify(this.projects));
  }

  saveArchivedProjects() {
    localStorage.setItem('scl_projects_archived', JSON.stringify(this.archivedProjects));
  }

  saveActivityLogs() {
    localStorage.setItem('scl_activity_logs', JSON.stringify(this.activityLogs));
  }

  addActivityLog(projectId, action, message) {
    if (!this.activityLogs[projectId]) {
      this.activityLogs[projectId] = [];
    }
    this.activityLogs[projectId].push({
      action,
      message,
      timestamp: new Date().toISOString()
    });
    this.saveActivityLogs();
  }

  setupEventListeners() {
    // View toggle
    document.addEventListener('click', (e) => {
      if (e.target.closest('[data-view-toggle]')) {
        const view = e.target.closest('[data-view-toggle]').dataset.viewToggle;
        this.currentView = view;
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
      'in-progress': '#3B82F6',
      'review': '#A78BFA',
      'completed': '#10B981'
    };
    return colors[status] || '#E5E7EB';
  }

  getStatusBadgeClass(status) {
    const classes = {
      'pending': 'badge-warning',
      'in-progress': 'badge-info',
      'review': 'badge-purple',
      'completed': 'badge-success'
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

    const projectsToShow = this.viewArchived ? this.archivedProjects : this.projects;

    let html = `
      <div class="project-manager">
        <div class="project-header">
          <h3>Project Management</h3>
          <div class="project-controls">
            <button class="btn btn-primary btn-sm" onclick="projectManager.openCreateProjectModal()">
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
            <button class="btn btn-outline-secondary btn-sm ms-2" data-archive-toggle>
              <i class="bi bi-archive"></i> ${this.viewArchived ? 'Active' : 'Archived'} (${this.viewArchived ? this.archivedProjects.length : this.projects.length})
            </button>
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
              <th>Start Date</th>
              <th>Deadline</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${projectsToShow.length === 0 ? `<tr><td colspan="8" class="text-center text-muted">No projects found</td></tr>` : ''}
            ${projectsToShow.map(project => `
              <tr>
                <td><strong>${project.title}</strong></td>
                <td>
                  <div>${project.client}</div>
                  <small class="text-muted">${project.clientEmail}</small>
                </td>
                <td>
                  <span class="badge ${this.getStatusBadgeClass(project.status)}" style="background-color: ${this.getStatusColor(project.status)}; color: #000;">
                    ${project.status.replace('-', ' ')}
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
                <td>${this.formatDate(project.startDate)}</td>
                <td>
                  ${this.formatDate(project.deadline)}
                  <br><small class="text-muted">${this.daysUntilDeadline(project.deadline)} days left</small>
                </td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-primary" data-action="view" data-project-id="${project.id}">
                      <i class="bi bi-eye"></i> View
                    </button>
                    <button class="btn btn-outline-secondary" data-action="edit" data-project-id="${project.id}">
                      <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-outline-info" data-action="pdf" data-project-id="${project.id}">
                      <i class="bi bi-file-pdf"></i> PDF
                    </button>
                    <button class="btn btn-outline-danger" data-action="archive" data-project-id="${project.id}">
                      <i class="bi bi-archive"></i> ${this.viewArchived ? 'Delete' : 'Archive'}
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
    const statuses = ['pending', 'in-progress', 'review', 'completed'];
    const statusLabels = {
      'pending': 'Backlog',
      'in-progress': 'In Progress',
      'review': 'Review',
      'completed': 'Completed'
    };

    return `
      <div class="kanban-board">
        ${statuses.map(status => `
          <div class="kanban-column" data-status="${status}">
            <div class="kanban-column-header" style="background-color: ${this.getStatusColor(status)};">
              <h5>${statusLabels[status]}</h5>
              <span class="badge bg-dark">${projectsToShow.filter(p => p.status === status).length}</span>
            </div>
            <div class="kanban-cards" data-status="${status}">
              ${projectsToShow.filter(p => p.status === status).map(project => `
                <div class="kanban-card" draggable="true" data-project-id="${project.id}" data-status="${project.status}">
                  <div class="card-header-custom">
                    <h6>${project.title}</h6>
                    <button class="btn btn-sm btn-light" data-action="quick-menu" data-project-id="${project.id}" style="padding: 2px 6px;">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                  </div>
                  <p class="card-client"><i class="bi bi-person"></i> ${project.client}</p>
                  <p class="card-amount" style="font-weight: bold; color: #1e348d;">${this.formatCurrency(project.amount)}</p>
                  <div class="card-progress">
                    <div class="progress" style="height: 8px;">
                      <div class="progress-bar" style="width: ${project.progress}%; background-color: ${this.getStatusColor(project.status)};"></div>
                    </div>
                    <small>${project.progress}%</small>
                  </div>
                  <p class="card-deadline"><i class="bi bi-calendar"></i> ${this.formatDate(project.deadline)}</p>
                </div>
              `).join('')}
            </div>
          </div>
        `).join('')}
      </div>
    `;
  }

  attachTableEventListeners() {
    document.querySelectorAll('[data-action]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const action = btn.dataset.action;
        const projectId = parseInt(btn.dataset.projectId);
        
        if (action === 'view') this.openProjectModal(projectId);
        if (action === 'edit') this.openEditProjectModal(projectId);
        if (action === 'pdf') this.downloadProjectPDF(projectId);
        if (action === 'archive') this.toggleArchive(projectId);
        if (action === 'quick-menu') this.showQuickMenu(e, projectId);
      });
    });

    // Kanban drag and drop
    if (this.currentView === 'kanban') {
      this.setupKanbanDragDrop();
    }
  }

  setupKanbanDragDrop() {
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-cards');

    cards.forEach(card => {
      card.addEventListener('dragstart', (e) => {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('projectId', card.dataset.projectId);
      });
    });

    columns.forEach(column => {
      column.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        column.classList.add('drag-over');
      });

      column.addEventListener('dragleave', () => {
        column.classList.remove('drag-over');
      });

      column.addEventListener('drop', (e) => {
        e.preventDefault();
        column.classList.remove('drag-over');
        const projectId = parseInt(e.dataTransfer.getData('projectId'));
        const newStatus = column.dataset.status;
        this.updateProjectStatus(projectId, newStatus);
      });
    });
  }

  updateProjectStatus(projectId, newStatus) {
    const project = this.projects.find(p => p.id === projectId);
    if (project && project.status !== newStatus) {
      const oldStatus = project.status;
      project.status = newStatus;
      this.addActivityLog(projectId, 'status-changed', `Status changed from ${oldStatus} to ${newStatus}`);
      this.saveProjects();
      this.render();
    }
  }

  openProjectModal(projectId) {
    const project = this.projects.find(p => p.id === projectId);
    if (!project) return;

    const logs = this.activityLogs[projectId] || [];

    const html = `
      <div class="modal fade" id="projectModal" tabindex="-1">
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
                  <p><strong>${project.client}</strong><br><small>${project.clientEmail}</small></p>
                </div>
                <div class="col-md-6">
                  <h6 class="text-muted">Status</h6>
                  <span class="badge" style="background-color: ${this.getStatusColor(project.status)}; color: #000; padding: 8px 12px; font-size: 13px;">
                    ${project.status.replace('-', ' ')}
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
                  <p>${this.formatDate(project.startDate)}</p>
                </div>
                <div class="col-md-6">
                  <h6 class="text-muted">Deadline</h6>
                  <p>${this.formatDate(project.deadline)} <small class="text-muted">(${this.daysUntilDeadline(project.deadline)} days)</small></p>
                </div>
              </div>

              <div class="mb-3">
                <h6 class="text-muted">Progress Bar</h6>
                <div class="progress" style="height: 25px;">
                  <div class="progress-bar" style="width: ${project.progress}%; background-color: ${this.getStatusColor(project.status)};">
                    ${project.progress}%
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <h6 class="text-muted">Description</h6>
                <p>${project.description}</p>
              </div>

              <div class="mb-3">
                <h6 class="text-muted">Notes</h6>
                <p>${project.notes || '<em>No notes yet</em>'}</p>
              </div>

              <hr>
              <h6>Activity Log</h6>
              <div class="activity-log" style="max-height: 300px; overflow-y: auto;">
                ${logs.length === 0 ? '<p class="text-muted">No activity yet</p>' : ''}
                ${logs.map(log => `
                  <div class="activity-item" style="padding: 8px; border-left: 3px solid ${this.getStatusColor('in-progress')}; margin-bottom: 8px;">
                    <strong>${log.message}</strong><br>
                    <small class="text-muted">${new Date(log.timestamp).toLocaleString()}</small>
                  </div>
                `).join('')}
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="projectManager.openEditProjectModal(${projectId})">Edit</button>
            </div>
          </div>
        </div>
      </div>
    `;

    let container = document.getElementById('projectModalContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'projectModalContainer';
      document.body.appendChild(container);
    }
    container.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
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
              <div class="mb-2">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" id="ep_title" value="${project.title}">
              </div>
              <div class="mb-2">
                <label class="form-label">Client</label>
                <input type="text" class="form-control" id="ep_client" value="${project.client}">
              </div>
              <div class="mb-2">
                <label class="form-label">Client Email</label>
                <input type="email" class="form-control" id="ep_email" value="${project.clientEmail}">
              </div>
              <div class="mb-2">
                <label class="form-label">Amount (ZAR)</label>
                <input type="number" class="form-control" id="ep_amount" value="${project.amount}" min="0" step="100">
              </div>
              <div class="mb-2">
                <label class="form-label">Status</label>
                <select class="form-select" id="ep_status">
                  <option value="pending" ${project.status === 'pending' ? 'selected' : ''}>Pending</option>
                  <option value="in-progress" ${project.status === 'in-progress' ? 'selected' : ''}>In Progress</option>
                  <option value="review" ${project.status === 'review' ? 'selected' : ''}>Review</option>
                  <option value="completed" ${project.status === 'completed' ? 'selected' : ''}>Completed</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Progress (%)</label>
                <input type="number" class="form-control" id="ep_progress" value="${project.progress}" min="0" max="100">
              </div>
              <div class="mb-2">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" id="ep_start" value="${project.startDate}">
              </div>
              <div class="mb-2">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" id="ep_deadline" value="${project.deadline}">
              </div>
              <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="ep_description" rows="3">${project.description}</textarea>
              </div>
              <div class="mb-2">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="ep_notes" rows="2">${project.notes}</textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="projectManager.saveProjectEdit(${projectId})">Save Changes</button>
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

  saveProjectEdit(projectId) {
    const project = this.projects.find(p => p.id === projectId);
    if (!project) return;

    const oldStatus = project.status;
    const newStatus = document.getElementById('ep_status').value;

    project.title = document.getElementById('ep_title').value;
    project.client = document.getElementById('ep_client').value;
    project.clientEmail = document.getElementById('ep_email').value;
    project.amount = parseFloat(document.getElementById('ep_amount').value) || 0;
    project.status = newStatus;
    project.progress = parseInt(document.getElementById('ep_progress').value) || 0;
    project.startDate = document.getElementById('ep_start').value;
    project.deadline = document.getElementById('ep_deadline').value;
    project.description = document.getElementById('ep_description').value;
    project.notes = document.getElementById('ep_notes').value;

    if (oldStatus !== newStatus) {
      this.addActivityLog(projectId, 'status-changed', `Status changed from ${oldStatus} to ${newStatus}`);
    }
    this.addActivityLog(projectId, 'edited', 'Project details updated');

    this.saveProjects();
    bootstrap.Modal.getInstance(document.getElementById('editProjectModal')).hide();
    this.render();
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
              <div class="mb-2">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" id="cp_title" placeholder="Project title">
              </div>
              <div class="mb-2">
                <label class="form-label">Client</label>
                <input type="text" class="form-control" id="cp_client" placeholder="Client name">
              </div>
              <div class="mb-2">
                <label class="form-label">Client Email</label>
                <input type="email" class="form-control" id="cp_email" placeholder="client@example.com">
              </div>
              <div class="mb-2">
                <label class="form-label">Amount (ZAR)</label>
                <input type="number" class="form-control" id="cp_amount" placeholder="0" min="0" step="100">
              </div>
              <div class="mb-2">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" id="cp_start">
              </div>
              <div class="mb-2">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" id="cp_deadline">
              </div>
              <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="cp_description" rows="2" placeholder="Project description"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="projectManager.createNewProject()">Create Project</button>
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

  createNewProject() {
    const title = document.getElementById('cp_title').value.trim();
    const client = document.getElementById('cp_client').value.trim();
    const clientEmail = document.getElementById('cp_email').value.trim();
    const amount = parseFloat(document.getElementById('cp_amount').value) || 0;
    const startDate = document.getElementById('cp_start').value;
    const deadline = document.getElementById('cp_deadline').value;
    const description = document.getElementById('cp_description').value.trim();

    if (!title || !client) {
      alert('Title and Client are required');
      return;
    }

    const newProject = {
      id: Math.max(0, ...this.projects.map(p => p.id)) + 1,
      title,
      client,
      clientEmail,
      amount,
      status: 'pending',
      progress: 0,
      startDate: startDate || new Date().toISOString().split('T')[0],
      deadline: deadline || new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      description,
      notes: '',
      attachments: [],
      createdAt: new Date().toISOString()
    };

    this.projects.push(newProject);
    this.activityLogs[newProject.id] = [
      { action: 'created', message: 'Project created', timestamp: new Date().toISOString() }
    ];
    this.saveProjects();
    this.saveActivityLogs();

    bootstrap.Modal.getInstance(document.getElementById('createProjectModal')).hide();
    this.render();
  }

  toggleArchive(projectId) {
    const projectIndex = this.projects.findIndex(p => p.id === projectId);
    const archivedIndex = this.archivedProjects.findIndex(p => p.id === projectId);

    if (projectIndex !== -1) {
      // Archive
      const project = this.projects.splice(projectIndex, 1)[0];
      this.archivedProjects.push(project);
      this.addActivityLog(projectId, 'archived', 'Project archived');
    } else if (archivedIndex !== -1) {
      // Restore or delete
      if (confirm('Restore this project? Click Cancel to permanently delete.')) {
        const project = this.archivedProjects.splice(archivedIndex, 1)[0];
        this.projects.push(project);
        this.addActivityLog(projectId, 'restored', 'Project restored from archive');
      } else {
        this.archivedProjects.splice(archivedIndex, 1);
        delete this.activityLogs[projectId];
      }
    }

    this.saveProjects();
    this.saveArchivedProjects();
    this.saveActivityLogs();
    this.render();
  }

  downloadProjectPDF(projectId) {
    const project = this.projects.find(p => p.id === projectId);
    if (!project) return;

    const logs = this.activityLogs[projectId] || [];

    const html = `
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="UTF-8">
        <style>
          body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
          .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1e348d; padding-bottom: 15px; }
          h1 { color: #1e348d; margin: 0; }
          .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
          .meta-item { }
          .meta-label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; }
          .meta-value { color: #333; margin-top: 5px; }
          .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; color: #000; font-weight: bold; }
          .progress-container { margin: 20px 0; }
          .progress-bar { background: #e0e0e0; height: 20px; border-radius: 4px; overflow: hidden; }
          .progress-fill { height: 100%; background: #3b82f6; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold; }
          .section { margin: 30px 0; }
          .section-title { font-size: 14px; font-weight: bold; color: #1e348d; margin-bottom: 10px; text-transform: uppercase; border-bottom: 2px solid #1e348d; padding-bottom: 5px; }
          .section-content { color: #666; line-height: 1.6; }
          .activity-log { font-size: 11px; }
          .activity-item { padding: 5px; border-left: 3px solid #3b82f6; margin-bottom: 8px; }
          .activity-timestamp { color: #999; }
          .footer { margin-top: 40px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>
      </head>
      <body>
        <div class="header">
          <h1>${project.title}</h1>
          <p>Project Summary Report</p>
        </div>

        <div class="meta">
          <div class="meta-item">
            <div class="meta-label">Client</div>
            <div class="meta-value">${project.client}<br><small>${project.clientEmail}</small></div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Status</div>
            <div class="meta-value">
              <span class="status-badge" style="background-color: ${this.getStatusColor(project.status)};">${project.status.replace('-', ' ')}</span>
            </div>
          </div>
        </div>

        <div class="meta">
          <div class="meta-item">
            <div class="meta-label">Amount</div>
            <div class="meta-value">${this.formatCurrency(project.amount)}</div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Progress</div>
            <div class="meta-value">${project.progress}%</div>
          </div>
        </div>

        <div class="meta">
          <div class="meta-item">
            <div class="meta-label">Start Date</div>
            <div class="meta-value">${this.formatDate(project.startDate)}</div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Deadline</div>
            <div class="meta-value">${this.formatDate(project.deadline)}</div>
          </div>
        </div>

        <div class="progress-container">
          <div class="meta-label">Progress</div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: ${project.progress}%;">${project.progress}%</div>
          </div>
        </div>

        <div class="section">
          <div class="section-title">Description</div>
          <div class="section-content">${project.description}</div>
        </div>

        ${project.notes ? `
          <div class="section">
            <div class="section-title">Notes</div>
            <div class="section-content">${project.notes}</div>
          </div>
        ` : ''}

        <div class="section">
          <div class="section-title">Activity Log</div>
          <div class="activity-log">
            ${logs.map(log => `
              <div class="activity-item">
                <strong>${log.message}</strong>
                <div class="activity-timestamp">${new Date(log.timestamp).toLocaleString()}</div>
              </div>
            `).join('')}
          </div>
        </div>

        <div class="footer">
          <p>Generated on ${new Date().toLocaleString()}</p>
          <p>SCL Digital Agency - Project Management System</p>
        </div>
      </body>
      </html>
    `;

    const element = document.createElement('div');
    element.innerHTML = html;

    // Use html2pdf if available, otherwise create a simple print PDF
    if (typeof html2pdf !== 'undefined') {
      html2pdf().setOptions({ margin: 10, filename: `${project.title}.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' } }).from(html).save();
    } else {
      // Fallback: open print dialog
      const printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write(html);
      printWindow.document.close();
      printWindow.print();
    }
  }

  showQuickMenu(e, projectId) {
    const menu = `
      <div style="position: absolute; top: ${e.clientY}px; left: ${e.clientX}px; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); z-index: 1000;">
        <a href="#" onclick="projectManager.openProjectModal(${projectId}); return false;" style="display: block; padding: 8px 16px; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">View</a>
        <a href="#" onclick="projectManager.openEditProjectModal(${projectId}); return false;" style="display: block; padding: 8px 16px; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">Edit</a>
        <a href="#" onclick="projectManager.downloadProjectPDF(${projectId}); return false;" style="display: block; padding: 8px 16px; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">Download PDF</a>
        <a href="#" onclick="projectManager.toggleArchive(${projectId}); return false;" style="display: block; padding: 8px 16px; text-decoration: none; color: #d32f2f;">Archive</a>
      </div>
    `;
    const container = document.createElement('div');
    container.innerHTML = menu;
    document.body.appendChild(container.firstChild);
    setTimeout(() => container.remove(), 5000);
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('projectManagerContainer');
  if (container) {
    window.projectManager = new ProjectManager('#projectManagerContainer');
  }
});
