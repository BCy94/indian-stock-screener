// Integrated Stock Screener with Real-Time Data
// Combines stock universe, Yahoo Finance API, and screener functionality

let allStocks = [];
let filteredStocks = [];
let currentView = 'table';
let currentSort = 'name';
let yahooAPI = null;
let loadingManager = null;

// Tracks where the data on screen came from so the UI can be honest about it:
// 'live' = just fetched, 'stale' = live fetch failed but we still have an earlier live load,
// 'cached' = loaded from localStorage from a previous session, 'none' = nothing available at all.
// Exposed on window (not `let`) so it's a real global other scripts/devtools/tests can read.
window.dataSource = 'none';
let lastUpdateTimestamp = null;

// Deep-dive modal state: the modal element is created once and reused; the "current symbol"
// guards against a slow/late fetchHistory response overwriting the modal after the user has
// already closed it or opened a different stock.
let stockDetailModalEl = null;
let stockDetailCurrentSymbol = null;

const CACHE_KEY_PREFIX = 'screenerCache_v1_';

// Initialize on page load
document.addEventListener('DOMContentLoaded', async () => {
    // Initialize API and loading manager
    yahooAPI = new YahooFinanceAPI();
    loadingManager = new LoadingManager();

    // Add sector change listener for cascading sub-sectors
    document.getElementById('sector-filter').addEventListener('change', updateSubSectorFilter);

    // Keep working offline: fall back to cached data when the network drops,
    // and try a live refresh automatically the moment the browser reports it's back.
    window.addEventListener('online', () => refreshPrices());
    window.addEventListener('offline', () => {
        if (dataSource === 'live') dataSource = 'stale';
        updateConnectionStatus();
    });

    // Load stock data
    await loadStockData();

    // Start auto-refresh (every 5 minutes)
    setInterval(() => refreshPrices(), 5 * 60 * 1000);
});

// Add calculated fields shared by both live and cached data
function enrichStocks(rawStocks, selectedIndex) {
    return rawStocks.map((stock, index) => enrichOneStock(stock, index + 1, selectedIndex));
}

// Per-stock enrichment, factored out of enrichStocks so the ticker-lookup path
// (a single ad-hoc symbol, not part of any loaded index list) can reuse the exact
// same scoring/momentum/breakout logic instead of duplicating it.
function enrichOneStock(stock, rank, selectedIndex) {
    const enriched = {
        ...stock,
        rank: rank,
        score: calculateScore(stock),
        // Use proper sector from stock universe
        sector: stock.sector || 'Unknown',
        industry: stock.industry || 'Unknown',
        basicIndustry: stock.basicIndustry || 'Unknown',
        index: selectedIndex
    };
    Object.assign(enriched, calculateMomentum(enriched));
    enriched.breakout = isBreakoutCandidate(enriched);
    return enriched;
}

// Offline cache: keeps the last successful live load in localStorage so the
// screener still works (read-only, on whatever was last fetched) without a
// network connection — including when opened directly as a file://.
function saveStockCache(indexKey, stocks) {
    try {
        localStorage.setItem(CACHE_KEY_PREFIX + indexKey, JSON.stringify({
            stocks,
            timestamp: Date.now()
        }));
    } catch (error) {
        console.warn('Could not save offline cache:', error);
    }
}

function loadStockCache(indexKey) {
    try {
        const raw = localStorage.getItem(CACHE_KEY_PREFIX + indexKey);
        return raw ? JSON.parse(raw) : null;
    } catch (error) {
        console.warn('Could not read offline cache:', error);
        return null;
    }
}

// Load stock data with real-time prices, falling back to cached/offline data when unavailable
async function loadStockData() {
    const selectedIndex = document.getElementById('index-filter')?.value || 'NIFTY50';
    let stockList = STOCK_UNIVERSE[selectedIndex] || STOCK_UNIVERSE.NIFTY50;

    try {
        loadingManager.show('Loading stock data from Yahoo Finance...');

        // Fetch real-time data
        const realTimeStocks = await yahooAPI.fetchIndexStocks(stockList, (progress, current, total) => {
            loadingManager.updateProgress(progress, current, total);
        });

        if (!realTimeStocks || realTimeStocks.length === 0) {
            throw new Error('No live data returned');
        }

        allStocks = enrichStocks(realTimeStocks, selectedIndex);
        filteredStocks = [...allStocks];
        dataSource = 'live';
        lastUpdateTimestamp = Date.now();
        saveStockCache(selectedIndex, allStocks);

        console.log(`Loaded ${allStocks.length} stocks for ${selectedIndex} (live)`);
    } catch (error) {
        console.warn('Live data unavailable, falling back to offline cache:', error.message);

        const cached = loadStockCache(selectedIndex);
        if (cached && cached.stocks?.length) {
            allStocks = enrichStocks(cached.stocks, selectedIndex);
            filteredStocks = [...allStocks];
            dataSource = 'cached';
            lastUpdateTimestamp = cached.timestamp;
            console.log(`Loaded ${allStocks.length} stocks for ${selectedIndex} (offline cache)`);
        } else {
            allStocks = [];
            filteredStocks = [];
            dataSource = 'none';
            lastUpdateTimestamp = null;
        }
    } finally {
        loadingManager.hide();
        populateSectorFilters();
        renderStocks();
        updateStats();
        updateConnectionStatus();
    }
}

