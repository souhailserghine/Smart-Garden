const API = '/view/Backend/public/index.php';
const PER_PAGE = 5;
let events = [], currentPage = 1, calendar = null, chart = null;
let map = null, marker = null;

const toast = new bootstrap.Toast(document.getElementById('appToast'));
const showToast = (msg, success = true) => {
    document.getElementById('toastBody').textContent = msg;
    document.getElementById('appToast').className = `toast align-items-center text-white border-0 bg-${success ? 'success' : 'danger'}`;
    toast.show();
};

// INITIALISATION CARTE + PROXY NOMINATIM
function initMap() {
    if (map) {
        map.invalidateSize();
        return;
    }

    map = L.map('mapPreview').setView([48.8566, 2.3522], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([48.8566, 2.3522], { draggable: true }).addTo(map);

    // RECHERCHE VIA TON PROXY (plus de CORS)
    L.Control.geocoder({
        defaultMarkGeocode: true,
        placeholder: "Rechercher un lieu...",
        collapsed: false,
        geocoder: new L.Control.Geocoder.Nominatim({
            serviceUrl: '/view/Backend/public/proxy-nominatim.php'  // Chemin absolu
        })
    }).addTo(map).on('markgeocode', e => {
        const l = e.geocode.center;
        marker.setLatLng(l);
        map.setView(l, 16);
        updateLocation(l, e.geocode.name || e.geocode.properties.display_name);
    });

    // Clic sur carte
    map.on('click', e => {
        marker.setLatLng(e.latlng);
        map.setView(e.latlng, 16);
        reverseGeocode(e.latlng);
    });

    marker.on('dragend', e => reverseGeocode(e.target.getLatLng()));

    // Ma position
    document.getElementById('btnMyLocation').onclick = () => {
        showToast("Recherche de votre position...");
        map.locate({ setView: true, maxZoom: 16 });
    };

    map.on('locationfound', e => {
        marker.setLatLng(e.latlng);
        reverseGeocode(e.latlng);
        showToast("Position détectée !");
    });

    map.on('locationerror', () => showToast("Position non disponible", false));
}

// REVERSE GEOCODING VIA TON PROXY
function reverseGeocode(latlng) {
    fetch(`/view/Backend/public/proxy-nominatim.php?lat=${latlng.lat}&lon=${latlng.lng}`)
        .then(r => r.json())
        .then(data => {
            const address = data.display_name || "Position personnalisée";
            updateLocation(latlng, address);
        })
        .catch(() => updateLocation(latlng, "Adresse non disponible"));
}

function updateLocation(latlng, address) {
    document.getElementById('location').value = address;
    document.getElementById('latitude').value = latlng.lat.toFixed(8);
    document.getElementById('longitude').value = latlng.lng.toFixed(8);
}

// CHARGEMENT
const loadCategories = async () => {
    try {
        const r = await fetch(API + '?action=listCategories'); // ✅ Updated to match backend
        const { data } = await r.json();
        const select = document.getElementById('id_categorie');
        select.innerHTML = '<option value="">-- Choisir une catégorie --</option>';
        data.forEach(c => select.add(new Option(c.nom_categorie, c.id_categorie)));
    } catch (e) { console.error(e); }
};

const loadEvents = async () => {
    document.getElementById('listMessage').textContent = 'Chargement...';
    try {
        const r = await fetch(API + '?action=listEvents'); // ✅ Updated to match backend
        const { data } = await r.json();
        events = (data || []).map(e => ({
            id: e.id_event,
            title: e.type_event || 'Sans titre',
            date: e.date_event,
            type: e.type_event || '',
            categorie: e.nom_categorie || 'Non classé',
            id_categorie: e.id_categorie || '',
            desc: e.description || '',
            lieu: e.lieu || null,
            latitude: e.latitude ? parseFloat(e.latitude) : null,
            longitude: e.longitude ? parseFloat(e.longitude) : null
        }));
        currentPage = 1;
        renderAll();
        document.getElementById('listMessage').textContent = '';
    } catch (e) {
        showToast('Erreur de chargement', false);
    }
};

const renderAll = () => {
    renderTable();
    renderStats();
    renderCalendar();
    renderChart();
};

// TABLEAU + PAGINATION
const renderTable = () => {
    const start = (currentPage - 1) * PER_PAGE;
    const pageEvents = events.slice(start, start + PER_PAGE);

    const tbody = document.getElementById('eventsBody');
    tbody.innerHTML = pageEvents.length === 0
        ? '<tr><td colspan="7" class="text-center text-muted py-5"><h4>Aucun événement</h4></td></tr>'
        : pageEvents.map((e, i) => `
            <tr>
                <td><strong>${start + i + 1}</strong></td>
                <td><strong>${e.title}</strong></td>
                <td>${e.date || '-'}</td>
                <td>${e.type}</td>
                <td><span class="badge bg-primary text-white">${e.categorie}</span></td>
                <td>
                    ${e.lieu 
                        ? `<small><i class="fa fa-map-marker-alt text-success me-1"></i>${e.lieu.substring(0, 35)}${e.lieu.length > 35 ? '...' : ''}</small>`
                        : '<small class="text-muted">Non spécifié</small>'
                    }
                </td>
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

// STATS + CALENDRIER + GRAPHIQUE
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
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
        events: events.map(e => ({
            id: e.id,
            title: e.title + (e.lieu ? ` @ ${e.lieu.substring(0, 20)}...` : ''),
            start: e.date,
            extendedProps: { lieu: e.lieu }
        })),
        eventClick: info => info.event.extendedProps.lieu && alert(`Lieu : ${info.event.extendedProps.lieu}`)
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

// ACTIONS
window.reserveEvent = async id => {
    if (!confirm('Confirmer votre réservation pour cet événement ?')) return;
    try {
        const r = await fetch(API + '?action=reserveEvent', { // ✅ Updated to match backend
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

document.getElementById('eventForm').onsubmit = async e => {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;

    const payload = {
        type_event: document.getElementById('title').value.trim(),
        date_event: document.getElementById('date').value,
        description: document.getElementById('description').value.trim(),
        id_categorie: document.getElementById('id_categorie').value,
        lieu: document.getElementById('location').value,
        latitude: document.getElementById('latitude').value || null,
        longitude: document.getElementById('longitude').value || null,
        etat: 'active'
    };

    const url = id ? `${API}?action=updateEvent&id=${id}` : `${API}?action=addEvent`; // ✅ Updated to match backend

    try {
        const response = await fetch(url, { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify(payload) 
        });
        const result = await response.json();
        showToast(result.message || 'Événement enregistré !', result.status !== 'error');
        bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
        loadEvents();
    } catch (error) {
        showToast('Erreur lors de l\'enregistrement', false);
    }
};

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
    document.getElementById('location').value = ev.lieu || '';
    document.getElementById('latitude').value = ev.latitude || '';
    document.getElementById('longitude').value = ev.longitude || '';
    document.getElementById('btnDeleteModal').style.display = 'block';

    if (ev.latitude && ev.longitude) {
        setTimeout(() => {
            if (map) {
                const l = L.latLng(ev.latitude, ev.longitude);
                map.setView(l, 16);
                marker.setLatLng(l);
            }
        }, 600);
    }

    $('#eventModal').modal('show');
};

window.deleteEvent = async id => {
    if (!confirm('Supprimer cet événement ?')) return;
    await fetch(`${API}?action=deleteEvent&id=${id}`, { method: 'POST' }); // ✅ Already correct
    showToast('Supprimé !');
    loadEvents();
};

// RESET MODAL
document.querySelector('[data-bs-target="#eventModal"]').onclick = () => {
    document.getElementById('eventForm').reset();
    document.getElementById('edit_id').value = '';
    document.getElementById('eventModalTitle').textContent = "Nouvel événement";
    document.getElementById('btnDeleteModal').style.display = 'none';
    document.getElementById('location').value = '';
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';

    if (map) {
        map.setView([48.8566, 2.3522], 2);
        marker.setLatLng([48.8566, 2.3522]);
    }
};

document.getElementById('btnDeleteModal').onclick = () => deleteEvent(document.getElementById('edit_id').value);
document.getElementById('refreshBtn').onclick = loadEvents;

// INITIALISATION CARTE AU BON MOMENT
document.getElementById('eventModal').addEventListener('shown.bs.modal', () => {
    setTimeout(() => initMap(), 400);
});

// LANCEMENT
loadCategories();
loadEvents();