// Pure technical-indicator math over OHLC bar arrays: [{ date, open, high, low, close, volume }, ...]
// sorted ascending by date. No DOM access, no network calls, no globals — reused as-is by the
// deep-dive view today, and intended for backtesting/position-sizing features later.
//
// Every function returns null when there isn't enough history to compute a real value.
// Callers must render null as "N/A", never as 0 — a 0 would look like a real (and alarming)
// reading (e.g. "RSI: 0") instead of "we don't have enough data yet".

// Simple moving average of the last `period` closes.
function calculateSMA(bars, period) {
    if (!Array.isArray(bars) || bars.length < period) return null;
    const slice = bars.slice(bars.length - period);
    const sum = slice.reduce((total, bar) => total + bar.close, 0);
    return sum / period;
}

// Trailing SMA at every point in `bars` (same length as `bars`), for drawing an SMA overlay
// line alongside the price chart. Leading entries where there isn't enough history are null.
function calculateSMASeries(bars, period) {
    if (!Array.isArray(bars)) return [];
    return bars.map((_, i) => {
        if (i + 1 < period) return null;
        const slice = bars.slice(i + 1 - period, i + 1);
        const sum = slice.reduce((total, bar) => total + bar.close, 0);
        return sum / period;
    });
}

// Wilder's RSI over closing prices. Returns the most recent RSI value, or null if there
// isn't at least `period` + 1 closes to derive `period` price changes from.
function calculateRSI(bars, period = 14) {
    if (!Array.isArray(bars) || bars.length <= period) return null;

    const closes = bars.map(bar => bar.close);
    let avgGain = 0;
    let avgLoss = 0;

    for (let i = 1; i <= period; i++) {
        const delta = closes[i] - closes[i - 1];
        if (delta > 0) avgGain += delta;
        else avgLoss += -delta;
    }
    avgGain /= period;
    avgLoss /= period;

    for (let i = period + 1; i < closes.length; i++) {
        const delta = closes[i] - closes[i - 1];
        const gain = delta > 0 ? delta : 0;
        const loss = delta < 0 ? -delta : 0;
        avgGain = (avgGain * (period - 1) + gain) / period;
        avgLoss = (avgLoss * (period - 1) + loss) / period;
    }

    if (avgLoss === 0) return 100;
    const rs = avgGain / avgLoss;
    return 100 - (100 / (1 + rs));
}

// Annualized volatility (% stdev of simple daily returns) over the trailing `period` bars.
// Simple returns rather than log returns — close enough for a descriptive "how choppy has
// this been" number, and easier to explain than log returns to a non-quant reader.
// Needs `period` + 1 closes to derive `period` daily returns.
function calculateVolatility(bars, period = 20) {
    if (!Array.isArray(bars) || bars.length < period + 1) return null;

    const slice = bars.slice(bars.length - period - 1);
    const returns = [];
    for (let i = 1; i < slice.length; i++) {
        returns.push((slice[i].close - slice[i - 1].close) / slice[i - 1].close);
    }

    const mean = returns.reduce((sum, r) => sum + r, 0) / returns.length;
    const variance = returns.reduce((sum, r) => sum + (r - mean) ** 2, 0) / returns.length;
    const dailyStdev = Math.sqrt(variance);

    return dailyStdev * Math.sqrt(252) * 100;
}

// Descriptive label only — not a signal. Callers must word this as a statement of where
// price currently sits ("price is currently above its 50-day average"), not as advice.
function getTrendVsSMA(price, sma) {
    if (sma === null || sma === undefined || !Number.isFinite(price)) return null;
    const pctDiff = ((price - sma) / sma) * 100;
    if (pctDiff > 0.1) return 'above';
    if (pctDiff < -0.1) return 'below';
    return 'at';
}