// Refresh prices only (faster than full reload)
async function refreshPrices() {
    if (allStocks.length === 0) {
        // Nothing loaded yet (e.g. first run offline) — try a full load instead
        await loadStockData();
        return;
    }

    try {
        console.log('Refreshing prices...');
        const symbols = allStocks.map(s => s.symbol);
        const priceData = await yahooAPI.fetchMultipleStocks(symbols);

        if (!priceData || priceData.length === 0) {
            throw new Error('No live data returned');
        }

        // Update prices in allStocks
        priceData.forEach(newData => {
            const stock = allStocks.find(s => s.symbol === newData.symbol);
            if (stock) {
                stock.price = newData.price;
                stock.change = newData.change;
                stock.changePercent = newData.changePercent;
                stock.volume = newData.volume;
                stock.score = calculateScore(stock);
                Object.assign(stock, calculateMomentum(stock));
                stock.breakout = isBreakoutCandidate(stock);
            }
        });

        dataSource = 'live';
        lastUpdateTimestamp = Date.now();
        const selectedIndex = document.getElementById('index-filter')?.value || 'NIFTY50';
        saveStockCache(selectedIndex, allStocks);

        // Re-apply filters and render
        applyFilters();
        updateConnectionStatus();

        console.log('Prices refreshed successfully');
    } catch (error) {
        console.warn('Refresh failed, staying on last known data:', error.message);
        if (dataSource === 'live') dataSource = 'stale';
        updateConnectionStatus();
    }
}

// % a stock has already run from its 52-week low, and how close it sits to its 52-week high
function calculateMomentum(stock) {
    const price = stock.price || 0;
    const low = stock.low52w || price;
    const high = stock.high52w || price;

    return {
        offLow52w: low > 0 ? ((price - low) / low) * 100 : 0,
        nearHigh52w: high > 0 ? ((high - price) / high) * 100 : 0
    };
}

// Flags small/micro-caps already up sharply from their low and still trading near their high —
// the price profile multibagger small-caps (e.g. Cupid, STLTECH) showed mid-rally.
// This describes current price action only; it is not a prediction of future returns.
function isBreakoutCandidate(stock) {
    const mcap = (stock.marketCap || 0) / 10000000; // Crores
    return mcap > 0 && mcap <= 5000 &&
        stock.offLow52w >= 50 &&
        stock.nearHigh52w <= 15;
}

// Calculate composite score
function calculateScore(stock) {
    if (!stock.pe || !stock.pb) return 0;

    const peScore = Math.max(0, 100 - (stock.pe * 2));
    const pbScore = Math.max(0, 100 - (stock.pb * 10));
    const roeScore = Math.min(100, (stock.roe || 0) * 2);
    const divScore = Math.min(100, (stock.divYield || 0) * 20);
    const changeScore = Math.max(0, Math.min(100, (stock.changePercent || 0) * 10 + 50));

    return Math.round((peScore * 0.25 + pbScore * 0.20 + roeScore * 0.25 + divScore * 0.15 + changeScore * 0.15) * 10) / 10;
}

// Get performance status
function getStatus(stock) {
    let goodCount = 0;
    let badCount = 0;

    if (stock.changePercent > 0.5) goodCount++;
    if (stock.changePercent < -0.5) badCount++;
    if (stock.pe && stock.pe < 25) goodCount++;
    if (stock.pe && stock.pe > 35) badCount++;
    if (stock.divYield && stock.divYield > 1) goodCount++;
    if (stock.roe && stock.roe > 15) goodCount++;
    if (stock.roe && stock.roe < 10) badCount++;

    if (goodCount >= 3 && badCount === 0) return 'good';
    if (badCount >= 2) return 'bad';
    return 'neutral';
}

// Populate sector filters dynamically
function populateSectorFilters() {
    const sectors = [...new Set(allStocks.map(s => s.sector))].sort();
    const sectorSelect = document.getElementById('sector-filter');

    // Clear existing options except "All Sectors"
    sectorSelect.innerHTML = '<option value="">All Sectors</option>';

    sectors.forEach(sector => {
        const option = document.createElement('option');
        option.value = sector;
        option.textContent = sector;
        sectorSelect.appendChild(option);
    });
}

// Update sub-sector filter based on selected sector (cascading)
function updateSubSectorFilter() {
    const selectedSector = document.getElementById('sector-filter').value;
    const subSectorSelect = document.getElementById('subsector-filter');

    // Clear existing options
    subSectorSelect.innerHTML = '<option value="">All Sub-Sectors</option>';

    // Get sub-sectors for selected sector
    let subSectors;
    if (selectedSector) {
        subSectors = [...new Set(allStocks
            .filter(s => s.sector === selectedSector)
            .map(s => s.basicIndustry))].sort();
    } else {
        subSectors = [...new Set(allStocks.map(s => s.basicIndustry))].sort();
    }

    subSectors.forEach(subSector => {
        const option = document.createElement('option');
        option.value = subSector;
        option.textContent = subSector;
        subSectorSelect.appendChild(option);
    });

    applyFilters();
}

