// API Base URL
const API_BASE = '../backend/api';

// State
let terminalActive = false;
let queryPollingInterval = null;
let stats = {
    totalQueries: 0,
    successQueries: 0,
    errorQueries: 0
};

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    initializeTabs();
    loadDashboard();
    startQueryPolling();
    setupEventListeners();
});

// Tab Management
function initializeTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.dataset.tab;
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    // Update buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(tabName).classList.add('active');

    // Load data for specific tabs
    switch(tabName) {
        case 'movies':
            loadMovies();
            break;
        case 'series':
            loadSeries();
            break;
        case 'celebrities':
            loadCelebrities();
            break;
        case 'users':
            loadUsers();
            break;
        case 'reviews':
            loadReviews();
            break;
        case 'views':
            loadViews('list');
            break;
    }
}

// Event Listeners
function setupEventListeners() {
    // Search and filters
    document.getElementById('movieSearch')?.addEventListener('input', debounce(loadMovies, 500));
    document.getElementById('genreFilter')?.addEventListener('change', loadMovies);
    document.getElementById('seriesSearch')?.addEventListener('input', debounce(loadSeries, 500));
    document.getElementById('statusFilter')?.addEventListener('change', loadSeries);
    document.getElementById('celebritySearch')?.addEventListener('input', debounce(loadCelebrities, 500));
    document.getElementById('professionFilter')?.addEventListener('change', loadCelebrities);

    // Users search and filters
    document.getElementById('userSearch')?.addEventListener('input', debounce(loadUsers, 500));

    // Terminal toggle
    document.getElementById('toggleTerminal')?.addEventListener('click', toggleTerminal);
}

// Dashboard
async function loadDashboard() {
    try {
        // Load movies count
        const moviesRes = await fetch(`${API_BASE}/movies.php`);
        const moviesData = await moviesRes.json();
        document.getElementById('totalMovies').textContent = moviesData.count || 0;

        // Load series count
        const seriesRes = await fetch(`${API_BASE}/tv-series.php`);
        const seriesData = await seriesRes.json();
        document.getElementById('totalSeries').textContent = seriesData.data?.length || 0;

        // Load celebrities count
        const celebRes = await fetch(`${API_BASE}/celebrities.php`);
        const celebData = await celebRes.json();
        document.getElementById('totalCelebrities').textContent = celebData.data?.length || 0;

        // Load SQL stats
        const statsRes = await fetch(`${API_BASE}/sql-logs.php`, { method: 'POST' });
        const statsData = await statsRes.json();
        document.getElementById('totalQueries').textContent = statsData.data?.total_queries || 0;

        // Load recent activity
        loadRecentActivity();
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

async function loadRecentActivity() {
    try {
        const res = await fetch(`${API_BASE}/sql-logs.php?limit=10`);
        const data = await res.json();
        
        if (data.success) {
            const activityList = document.getElementById('recentActivity');
            activityList.innerHTML = '';

            data.data.forEach(query => {
                const item = document.createElement('div');
                item.className = `activity-item ${query.status}`;
                item.innerHTML = `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span class="query-type ${query.type}">${query.type}</span>
                        <span style="font-size: 0.85rem; color: #6b7280;">${query.datetime}</span>
                    </div>
                    <div style="font-family: monospace; font-size: 0.9rem; color: #374151;">
                        ${query.query.substring(0, 100)}${query.query.length > 100 ? '...' : ''}
                    </div>
                `;
                activityList.appendChild(item);
            });
        }
    } catch (error) {
        console.error('Error loading recent activity:', error);
    }
}

// Movies Management
async function loadMovies() {
    const search = document.getElementById('movieSearch')?.value || '';
    const genre = document.getElementById('genreFilter')?.value || '';
    
    let url = `${API_BASE}/movies.php?`;
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (genre) url += `genre=${encodeURIComponent(genre)}&`;

    try {
        const res = await fetch(url);
        const data = await res.json();

        if (data.success) {
            displayMovies(data.data);
        }
    } catch (error) {
        console.error('Error loading movies:', error);
    }
}

function displayMovies(movies) {
    const container = document.getElementById('moviesList');
    container.innerHTML = '';

    if (!movies || movies.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280;">No movies found</p>';
        return;
    }

    movies.forEach(movie => {
        const card = document.createElement('div');
        card.className = 'data-card';
        card.innerHTML = `
            <h3>${movie.title}</h3>
            <p><strong>Director:</strong> ${movie.director_name || 'N/A'}</p>
            <p><strong>Genre:</strong> ${movie.genre || 'N/A'}</p>
            <p><strong>Release Date:</strong> ${movie.release_date || 'N/A'}</p>
            <p><strong>Rating:</strong> ⭐ ${movie.rating || 'N/A'}/10</p>
            <p><strong>Duration:</strong> ${movie.duration || 'N/A'} minutes</p>
            <div class="data-card-actions">
                <button class="btn btn-primary" onclick="viewMovie(${movie.movie_id})">View Details</button>
                <button class="btn btn-danger" onclick="deleteMovie(${movie.movie_id})">Delete</button>
            </div>
        `;
        container.appendChild(card);
    });
}

async function deleteMovie(id) {
    if (!confirm('Are you sure you want to delete this movie?')) return;

    try {
        const res = await fetch(`${API_BASE}/movies.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ movie_id: id })
        });

        const data = await res.json();
        if (data.success) {
            alert('Movie deleted successfully');
            loadMovies();
            loadDashboard();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting movie');
    }
}

function showAddMovieForm() {
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modalBody');

    modalBody.innerHTML = `
        <h2>Add New Movie</h2>
        <form id="addMovieForm">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Release Date</label>
                <input type="date" name="release_date">
            </div>
            <div class="form-group">
                <label>Duration (minutes)</label>
                <input type="number" name="duration">
            </div>
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" placeholder="e.g., Action, Drama">
            </div>
            <div class="form-group">
                <label>Language</label>
                <input type="text" name="language">
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country">
            </div>
            <div class="form-group">
                <label>Rating (0-10)</label>
                <input type="number" name="rating" min="0" max="10" step="0.1">
            </div>
            <div class="form-group">
                <label>Plot Summary</label>
                <textarea name="plot_summary" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Movie</button>
        </form>
    `;

    modal.classList.add('active');

    // Use setTimeout to ensure DOM is ready and get fresh reference
    setTimeout(() => {
        const form = document.getElementById('addMovieForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    const res = await fetch(`${API_BASE}/movies.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await res.json();
                    if (result.success) {
                        alert('Movie added successfully!');
                        closeModal();
                        loadMovies();
                        loadDashboard();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error adding movie');
                }
            });
        }
    }, 0);
}

