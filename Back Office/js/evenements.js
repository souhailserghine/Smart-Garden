const API = '/Smart-Garden/Backend/public/index.php';
let PER_PAGE = 10;
let events = [], filteredEvents = [], currentPage = 1, calendar = null, chart = null;
let map = null, marker = null;
let searchFilters = { 
    search: '', 
    category: '', 
    status: 'all',
    sortBy: 'date_desc',
    dateStart: null,
    dateEnd: null,
    location: '',
    operator: 'OR'
};

let searchTimeout = null;
let searchHistory = [];

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
            date: e.date_event, // Format attendu: YYYY-MM-DD ou YYYY-MM-DD HH:MM:SS
            type: e.type_event || '',
            categorie: e.nom_categorie || 'Non classé',
            id_categorie: e.id_categorie || '',
            desc: e.description || '',
            lieu: e.lieu || null,
            latitude: e.latitude ? parseFloat(e.latitude) : null,
            longitude: e.longitude ? parseFloat(e.longitude) : null
        }));
        
        applySearchFilters();
        currentPage = 1;
        renderAll();
        document.getElementById('listMessage').textContent = '';
        
        // Charger les catégories pour le filtre après le chargement des événements
        await loadCategoriesForFilter();
    } catch (e) {
        console.error('Erreur de chargement:', e);
        showToast('Erreur de chargement', false);
    }
};

const renderAll = () => {
    renderTable();
    renderStats();
    renderCalendar();
    renderChart();
};

// Fonction utilitaire pour normaliser les dates
const normalizeDate = (dateString) => {
    if (!dateString) return null;
    if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}/)) {
        return dateString.split(' ')[0];
    }
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return null;
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Charger les catégories pour le filtre
const loadCategoriesForFilter = async () => {
    // Attendre que l'élément soit disponible
    let select = document.getElementById('filterCategory');
    if (!select) {
        // Réessayer après un court délai
        setTimeout(() => {
            loadCategoriesForFilter();
        }, 500);
        return;
    }
    
    let data = [];
    
    // Méthode 1: Essayer de charger depuis l'API
    try {
        const r = await fetch(API + '?action=listCategories');
        
        if (r.ok) {
            const response = await r.json();
            console.log('Réponse API catégories:', response);
            
            // Gérer différents formats de réponse
            if (response.data && Array.isArray(response.data)) {
                data = response.data;
            } else if (Array.isArray(response)) {
                data = response;
            }
        }
    } catch (e) {
        console.warn('Erreur API catégories, utilisation du fallback:', e);
    }
    
    // Méthode 2 (Fallback): Extraire les catégories des événements chargés
    if (data.length === 0 && events.length > 0) {
        const uniqueCategories = {};
        events.forEach(e => {
            if (e.id_categorie && e.categorie && e.categorie !== 'Non classé') {
                uniqueCategories[e.id_categorie] = e.categorie;
            }
        });
        data = Object.keys(uniqueCategories).map(id => ({
            id_categorie: id,
            nom_categorie: uniqueCategories[id]
        }));
        console.log('Catégories extraites des événements (fallback):', data);
    }
    
    // Vider et réinitialiser
    select.innerHTML = '<option value="">Toutes les catégories</option>';
    
    if (data.length === 0) {
        console.warn('Aucune catégorie trouvée');
        select.innerHTML += '<option value="" disabled>Aucune catégorie disponible</option>';
        return;
    }
    
    // Trier les catégories par nom
    data.sort((a, b) => {
        const nomA = (a.nom_categorie || a.nom || '').toLowerCase();
        const nomB = (b.nom_categorie || b.nom || '').toLowerCase();
        return nomA.localeCompare(nomB);
    });
    
    // Ajouter les catégories
    data.forEach(c => {
        const id = c.id_categorie || c.id;
        const nom = c.nom_categorie || c.nom || c.name;
        if (nom && id) {
            const option = new Option(nom, id);
            select.add(option);
        }
    });
    
    console.log(`✅ ${data.length} catégorie(s) chargée(s) dans le filtre`);
};