// Apply all filters
function applyFilters() {
    const filters = {
        search: document.getElementById('global-search').value.toLowerCase(),
        index: document.getElementById('index-filter').value,
        sector: document.getElementById('sector-filter').value,
        subSector: document.getElementById('subsector-filter').value,
        mcapMin: parseFloat(document.getElementById('mcap-min').value) || 0,
        mcapMax: parseFloat(document.getElementById('mcap-max').value) || Infinity,
        peMin: parseFloat(document.getElementById('pe-min').value) || 0,
        peMax: parseFloat(document.getElementById('pe-max').value) || Infinity,
        pbMin: parseFloat(document.getElementById('pb-min').value) || 0,
        pbMax: parseFloat(document.getElementById('pb-max').value) || Infinity,
        divMin: parseFloat(document.getElementById('div-min').value) || 0,
        divMax: parseFloat(document.getElementById('div-max').value) || Infinity,
        roeMin: parseFloat(document.getElementById('roe-min').value) || 0,
        roeMax: parseFloat(document.getElementById('roe-max').value) || Infinity,
        changeMin: parseFloat(document.getElementById('change-min').value) || -Infinity,
        changeMax: parseFloat(document.getElementById('change-max').value) || Infinity,
        breakoutOffLowMin: parseFloat(document.getElementById('breakout-offlow-min').value) || 0,
        breakoutNearHighMax: parseFloat(document.getElementById('breakout-nearhigh-max').value) || Infinity,
        statusGood: document.getElementById('status-good').checked,
        statusNeutral: document.getElementById('status-neutral').checked,
        statusBad: document.getElementById('status-bad').checked
    };

    filteredStocks = allStocks.filter(stock => {
        // Search filter
        if (filters.search && !stock.name.toLowerCase().includes(filters.search) &&
            !stock.symbol.toLowerCase().includes(filters.search)) {
            return false;
        }

        // Index filter
        if (filters.index && stock.index !== filters.index) return false;

        // Sector filters
        if (filters.sector && stock.sector !== filters.sector) return false;
        if (filters.subSector && stock.basicIndustry !== filters.subSector) return false;

        // Range filters
        const mcap = stock.marketCap / 10000000; // Convert to Crores
        if (mcap < filters.mcapMin || mcap > filters.mcapMax) return false;
        if (stock.pe && (stock.pe < filters.peMin || stock.pe > filters.peMax)) return false;
        if (stock.pb && (stock.pb < filters.pbMin || stock.pb > filters.pbMax)) return false;
        if (stock.divYield && (stock.divYield < filters.divMin || stock.divYield > filters.divMax)) return false;
        if (stock.roe && (stock.roe < filters.roeMin || stock.roe > filters.roeMax)) return false;
        if (stock.changePercent < filters.changeMin || stock.changePercent > filters.changeMax) return false;

        // Momentum / breakout filters
        if ((stock.offLow52w || 0) < filters.breakoutOffLowMin) return false;
        if ((stock.nearHigh52w ?? 100) > filters.breakoutNearHighMax) return false;

        // Status filter - only apply if at least one checkbox is checked
        if (filters.statusGood || filters.statusNeutral || filters.statusBad) {
            const status = getStatus(stock);
            let matchesStatus = false;
            if (filters.statusGood && status === 'good') matchesStatus = true;
            if (filters.statusNeutral && status === 'neutral') matchesStatus = true;
            if (filters.statusBad && status === 'bad') matchesStatus = true;
            if (!matchesStatus) return false;
        }

        return true;
    });

    sortStocks();
    renderStocks();
    updateStats();
}

// Toggle dark mode
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    const icon = document.getElementById('theme-icon');
    if (document.body.classList.contains('dark-mode')) {
        icon.textContent = '☀️';
        localStorage.setItem('theme', 'dark');
    } else {
        icon.textContent = '🌙';
        localStorage.setItem('theme', 'light');
    }
}

// Load saved theme
if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    if (document.getElementById('theme-icon')) {
        document.getElementById('theme-icon').textContent = '☀️';
    }
}

// Quick filter helpers
function setMCapFilter(type) {
    const minInput = document.getElementById('mcap-min');
    const maxInput = document.getElementById('mcap-max');

    if (type === 'large') {
        minInput.value = 20000;
        maxInput.value = '';
    } else if (type === 'mid') {
        minInput.value = 5000;
        maxInput.value = 20000;
    } else if (type === 'small') {
        minInput.value = '';
        maxInput.value = 5000;
    }

    applyFilters();
}

function setPEFilter(type) {
    const minInput = document.getElementById('pe-min');
    const maxInput = document.getElementById('pe-max');

    if (type === 'low') {
        minInput.value = '';
        maxInput.value = 15;
    } else if (type === 'moderate') {
        minInput.value = 15;
        maxInput.value = 25;
    } else if (type === 'high') {
        minInput.value = 25;
        maxInput.value = '';
    }

    applyFilters();
}

function setDivFilter(type) {
    const minInput = document.getElementById('div-min');
    const maxInput = document.getElementById('div-max');

    if (type === 'high') {
        minInput.value = 2;
        maxInput.value = '';
    } else if (type === 'moderate') {
        minInput.value = 1;
        maxInput.value = 2;
    }

    applyFilters();
}

function setChangeFilter(type) {
    const minInput = document.getElementById('change-min');
    const maxInput = document.getElementById('change-max');

    if (type === 'gainers') {
        minInput.value = 1;
        maxInput.value = '';
    } else if (type === 'losers') {
        minInput.value = '';
        maxInput.value = -1;
    }

    applyFilters();
}

// Quick filter: small/micro-caps already running hard off their 52W low and still near their high.
// Surfaces stocks with the price profile multibagger small-caps showed mid-rally — not a forecast.
function setBreakoutFilter() {
    document.getElementById('mcap-min').value = '';
    document.getElementById('mcap-max').value = 5000;
    document.getElementById('breakout-offlow-min').value = 50;
    document.getElementById('breakout-nearhigh-max').value = 15;

    applyFilters();
}

