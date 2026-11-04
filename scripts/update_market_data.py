#!/usr/bin/env python3
"""Fetch the latest NSE CM Bhavcopy and refresh the fixed watchlist."""
import csv, io, json, os, re, sys, time, zipfile
from datetime import date, datetime, timedelta
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.request import Request, build_opener, HTTPCookieProcessor
from http.cookiejar import CookieJar
from zoneinfo import ZoneInfo

ROOT = Path(__file__).resolve().parents[1]
DATA = ROOT / "market-data" / "data"
BASE = DATA / "base_watchlist.csv"
LATEST = DATA / "latest.csv"
STATUS = DATA / "status.json"
IST = ZoneInfo("Asia/Kolkata")
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36"

ALIASES = {
    "symbol": ("SYMBOL", "TckrSymb"), "series": ("SERIES", "SctySrs"),
    "open": ("OPEN", "OPEN_PRICE", "OpnPric"), "high": ("HIGH", "HIGH_PRICE", "HghPric"),
    "low": ("LOW", "LOW_PRICE", "LwPric"), "close": ("CLOSE", "CLOSE_PRICE", "ClsPric"),
    "prev": ("PREVCLOSE", "PREV_CLOSE", "PrvsClsgPric"),
    "date": ("TIMESTAMP", "DATE1", "TradDt", "TRADDT", "TRADEDATE"),
}
NEW_COLUMNS = ["TODAY % CHANGE", "TODAY CANDLE BODY %", "TODAY CANDLE RANGE %", "EOD SERIES", "EOD OPEN", "EOD HIGH", "EOD LOW", "EOD CLOSE", "EOD PREV CLOSE", "EOD DATE", "EOD SOURCE REPORT"]


def key(s): return re.sub(r"[^A-Z0-9]", "", (s or "").upper())
def number(s):
    try: return float((s or "").strip().replace(",", ""))
    except (ValueError, AttributeError): return None

def parse_day(s):
    s = (s or "").strip()
    candidates = [s, s.split(" ", 1)[0]]
    for value in candidates:
        for fmt in ("%d-%b-%Y", "%Y-%m-%d", "%d/%m/%Y", "%d-%m-%Y", "%Y%m%d"):
            try: return datetime.strptime(value, fmt).date()
            except ValueError: pass
    return None

def session():
    opener = build_opener(HTTPCookieProcessor(CookieJar()))
    try:
        req = Request("https://www.nseindia.com/", headers={"User-Agent": UA, "Accept": "text/html,*/*"})
        opener.open(req, timeout=20).read(1024)
    except Exception as e:
        print(f"NSE landing-page bootstrap warning: {e}", file=sys.stderr)
    return opener


def get_bytes(opener, url):
    headers = {"User-Agent": UA, "Accept": "text/csv,application/zip,application/octet-stream,*/*", "Referer": "https://www.nseindia.com/all-reports", "Origin": "https://www.nseindia.com"}
    for n in range(3):
        try:
            with opener.open(Request(url, headers=headers), timeout=50) as r:
                b = r.read()
                if b and r.status == 200: return b
        except HTTPError as e:
            if e.code == 404: return None
            print(f"NSE returned HTTP {e.code} for {url}", file=sys.stderr)
        except (URLError, TimeoutError, OSError) as e:
            print(f"NSE request error: {e}", file=sys.stderr)
        if n < 2: time.sleep(4 * (n + 1))
    return None


def read_report(raw, zipped=False):
    if zipped:
        with zipfile.ZipFile(io.BytesIO(raw)) as z:
            names = [x for x in z.namelist() if x.lower().endswith(".csv")]
            if not names: raise ValueError("ZIP has no CSV")
            raw = z.read(max(names, key=lambda x: z.getinfo(x).file_size))
    try: text = raw.decode("utf-8-sig")
    except UnicodeDecodeError: text = raw.decode("latin-1")
    reader = csv.DictReader(io.StringIO(text))
    if not reader.fieldnames: raise ValueError("Empty CSV")
    fields = {key(h): h for h in reader.fieldnames}
    col = {name: next((fields[key(a)] for a in aliases if key(a) in fields), None) for name, aliases in ALIASES.items()}
    missing = [x for x in ("symbol", "series", "open", "high", "low", "close", "prev") if not col[x]]
    if missing: raise ValueError(f"Missing required bhavcopy columns: {missing}")
    rows = []
    for r in reader:
        sym = (r.get(col["symbol"]) or "").strip().upper()
        vals = {x: number(r.get(col[x], "")) for x in ("open", "high", "low", "close", "prev")}
        if not sym or any(v is None for v in vals.values()): continue
        rows.append({"symbol": sym, "series": (r.get(col["series"]) or "").strip().upper(), "day": parse_day(r.get(col["date"], "")) if col["date"] else None, **vals})
    if not rows: raise ValueError("No usable OHLC rows in report")
    return rows


def fetch_day(opener, d):
    dmy, ymd = d.strftime("%d%m%Y"), d.strftime("%Y%m%d")
    urls = [
        (f"https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_{dmy}.csv", False, "Full Bhavcopy and Security Deliverable data"),
        (f"https://nsearchives.nseindia.com/content/cm/BhavCopy_NSE_CM_0_0_0_{ymd}_F_0000.csv.zip", True, "CM-UDiFF Common Bhavcopy Final"),
    ]
    for url, zipped, name in urls:
        raw = get_bytes(opener, url)
        if not raw: continue
        try:
            rows = read_report(raw, zipped)
            dates = {r["day"] for r in rows if r["day"]}
            if dates and dates != {d}: raise ValueError(f"Report dates {dates} do not equal requested {d}")
            if not dates and not zipped: raise ValueError("No embedded report date")
            return rows, name, url
        except Exception as e: print(f"Report rejected ({url}): {e}", file=sys.stderr)
    return None