// SYSTÈME DE RECHERCHE ET TRI AVANCÉ AMÉLIORÉ
const applySearchFilters = () => {
    if (!events || events.length === 0) {
        filteredEvents = [];
        updateSearchResultsInfo();
        updateQuickStats();
        return;
    }

    let filtered = [...events];

    // Recherche textuelle améliorée avec opérateurs
    if (searchFilters.search && searchFilters.search.trim()) {
        const searchTerms = searchFilters.search.toLowerCase().trim().split(/\s+/).filter(t => t.length > 0);
        
        filtered = filtered.filter(e => {
            const title = (e.title || '').toLowerCase();
            const desc = (e.desc || '').toLowerCase();
            const lieu = (e.lieu || '').toLowerCase();
            const categorie = (e.categorie || '').toLowerCase();
            const type = (e.type || '').toLowerCase();
            
            const searchText = `${title} ${desc} ${lieu} ${categorie} ${type}`;
            
            if (searchFilters.operator === 'AND') {
                // Tous les mots doivent être présents
                return searchTerms.every(term => searchText.includes(term));
            } else {
                // Au moins un mot doit être présent (OR)
                return searchTerms.some(term => searchText.includes(term));
            }
        });
    }

    // Filtre par catégorie
    if (searchFilters.category) {
        filtered = filtered.filter(e => String(e.id_categorie) === String(searchFilters.category));
        console.log('Après filtre catégorie:', filtered.length);
    }

    // Filtre par statut
    if (searchFilters.status !== 'all') {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        filtered = filtered.filter(e => {
            if (!e.date) return false;
            const normalized = normalizeDate(e.date);
            if (!normalized) return false;
            const eventDate = new Date(normalized);
            eventDate.setHours(0, 0, 0, 0);
            
            switch (searchFilters.status) {
                case 'upcoming':
                    return eventDate >= today;
                case 'past':
                    return eventDate < today;
                case 'today':
                    const todayStr = today.toISOString().split('T')[0];
                    return normalized === todayStr;
                default:
                    return true;
            }
        });
    }

    // Filtre par période de dates
    if (searchFilters.dateStart || searchFilters.dateEnd) {
        filtered = filtered.filter(e => {
            if (!e.date) return false;
            const normalized = normalizeDate(e.date);
            if (!normalized) return false;
            const eventDate = new Date(normalized);
            eventDate.setHours(0, 0, 0, 0);
            
            if (searchFilters.dateStart && searchFilters.dateEnd) {
                const start = new Date(searchFilters.dateStart);
                const end = new Date(searchFilters.dateEnd);
                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59, 999);
                return eventDate >= start && eventDate <= end;
            } else if (searchFilters.dateStart) {
                const start = new Date(searchFilters.dateStart);
                start.setHours(0, 0, 0, 0);
                return eventDate >= start;
            } else if (searchFilters.dateEnd) {
                const end = new Date(searchFilters.dateEnd);
                end.setHours(23, 59, 59, 999);
                return eventDate <= end;
            }
            return true;
        });
    }

    // Filtre par lieu
    if (searchFilters.location && searchFilters.location.trim()) {
        const locationLower = searchFilters.location.toLowerCase().trim();
        filtered = filtered.filter(e => {
            const lieu = (e.lieu || '').toLowerCase();
            return lieu.includes(locationLower);
        });
    }

    // Tri
    filtered.sort((a, b) => {
        switch (searchFilters.sortBy) {
            case 'date_desc':
                const dateA = a.date ? new Date(a.date) : new Date(0);
                const dateB = b.date ? new Date(b.date) : new Date(0);
                return dateB - dateA;
            case 'date_asc':
                const dateA2 = a.date ? new Date(a.date) : new Date(0);
                const dateB2 = b.date ? new Date(b.date) : new Date(0);
                return dateA2 - dateB2;
            case 'title_asc':
                return (a.title || '').localeCompare(b.title || '');
            case 'title_desc':
                return (b.title || '').localeCompare(a.title || '');
            case 'category_asc':
                return (a.categorie || '').localeCompare(b.categorie || '');
            default:
                return 0;
        }
    });

    filteredEvents = filtered;
    updateSearchResultsInfo();
    updateQuickStats();
    highlightSearchTerms();
};

// Mise à jour des statistiques rapides
const updateQuickStats = () => {
    const totalEl = document.getElementById('quickStatTotal');
    const filteredEl = document.getElementById('quickStatFiltered');
    if (totalEl) totalEl.textContent = events.length;
    if (filteredEl) filteredEl.textContent = filteredEvents.length;
};