function resetAllFilters() {
    document.getElementById('global-search').value = '';
    document.getElementById('index-filter').value = '';
    document.getElementById('sector-filter').value = '';
    document.getElementById('subsector-filter').value = '';
    document.getElementById('mcap-min').value = '';
    document.getElementById('mcap-max').value = '';
    document.getElementById('pe-min').value = '';
    document.getElementById('pe-max').value = '';
    document.getElementById('pb-min').value = '';
    document.getElementById('pb-max').value = '';
    document.getElementById('div-min').value = '';
    document.getElementById('div-max').value = '';
    document.getElementById('roe-min').value = '';
    document.getElementById('roe-max').value = '';
    document.getElementById('change-min').value = '';
    document.getElementById('change-max').value = '';
    document.getElementById('breakout-offlow-min').value = '';
    document.getElementById('breakout-nearhigh-max').value = '';
    document.getElementById('status-good').checked = false;
    document.getElementById('status-neutral').checked = false;
    document.getElementById('status-bad').checked = false;

    applyFilters();
}

// Sort stocks
function sortStocks() {
    const sortBy = document.getElementById('sort-by').value;

    filteredStocks.sort((a, b) => {
        switch (sortBy) {
            case 'name': return a.name.localeCompare(b.name);
            case 'price': return (b.price || 0) - (a.price || 0);
            case 'change': return (b.changePercent || 0) - (a.changePercent || 0);
            case 'pe': return (a.pe || 999) - (b.pe || 999);
            case 'pb': return (a.pb || 999) - (b.pb || 999);
            case 'div': return (b.divYield || 0) - (a.divYield || 0);
            case 'mcap': return (b.marketCap || 0) - (a.marketCap || 0);
            case 'offlow': return (b.offLow52w || 0) - (a.offLow52w || 0);
            case 'score': return (b.score || 0) - (a.score || 0);
            default: return 0;
        }
    });

    // Update ranks
    filteredStocks.forEach((stock, index) => {
        stock.rank = index + 1;
    });

    // Re-render after sorting
    if (currentView === 'table') {
        renderTableView();
    }
}

// Render stocks based on current view
function renderStocks() {
    if (currentView === 'table') {
        renderTableView();
    } else if (currentView === 'charts') {
        renderChartsView();
    }
}

