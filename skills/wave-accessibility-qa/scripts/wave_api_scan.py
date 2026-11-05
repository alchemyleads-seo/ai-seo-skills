#!/usr/bin/env python3
"""Batch WebAIM WAVE API scan -> CSV (+ per-item JSON).

Usage:
  WAVE_API_KEY=... python3 wave_api_scan.py urls.txt --out wave.csv [--reporttype 2] [--delay 1.0]

urls.txt: one URL per line (# comments allowed). Each request spends WAVE API credits.
A failed request is recorded as status=error with the message; counts are left
blank (never 0), so a failure can't be mistaken for a clean page.
"""
import argparse
import csv
import json
import os
import sys
import time
import urllib.parse
import urllib.request

API = "https://wave.webaim.org/api/request"
CATS = ["error", "contrast", "alert", "feature", "structure", "aria"]


def scan(url, key, reporttype):
    q = urllib.parse.urlencode({"key": key, "url": url, "reporttype": reporttype, "format": "json"})
    with urllib.request.urlopen(f"{API}?{q}", timeout=120) as r:
        data = json.loads(r.read().decode("utf-8"))
    status = data.get("status", {})
    if not status.get("success"):
        raise RuntimeError(status.get("error") or json.dumps(status)[:300])
    cats = data.get("categories") or {}
    missing = [c for c in CATS if c not in cats]
    if missing:
        raise RuntimeError(f"response missing categories: {missing}")
    counts = {c: cats[c].get("count") for c in CATS}
    items = {c: {k: v.get("count") for k, v in (cats[c].get("items") or {}).items()} for c in CATS}
    stats = data.get("statistics") or {}
    return counts, items, stats


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("urls")
    ap.add_argument("--out", default="wave.csv")
    ap.add_argument("--reporttype", type=int, default=2)
    ap.add_argument("--delay", type=float, default=1.0)
    a = ap.parse_args()
    key = os.environ.get("WAVE_API_KEY")
    if not key:
        sys.exit("Set WAVE_API_KEY (WebAIM API key). Not scanning without it.")
    urls = [l.strip() for l in open(a.urls, encoding="utf-8") if l.strip() and not l.startswith("#")]
    detail = {}
    with open(a.out, "w", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        w.writerow(["url", "status", *CATS, "top_errors", "top_contrast", "credits_remaining", "message"])
        for u in urls:
            try:
                counts, items, stats = scan(u, key, a.reporttype)
                top = lambda c: "; ".join(f"{k}:{v}" for k, v in sorted(items[c].items(), key=lambda x: -(x[1] or 0))[:5])
                w.writerow([u, "ok", *[counts[c] for c in CATS], top("error"), top("contrast"), stats.get("creditsremaining", ""), ""])
                detail[u] = {"counts": counts, "items": items}
                print(f"ok    {u}  errors={counts['error']} contrast={counts['contrast']} alerts={counts['alert']}")
            except Exception as e:  # record, never zero-fill
                w.writerow([u, "error", *[""] * len(CATS), "", "", "", str(e)[:300]])
                print(f"ERROR {u}  {e}", file=sys.stderr)
            time.sleep(a.delay)
    with open(os.path.splitext(a.out)[0] + "-items.json", "w", encoding="utf-8") as f:
        json.dump(detail, f, indent=2)


if __name__ == "__main__":
    main()