// TV Series Management
async function loadSeries() {
    const search = document.getElementById('seriesSearch')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    
    let url = `${API_BASE}/tv-series.php?`;
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (status) url += `status=${encodeURIComponent(status)}&`;

    try {
        const res = await fetch(url);
        const data = await res.json();

        if (data.success) {
            displaySeries(data.data);
        }
    } catch (error) {
        console.error('Error loading series:', error);
    }
}

function displaySeries(series) {
    const container = document.getElementById('seriesList');
    container.innerHTML = '';

    if (!series || series.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280;">No TV series found</p>';
        return;
    }

    series.forEach(show => {
        const card = document.createElement('div');
        card.className = 'data-card';
        card.innerHTML = `
            <h3>${show.title}</h3>
            <p><strong>Creator:</strong> ${show.creator_name || 'N/A'}</p>
            <p><strong>Genre:</strong> ${show.genre || 'N/A'}</p>
            <p><strong>Seasons:</strong> ${show.number_of_seasons || 'N/A'}</p>
            <p><strong>Episodes:</strong> ${show.number_of_episodes || 'N/A'}</p>
            <p><strong>Status:</strong> ${show.status || 'N/A'}</p>
            <p><strong>Rating:</strong> ⭐ ${show.rating || 'N/A'}/10</p>
            <div class="data-card-actions">
                <button class="btn btn-primary" onclick="viewSeries(${show.series_id})">View Details</button>
                <button class="btn btn-danger" onclick="deleteSeries(${show.series_id})">Delete</button>
            </div>
        `;
        container.appendChild(card);
    });
}

async function deleteSeries(id) {
    if (!confirm('Are you sure you want to delete this TV series?')) return;

    try {
        const res = await fetch(`${API_BASE}/tv-series.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ series_id: id })
        });

        const data = await res.json();
        if (data.success) {
            alert('TV Series deleted successfully');
            loadSeries();
            loadDashboard();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting series');
    }
}

async function viewSeries(id) {
    try {
        const res = await fetch(`${API_BASE}/tv-series.php?id=${id}`);
        const data = await res.json();

        if (data.success) {
            const series = data.data;
            const modal = document.getElementById('modal');
            const modalBody = document.getElementById('modalBody');

            let castHTML = '';
            if (series.cast && series.cast.length > 0) {
                castHTML = '<h3 style="margin-top: 20px; color: #2563eb;">Main Cast</h3><div class="cast-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;">';
                series.cast.forEach(c => {
                    castHTML += `
                        <div style="padding: 10px; background: #f3f4f6; border-radius: 8px; border-left: 3px solid #2563eb;">
                            <strong style="color: #1f2937;">${c.name}</strong><br>
                            <span style="color: #6b7280; font-size: 0.9em;">as ${c.role || 'N/A'}</span><br>
                            <span style="color: #9ca3af; font-size: 0.85em;">(${c.cast_type})</span>
                        </div>
                    `;
                });
                castHTML += '</div>';
            }

            modalBody.innerHTML = `
                <h2 style="color: #1f2937; margin-bottom: 20px;">${series.title}</h2>
                <div style="display: grid; gap: 10px;">
                    <p><strong>Creator:</strong> ${series.creator_name || 'N/A'}</p>
                    <p><strong>Genre:</strong> ${series.genre || 'N/A'}</p>
                    <p><strong>First Air Date:</strong> ${series.first_air_date || 'N/A'}</p>
                    <p><strong>Seasons:</strong> ${series.number_of_seasons || 'N/A'}</p>
                    <p><strong>Episodes:</strong> ${series.number_of_episodes || 'N/A'}</p>
                    <p><strong>Status:</strong> <span style="color: ${series.status === 'Ongoing' ? '#10b981' : '#ef4444'}; font-weight: bold;">${series.status || 'N/A'}</span></p>
                    <p><strong>Language:</strong> ${series.language || 'N/A'}</p>
                    <p><strong>Country:</strong> ${series.country || 'N/A'}</p>
                    <p><strong>Rating:</strong> ⭐ ${series.rating || 'N/A'}/10</p>
                    <p><strong>Total Ratings:</strong> ${series.total_ratings || 0}</p>
                    ${series.plot_summary ? `<p><strong>Plot:</strong> ${series.plot_summary}</p>` : ''}
                </div>
                ${castHTML}
                <div style="margin-top: 20px;">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            `;

            modal.classList.add('active');
        }
    } catch (error) {
        alert('Error loading series details');
    }
}

function showAddSeriesForm() {
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modalBody');

    modalBody.innerHTML = `
        <h2>Add New TV Series</h2>
        <form id="addSeriesForm">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>First Air Date</label>
                <input type="date" name="first_air_date">
            </div>
            <div class="form-group">
                <label>Number of Seasons</label>
                <input type="number" name="number_of_seasons">
            </div>
            <div class="form-group">
                <label>Number of Episodes</label>
                <input type="number" name="number_of_episodes">
            </div>
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Ongoing">Ongoing</option>
                    <option value="Ended">Ended</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label>Rating (0-10)</label>
                <input type="number" name="rating" min="0" max="10" step="0.1">
            </div>
            <div class="form-group">
                <label>Plot Summary</label>
                <textarea name="plot_summary" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Series</button>
        </form>
    `;

    modal.classList.add('active');

    // Use setTimeout to ensure DOM is ready and get fresh reference
    setTimeout(() => {
        const form = document.getElementById('addSeriesForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    const res = await fetch(`${API_BASE}/tv-series.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await res.json();
                    if (result.success) {
                        alert('TV Series added successfully!');
                        closeModal();
                        loadSeries();
                        loadDashboard();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error adding series');
                }
            });
        }
    }, 0);
}