def latest_report(today):
    opener = session()
    requested = os.environ.get("BHAVCOPY_DATE", "").strip()
    start = date.fromisoformat(requested) if requested else today
    lookback = 0 if requested else 8
    for offset in range(lookback + 1):
        d = start - timedelta(days=offset)
        result = fetch_day(opener, d)
        if result: return d, *result
    raise RuntimeError(f"No valid NSE Bhavcopy found from {start} back {lookback} calendar days")


def load_base():
    with BASE.open("r", encoding="utf-8-sig", newline="") as f:
        r = csv.DictReader(f)
        fields = r.fieldnames or []
        rows = [{k: (v or "").strip() for k, v in x.items()} for x in r]
    if not rows or "SYMBOL" not in fields or "%CHANGE" not in fields: raise RuntimeError("Base watchlist must have SYMBOL and %CHANGE columns")
    return rows, fields


def calculate(close, prev): return (close / prev - 1) * 100 if prev else None

def main():
    today = datetime.now(IST).date()
    d, eod_rows, report, url = latest_report(today)
    if STATUS.exists():
        old = json.loads(STATUS.read_text())
        if old.get("data_as_of") and date.fromisoformat(old["data_as_of"]) > d: raise RuntimeError("Refusing to replace newer published data with an older bhavcopy")
    # Select EQ whenever present; only use an alternate series if EQ does not exist for this symbol.
    priorities = {"EQ": 0, "BE": 1, "BZ": 2, "SM": 3}
    chosen = {}
    for r in sorted(eod_rows, key=lambda x: (priorities.get(x["series"], 20), x["series"])): chosen.setdefault(r["symbol"], r)
    base_rows, base_headers = load_base()
    headers = list(base_headers)
    insert = headers.index("%CHANGE") + 1
    for c in NEW_COLUMNS:
        if c not in headers: headers.insert(insert, c); insert += 1
    out, missing = [], []
    for base in base_rows:
        row = dict(base); e = chosen.get(base["SYMBOL"].upper())
        for c in NEW_COLUMNS: row[c] = ""
        if e:
            op, hi, lo, cl, prev = (e[k] for k in ("open", "high", "low", "close", "prev"))
            row.update({
                "TODAY % CHANGE": f"{calculate(cl, prev):.4f}",
                "TODAY CANDLE BODY %": f"{calculate(cl, op):.4f}",
                "TODAY CANDLE RANGE %": f"{(hi - lo) / op * 100:.4f}" if op else "",
                "EOD SERIES": e["series"], "EOD OPEN": f"{op:.2f}", "EOD HIGH": f"{hi:.2f}", "EOD LOW": f"{lo:.2f}",
                "EOD CLOSE": f"{cl:.2f}", "EOD PREV CLOSE": f"{prev:.2f}", "EOD DATE": d.strftime("%d-%b-%Y"), "EOD SOURCE REPORT": report,
            })
        else: missing.append(base["SYMBOL"])
        out.append(row)
    coverage = (len(out) - len(missing)) / len(out)
    threshold = float(os.environ.get("MIN_MATCH_COVERAGE", "0.85"))
    if not out or coverage < threshold: raise RuntimeError(f"Only {len(out)-len(missing)}/{len(out)} watchlist matches ({coverage:.1%}); refusing publication")
    DATA.mkdir(parents=True, exist_ok=True)
    tmp = LATEST.with_suffix(".tmp")
    with tmp.open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=headers); w.writeheader(); w.writerows(out)
    tmp.replace(LATEST)
    now = datetime.now(IST)
    status = {
        "data_as_of": d.isoformat(), "data_as_of_display": d.strftime("%d %B %Y"),
        "updated_at_ist": now.isoformat(timespec="seconds"), "updated_at_display": now.strftime("%d %b %Y, %I:%M:%S %p IST"),
        "total_watchlist_symbols": len(out), "matched_symbols": len(out)-len(missing), "unmatched_symbols": len(missing), "match_coverage_pct": round(coverage*100, 2),
        "unmatched_sample": missing[:40], "source_report": report, "source_url": url, "status": "ok",
        "series_policy": "Prefer EQ; use an available alternate series only if EQ is absent.",
        "metrics": {"today_pct_change": "(Close / Previous Close - 1) * 100", "candle_body_pct": "(Close / Open - 1) * 100", "candle_range_pct": "(High - Low) / Open * 100"},
        "preopen_snapshot_date": "2026-11-03", "preopen_column_note": "Original %CHANGE remains the 03-Nov-2026 pre-open snapshot; TODAY fields are refreshed each EOD session.",
    }
    stmp = STATUS.with_suffix(".json.tmp"); stmp.write_text(json.dumps(status, indent=2), encoding="utf-8"); stmp.replace(STATUS)
    print(json.dumps({"date": d.isoformat(), "matched": status["matched_symbols"], "total": len(out), "coverage_pct": status["match_coverage_pct"], "source": url}, indent=2))

if __name__ == "__main__": main()
