const API = '/view/Backend/public/index.php';
const PER_PAGE = 5;
let events = [], currentPage = 1, calendar = null, chart = null;

const toast = new bootstrap.Toast(document.getElementById('appToast'));
const showToast = (msg, success = true) => {
    document.getElementById('toastBody').textContent = msg;
    document.getElementById('appToast').className = `toast align-items-center text-white border-0 bg-${success ? 'success' : 'danger'}`;
    toast.show();
};

// Charger les catégories
const loadCategories = async () => {
    try {
        const r = await fetch(API + '?action=categories');
        if (!r.ok) throw new Error('Erreur catégories');
        const { data } = await r.json();
        const select = document.getElementById('id_categorie');
        select.innerHTML = '<option value="">-- Choisir une catégorie --</option>';
        data.forEach(c => select.add(new Option(c.nom_categorie, c.id_categorie)));
    } catch (e) {
        console.error('Erreur chargement catégories:', e);
    }
};

// Charger les événements
const loadEvents = async () => {
    document.getElementById('listMessage').textContent = 'Chargement...';
    try {
        const r = await fetch(API + '?action=list');
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const { data } = await r.json();
        events = (data || []).map(e => ({
            id: e.id_event,
            title: e.type_event || 'Sans titre',
            date: e.date_event,
            type: e.type_event || '',
            categorie: e.nom_categorie || 'Non classé',
            id_categorie: e.id_categorie || '',
            desc: e.description || ''
        }));

        currentPage = 1;
        renderAll();
        document.getElementById('listMessage').textContent = '';
    } catch (e) {
        console.error('Erreur API:', e);
        document.getElementById('eventsBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement</td></tr>';
    }
};

const renderAll = () => {
    renderTable();
    renderStats();
    renderCalendar();
    renderChart();
};

const renderTable = () => {
    const start = (currentPage - 1) * PER_PAGE;
    const pageEvents = events.slice(start, start + PER_PAGE);

    const tbody = document.getElementById('eventsBody');
    tbody.innerHTML = pageEvents.length === 0
        ? '<tr><td colspan="6" class="text-center text-muted py-5"><h4>Aucun événement</h4></td></tr>'
        : pageEvents.map((e, i) => `
            <tr>
                <td><strong>${start + i + 1}</strong></td>
                <td><strong>${e.title}</strong></td>
                <td>${e.date || '-'}</td>
                <td>${e.type}</td>
                <td><span class="badge-cat bg-primary text-white">${e.categorie}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editEvent(${e.id})"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger me-1" onclick="deleteEvent(${e.id})"><i class="fa fa-trash"></i></button>
                    <button class="btn btn-sm btn-success" onclick="reserveEvent(${e.id})">
                        <i class="fa fa-ticket-alt"></i> Réserver
                    </button>
                </td>
            </tr>`).join('');

    renderPagination();
};

const renderPagination = () => {
    const totalPages = Math.ceil(events.length / PER_PAGE);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    pagination.innerHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Précédent</a>
    </li>`;

    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
        </li>`;
    }

    pagination.innerHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Suivant</a>
    </li>`;
};

window.changePage = page => {
    if (page < 1 || page > Math.ceil(events.length / PER_PAGE)) return;
    currentPage = page;
    renderTable();
};

const renderStats = () => {
    const today = new Date(); today.setHours(0,0,0,0);
    const coming = events.filter(e => e.date && new Date(e.date) >= today).length;
    document.getElementById('statTotal').textContent = events.length;
    document.getElementById('statUpcoming').textContent = coming;
    document.getElementById('statPast').textContent = events.length - coming;
};

const renderCalendar = () => {
    if (calendar) calendar.destroy();
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek' },
        events: events.map(e => ({ id: e.id, title: e.title + ' [' + e.categorie + ']', start: e.date }))
    });
    calendar.render();
};

const renderChart = () => {
    const ctx = document.getElementById('eventsChart').getContext('2d');
    const counts = {};
    events.forEach(e => {
        if (e.date) {
            const d = new Date(e.date);
            const key = `${d.getMonth()+1}/${d.getFullYear()}`;
            counts[key] = (counts[key] || 0) + 1;
        }
    });
    const labels = Object.keys(counts).sort();
    if (chart) chart.destroy();
    chart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Événements', data: labels.map(k => counts[k]), backgroundColor: '#20c997' }] },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
};

// RÉSERVATION
window.reserveEvent = async id => {
    if (!confirm('Confirmer votre réservation pour cet événement ?')) return;

    try {
        const r = await fetch(API + '?action=reserve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_event: id })
        });
        const res = await r.json();
        showToast(res.message || 'Réservé !', res.status !== 'error');
        loadEvents();
    } catch (err) {
        showToast('Erreur réservation', false);
    }
};

// Ajout / Modification
document.getElementById('eventForm').onsubmit = async e => {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const payload = {
        type_event: document.getElementById('title').value.trim(),
        date_event: document.getElementById('date').value,
        description: document.getElementById('description').value.trim(),
        id_categorie: document.getElementById('id_categorie').value,
        etat: 'active'
    };

    const url = id ? `${API}?action=update&id=${id}` : `${API}?action=add`;

    try {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const res = await r.json();
        showToast(res.message || 'Succès !', true);
        bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
        loadEvents();
    } catch (err) {
        showToast('Erreur serveur', false);
    }
};

// Édition
window.editEvent = id => {
    const ev = events.find(e => e.id == id);
    if (!ev) return;
    document.getElementById('eventModalTitle').textContent = 'Modifier l\'événement';
    document.getElementById('edit_id').value = id;
    document.getElementById('title').value = ev.title;
    document.getElementById('date').value = ev.date;
    document.getElementById('type').value = ev.type;
    document.getElementById('id_categorie').value = ev.id_categorie;
    document.getElementById('description').value = ev.desc;
    document.getElementById('btnDeleteModal').style.display = 'block';
    new bootstrap.Modal(document.getElementById('eventModal')).show();
};

// Suppression
window.deleteEvent = async id => {
    if (!confirm('Supprimer cet événement ?')) return;
    try {
        await fetch(`${API}?action=delete&id=${id}`, { method: 'POST' });
        showToast('Supprimé !', true);
        loadEvents();
    } catch (err) {
        showToast('Erreur suppression', false);
    }
};

// Reset modal
document.querySelector('[data-bs-target="#eventModal"]').onclick = () => {
    document.getElementById('eventModalTitle').textContent = 'Nouvel événement';
    document.getElementById('edit_id').value = '';
    document.getElementById('title').value = document.getElementById('date').value = document.getElementById('type').value = document.getElementById('description').value = '';
    document.getElementById('id_categorie').value = '';
    document.getElementById('btnDeleteModal').style.display = 'none';
};

document.getElementById('btnDeleteModal').onclick = () => deleteEvent(document.getElementById('edit_id').value);
document.getElementById('refreshBtn').onclick = loadEvents;

// Lancement
loadCategories();
loadEvents();