// Celebrities Management
async function loadCelebrities() {
    const search = document.getElementById('celebritySearch')?.value || '';
    const profession = document.getElementById('professionFilter')?.value || '';
    
    let url = `${API_BASE}/celebrities.php?`;
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (profession) url += `profession=${encodeURIComponent(profession)}&`;

    try {
        const res = await fetch(url);
        const data = await res.json();

        if (data.success) {
            displayCelebrities(data.data);
        }
    } catch (error) {
        console.error('Error loading celebrities:', error);
    }
}

function displayCelebrities(celebrities) {
    const container = document.getElementById('celebritiesList');
    container.innerHTML = '';

    if (!celebrities || celebrities.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280;">No celebrities found</p>';
        return;
    }

    celebrities.forEach(celeb => {
        const card = document.createElement('div');
        card.className = 'data-card';
        card.innerHTML = `
            <h3>${celeb.name}</h3>
            <p><strong>Profession:</strong> ${celeb.profession || 'N/A'}</p>
            <p><strong>Nationality:</strong> ${celeb.nationality || 'N/A'}</p>
            <p><strong>Birth Date:</strong> ${celeb.birth_date || 'N/A'}</p>
            <div class="data-card-actions">
                <button class="btn btn-danger" onclick="deleteCelebrity(${celeb.celebrity_id})">Delete</button>
            </div>
        `;
        container.appendChild(card);
    });
}

async function deleteCelebrity(id) {
    if (!confirm('Are you sure you want to delete this celebrity?')) return;

    try {
        const res = await fetch(`${API_BASE}/celebrities.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ celebrity_id: id })
        });

        const data = await res.json();
        if (data.success) {
            alert('Celebrity deleted successfully');
            loadCelebrities();
            loadDashboard();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting celebrity');
    }
}

function showAddCelebrityForm() {
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modalBody');

    modalBody.innerHTML = `
        <h2>Add New Celebrity</h2>
        <form id="addCelebrityForm">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Birth Date</label>
                <input type="date" name="birth_date">
            </div>
            <div class="form-group">
                <label>Profession</label>
                <select name="profession">
                    <option value="Actor">Actor</option>
                    <option value="Director">Director</option>
                    <option value="Writer">Writer</option>
                    <option value="Producer">Producer</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nationality</label>
                <input type="text" name="nationality">
            </div>
            <div class="form-group">
                <label>Biography</label>
                <textarea name="biography" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Celebrity</button>
        </form>
    `;

    modal.classList.add('active');

    // Use setTimeout to ensure DOM is ready and get fresh reference
    setTimeout(() => {
        const form = document.getElementById('addCelebrityForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    const res = await fetch(`${API_BASE}/celebrities.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await res.json();
                    if (result.success) {
                        alert('Celebrity added successfully!');
                        closeModal();
                        loadCelebrities();
                        loadDashboard();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error adding celebrity');
                }
            });
        }
    }, 0);
}

