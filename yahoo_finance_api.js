// Yahoo Finance API Integration for Indian Stocks
// Fetches real-time prices and fundamental data

class YahooFinanceAPI {
    constructor() {
        this.baseURL = 'https://query1.finance.yahoo.com';
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
        this.batchSize = 10; // Fetch 10 stocks at a time
        this.requestDelay = 100; // 100ms delay between requests
    }

    // Fetch single stock data
    async fetchStock(symbol) {
        try {
            // Check cache first
            const cached = this.cache.get(symbol);
            if (cached && (Date.now() - cached.timestamp < this.cacheTimeout)) {
                return cached.data;
            }

            const url = `${this.baseURL}/v8/finance/chart/${symbol}.NS?interval=1d&range=1d`;
            const response = await fetch(url);

            if (!response.ok) {
                console.warn(`Failed to fetch ${symbol}: ${response.status}`);
                return null;
            }

            const data = await response.json();
            const stockData = this.parseChartData(data, symbol);

            // Cache the result
            this.cache.set(symbol, {
                data: stockData,
                timestamp: Date.now()
            });

            return stockData;
        } catch (error) {
            console.error(`Error fetching ${symbol}:`, error);
            return null;
        }
    }

    // Parse Yahoo Finance chart response
    parseChartData(json, symbol) {
        try {
            const result = json.chart.result[0];
            const meta = result.meta;
            const quote = result.indicators.quote[0];

            // Calculate change
            const currentPrice = meta.regularMarketPrice || meta.previousClose;
            const previousClose = meta.previousClose || meta.chartPreviousClose;
            const change = currentPrice - previousClose;
            const changePercent = (change / previousClose) * 100;

            return {
                symbol: symbol,
                price: currentPrice,
                change: change,
                changePercent: changePercent,
                volume: meta.regularMarketVolume || 0,
                marketCap: meta.marketCap || 0,
                high52w: meta.fiftyTwoWeekHigh || currentPrice,
                low52w: meta.fiftyTwoWeekLow || currentPrice,
                timestamp: Date.now()
            };
        } catch (error) {
            console.error(`Error parsing data for ${symbol}:`, error);
            return null;
        }
    }

    // Fetch fundamentals (P/E, P/B, etc.) from Yahoo Finance
    async fetchFundamentals(symbol) {
        try {
            const url = `${this.baseURL}/v10/finance/quoteSummary/${symbol}.NS?modules=defaultKeyStatistics,financialData`;
            const response = await fetch(url);

            if (!response.ok) {
                return this.getDefaultFundamentals();
            }

            const data = await response.json();
            return this.parseFundamentals(data);
        } catch (error) {
            console.error(`Error fetching fundamentals for ${symbol}:`, error);
            return this.getDefaultFundamentals();
        }
    }

    // Parse fundamentals data
    parseFundamentals(json) {
        try {
            const keyStats = json.quoteSummary.result[0].defaultKeyStatistics || {};
            const financialData = json.quoteSummary.result[0].financialData || {};

            return {
                pe: keyStats.trailingPE?.raw || keyStats.forwardPE?.raw || 0,
                pb: keyStats.priceToBook?.raw || 0,
                roe: financialData.returnOnEquity?.raw ? financialData.returnOnEquity.raw * 100 : 0,
                divYield: keyStats.dividendYield?.raw ? keyStats.dividendYield.raw * 100 : 0,
                eps: keyStats.trailingEps?.raw || 0,
                bookValue: keyStats.bookValue?.raw || 0
            };
        } catch (error) {
            return this.getDefaultFundamentals();
        }
    }

    // Default fundamentals when data unavailable
    getDefaultFundamentals() {
        return {
            pe: 0,
            pb: 0,
            roe: 0,
            divYield: 0,
            eps: 0,
            bookValue: 0
        };
    }

    // Fetch multiple stocks in batches
    async fetchMultipleStocks(symbols, onProgress = null) {
        const results = [];
        const total = symbols.length;

        for (let i = 0; i < symbols.length; i += this.batchSize) {
            const batch = symbols.slice(i, i + this.batchSize);
            const batchPromises = batch.map(symbol => this.fetchStock(symbol));
            const batchResults = await Promise.all(batchPromises);

            results.push(...batchResults.filter(r => r !== null));

            // Report progress
            if (onProgress) {
                const progress = Math.min(100, Math.round(((i + batch.length) / total) * 100));
                onProgress(progress, i + batch.length, total);
            }

            // Delay between batches
            if (i + this.batchSize < symbols.length) {
                await this.sleep(this.requestDelay);
            }
        }

        return results;
    }

    // Fetch complete stock data (price + fundamentals)
    async fetchCompleteData(symbol) {
        const [priceData, fundamentals] = await Promise.all([
            this.fetchStock(symbol),
            this.fetchFundamentals(symbol)
        ]);

        if (!priceData) return null;

        return {
            ...priceData,
            ...fundamentals
        };
    }

    // Fetch all stocks for an index
    async fetchIndexStocks(stockList, onProgress = null) {
        const symbols = stockList.map(s => typeof s === 'string' ? s : s.symbol);
        const priceData = await this.fetchMultipleStocks(symbols, onProgress);

        // Merge with stock universe data
        const completeData = priceData.map(price => {
            const stockInfo = stockList.find(s => {
                const sym = typeof s === 'string' ? s : s.symbol;
                return sym === price.symbol;
            });

            if (typeof stockInfo === 'object') {
                return {
                    ...stockInfo,
                    ...price
                };
            }

            return price;
        });

        return completeData;
    }

    // Helper: Sleep function
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
    }

    // Get cache stats
    getCacheStats() {
        return {
            size: this.cache.size,
            entries: Array.from(this.cache.keys())
        };
    }
}

// Loading indicator management
class LoadingManager {
    constructor() {
        this.loadingElement = null;
        this.progressBar = null;
        this.progressText = null;
    }

    show(message = 'Loading stock data...') {
        // Create loading overlay if it doesn't exist
        if (!this.loadingElement) {
            this.loadingElement = document.createElement('div');
            this.loadingElement.className = 'loading-overlay';
            this.loadingElement.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-message">${message}</div>
                    <div class="loading-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progress-fill"></div>
                        </div>
                        <div class="progress-text" id="progress-text">0%</div>
                    </div>
                </div>
            `;
            document.body.appendChild(this.loadingElement);

            this.progressBar = document.getElementById('progress-fill');
            this.progressText = document.getElementById('progress-text');
        }

        this.loadingElement.style.display = 'flex';
    }

    updateProgress(percent, current, total) {
        if (this.progressBar) {
            this.progressBar.style.width = `${percent}%`;
        }
        if (this.progressText) {
            this.progressText.textContent = `${percent}% (${current}/${total})`;
        }
    }

    hide() {
        if (this.loadingElement) {
            this.loadingElement.style.display = 'none';
        }
    }

    remove() {
        if (this.loadingElement) {
            this.loadingElement.remove();
            this.loadingElement = null;
        }
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { YahooFinanceAPI, LoadingManager };
}
