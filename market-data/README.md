# NSE Market Data Explorer

A dedicated market-data page in the existing `BCy94/indian-stock-screener` GitHub Pages site. The existing screener homepage is not replaced.

## Daily update pipeline

- GitHub Actions runs at **20:00 IST** and **22:00 IST**, Monday to Friday. The later run retries if NSE publishes its bhavcopy late.
- The updater downloads NSE's official **Full Bhavcopy and Security Deliverable data** CSV and falls back to the official **CM-UDiFF Common Bhavcopy Final** ZIP.
- It checks the report date, maps the fixed 2,234-symbol watchlist, prefers the `EQ` series and validates symbol-match coverage.
- Only valid results replace `data/latest.csv` and `data/status.json`. A failed download or insufficient coverage keeps the previous published data intact.
- The dashboard refreshes from the hosted CSV every 15 minutes and also has a manual refresh button.

## Calculations

- `TODAY % CHANGE` = `(Close / Previous Close - 1) * 100`
- `TODAY CANDLE BODY %` = `(Close / Open - 1) * 100`
- `TODAY CANDLE RANGE %` = `(High - Low) / Open * 100`

The original `%CHANGE` is preserved from the user's **03-Nov-2026 pre-open snapshot**. The three `TODAY` fields and EOD OHLC fields refresh from each published trading session.

## Website

`https://bcy94.github.io/indian-stock-screener/market-data/`

GitHub Pages was already enabled for the parent repository. This is a new subdirectory; the root screener is left untouched.

## Manual refresh / backfill

Open **Actions → Refresh NSE EOD Market Data → Run workflow**. Leave the date blank for the latest available session. To backfill, enter an exact date in `YYYY-MM-DD` format.

The updater never invents data. If NSE is unavailable, the last successful snapshot remains on the site, and its report date remains visible.
