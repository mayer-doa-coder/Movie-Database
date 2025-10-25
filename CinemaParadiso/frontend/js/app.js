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

    document.getElementById('addMovieForm').addEventListener('submit', async (e) => {
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

    document.getElementById('addSeriesForm').addEventListener('submit', async (e) => {
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

    document.getElementById('addCelebrityForm').addEventListener('submit', async (e) => {
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
        const res = await fetch(`${API_BASE}/advanced-queries.php?operation=${operation}`);
        const data = await res.json();
        
        const container = document.getElementById('advancedResult');
        
        if (data.success) {
            const table = createTable(data.data);
            container.innerHTML = `
                <div style="margin-bottom: 15px;">
                    <h3>Operation: ${data.operation}</h3>
                    <p>Results: ${Array.isArray(data.data) ? data.data.length : 1} row(s)</p>
                </div>
                ${table || `<pre>${JSON.stringify(data.data, null, 2)}</pre>`}
            `;
        } else {
            container.innerHTML = `
                <div style="color: #ef4444;">Error: ${data.error}</div>
            `;
        }
    } catch (error) {
        console.error('Error running advanced query:', error);
    }
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
    document.getElementById('modal').classList.remove('active');
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
                castHTML = '<h3>Cast</h3><ul>';
                movie.cast.forEach(c => {
                    castHTML += `<li>${c.name} as ${c.role || 'N/A'} (${c.cast_type})</li>`;
                });
                castHTML += '</ul>';
            }

            modalBody.innerHTML = `
                <h2>${movie.title}</h2>
                <p><strong>Director:</strong> ${movie.director_name || 'N/A'}</p>
                <p><strong>Genre:</strong> ${movie.genre || 'N/A'}</p>
                <p><strong>Release Date:</strong> ${movie.release_date || 'N/A'}</p>
                <p><strong>Duration:</strong> ${movie.duration || 'N/A'} minutes</p>
                <p><strong>Language:</strong> ${movie.language || 'N/A'}</p>
                <p><strong>Country:</strong> ${movie.country || 'N/A'}</p>
                <p><strong>Rating:</strong> ⭐ ${movie.rating || 'N/A'}/10</p>
                <p><strong>Total Ratings:</strong> ${movie.total_ratings || 0}</p>
                ${movie.plot_summary ? `<p><strong>Plot:</strong> ${movie.plot_summary}</p>` : ''}
                ${castHTML}
            `;

            modal.classList.add('active');
        }
    } catch (error) {
        alert('Error loading movie details');
    }
}