// Mise en évidence des termes de recherche dans les résultats
const highlightSearchTerms = () => {
    if (!searchFilters.search || !searchFilters.search.trim()) return;
    
    const terms = searchFilters.search.trim().split(/\s+/).filter(t => t.length > 0);
    const rows = document.querySelectorAll('#eventsBody tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            let html = cell.innerHTML;
            terms.forEach(term => {
                const regex = new RegExp(`(${term})`, 'gi');
                html = html.replace(regex, '<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px;">$1</mark>');
            });
            cell.innerHTML = html;
        });
    });
};

// Générer des suggestions de recherche
const generateSearchSuggestions = (query) => {
    if (!query || query.length < 2) {
        document.getElementById('searchSuggestions').style.display = 'none';
        return;
    }
    
    const queryLower = query.toLowerCase();
    const suggestions = [];
    
    // Suggestions basées sur les titres
    events.forEach(e => {
        const title = e.title || '';
        if (title.toLowerCase().includes(queryLower) && !suggestions.includes(title)) {
            suggestions.push(title);
        }
    });
    
    // Suggestions basées sur les catégories
    const categories = [...new Set(events.map(e => e.categorie).filter(c => c))];
    categories.forEach(cat => {
        if (cat.toLowerCase().includes(queryLower) && !suggestions.includes(cat)) {
            suggestions.push(cat);
        }
    });
    
    // Suggestions basées sur les lieux
    const locations = [...new Set(events.map(e => e.lieu).filter(l => l))];
    locations.forEach(loc => {
        if (loc.toLowerCase().includes(queryLower) && !suggestions.includes(loc)) {
            suggestions.push(loc);
        }
    });
    
    // Limiter à 5 suggestions
    const limitedSuggestions = suggestions.slice(0, 5);
    
    const suggestionsDiv = document.getElementById('searchSuggestions');
    if (limitedSuggestions.length > 0) {
        suggestionsDiv.innerHTML = limitedSuggestions.map(s => `
            <a href="#" class="list-group-item list-group-item-action suggestion-item" data-suggestion="${s}">
                <i class="fa fa-search me-2 text-muted"></i>${s}
            </a>
        `).join('');
        suggestionsDiv.style.display = 'block';
        
        // Attacher les événements de clic
        suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const suggestion = e.currentTarget.getAttribute('data-suggestion');
                document.getElementById('searchInput').value = suggestion;
                searchFilters.search = suggestion;
                suggestionsDiv.style.display = 'none';
                applySearchFilters();
                currentPage = 1;
                renderAll();
            });
        });
    } else {
        suggestionsDiv.style.display = 'none';
    }
};