// SQL Query Executor
async function executeQuery() {
    const queryInput = document.getElementById('sqlQuery');
    const query = queryInput.value.trim();

    if (!query) {
        alert('Please enter a SQL query');
        return;
    }

    try {
        const res = await fetch(`${API_BASE}/execute-query.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query })
        });

        const data = await res.json();
        displayQueryResult(data);
    } catch (error) {
        displayQueryResult({ success: false, error: error.message });
    }
}

function displayQueryResult(result) {
    const container = document.getElementById('queryResult');
    
    if (!result.success) {
        container.innerHTML = `
            <div style="color: #ef4444; padding: 20px; background: #fee2e2; border-radius: 8px;">
                <strong>Error:</strong> ${result.error}
            </div>
        `;
        return;
    }

    if (result.type === 'select') {
        if (result.data.length === 0) {
            container.innerHTML = '<p>Query executed successfully. No results returned.</p>';
            return;
        }

        const table = createTable(result.data);
        container.innerHTML = `
            <div style="margin-bottom: 15px;">
                <span style="color: #10b981; font-weight: 600;">✓ Query executed successfully</span>
                <span style="margin-left: 20px;">Rows: ${result.rows}</span>
                <span style="margin-left: 20px;">Execution time: ${result.execution_time}</span>
            </div>
            ${table}
        `;
    } else {
        container.innerHTML = `
            <div style="color: #10b981; padding: 20px; background: #d1fae5; border-radius: 8px;">
                <strong>✓ Success:</strong> ${result.affected_rows} row(s) affected
                ${result.insert_id ? `<br>Insert ID: ${result.insert_id}` : ''}
                <br>Execution time: ${result.execution_time}
            </div>
        `;
    }
}

function createTable(data) {
    if (!data || data.length === 0) return '';

    const headers = Object.keys(data[0]);
    let html = '<table class="result-table"><thead><tr>';
    
    headers.forEach(header => {
        html += `<th>${header}</th>`;
    });
    
    html += '</tr></thead><tbody>';
    
    data.forEach(row => {
        html += '<tr>';
        headers.forEach(header => {
            html += `<td>${row[header] !== null ? row[header] : 'NULL'}</td>`;
        });
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    return html;
}

function clearQuery() {
    document.getElementById('sqlQuery').value = '';
    document.getElementById('queryResult').innerHTML = '';
}

// Advanced Queries
async function runAdvancedQuery(operation) {
    try {
        const container = document.getElementById('advancedResult');
        container.innerHTML = '<div class="loading">Executing query...</div>';
        
        const res = await fetch(`${API_BASE}/advanced-queries.php?operation=${operation}`);
        const data = await res.json();
        
        if (data.success) {
            let content = '';
            
            // Handle different data structures
            if (Array.isArray(data.data)) {
                const table = createTable(data.data);
                content = `
                    <div class="query-info">
                        <h3>✅ ${data.operation}</h3>
                        <div class="info-badges">
                            <span class="badge badge-success">Success</span>
                            <span class="badge badge-info">${data.data.length} rows returned</span>
                        </div>
                    </div>
                    ${table}
                `;
            } else if (typeof data.data === 'object') {
                // Handle complex objects (like aggregates, comparisons)
                content = `
                    <div class="query-info">
                        <h3>✅ ${data.operation}</h3>
                        <div class="info-badges">
                            <span class="badge badge-success">Success</span>
                        </div>
                    </div>
                `;
                
                // Display nested objects nicely
                for (const [key, value] of Object.entries(data.data)) {
                    if (Array.isArray(value)) {
                        content += `<h4>${key.replace(/_/g, ' ').toUpperCase()}</h4>`;
                        content += createTable(value);
                    } else if (typeof value === 'object') {
                        content += `<h4>${key.replace(/_/g, ' ').toUpperCase()}</h4>`;
                        content += `<div class="stats-grid">${createStatsCards(value)}</div>`;
                    } else {
                        content += `<div class="stat-item"><strong>${key}:</strong> ${value}</div>`;
                    }
                }
            }
            
            container.innerHTML = content;
        } else {
            container.innerHTML = `
                <div class="query-info error">
                    <h3>❌ Error</h3>
                    <p>${data.error}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error running advanced query:', error);
        document.getElementById('advancedResult').innerHTML = `
            <div class="query-info error">
                <h3>❌ Error</h3>
                <p>${error.message}</p>
            </div>
        `;
    }
}

// Helper function to create stats cards from object
function createStatsCards(obj) {
    let html = '';
    for (const [key, value] of Object.entries(obj)) {
        html += `
            <div class="stat-card-mini">
                <div class="stat-label">${key.replace(/_/g, ' ')}</div>
                <div class="stat-value">${value !== null ? value : 'N/A'}</div>
            </div>
        `;
    }
    return html;
}


// SQL Terminal
function toggleTerminal() {
    const terminal = document.getElementById('sqlTerminal');
    terminalActive = !terminalActive;
    
    if (terminalActive) {
        terminal.classList.add('active');
    } else {
        terminal.classList.remove('active');
    }
}

function clearTerminal() {
    const content = document.getElementById('terminalContent');
    content.innerHTML = '';
    stats = { totalQueries: 0, successQueries: 0, errorQueries: 0 };
    updateTerminalStats();
}

function updateTerminalStats() {
    const statsEl = document.getElementById('terminalStats');
    statsEl.textContent = `Queries: ${stats.totalQueries} | Success: ${stats.successQueries} | Errors: ${stats.errorQueries}`;
}

async function startQueryPolling() {
    let lastQueryId = null;

    async function pollQueries() {
        try {
            const res = await fetch(`${API_BASE}/sql-logs.php?limit=50`);
            const data = await res.json();

            if (data.success && data.data.length > 0) {
                const newQueries = [];
                
                for (const query of data.data) {
                    if (lastQueryId === null || query.id !== lastQueryId) {
                        newQueries.push(query);
                    } else {
                        break;
                    }
                }

                if (newQueries.length > 0) {
                    lastQueryId = data.data[0].id;
                    newQueries.reverse().forEach(addQueryToTerminal);
                }

                // Update stats
                stats.totalQueries = data.data.length;
                stats.successQueries = data.data.filter(q => q.status === 'success').length;
                stats.errorQueries = data.data.filter(q => q.status === 'error').length;
                updateTerminalStats();
            }
        } catch (error) {
            console.error('Error polling queries:', error);
        }
    }

    // Poll every 2 seconds
    queryPollingInterval = setInterval(pollQueries, 2000);
    pollQueries(); // Initial call
}

function addQueryToTerminal(query) {
    const content = document.getElementById('terminalContent');
    const queryEl = document.createElement('div');
    queryEl.className = `terminal-query ${query.status === 'error' ? 'error' : ''}`;
    
    queryEl.innerHTML = `
        <div class="query-time">${query.datetime}</div>
        <div>
            <span class="query-type ${query.type}">${query.type}</span>
            <span style="color: ${query.status === 'success' ? '#10b981' : '#ef4444'};">
                ${query.status === 'success' ? '✓' : '✗'} ${query.status.toUpperCase()}
            </span>
        </div>
        <div class="query-sql">${query.query}</div>
        ${query.error ? `<div class="query-error">Error: ${query.error}</div>` : ''}
    `;
    
    content.insertBefore(queryEl, content.firstChild);

    // Keep only last 50 queries
    while (content.children.length > 50) {
        content.removeChild(content.lastChild);
    }
}

// Modal Management
function closeModal() {
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modalBody');
    modal.style.display = 'none';
    modal.classList.remove('active');
    // Clear modal content to prevent event listener conflicts
    if (modalBody) {
        modalBody.innerHTML = '';
    }
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// View movie details
async function viewMovie(id) {
    try {
        const res = await fetch(`${API_BASE}/movies.php?id=${id}`);
        const data = await res.json();

        if (data.success) {
            const movie = data.data;
            const modal = document.getElementById('modal');
            const modalBody = document.getElementById('modalBody');

            let castHTML = '';
            if (movie.cast && movie.cast.length > 0) {
                castHTML = '<h3 style="margin-top: 20px; color: #2563eb;">Cast & Crew</h3><div class="cast-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;">';
                movie.cast.forEach(c => {
                    castHTML += `
                        <div style="padding: 10px; background: #f3f4f6; border-radius: 8px; border-left: 3px solid #2563eb;">
                            <strong style="color: #1f2937;">${c.name}</strong><br>
                            <span style="color: #6b7280; font-size: 0.9em;">as ${c.role || 'N/A'}</span><br>
                            <span style="color: #9ca3af; font-size: 0.85em;">(${c.cast_type})</span>
                        </div>
                    `;
                });
                castHTML += '</div>';
            }

            modalBody.innerHTML = `
                <h2 style="color: #1f2937; margin-bottom: 20px;">${movie.title}</h2>
                <div style="display: grid; gap: 10px;">
                    <p><strong>Director:</strong> ${movie.director_name || 'N/A'}</p>
                    <p><strong>Genre:</strong> ${movie.genre || 'N/A'}</p>
                    <p><strong>Release Date:</strong> ${movie.release_date || 'N/A'}</p>
                    <p><strong>Duration:</strong> ${movie.duration || 'N/A'} minutes</p>
                    <p><strong>Language:</strong> ${movie.language || 'N/A'}</p>
                    <p><strong>Country:</strong> ${movie.country || 'N/A'}</p>
                    <p><strong>Rating:</strong> ⭐ ${movie.rating || 'N/A'}/10</p>
                    <p><strong>Total Ratings:</strong> ${movie.total_ratings || 0}</p>
                    ${movie.plot_summary ? `<p><strong>Plot:</strong> ${movie.plot_summary}</p>` : ''}
                </div>
                ${castHTML}
                <div style="margin-top: 20px;">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            `;

            modal.classList.add('active');
        }
    } catch (error) {
        alert('Error loading movie details');
    }
}

// ========== ANALYTICS & DISCOVERY FUNCTIONS ==========

// Helper function to run analytics actions
async function runAnalytics(action, params = {}) {
    try {
        const container = document.getElementById('analyticsResult');
        container.innerHTML = '<div class="loading">⏳ Processing...</div>';
        
        // Build query string
        const queryParams = new URLSearchParams({ action, ...params });
        const res = await fetch(`${API_BASE}/analytics.php?${queryParams}`);
        const data = await res.json();
        
        if (data.success) {
            let content = `
                <div class="query-info">
                    <h3>✅ ${data.action}</h3>
                    <div class="info-badges">
                        <span class="badge badge-success">Success</span>
                        ${Array.isArray(data.data) ? `<span class="badge badge-info">${data.data.length} results</span>` : ''}
                    </div>
                </div>
            `;
            
            // Handle different data structures
            if (Array.isArray(data.data)) {
                content += createTable(data.data);
            } else if (typeof data.data === 'object') {
                // Handle complex objects
                for (const [key, value] of Object.entries(data.data)) {
                    if (Array.isArray(value)) {
                        content += `<h4 style="margin-top: 20px;">${key.replace(/_/g, ' ').toUpperCase()}</h4>`;
                        content += createTable(value);
                    } else if (typeof value === 'object' && value !== null) {
                        content += `<h4 style="margin-top: 20px;">${key.replace(/_/g, ' ').toUpperCase()}</h4>`;
                        content += `<div class="stats-grid">${createStatsCards(value)}</div>`;
                    }
                }
            }
            
            container.innerHTML = content;
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            container.innerHTML = `
                <div class="query-info error">
                    <h3>❌ Error</h3>
                    <p>${data.error || 'An error occurred'}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Analytics error:', error);
        document.getElementById('analyticsResult').innerHTML = `
            <div class="query-info error">
                <h3>❌ Error</h3>
                <p>${error.message}</p>
            </div>
        `;
    }
}

// Search & Discovery
function searchContent() {
    const keyword = document.getElementById('searchKeyword').value;
    if (!keyword) {
        alert('Please enter a search keyword');
        return;
    }
    runAnalytics('search_content', { keyword });
}

function findSimilar() {
    const movieId = document.getElementById('similarMovieId').value;
    if (!movieId) {
        alert('Please enter a movie ID');
        return;
    }
    runAnalytics('find_similar', { movie_id: movieId });
}

function discoverTopRated() {
    const minRating = document.getElementById('minRatingDiscover').value;
    runAnalytics('discover_top_rated', { min_rating: minRating });
}

function browseByYear() {
    const startYear = document.getElementById('startYear').value;
    const endYear = document.getElementById('endYear').value;
    runAnalytics('browse_by_year', { start_year: startYear, end_year: endYear });
}

// Filtering
function filterByGenre() {
    const genre = document.getElementById('filterGenre').value;
    if (!genre) {
        alert('Please select a genre');
        return;
    }
    const sortBy = document.getElementById('sortBy').value;
    runAnalytics('filter_by_genre', { genre, sort: sortBy, order: 'DESC' });
}

function filterByRatingRange() {
    const min = document.getElementById('minRating').value;
    const max = document.getElementById('maxRating').value;
    runAnalytics('filter_by_rating_range', { min, max });
}

function filterMultipleGenres() {
    const genres = document.getElementById('multipleGenres').value;
    if (!genres) {
        alert('Please enter genres (comma-separated)');
        return;
    }
    runAnalytics('filter_multiple_genres', { genres });
}

// Analytics
function getGenreStatistics() {
    runAnalytics('genre_statistics');
}

function getDirectorPerformance() {
    runAnalytics('director_performance');
}

function getContentDistribution() {
    runAnalytics('content_distribution');
}

function getRatingDistribution() {
    runAnalytics('rating_distribution');
}

// Comparisons
function compareGenres() {
    const genre1 = document.getElementById('genre1').value;
    const genre2 = document.getElementById('genre2').value;
    runAnalytics('compare_genres', { genre1, genre2 });
}

function compareMoviesVsSeries() {
    runAnalytics('movies_vs_series');
}

// Recommendations
function getRecommendations() {
    const userId = document.getElementById('userIdRec').value || 1;
    runAnalytics('get_recommendations', { user_id: userId });
}

function getTrendingNow() {
    runAnalytics('trending_now');
}

function getHiddenGems() {
    runAnalytics('hidden_gems');
}

// Cast & Crew
function getMovieWithCast() {
    const movieId = document.getElementById('movieIdCast').value;
    if (!movieId) {
        alert('Please enter a movie ID');
        return;
    }
    runAnalytics('movie_with_cast', { movie_id: movieId });
}

function getCelebrityFilmography() {
    const celebId = document.getElementById('celebrityIdFilm').value;
    if (!celebId) {
        alert('Please enter a celebrity ID');
        return;
    }
    runAnalytics('celebrity_filmography', { celebrity_id: celebId });
}

// ========================================
// USERS MANAGEMENT
// ========================================

async function loadUsers() {
    try {
        const search = document.getElementById('userSearch')?.value || '';
        const country = document.getElementById('countryFilter')?.value || '';
        
        let url = `${API_BASE}/users.php?`;
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (country) url += `country=${encodeURIComponent(country)}&`;
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            displayUsers(data.data);
        } else {
            console.error('Error loading users:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayUsers(users) {
    const container = document.getElementById('usersList');
    if (!users || users.length === 0) {
        container.innerHTML = '<p class="no-data">No users found</p>';
        return;
    }
    
    container.innerHTML = users.map(user => `
        <div class="data-card">
            <div class="card-header">
                <h3>${user.username}</h3>
            </div>
            <div class="card-body">
                <p><strong>Full Name:</strong> ${user.full_name || 'N/A'}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Country:</strong> ${user.country || 'N/A'}</p>
                <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                <div class="user-stats" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <p style="margin: 5px 0;"><strong>Reviews:</strong> ${user.total_reviews || 0}</p>
                    <p style="margin: 5px 0;"><strong>Watchlist:</strong> ${user.watchlist_count || 0}</p>
                    <p style="margin: 5px 0;"><strong>Favorites:</strong> ${user.favorites_count || 0}</p>
                    <p style="margin: 5px 0;"><strong>Followers:</strong> ${user.followers || 0} | <strong>Following:</strong> ${user.following || 0}</p>
                </div>
            </div>
            <div class="card-actions">
                <button class="btn btn-sm btn-info" onclick="viewUserDetails(${user.user_id})">View Details</button>
                <button class="btn btn-sm btn-warning" onclick="editUser(${user.user_id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.user_id})">Delete</button>
            </div>
        </div>
    `).join('');
}

function showAddUserForm() {
    const form = `
        <h2>Add New User</h2>
        <form id="addUserForm" class="modal-form">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required class="form-input">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required class="form-input">
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required class="form-input">
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-input">
            </div>
            <div class="form-group">
                <label>Country</label>
                <select name="country" class="form-input">
                    <option value="">Select Country</option>
                    <option value="USA">USA</option>
                    <option value="UK">UK</option>
                    <option value="Canada">Canada</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="India">India</option>
                    <option value="Australia">Australia</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-input">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add User</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    `;
    
    document.getElementById('modalBody').innerHTML = form;
    document.getElementById('modal').style.display = 'flex';
    
    // Use setTimeout to ensure DOM is ready and get fresh reference
    setTimeout(() => {
        const addUserForm = document.getElementById('addUserForm');
        if (addUserForm) {
            addUserForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const userData = Object.fromEntries(formData);
                
                try {
                    const response = await fetch(`${API_BASE}/users.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(userData)
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        alert('User added successfully!');
                        closeModal();
                        loadUsers();
                    } else {
                        alert('Error: ' + data.error);
                    }
                } catch (error) {
                    alert('Error adding user: ' + error.message);
                }
            });
        }
    }, 0);
}

async function viewUserDetails(userId) {
    try {
        const response = await fetch(`${API_BASE}/users.php?id=${userId}`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.data;
            const details = `
                <h2>User Details</h2>
                <div class="user-details">
                    <p><strong>Username:</strong> ${user.username}</p>
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>Full Name:</strong> ${user.full_name || 'N/A'}</p>
                    <p><strong>Country:</strong> ${user.country || 'N/A'}</p>
                    <p><strong>Date of Birth:</strong> ${user.date_of_birth || 'N/A'}</p>
                    <p><strong>Bio:</strong> ${user.bio || 'N/A'}</p>
                    <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleString()}</p>
                    <p><strong>Last Login:</strong> ${user.last_login ? new Date(user.last_login).toLocaleString() : 'Never'}</p>
                    <p><strong>Status:</strong> ${user.is_active ? 'Active' : 'Inactive'}</p>
                    
                    <h3>Statistics</h3>
                    <p><strong>Total Reviews:</strong> ${user.stats.total_reviews}</p>
                    <p><strong>Watchlist:</strong> ${user.stats.watchlist_count}</p>
                    <p><strong>Favorites:</strong> ${user.stats.favorites_count}</p>
                    <p><strong>Followers:</strong> ${user.stats.followers}</p>
                    <p><strong>Following:</strong> ${user.stats.following}</p>
                </div>
                <div class="form-actions">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = details;
            document.getElementById('modal').style.display = 'flex';
        }
    } catch (error) {
        alert('Error loading user details: ' + error.message);
    }
}

async function editUser(userId) {
    try {
        const response = await fetch(`${API_BASE}/users.php?id=${userId}`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.data;
            const form = `
                <h2>Edit User</h2>
                <form id="editUserForm" class="modal-form">
                    <input type="hidden" name="user_id" value="${user.user_id}">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="${user.username}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="${user.email}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="${user.full_name || ''}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" value="${user.country || ''}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" value="${user.date_of_birth || ''}" class="form-input">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            `;
            
            document.getElementById('modalBody').innerHTML = form;
            document.getElementById('modal').style.display = 'block';
            
            // Use setTimeout to ensure DOM is ready and get fresh reference
            setTimeout(() => {
                const editUserForm = document.getElementById('editUserForm');
                if (editUserForm) {
                    editUserForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        const userData = Object.fromEntries(formData);
                        
                        try {
                            const response = await fetch(`${API_BASE}/users.php`, {
                                method: 'PUT',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(userData)
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                alert('User updated successfully!');
                                closeModal();
                                loadUsers();
                            } else {
                                alert('Error: ' + data.error);
                            }
                        } catch (error) {
                            alert('Error updating user: ' + error.message);
                        }
                    });
                }
            }, 0);
        }
    } catch (error) {
        alert('Error loading user: ' + error.message);
    }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This will also delete all their reviews, watchlist, and favorites.')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/users.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        if (data.success) {
            alert('User deleted successfully!');
            loadUsers();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting user: ' + error.message);
    }
}

// ========================================
// REVIEWS MANAGEMENT
// ========================================

async function loadReviews() {
    try {
        const contentType = document.getElementById('reviewContentType')?.value || '';
        const minRating = document.getElementById('reviewRatingFilter')?.value || '';
        
        let url = `${API_BASE}/reviews.php?`;
        if (contentType) url += `content_type=${contentType}&`;
        if (minRating) url += `min_rating=${minRating}&`;
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            displayReviews(data.data);
        } else {
            console.error('Error loading reviews:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayReviews(reviews) {
    const container = document.getElementById('reviewsList');
    if (!reviews || reviews.length === 0) {
        container.innerHTML = '<p class="no-data">No reviews found</p>';
        return;
    }
    
    container.innerHTML = reviews.map(review => `
        <div class="data-card">
            <div class="card-header">
                <h3>${review.review_title || 'Review'}</h3>
                <span class="badge badge-primary">★ ${review.rating}/10</span>
            </div>
            <div class="card-body">
                <p><strong>By:</strong> ${review.username}${review.full_name ? ` (${review.full_name})` : ''}</p>
                <p><strong>Content:</strong> ${review.content_title || 'Unknown'} <span class="badge ${review.content_type === 'movie' ? 'badge-success' : 'badge-warning'}">${review.content_type === 'movie' ? 'Movie' : 'TV Series'}</span></p>
                <p class="review-text"><strong>Review:</strong> ${review.review_text || 'No text provided'}</p>
                ${review.is_spoiler == 1 ? '<p><span class="badge badge-danger">⚠ Contains Spoilers</span></p>' : ''}
                <p class="review-date"><strong>Posted:</strong> ${new Date(review.created_at).toLocaleString()}</p>
            </div>
            <div class="card-actions">\
                <button class="btn btn-sm btn-warning" onclick="editReview(${review.review_id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteReview(${review.review_id})">Delete</button>
            </div>
        </div>
    `).join('');
}

function showAddReviewForm() {
    const form = `
        <h2>Add New Review</h2>
        <form id="addReviewForm" class="modal-form">
            <div class="form-group">
                <label>User ID *</label>
                <input type="number" name="user_id" required class="form-input" value="1">
            </div>
            <div class="form-group">
                <label>Content Type *</label>
                <select name="content_type" required class="form-input">
                    <option value="movie">Movie</option>
                    <option value="tv_series">TV Series</option>
                </select>
            </div>
            <div class="form-group">
                <label>Content ID *</label>
                <input type="number" name="content_id" required class="form-input">
            </div>
            <div class="form-group">
                <label>Rating (0-10) *</label>
                <input type="number" name="rating" min="0" max="10" step="0.1" required class="form-input">
            </div>
            <div class="form-group">
                <label>Review Title</label>
                <input type="text" name="review_title" class="form-input">
            </div>
            <div class="form-group">
                <label>Review Text</label>
                <textarea name="review_text" rows="4" class="form-input"></textarea>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_spoiler" value="1">
                    Contains Spoilers
                </label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Review</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    `;
    
    document.getElementById('modalBody').innerHTML = form;
    document.getElementById('modal').style.display = 'block';
    
    // Use setTimeout to ensure DOM is ready and get fresh reference
    setTimeout(() => {
        const addReviewForm = document.getElementById('addReviewForm');
        if (addReviewForm) {
            addReviewForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const reviewData = Object.fromEntries(formData);
                reviewData.is_spoiler = formData.get('is_spoiler') ? 1 : 0;
                
                try {
                    const response = await fetch(`${API_BASE}/reviews.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(reviewData)
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        alert('Review added successfully!');
                        closeModal();
                        loadReviews();
                    } else {
                        alert('Error: ' + data.error);
                    }
                } catch (error) {
                    alert('Error adding review: ' + error.message);
                }
            });
        }
    }, 0);
}

async function editReview(reviewId) {
    try {
        const response = await fetch(`${API_BASE}/reviews.php?action=getAll`);
        const data = await response.json();
        
        if (data.success) {
            const review = data.data.find(r => r.review_id === reviewId);
            if (!review) {
                alert('Review not found');
                return;
            }
            
            const form = `
                <h2>Edit Review</h2>
                <form id="editReviewForm" class="modal-form">
                    <input type="hidden" name="review_id" value="${review.review_id}">
                    <input type="hidden" name="user_id" value="${review.user_id}">
                    <input type="hidden" name="content_type" value="${review.content_type}">
                    <input type="hidden" name="content_id" value="${review.content_id}">
                    <div class="form-group">
                        <label>Rating (0-10) *</label>
                        <input type="number" name="rating" min="0" max="10" step="0.1" value="${review.rating}" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Review Title</label>
                        <input type="text" name="review_title" value="${review.review_title || ''}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Review Text</label>
                        <textarea name="review_text" rows="4" class="form-input">${review.review_text || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_spoiler" value="1" ${review.is_spoiler ? 'checked' : ''}>
                            Contains Spoilers
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Review</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            `;
            
            document.getElementById('modalBody').innerHTML = form;
            document.getElementById('modal').style.display = 'block';
            
            // Use setTimeout to ensure DOM is ready and get fresh reference
            setTimeout(() => {
                const editReviewForm = document.getElementById('editReviewForm');
                if (editReviewForm) {
                    editReviewForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        const reviewData = Object.fromEntries(formData);
                        reviewData.is_spoiler = formData.get('is_spoiler') ? 1 : 0;
                        
                        try {
                            const response = await fetch(`${API_BASE}/reviews.php`, {
                                method: 'PUT',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(reviewData)
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                alert('Review updated successfully!');
                                closeModal();
                                loadReviews();
                            } else {
                                alert('Error: ' + data.error);
                            }
                        } catch (error) {
                            alert('Error updating review: ' + error.message);
                        }
                    });
                }
            }, 0);
        }
    } catch (error) {
        alert('Error loading review: ' + error.message);
    }
}

async function deleteReview(reviewId) {
    if (!confirm('Are you sure you want to delete this review?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/reviews.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ review_id: reviewId })
        });
        
        const data = await response.json();
        if (data.success) {
            alert('Review deleted successfully!');
            loadReviews();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error deleting review: ' + error.message);
    }
}

// Timeline & Trends
function getReleaseTimeline() {
    runAnalytics('release_timeline');
}

function getDecadeAnalysis() {
    runAnalytics('decade_analysis');
}

// Views Functions
async function loadViews(viewName = 'list') {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '<div class="loading">Loading views...</div>';
    
    try {
        let url = `${API_BASE}/views.php?action=${viewName}`;
        
        // Add parameters based on view
        if (viewName === 'user_statistics') {
            const userId = document.getElementById('view-user-id')?.value;
            if (userId) url += `&user_id=${userId}`;
        } else if (viewName === 'celebrity_filmography') {
            const celebId = document.getElementById('view-celeb-id')?.value;
            if (celebId) url += `&celebrity_id=${celebId}`;
        } else {
            const limit = document.getElementById('view-limit')?.value || 20;
            url += `&limit=${limit}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            displayViewResults(data, viewName);
        } else {
            resultsDiv.innerHTML = `<div class="error">Error: ${data.error}</div>`;
        }
    } catch (error) {
        resultsDiv.innerHTML = `<div class="error">Error loading view: ${error.message}</div>`;
    }
}

function displayViewResults(data, viewName) {
    const resultsDiv = document.getElementById('results');
    let html = `<div class="result-card">`;
    
    if (viewName === 'list') {
        html += `<h3>Available Database Views</h3>`;
        html += `<div class="info-text">${data.message}</div>`;
        html += `<div class="view-list">`;
        for (const [key, description] of Object.entries(data.views)) {
            html += `
                <div class="view-item" onclick="loadViews('${key}')">
                    <strong>${key.replace(/_/g, ' ').toUpperCase()}</strong>
                    <p>${description}</p>
                </div>`;
        }
        html += `</div>`;
    } else {
        html += `<h3>${data.view || 'View Results'}</h3>`;
        html += `<p class="info-text">${data.description}</p>`;
        html += `<p><strong>Total Records:</strong> ${data.count || 1}</p>`;
        
        if (Array.isArray(data.data)) {
            html += `<div class="table-container"><table><thead><tr>`;
            const headers = Object.keys(data.data[0] || {});
            headers.forEach(header => {
                html += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
            });
            html += `</tr></thead><tbody>`;
            
            data.data.forEach(row => {
                html += `<tr>`;
                headers.forEach(header => {
                    let value = row[header];
                    if (value === null) value = 'N/A';
                    if (header.includes('date') && value && value !== 'N/A') {
                        value = new Date(value).toLocaleDateString();
                    }
                    html += `<td>${value}</td>`;
                });
                html += `</tr>`;
            });
            html += `</tbody></table></div>`;
        } else if (data.data) {
            html += `<div class="data-display">`;
            for (const [key, value] of Object.entries(data.data)) {
                html += `<div class="data-row">
                    <strong>${key.replace(/_/g, ' ').toUpperCase()}:</strong> ${value || 'N/A'}
                </div>`;
            }
            html += `</div>`;
        }
    }
    
    html += `</div>`;
    resultsDiv.innerHTML = html;
}

// Set Operations (INTERSECT/MINUS) Functions
async function loadSetOperations(operation = 'list') {
    const resultsDiv = document.getElementById('analyticsResult');
    resultsDiv.innerHTML = '<div class="loading">Loading set operations...</div>';
    
    try {
        let url = `${API_BASE}/set-operations.php?operation=${operation}`;
        
        // Add parameters based on operation
        if (operation.includes('user_common')) {
            const user1 = document.getElementById('set-user1-id')?.value || 1;
            const user2 = document.getElementById('set-user2-id')?.value || 2;
            url += `&user1_id=${user1}&user2_id=${user2}`;
        } else if (operation.includes('high_rated')) {
            const genre = document.getElementById('set-genre')?.value || 'Action';
            const rating = document.getElementById('set-rating')?.value || 8.0;
            url += `&genre=${genre}&min_rating=${rating}`;
        } else if (operation.includes('genre_year')) {
            const genre = document.getElementById('set-genre-year')?.value || 'Drama';
            const startYear = document.getElementById('set-start-year')?.value || 2010;
            const endYear = document.getElementById('set-end-year')?.value || 2020;
            url += `&genre=${genre}&start_year=${startYear}&end_year=${endYear}`;
        } else if (operation.includes('not_watched')) {
            const userId = document.getElementById('set-user-id')?.value || 1;
            url += `&user_id=${userId}`;
        } else if (operation.includes('watchlist_not_favorites')) {
            const userId = document.getElementById('set-user-id-wl')?.value || 1;
            url += `&user_id=${userId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            displaySetOperationResults(data, operation);
        } else {
            resultsDiv.innerHTML = `<div class="error">Error: ${data.error}</div>`;
        }
    } catch (error) {
        resultsDiv.innerHTML = `<div class="error">Error loading set operation: ${error.message}</div>`;
    }
}

function displaySetOperationResults(data, operation) {
    const resultsDiv = document.getElementById('analyticsResult');
    let html = `<div class="result-card">`;
    
    if (operation === 'list') {
        html += `<h3>Available Set Operations (INTERSECT & MINUS)</h3>`;
        html += `<div class="info-text">${data.message}</div>`;
        html += `<div class="set-ops-list">`;
        
        html += `<h4>INTERSECT Operations (A AND B)</h4>`;
        html += `<div class="view-item" onclick="loadSetOperations('intersect_high_rated')">
            <strong>INTERSECT: High Rated by Genre</strong>
            <p>Movies in specific genre AND high rating</p>
        </div>`;
        html += `<div class="view-item" onclick="loadSetOperations('intersect_user_common')">
            <strong>INTERSECT: Common Watchlist Items</strong>
            <p>Content in both users' watchlists</p>
        </div>`;
        html += `<div class="view-item" onclick="loadSetOperations('intersect_genre_year')">
            <strong>INTERSECT: Genre and Year Range</strong>
            <p>Movies in genre AND specific year range</p>
        </div>`;
        
        html += `<h4>MINUS Operations (A NOT IN B)</h4>`;
        html += `<div class="view-item" onclick="loadSetOperations('minus_all_not_watched')">
            <strong>MINUS: Unwatched Movies</strong>
            <p>All movies MINUS user's watchlist</p>
        </div>`;
        html += `<div class="view-item" onclick="loadSetOperations('minus_watchlist_not_favorites')">
            <strong>MINUS: Watchlist Not Favorites</strong>
            <p>User's watchlist MINUS favorites</p>
        </div>`;
        html += `<div class="view-item" onclick="loadSetOperations('minus_movies_no_reviews')">
            <strong>MINUS: Movies Without Reviews</strong>
            <p>All movies MINUS movies with reviews</p>
        </div>`;
        
        html += `</div>`;
    } else {
        const opType = data.operation === 'INTERSECT' ? '∩' : '−';
        html += `<h3>${opType} ${data.operation}: ${data.description}</h3>`;
        html += `<p><strong>Total Results:</strong> ${data.count}</p>`;
        
        if (data.data && data.data.length > 0) {
            html += `<div class="table-container"><table><thead><tr>`;
            const headers = Object.keys(data.data[0]);
            headers.forEach(header => {
                html += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
            });
            html += `</tr></thead><tbody>`;
            
            data.data.forEach(row => {
                html += `<tr>`;
                headers.forEach(header => {
                    let value = row[header];
                    if (value === null) value = 'N/A';
                    if (header.includes('date') && value && value !== 'N/A') {
                        value = new Date(value).toLocaleDateString();
                    }
                    html += `<td>${value}</td>`;
                });
                html += `</tr>`;
            });
            html += `</tbody></table></div>`;
        } else {
            html += `<p class="info-text">No results found for this operation.</p>`;
        }
    }
    
    html += `</div>`;
    resultsDiv.innerHTML = html;
}