// Render table view
function renderTableView() {
    const tbody = document.getElementById('stocks-tbody');
    tbody.innerHTML = '';

    if (filteredStocks.length === 0) {
        const message = allStocks.length === 0
            ? 'No stock data available. Check your connection and use the Retry button above.'
            : 'No stocks found matching your filters';
        tbody.innerHTML = `<tr><td colspan="19" style="text-align: center; padding: 2rem;">${message}</td></tr>`;
        return;
    }

    filteredStocks.forEach(stock => {
        const status = getStatus(stock);
        const changeClass = (stock.changePercent || 0) >= 0 ? 'change-positive' : 'change-negative';
        const changeSymbol = (stock.changePercent || 0) >= 0 ? '▲' : '▼';
        const rankClass = stock.rank <= 5 ? 'rank-top5' : '';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><span class="${rankClass}">${stock.rank}</span></td>
            <td>${stock.name}</td>
            <td>${stock.symbol}</td>
            <td>${stock.index || 'N/A'}</td>
            <td>${stock.sector || 'N/A'}</td>
            <td>${stock.basicIndustry || 'N/A'}</td>
            <td>₹${(stock.price || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
            <td class="${changeClass}">${changeSymbol} ${Math.abs(stock.changePercent || 0).toFixed(2)}%</td>
            <td>${stock.pe ? stock.pe.toFixed(1) : 'N/A'}</td>
            <td>${stock.pb ? stock.pb.toFixed(1) : 'N/A'}</td>
            <td>${stock.roe ? stock.roe.toFixed(1) + '%' : 'N/A'}</td>
            <td>${stock.divYield ? stock.divYield.toFixed(2) + '%' : 'N/A'}</td>
            <td>₹${((stock.marketCap || 0) / 10000000).toFixed(0)} Cr</td>
            <td>₹${(stock.high52w || 0).toLocaleString('en-IN')}</td>
            <td>₹${(stock.low52w || 0).toLocaleString('en-IN')}</td>
            <td class="${(stock.offLow52w || 0) >= 50 ? 'momentum-high' : ''}">${(stock.offLow52w || 0).toFixed(1)}%</td>
            <td>${(stock.nearHigh52w || 0).toFixed(1)}%</td>
            <td><strong>${stock.score || 0}</strong></td>
            <td><span class="status-badge status-${status}">${status === 'good' ? '🟢' : status === 'neutral' ? '🟡' : '🔴'} ${status.toUpperCase()}</span>${stock.breakout ? ' <span class="status-badge badge-breakout">🚀 BREAKOUT</span>' : ''}</td>
        `;

        row.addEventListener('click', () => openStockDetail(stock));
        tbody.appendChild(row);
    });
}

// Render charts view (simplified version from original)
function renderChartsView() {
    renderSectorChart();
    renderMarketCapChart();
    renderPEChart();
    renderPerformanceChart();
}

function renderSectorChart() {
    const sectorCounts = {};
    filteredStocks.forEach(stock => {
        const sector = stock.sector || 'Unknown';
        sectorCounts[sector] = (sectorCounts[sector] || 0) + 1;
    });

    const chart = document.getElementById('sector-chart');
    chart.innerHTML = '';

    Object.entries(sectorCounts).sort((a, b) => b[1] - a[1]).forEach(([sector, count]) => {
        const percentage = (count / filteredStocks.length * 100).toFixed(1);
        const bar = document.createElement('div');
        bar.style.cssText = 'margin: 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;';
        bar.innerHTML = `
            <div style="width: 200px; font-size: 0.875rem; color: var(--text-primary);">${sector}</div>
            <div style="flex: 1; background: var(--border-color); border-radius: 4px; height: 24px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; width: ${percentage}%; border-radius: 4px;"></div>
            </div>
            <div style="width: 80px; text-align: right; font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${count} (${percentage}%)</div>
        `;
        chart.appendChild(bar);
    });
}

function renderMarketCapChart() {
    const ranges = { 'Large Cap': 0, 'Mid Cap': 0, 'Small Cap': 0 };
    filteredStocks.forEach(stock => {
        const mcap = (stock.marketCap || 0) / 10000000;
        if (mcap > 20000) ranges['Large Cap']++;
        else if (mcap >= 5000) ranges['Mid Cap']++;
        else ranges['Small Cap']++;
    });

    const chart = document.getElementById('mcap-chart');
    chart.innerHTML = '';

    Object.entries(ranges).forEach(([range, count]) => {
        const percentage = filteredStocks.length > 0 ? (count / filteredStocks.length * 100).toFixed(1) : 0;
        const bar = document.createElement('div');
        bar.style.cssText = 'margin: 1rem 0; display: flex; align-items: center; gap: 0.5rem;';
        bar.innerHTML = `
            <div style="width: 100px; font-size: 0.875rem; color: var(--text-primary);">${range}</div>
            <div style="flex: 1; background: var(--border-color); border-radius: 4px; height: 24px;">
                <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); height: 100%; width: ${percentage}%; border-radius: 4px;"></div>
            </div>
            <div style="width: 80px; text-align: right; font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${count} (${percentage}%)</div>
        `;
        chart.appendChild(bar);
    });
}

function renderPEChart() {
    const ranges = { 'Low (<15)': 0, 'Moderate (15-25)': 0, 'High (25-35)': 0, 'Very High (>35)': 0 };
    filteredStocks.forEach(stock => {
        if (!stock.pe) return;
        if (stock.pe < 15) ranges['Low (<15)']++;
        else if (stock.pe < 25) ranges['Moderate (15-25)']++;
        else if (stock.pe < 35) ranges['High (25-35)']++;
        else ranges['Very High (>35)']++;
    });

    const chart = document.getElementById('pe-chart');
    chart.innerHTML = '';

    Object.entries(ranges).forEach(([range, count]) => {
        const percentage = filteredStocks.length > 0 ? (count / filteredStocks.length * 100).toFixed(1) : 0;
        const bar = document.createElement('div');
        bar.style.cssText = 'margin: 1rem 0; display: flex; align-items: center; gap: 0.5rem;';
        bar.innerHTML = `
            <div style="width: 150px; font-size: 0.875rem; color: var(--text-primary);">${range}</div>
            <div style="flex: 1; background: var(--border-color); border-radius: 4px; height: 24px;">
                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); height: 100%; width: ${percentage}%; border-radius: 4px;"></div>
            </div>
            <div style="width: 80px; text-align: right; font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${count} (${percentage}%)</div>
        `;
        chart.appendChild(bar);
    });
}

function renderPerformanceChart() {
    const counts = { good: 0, neutral: 0, bad: 0 };
    filteredStocks.forEach(stock => {
        counts[getStatus(stock)]++;
    });

    const chart = document.getElementById('performance-chart');
    chart.innerHTML = '';

    const total = filteredStocks.length || 1;
    const data = [
        { label: '🟢 Good', count: counts.good, color: '#198754' },
        { label: '🟡 Neutral', count: counts.neutral, color: '#ffc107' },
        { label: '🔴 Bad', count: counts.bad, color: '#dc3545' }
    ];

    data.forEach(item => {
        const percentage = (item.count / total * 100).toFixed(1);
        const bar = document.createElement('div');
        bar.style.cssText = 'margin: 1rem 0; display: flex; align-items: center; gap: 0.5rem;';
        bar.innerHTML = `
            <div style="width: 100px; font-size: 0.875rem; color: var(--text-primary);">${item.label}</div>
            <div style="flex: 1; background: var(--border-color); border-radius: 4px; height: 24px;">
                <div style="background: ${item.color}; height: 100%; width: ${percentage}%; border-radius: 4px;"></div>
            </div>
            <div style="width: 80px; text-align: right; font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${item.count} (${percentage}%)</div>
        `;
        chart.appendChild(bar);
    });
}

// Switch view
function switchView(view) {
    currentView = view;

    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-view="${view}"]`).classList.add('active');

    document.querySelectorAll('.data-view').forEach(v => v.classList.remove('active'));
    document.getElementById(`${view}-view`).classList.add('active');

    renderStocks();
}

// Update stats
function updateStats() {
    document.getElementById('total-stocks').textContent = allStocks.length;
    document.getElementById('filtered-stocks').textContent = filteredStocks.length;

    if (filteredStocks.length > 0) {
        const validPE = filteredStocks.filter(s => s.pe && s.pe > 0);
        const avgPE = validPE.length > 0 ? (validPE.reduce((sum, s) => sum + s.pe, 0) / validPE.length).toFixed(1) : 'N/A';

        const validDiv = filteredStocks.filter(s => s.divYield && s.divYield > 0);
        const avgDiv = validDiv.length > 0 ? (validDiv.reduce((sum, s) => sum + s.divYield, 0) / validDiv.length).toFixed(2) + '%' : 'N/A';

        document.getElementById('avg-pe').textContent = avgPE;
        document.getElementById('avg-div').textContent = avgDiv;

        const goodCount = filteredStocks.filter(s => getStatus(s) === 'good').length;
        const badCount = filteredStocks.filter(s => getStatus(s) === 'bad').length;

        document.getElementById('count-good').textContent = goodCount;
        document.getElementById('count-bad').textContent = badCount;
    } else {
        document.getElementById('avg-pe').textContent = 'N/A';
        document.getElementById('avg-div').textContent = 'N/A';
        document.getElementById('count-good').textContent = '0';
        document.getElementById('count-bad').textContent = '0';
    }
}

// Update the connection status dot/text and the offline banner to honestly
// reflect where the data on screen came from (live / cached / unavailable)
function updateConnectionStatus() {
    const dot = document.querySelector('.status-dot');
    const text = document.getElementById('update-time');
    const banner = document.getElementById('connection-banner');
    const bannerText = document.getElementById('connection-banner-text');
    const isFileProtocol = location.protocol === 'file:';
    const stamp = lastUpdateTimestamp ? new Date(lastUpdateTimestamp).toLocaleString() : null;

    dot.classList.remove('status-dot-offline', 'status-dot-error');
    banner.classList.remove('error');
    banner.style.display = 'none';

    if (dataSource === 'live') {
        text.textContent = `Updated: ${new Date(lastUpdateTimestamp).toLocaleTimeString()}`;
        return;
    }

    if (dataSource === 'stale') {
        dot.classList.add('status-dot-offline');
        text.textContent = `Offline — last live update ${stamp}`;
        bannerText.textContent = `📴 You're offline. Showing the last live data from ${stamp}.`;
        banner.style.display = 'flex';
        return;
    }

    if (dataSource === 'cached') {
        dot.classList.add('status-dot-offline');
        text.textContent = `Offline — cached ${stamp}`;
        bannerText.textContent = isFileProtocol
            ? `📴 Opened as a local file, so live prices aren't reachable here. Showing data cached on this device from ${stamp}.`
            : `📴 No internet connection. Showing data cached on this device from ${stamp}.`;
        banner.style.display = 'flex';
        return;
    }

    // dataSource === 'none'
    dot.classList.add('status-dot-error');
    text.textContent = 'No data available';
    banner.classList.add('error');
    bannerText.textContent = isFileProtocol
        ? `⚠️ Opened as a local file — live prices need this to be served over http/https, and this view can't reuse data cached by the hosted site (browser storage is separate per origin). Run a local server (see README) or use the hosted version directly to get live data.`
        : `⚠️ No internet connection and no cached data yet. Connect once to load live data — it'll then stay available offline.`;
    banner.style.display = 'flex';
}

// Export to CSV
function exportToCSV() {
    const headers = ['Rank', 'Name', 'Symbol', 'Index', 'Sector', 'Sub-Sector', 'Price', 'Change %', 'P/E', 'P/B', 'ROE %', 'Div Yield %', 'Market Cap (Cr)', '52W High', '52W Low', 'Off 52W Low %', 'Near 52W High %', 'Score', 'Status', 'Breakout'];

    const rows = filteredStocks.map(stock => [
        stock.rank,
        stock.name,
        stock.symbol,
        stock.index || 'N/A',
        stock.sector || 'N/A',
        stock.basicIndustry || 'N/A',
        stock.price || 0,
        stock.changePercent || 0,
        stock.pe || 'N/A',
        stock.pb || 'N/A',
        stock.roe || 'N/A',
        stock.divYield || 'N/A',
        (stock.marketCap || 0) / 10000000,
        stock.high52w || 0,
        stock.low52w || 0,
        (stock.offLow52w || 0).toFixed(1),
        (stock.nearHigh52w || 0).toFixed(1),
        stock.score || 0,
        getStatus(stock),
        stock.breakout ? 'YES' : 'NO'
    ]);

    const csv = [headers, ...rows].map(row => row.join(',')).join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `stock_screener_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
}

// ============================================================================
// Per-ticker deep dive: fundamental + technical detail modal
// ============================================================================

// Builds the modal DOM once (mirrors LoadingManager's create-once/show-hide pattern)
// and reuses it for every stock opened afterwards.
function ensureStockDetailModal() {
    if (stockDetailModalEl) return stockDetailModalEl;

    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.style.display = 'none';
    overlay.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-info">
                    <h2 id="modal-stock-name"></h2>
                    <div class="modal-symbol" id="modal-stock-symbol"></div>
                </div>
                <div class="modal-header-price">
                    <span class="modal-price" id="modal-stock-price"></span>
                    <span id="modal-stock-change"></span>
                </div>
                <button class="modal-close-btn" id="modal-close-btn" title="Close">✕</button>
            </div>
            <div class="modal-body">
                <div class="modal-section" id="modal-badge-section"></div>
                <div class="modal-section">
                    <h3>Fundamentals</h3>
                    <div class="modal-metrics-grid" id="modal-fundamentals-grid"></div>
                </div>
                <div class="modal-section">
                    <h3>Technicals</h3>
                    <div id="modal-technicals-content">
                        <div class="modal-chart-loading"><div class="loading-spinner"></div>Loading price history...</div>
                    </div>
                </div>
                <div class="modal-disclaimer">
                    This panel describes how the stock has behaved historically and where its price currently sits
                    relative to its own recent range. It is not financial advice, a prediction, or a recommendation
                    to buy or sell. Data may be delayed or incomplete — verify independently before making decisions.
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector('#modal-close-btn').addEventListener('click', closeStockDetail);
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) closeStockDetail();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && overlay.style.display !== 'none') closeStockDetail();
    });

    stockDetailModalEl = overlay;
    return overlay;
}

function closeStockDetail() {
    if (stockDetailModalEl) stockDetailModalEl.style.display = 'none';
    stockDetailCurrentSymbol = null;
}

// Opens the modal for an already-enriched stock object (from the table, or from
// lookupAndOpenStock's fallback path) and kicks off the async history/technicals load.
function openStockDetail(stock) {
    const modal = ensureStockDetailModal();
    stockDetailCurrentSymbol = stock.symbol;

    // Name/symbol can carry raw user input via the ticker-lookup fallback path, so they are
    // set via textContent (never innerHTML) to rule out any HTML/script injection.
    modal.querySelector('#modal-stock-name').textContent = stock.name || stock.symbol;
    modal.querySelector('#modal-stock-symbol').textContent = stock.symbol;

    const changeClass = (stock.changePercent || 0) >= 0 ? 'change-positive' : 'change-negative';
    const changeSymbol = (stock.changePercent || 0) >= 0 ? '▲' : '▼';
    modal.querySelector('#modal-stock-price').textContent = `₹${(stock.price || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
    const changeEl = modal.querySelector('#modal-stock-change');
    changeEl.className = changeClass;
    changeEl.textContent = `${changeSymbol} ${Math.abs(stock.changePercent || 0).toFixed(2)}%`;

    const status = getStatus(stock);
    modal.querySelector('#modal-badge-section').innerHTML = `
        <span class="status-badge status-${status}">${status === 'good' ? '🟢' : status === 'neutral' ? '🟡' : '🔴'} ${status.toUpperCase()}</span>
        ${stock.breakout ? ' <span class="status-badge badge-breakout">🚀 BREAKOUT</span>' : ''}
        <span class="metric-item" style="display:inline-block; margin-left: 1rem;">
            Off 52W Low: <strong>${(stock.offLow52w || 0).toFixed(1)}%</strong> &nbsp;·&nbsp;
            Near 52W High: <strong>${(stock.nearHigh52w || 0).toFixed(1)}%</strong>
        </span>
    `;

    modal.querySelector('#modal-fundamentals-grid').innerHTML = `
        <div class="metric-item"><div class="metric-label">P/E</div><div class="metric-value">${stock.pe ? stock.pe.toFixed(1) : 'N/A'}</div></div>
        <div class="metric-item"><div class="metric-label">P/B</div><div class="metric-value">${stock.pb ? stock.pb.toFixed(1) : 'N/A'}</div></div>
        <div class="metric-item"><div class="metric-label">ROE</div><div class="metric-value">${stock.roe ? stock.roe.toFixed(1) + '%' : 'N/A'}</div></div>
        <div class="metric-item"><div class="metric-label">Div Yield</div><div class="metric-value">${stock.divYield ? stock.divYield.toFixed(2) + '%' : 'N/A'}</div></div>
        <div class="metric-item"><div class="metric-label">EPS</div><div class="metric-value">${stock.eps ? '₹' + stock.eps.toFixed(2) : 'N/A'}</div></div>
        <div class="metric-item"><div class="metric-label">Book Value</div><div class="metric-value">${stock.bookValue ? '₹' + stock.bookValue.toFixed(2) : 'N/A'}</div></div>
        <div class="metric-item"><div class="metric-label">Market Cap</div><div class="metric-value">₹${((stock.marketCap || 0) / 10000000).toFixed(0)} Cr</div></div>
        <div class="metric-item"><div class="metric-label">52W Range</div><div class="metric-value">₹${(stock.low52w || 0).toLocaleString('en-IN')} – ₹${(stock.high52w || 0).toLocaleString('en-IN')}</div></div>
    `;

    modal.querySelector('#modal-technicals-content').innerHTML =
        `<div class="modal-chart-loading"><div class="loading-spinner"></div>Loading price history...</div>`;

    modal.style.display = 'flex';

    loadStockDetailHistory(stock);
}

// Fetches historical bars for the modal's technicals/chart section and renders them.
// Guards every render against stockDetailCurrentSymbol so a slow response for a stock the
// user has since closed or navigated away from can't clobber what's currently on screen.
async function loadStockDetailHistory(stock) {
    const symbol = stock.symbol;
    const bars = await yahooAPI.fetchHistory(symbol, '6mo', '1d');

    if (stockDetailCurrentSymbol !== symbol) return; // user moved on before this resolved

    const modal = stockDetailModalEl;
    const content = modal.querySelector('#modal-technicals-content');

    if (!bars) {
        const offline = !navigator.onLine || window.dataSource !== 'live';
        content.innerHTML = `<div class="modal-chart-error">${offline
            ? '📴 Price history unavailable while offline — fundamentals and momentum above are still shown.'
            : '⚠️ Price history unavailable right now — fundamentals and momentum above are still shown.'}</div>`;
        return;
    }

    if (bars.length < 2) {
        content.innerHTML = `<div class="modal-chart-error">Not enough price history available yet for a chart.</div>`;
        return;
    }

    const sma20Series = calculateSMASeries(bars, 20);
    const sma20 = calculateSMA(bars, 20);
    const sma50 = calculateSMA(bars, 50);
    const rsi14 = calculateRSI(bars, 14);
    const volatility20 = calculateVolatility(bars, 20);
    const latestClose = bars[bars.length - 1].close;
    const trend = getTrendVsSMA(latestClose, sma20);
    const trendText = trend
        ? `Price is currently <strong>${trend}</strong> its 20-day average.`
        : 'Not enough history yet to compare price to its 20-day average.';

    content.innerHTML = `
        <div class="modal-metrics-grid" style="margin-bottom: 1rem;">
            <div class="metric-item"><div class="metric-label">SMA 20</div><div class="metric-value">${sma20 !== null ? '₹' + sma20.toFixed(2) : 'N/A'}</div></div>
            <div class="metric-item"><div class="metric-label">SMA 50</div><div class="metric-value">${sma50 !== null ? '₹' + sma50.toFixed(2) : 'N/A'}</div></div>
            <div class="metric-item"><div class="metric-label">RSI (14)</div><div class="metric-value">${rsi14 !== null ? rsi14.toFixed(1) : 'N/A'}</div></div>
            <div class="metric-item"><div class="metric-label">Volatility (ann.)</div><div class="metric-value">${volatility20 !== null ? volatility20.toFixed(1) + '%' : 'N/A'}</div></div>
        </div>
        <p class="filter-hint">${trendText} This describes current price action only — it is not a prediction of future returns.</p>
        <div id="modal-chart-container"></div>
    `;

    renderPriceHistoryChart(modal.querySelector('#modal-chart-container'), bars, sma20Series);
}

// Renders a price-history line chart as an inline SVG polyline (no charting library —
// consistent with the rest of this app's hand-rolled charts, just suited to a continuous
// ~126-point series instead of a handful of categorical bars).
function renderPriceHistoryChart(containerEl, bars, smaSeries) {
    const width = 600;
    const height = 200;

    const closes = bars.map(bar => bar.close);
    const smaValues = (smaSeries || []).filter(v => v !== null && v !== undefined);
    const allValues = closes.concat(smaValues);
    const minVal = Math.min(...allValues);
    const maxVal = Math.max(...allValues);
    const range = (maxVal - minVal) || 1;

    const toX = (i) => (i / (bars.length - 1)) * width;
    const toY = (v) => height - ((v - minVal) / range) * height;

    const pricePoints = closes.map((close, i) => `${toX(i).toFixed(2)},${toY(close).toFixed(2)}`).join(' ');

    let smaPolyline = '';
    if (smaSeries && smaValues.length >= 2) {
        const smaPoints = smaSeries
            .map((v, i) => (v === null || v === undefined) ? null : `${toX(i).toFixed(2)},${toY(v).toFixed(2)}`)
            .filter(p => p !== null)
            .join(' ');
        smaPolyline = `<polyline class="sma-line" points="${smaPoints}" />`;
    }

    const startDate = new Date(bars[0].date).toLocaleDateString('en-IN');
    const endDate = new Date(bars[bars.length - 1].date).toLocaleDateString('en-IN');

    containerEl.innerHTML = `
        <svg class="modal-chart-svg" viewBox="0 0 ${width} ${height}" preserveAspectRatio="none">
            <polyline class="price-line" points="${pricePoints}" />
            ${smaPolyline}
        </svg>
        <div class="modal-chart-range">
            <span>${startDate}</span>
            <span>Range: ₹${minVal.toFixed(2)} – ₹${maxVal.toFixed(2)}</span>
            <span>${endDate}</span>
        </div>
    `;
}

// Standalone ticker lookup: opens the deep-dive modal for any symbol, even one outside the
// currently-loaded index filter. Checks the in-memory stock list first (no network call needed),
// otherwise fetches fresh from Yahoo Finance directly.
const TICKER_SYMBOL_PATTERN = /^[A-Z0-9&.-]{1,20}$/;

async function lookupAndOpenStock(rawInput) {
    const errorEl = document.getElementById('ticker-lookup-error');
    errorEl.textContent = '';
    errorEl.style.display = 'none';

    const symbol = (rawInput || '').trim().toUpperCase();
    if (!symbol) return;

    if (!TICKER_SYMBOL_PATTERN.test(symbol)) {
        errorEl.textContent = `"${rawInput}" doesn't look like a valid NSE symbol.`;
        errorEl.style.display = 'block';
        return;
    }

    const existing = allStocks.find(s => s.symbol === symbol);
    if (existing) {
        openStockDetail(existing);
        return;
    }

    const selectedIndex = document.getElementById('index-filter')?.value || '';
    const data = await yahooAPI.fetchCompleteData(symbol);

    if (!data) {
        errorEl.textContent = `Could not find data for "${symbol}" — check the symbol and try again.`;
        errorEl.style.display = 'block';
        return;
    }

    const stock = enrichOneStock({
        symbol,
        name: symbol,
        sector: 'Unknown',
        industry: 'Unknown',
        basicIndustry: 'Unknown',
        ...data
    }, 0, selectedIndex);

    openStockDetail(stock);
}