const updateSearchResultsInfo = () => {
    const infoDiv = document.getElementById('searchResultsInfo');
    const textSpan = document.getElementById('searchResultsText');
    
    if (!infoDiv || !textSpan) return;
    
    const total = events.length;
    const filtered = filteredEvents.length;
    
    if (filtered !== total || searchFilters.search || searchFilters.category || searchFilters.status !== 'all') {
        textSpan.textContent = `${filtered} événement${filtered > 1 ? 's' : ''} trouvé${filtered > 1 ? 's' : ''} sur ${total}`;
        infoDiv.classList.remove('d-none');
    } else {
        infoDiv.classList.add('d-none');
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    
    // Normaliser la date d'abord
    const normalized = normalizeDate(dateString);
    if (!normalized) return dateString; // Retourner la chaîne originale si on ne peut pas la parser
    
    const date = new Date(normalized);
    if (isNaN(date.getTime())) return dateString; // Retourner la chaîne originale si la date est invalide
    
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

// TABLEAU + PAGINATION
const renderTable = () => {
    const start = (currentPage - 1) * PER_PAGE;
    const pageEvents = filteredEvents.slice(start, start + PER_PAGE);

    const tbody = document.getElementById('eventsBody');
    tbody.innerHTML = pageEvents.length === 0
        ? '<tr><td colspan="7" class="text-center text-muted py-5"><h4><i class="fa fa-calendar-times me-2"></i>Aucun événement trouvé</h4><p class="text-muted">Essayez de modifier vos filtres</p></td></tr>'
        : pageEvents.map((e, i) => {
            const eventDate = e.date ? formatDate(e.date) : '-';
            const eventDateNormalized = e.date ? normalizeDate(e.date) : null;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const isPast = eventDateNormalized ? new Date(eventDateNormalized) < today : false;
            const dateClass = isPast ? 'text-muted' : 'text-success fw-bold';
            return `
            <tr>
                <td><strong>${start + i + 1}</strong></td>
                <td><strong>${e.title}</strong></td>
                <td class="${dateClass}">
                    <i class="fa fa-calendar me-1"></i>${eventDate}
                    ${isPast ? '<small class="d-block text-muted">(Passé)</small>' : '<small class="d-block text-success">(À venir)</small>'}
                </td>
                <td>${e.type}</td>
                <td><span class="badge bg-primary text-white">${e.categorie}</span></td>
                <td>
                    ${e.lieu 
                        ? `<small><i class="fa fa-map-marker-alt text-success me-1"></i>${e.lieu.substring(0, 35)}${e.lieu.length > 35 ? '...' : ''}</small>`
                        : '<small class="text-muted">Non spécifié</small>'
                    }
                </td>
                <td class="text-center">
                    <button class="btn btn-sm me-1"
                            style="background-color: #FFC107; border-color: #FFC107; color: black;"
                            onclick="editEvent(${e.id})"
                            title="Modifier">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger me-1" 
                            onclick="deleteEvent(${e.id})"
                            title="Supprimer">
                        <i class="fa fa-trash"></i>
                    </button>
                    <button class="btn btn-sm btn-success" 
                            onclick="reserveEvent(${e.id})"
                            title="Réserver">
                        <i class="fa fa-ticket-alt"></i> Réserver
                    </button>
                </td>
            </tr>`;
        }).join('');

    renderPagination();
};

const renderPagination = () => {
    const totalPages = Math.ceil(filteredEvents.length / PER_PAGE);
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
    if (page < 1 || page > Math.ceil(filteredEvents.length / PER_PAGE)) return;
    currentPage = page;
    renderTable();
};

// STATS + CALENDRIER + GRAPHIQUE
const renderStats = () => {
    const today = new Date(); today.setHours(0,0,0,0);
    const coming = filteredEvents.filter(e => e.date && new Date(e.date) >= today).length;
    document.getElementById('statTotal').textContent = filteredEvents.length;
    document.getElementById('statUpcoming').textContent = coming;
    document.getElementById('statPast').textContent = filteredEvents.length - coming;
};

const renderCalendar = () => {
    if (calendar) calendar.destroy();
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
        events: filteredEvents.map(e => ({
            id: e.id,
            title: e.title + (e.lieu ? ` @ ${e.lieu.substring(0, 20)}...` : ''),
            start: e.date,
            extendedProps: { lieu: e.lieu },
            backgroundColor: '#4CAF50',
            borderColor: '#2E7D32'
        })),
        eventClick: info => info.event.extendedProps.lieu && alert(`Lieu : ${info.event.extendedProps.lieu}`)
    });
    calendar.render();
};

const renderChart = () => {
    const ctx = document.getElementById('eventsChart').getContext('2d');
    const counts = {};
    filteredEvents.forEach(e => {
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
        data: { 
            labels, 
            datasets: [{ 
                label: 'Événements', 
                data: labels.map(k => counts[k]), 
                backgroundColor: 'rgba(76, 175, 80, 0.8)',
                borderColor: '#2E7D32',
                borderWidth: 2,
                borderRadius: 8
            }] 
        },
        options: { 
            responsive: true, 
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(46, 125, 50, 0.9)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#555'
                    },
                    grid: {
                        color: 'rgba(76, 175, 80, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#555'
                    },
                    grid: {
                        color: 'rgba(76, 175, 80, 0.1)'
                    }
                }
            }
        }
    });
};

