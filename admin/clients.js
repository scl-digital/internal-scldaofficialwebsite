// admin/clients.js

export async function renderClientsTab() {
    document.getElementById('adminContent').innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Clients</h3>
            <button class="btn btn-primary" id="addClientBtn">Add Client</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody"></tbody>
            </table>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel">Add Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="clientForm">
                  <input type="hidden" id="clientId">
                  <div class="mb-3">
                    <label for="clientName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="clientName" required>
                  </div>
                  <div class="mb-3">
                    <label for="clientEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="clientEmail" required>
                  </div>
                  <div class="mb-3">
                    <label for="clientCompany" class="form-label">Company</label>
                    <input type="text" class="form-control" id="clientCompany">
                  </div>
                  <div class="mb-3">
                    <label for="clientPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="clientPassword" placeholder="Leave blank to keep unchanged">
                  </div>
                  <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
              </div>
            </div>
          </div>
        </div>
    `;
    await loadClients();
    document.getElementById('addClientBtn').onclick = () => showClientModal();
}

async function loadClients() {
    const tbody = document.getElementById('clientsTableBody');
    tbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';
    const res = await fetch('../api/admin/clients.php');
    const data = await res.json();
    if (data.success) {
        tbody.innerHTML = data.data.map(client => `
            <tr>
                <td>${client.id}</td>
                <td>${client.name}</td>
                <td>${client.email}</td>
                <td>${client.company || ''}</td>
                <td>${client.created_at}</td>
                <td>
                    <button class="btn btn-sm btn-secondary me-1" onclick="window.editClient(${client.id}, '${client.name}', '${client.email}', '${client.company || ''}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="window.deleteClient(${client.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = `<tr><td colspan="6">${data.error || 'Failed to load clients.'}</td></tr>`;
    }
}

window.editClient = function(id, name, email, company) {
    showClientModal({ id, name, email, company });
};

window.deleteClient = async function(id) {
    if (!confirm('Delete this client?')) return;
    const res = await fetch('../api/admin/clients.php', {
        method: 'DELETE',
        body: `id=${id}`
    });
    const data = await res.json();
    if (data.success) {
        await loadClients();
    } else {
        alert(data.error || 'Delete failed.');
    }
};

function showClientModal(client = {}) {
    const modal = new bootstrap.Modal(document.getElementById('clientModal'));
    document.getElementById('clientModalLabel').textContent = client.id ? 'Edit Client' : 'Add Client';
    document.getElementById('clientId').value = client.id || '';
    document.getElementById('clientName').value = client.name || '';
    document.getElementById('clientEmail').value = client.email || '';
    document.getElementById('clientCompany').value = client.company || '';
    document.getElementById('clientPassword').value = '';
    document.getElementById('clientForm').onsubmit = async function(e) {
        e.preventDefault();
        const id = document.getElementById('clientId').value;
        const name = document.getElementById('clientName').value;
        const email = document.getElementById('clientEmail').value;
        const company = document.getElementById('clientCompany').value;
        const password = document.getElementById('clientPassword').value;
        const payload = { name, email, company };
        if (password) payload.password = password;
        let method = 'POST';
        let isNew = false;
        if (id) {
            payload.id = id;
            method = 'PUT';
        } else {
            payload.password = password;
            isNew = true;
        }
        const res = await fetch('../api/admin/clients.php', {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            modal.hide();
            await loadClients();
            setTimeout(() => {
                let msg = isNew
                  ? `Client added!<br><b>Share these credentials with your client:</b><br>Email: <b>${email}</b><br>Password: <b>${password}</b>`
                  : 'Client updated!';
                showAdminSuccess(msg);
            }, 300);
        } else {
            alert(data.error || 'Save failed.');
        }
        document.getElementById('clientForm').reset();
    };
    modal.show();
}

function showAdminSuccess(msg) {
    let div = document.getElementById('adminSuccessMsg');
    if (!div) {
        div = document.createElement('div');
        div.id = 'adminSuccessMsg';
        div.className = 'alert alert-success position-fixed top-0 end-0 m-4';
        div.style.zIndex = 9999;
        document.body.appendChild(div);
    }
    div.innerHTML = msg;
    div.style.display = 'block';
    setTimeout(() => { div.style.display = 'none'; }, 7000);
} 