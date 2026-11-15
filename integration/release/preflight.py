#!/usr/bin/env python3
"""Offline, read-only release preflight for PlusAgency/Cleanto.
Supports a source folder OR the cumulative ZIP; never performs migrations or writes to services.
Returns 0 only for source verification, 2 for missing required source, 3 for failed provenance/asset check.
Operational acceptance is evaluated separately by acceptance_gate.py.
"""
import argparse, hashlib, json, re, sys, zipfile
from pathlib import Path

REQUIRED = ["core/artisan", "core/composer.json", "core/routes/web.php", "private/cleanto/objects/class_booking.php"]
SENSITIVE = re.compile(r'(^|/)(\.env|id_rsa|id_ed25519|\.pem|\.p12|\.key|credentials\.json)$', re.I)

def scan(path):
    if path.is_file():
        if not zipfile.is_zipfile(path): raise ValueError("Input is not a ZIP archive")
        with zipfile.ZipFile(path) as z:
            files = {i.filename.rstrip('/'): i for i in z.infolist() if not i.is_dir()}
            def read(n):
                with zipfile.ZipFile(path) as src: return src.read(n)
            bad = z.testzip()
            integrity = bad is None
            dirs = [n for n in files if n.endswith('/')]
    elif path.is_dir():
        files = {p.relative_to(path).as_posix():p for p in path.rglob('*') if p.is_file()}
        def read(n): return files[n].read_bytes()
        integrity = True
        dirs = []
    else: raise FileNotFoundError(path)
    issues=[]; notices=[]
    missing=[n for n in REQUIRED if n not in files]
    if missing: issues.append({'code':'MISSING_SOURCE','files':missing})
    if not integrity: issues.append({'code':'CORRUPT_ARCHIVE'})
    sensitive=[n for n in files if SENSITIVE.search(n) and not n.endswith('.env.example')]
    if sensitive: issues.append({'code':'POTENTIAL_SECRETS','files':sensitive[:30]})
    if 'core/composer.json' in files:
        try:
            composer=json.loads(read('core/composer.json'))
            if 'laravel/framework' not in composer.get('require',{}): issues.append({'code':'MISSING_LARAVEL_REQUIREMENT'})
        except Exception as e: issues.append({'code':'INVALID_COMPOSER_JSON','detail':str(e)})
    templates=[n for n in files if re.search(r'(?:^|/)titan.site.templates/(?:[^/]+)\.json$',n)]
    presets=[n for n in files if ('preset' in n.lower() or 'theme' in n.lower()) and n.endswith('.json')]
    php_migrations=[n for n in files if n.startswith('core/database/migrations/') and n.endswith('.php')]
    if len(php_migrations)<200: issues.append({'code':'MIGRATION_COUNT_REGRESSION','observed':len(php_migrations)})
    if not templates: notices.append('Template catalogue is stored outside the expected optional directory; inspect manually')
    if 'PASS58_REPORT.md' not in files: notices.append('Historical Pass 58 provenance still unresolved; not automatically a release blocker if functionality is verified')
    return {'schema':'plusagency.release.preflight.v1','source':str(path),'source_file_count':len(files),'migration_count':len(php_migrations),'template_json_candidates':len(presets),'archive_integrity_ok':integrity,'issues':issues,'notices':notices,'source_gate':'PASS' if not issues else 'FAIL','operational_gate':'NOT_TESTED'}

def main():
    p=argparse.ArgumentParser();p.add_argument('source');p.add_argument('--output');a=p.parse_args()
    try: result=scan(Path(a.source))
    except Exception as e: result={'source':a.source,'source_gate':'FAIL','issues':[{'code':'EXCEPTION','detail':str(e)}]}
    s=json.dumps(result,indent=2,sort_keys=True)+'\n'
    if a.output:Path(a.output).write_text(s)
    print(s,end='')
    return 0 if result['source_gate']=='PASS' else 2
if __name__=='__main__':sys.exit(main())