// ACTIONS
window.reserveEvent = async id => {
    // Trouver le titre de l'événement
    const event = events.find(e => e.id == id);
    const eventTitle = event ? event.title : '';
    
    // Afficher une confirmation personnalisée
    const oldConfirm = document.getElementById('reserveConfirmationToast');
    if (oldConfirm) oldConfirm.remove();

    const confirmDiv = document.createElement('div');
    confirmDiv.id = 'reserveConfirmationToast';
    confirmDiv.innerHTML = `
        <div style="position:fixed;top:20px;right:20px;z-index:10000;animation:slideInRight 0.4s ease;">
            <div style="background:linear-gradient(135deg, #4CAF50, #2E7D32);color:white;border-radius:20px;padding:25px 30px;min-width:380px;box-shadow:0 20px 50px rgba(76,175,80,0.5);border:2px solid rgba(255,255,255,0.2);">
                <div style="font-weight:700;font-size:1.3rem;margin-bottom:12px;display:flex;align-items:center;gap:10px;">
                    <i class="fa fa-calendar-check" style="font-size:1.5rem;"></i>
                    Confirmer la réservation
                </div>
                <div style="margin-bottom:25px;opacity:0.95;font-size:1rem;line-height:1.5;">
                    ${eventTitle ? `Voulez-vous réserver l'événement "<strong>${eventTitle}</strong>" ?` : 'Voulez-vous confirmer votre réservation pour cet événement ?'}
                </div>
                <div style="display:flex;justify-content:flex-end;gap:15px;">
                    <button onclick="document.getElementById('reserveConfirmationToast').remove()" 
                            style="background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.3);color:white;padding:10px 25px;border-radius:50px;cursor:pointer;font-weight:600;transition:all 0.3s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        Annuler
                    </button>
                    <button onclick="confirmReserve(${id})" 
                            style="background:rgba(255,255,255,0.3);border:2px solid white;color:white;padding:10px 30px;border-radius:50px;cursor:pointer;font-weight:600;box-shadow:0 5px 20px rgba(0,0,0,0.3);transition:all 0.3s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.4)';this.style.transform='scale(1.05)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.3)';this.style.transform='scale(1)'">
                        Oui, réserver
                    </button>
                </div>
            </div>
        </div>`;
    document.body.appendChild(confirmDiv);
};

window.confirmReserve = async (id) => {
    const confirmDiv = document.getElementById('reserveConfirmationToast');
    if (confirmDiv) confirmDiv.remove();
    
    try {
        const r = await fetch(API + '?action=reserveEvent', {
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

// Fonction pour afficher une confirmation personnalisée
const showDeleteConfirmation = (id, eventTitle = '') => {
    // Supprimer l'ancienne confirmation si elle existe
    const oldConfirm = document.getElementById('deleteConfirmationToast');
    if (oldConfirm) oldConfirm.remove();

    const confirmDiv = document.createElement('div');
    confirmDiv.id = 'deleteConfirmationToast';
    confirmDiv.innerHTML = `
        <div style="position:fixed;top:20px;right:20px;z-index:10000;animation:slideInRight 0.4s ease;">
            <div style="background:linear-gradient(135deg, #2d3436, #1a1a2e);color:white;border-radius:20px;padding:25px 30px;min-width:380px;box-shadow:0 20px 50px rgba(0,0,0,0.5);border:2px solid rgba(231,76,60,0.5);">
                <div style="font-weight:700;font-size:1.3rem;margin-bottom:12px;display:flex;align-items:center;gap:10px;">
                    <i class="fa fa-exclamation-triangle" style="font-size:1.5rem;color:#E74C3C;"></i>
                    Confirmer la suppression
                </div>
                <div style="margin-bottom:25px;opacity:0.95;font-size:1rem;line-height:1.5;">
                    ${eventTitle ? `Voulez-vous vraiment supprimer l'événement "<strong>${eventTitle}</strong>" ?` : 'Voulez-vous vraiment supprimer cet événement ?'}
                </div>
                <div style="display:flex;justify-content:flex-end;gap:15px;">
                    <button onclick="document.getElementById('deleteConfirmationToast').remove()" 
                            style="background:transparent;border:2px solid #636e72;color:#bdc3c7;padding:10px 25px;border-radius:50px;cursor:pointer;font-weight:600;transition:all 0.3s;"
                            onmouseover="this.style.borderColor='#95a5a6';this.style.color='#ecf0f1'"
                            onmouseout="this.style.borderColor='#636e72';this.style.color='#bdc3c7'">
                        Annuler
                    </button>
                    <button onclick="confirmDelete(${id})" 
                            style="background:linear-gradient(135deg, #E74C3C, #C0392B);border:none;color:white;padding:10px 30px;border-radius:50px;cursor:pointer;font-weight:600;box-shadow:0 5px 20px rgba(231,76,60,0.4);transition:all 0.3s;"
                            onmouseover="this.style.transform='scale(1.05)';this.style.boxShadow='0 8px 25px rgba(231,76,60,0.6)'"
                            onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 5px 20px rgba(231,76,60,0.4)'">
                        Oui, supprimer
                    </button>
                </div>
            </div>
        </div>`;
    document.body.appendChild(confirmDiv);
};

// Fonction pour confirmer la suppression
window.confirmDelete = async (id) => {
    const confirmDiv = document.getElementById('deleteConfirmationToast');
    if (confirmDiv) confirmDiv.remove();
    
    try {
        const response = await fetch(`${API}?action=deleteEvent&id=${id}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.status === 'success') {
            showToast('Événement supprimé avec succès !');
            loadEvents();
        } else {
            showToast(result.message || 'Erreur lors de la suppression', false);
        }
    } catch (error) {
        console.error('Erreur suppression:', error);
        showToast('Erreur lors de la suppression', false);
    }
};

window.deleteEvent = function(id) {
    // Trouver le titre de l'événement pour l'afficher dans la confirmation
    const event = events.find(e => e.id == id);
    const eventTitle = event ? event.title : '';
    showDeleteConfirmation(id, eventTitle);
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

// Initialisation des événements après chargement du DOM
const initEventListeners = () => {
    // Boutons de base
    const btnDeleteModal = document.getElementById('btnDeleteModal');
    const refreshBtn = document.getElementById('refreshBtn');
    if (btnDeleteModal) {
        btnDeleteModal.onclick = () => deleteEvent(document.getElementById('edit_id').value);
    }
    if (refreshBtn) {
        refreshBtn.onclick = loadEvents;
    }

    // GESTION DE LA RECHERCHE AVANCÉE
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const filterCategory = document.getElementById('filterCategory');
    const filterStatus = document.getElementById('filterStatus');
    const sortBy = document.getElementById('sortBy');
    const perPageSelect = document.getElementById('perPageSelect');
    const resetSearchBtn = document.getElementById('resetSearchBtn');
    const exportBtn = document.getElementById('exportBtn');

    if (searchInput) {
        // Recherche avec debounce pour améliorer les performances
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            searchFilters.search = query;
            
            // Générer des suggestions
            generateSearchSuggestions(query);
            
            // Debounce pour éviter trop de recherches
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applySearchFilters();
                currentPage = 1;
                renderAll();
            }, 300);
        });
        
        // Masquer les suggestions quand on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#searchInput') && !e.target.closest('#searchSuggestions')) {
                document.getElementById('searchSuggestions').style.display = 'none';
            }
        });
        
        // Navigation au clavier dans les suggestions
        searchInput.addEventListener('keydown', (e) => {
            const suggestions = document.querySelectorAll('.suggestion-item');
            if (e.key === 'ArrowDown' && suggestions.length > 0) {
                e.preventDefault();
                suggestions[0].focus();
            }
        });
    }

    if (clearSearch) {
        clearSearch.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            searchFilters.search = '';
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }

    if (filterCategory) {
        filterCategory.addEventListener('change', (e) => {
            searchFilters.category = e.target.value;
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', (e) => {
            searchFilters.status = e.target.value;
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }

    if (sortBy) {
        sortBy.addEventListener('change', (e) => {
            searchFilters.sortBy = e.target.value;
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', (e) => {
            PER_PAGE = parseInt(e.target.value);
            currentPage = 1;
            renderTable();
            renderPagination();
        });
    }

    if (resetSearchBtn) {
        resetSearchBtn.addEventListener('click', () => {
            searchFilters = { 
                search: '', 
                category: '', 
                status: 'all', 
                sortBy: 'date_desc',
                dateStart: null,
                dateEnd: null,
                location: '',
                operator: 'OR'
            };
            if (searchInput) searchInput.value = '';
            if (filterCategory) filterCategory.value = '';
            if (filterStatus) filterStatus.value = 'all';
            if (sortBy) sortBy.value = 'date_desc';
            const dateStart = document.getElementById('filterDateStart');
            const dateEnd = document.getElementById('filterDateEnd');
            const filterLocation = document.getElementById('filterLocation');
            if (dateStart) dateStart.value = '';
            if (dateEnd) dateEnd.value = '';
            if (filterLocation) filterLocation.value = '';
            document.getElementById('searchSuggestions').style.display = 'none';
            applySearchFilters();
            currentPage = 1;
            renderAll();
            showToast('Recherche réinitialisée');
        });
    }
    
    // Toggle options avancées
    const toggleAdvanced = document.getElementById('toggleAdvancedSearch');
    const advancedOptions = document.getElementById('advancedSearchOptions');
    if (toggleAdvanced && advancedOptions) {
        toggleAdvanced.addEventListener('click', () => {
            const isVisible = advancedOptions.style.display !== 'none';
            advancedOptions.style.display = isVisible ? 'none' : 'block';
            toggleAdvanced.innerHTML = isVisible 
                ? '<i class="fa fa-cog me-1"></i>Options avancées'
                : '<i class="fa fa-times me-1"></i>Masquer options';
        });
    }
    
    // Filtres de dates avancés
    const dateStart = document.getElementById('filterDateStart');
    const dateEnd = document.getElementById('filterDateEnd');
    const filterLocation = document.getElementById('filterLocation');
    const searchOperator = document.getElementById('searchOperator');
    
    if (dateStart) {
        dateStart.addEventListener('change', (e) => {
            searchFilters.dateStart = e.target.value || null;
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }
    
    if (dateEnd) {
        dateEnd.addEventListener('change', (e) => {
            searchFilters.dateEnd = e.target.value || null;
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }
    
    if (filterLocation) {
        filterLocation.addEventListener('input', (e) => {
            searchFilters.location = e.target.value.trim();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applySearchFilters();
                currentPage = 1;
                renderAll();
            }, 300);
        });
    }
    
    if (searchOperator) {
        searchOperator.addEventListener('change', (e) => {
            searchFilters.operator = e.target.value;
            applySearchFilters();
            currentPage = 1;
            renderAll();
        });
    }

    // EXPORT DES DONNÉES (CSV)
    const exportCSV = document.getElementById('exportCSV');
    if (exportCSV) {
        exportCSV.addEventListener('click', (e) => {
            e.preventDefault();
            if (filteredEvents.length === 0) {
                showToast('Aucun événement à exporter', false);
                return;
            }
            
            let csv = 'Titre,Date,Catégorie,Type,Description,Lieu,Latitude,Longitude\n';
            filteredEvents.forEach(e => {
                const title = (e.title || '').replace(/"/g, '""');
                const date = formatDate(e.date) || '-';
                const categorie = (e.categorie || '').replace(/"/g, '""');
                const type = (e.type || '').replace(/"/g, '""');
                const desc = (e.desc || '').replace(/"/g, '""').replace(/\n/g, ' ');
                const lieu = (e.lieu || '').replace(/"/g, '""');
                const lat = e.latitude || '';
                const lng = e.longitude || '';
                csv += `"${title}","${date}","${categorie}","${type}","${desc}","${lieu}","${lat}","${lng}"\n`;
            });
            
            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `evenements_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
            
            showToast(`${filteredEvents.length} événement(s) exporté(s) en CSV !`);
        });
    }
    
    // EXPORT JSON
    const exportJSON = document.getElementById('exportJSON');
    if (exportJSON) {
        exportJSON.addEventListener('click', (e) => {
            e.preventDefault();
            if (filteredEvents.length === 0) {
                showToast('Aucun événement à exporter', false);
                return;
            }
            
            const json = JSON.stringify(filteredEvents, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `evenements_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            
            showToast(`${filteredEvents.length} événement(s) exporté(s) en JSON !`);
        });
    }
    
    // Export principal (CSV par défaut)
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            if (exportCSV) exportCSV.click();
        });
    }
};

// INITIALISATION CARTE AU BON MOMENT
const eventModal = document.getElementById('eventModal');
if (eventModal) {
    eventModal.addEventListener('shown.bs.modal', () => {
        setTimeout(() => initMap(), 400);
    });
}

// Initialisation au chargement de la page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initEventListeners();
        loadCategories();
        loadEvents();
    });
} else {
    // DOM déjà chargé
    initEventListeners();
    loadCategories();
    loadEvents();
}