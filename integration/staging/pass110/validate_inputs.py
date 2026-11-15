#!/usr/bin/env python3
"""Fail-closed preflight for disposable Cleanto SQL and fixture inputs.

This is input validation, NOT database acceptance and not release authorization.
"""
import argparse
import hashlib
import importlib.util
import json
import re
import sys
from datetime import datetime, timedelta
from pathlib import Path
from zoneinfo import ZoneInfo

EXPECTED_DB='titan_booking_test_acceptance'
CREATE = re.compile(r'(?im)^\s*CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-z0-9_]+)`?\s*\(')
DANGEROUS = re.compile(r'(?im)\b(?:DROP\s+(?:DATABASE|TABLE)|TRUNCATE\s+TABLE|DELETE\s+FROM|CREATE\s+USER|GRANT\s+)\b')
SEED_INSERT = re.compile(r'(?im)^\s*INSERT\s+INTO\s+`?([a-z0-9_]+)`?')
ALLOWED_SEEDS={'ct_services','ct_services_method','ct_service_methods_units','ct_services_methods_units_rate','ct_admin_info','ct_week_days_available'}
NATIVE_REQUIRED={'ct_bookings','ct_order_client_info','ct_services','ct_admin_info','ct_week_days_available'}
LEDGER_REQUIRED={'titan_booking_orders','titan_booking_requests'}
FMT='%Y-%m-%d %H:%M:%S'

class Invalid(ValueError): pass

def checked(path):
    p=Path(path)
    if not p.is_file() or p.is_symlink() or p.stat().st_size<5 or p.stat().st_size>2_000_000:
        raise Invalid('input is missing, symlinked, empty or oversized: '+str(p))
    return p.read_bytes()

def validate(schema_path,seed_path,fixture_dir, manifest_path=None, now=None):
    schema=checked(schema_path); seed=checked(seed_path)
    try: schema_text=schema.decode('utf-8'); seed_text=seed.decode('utf-8')
    except UnicodeError: raise Invalid('SQL is not UTF-8')
    if DANGEROUS.search(schema_text) or DANGEROUS.search(seed_text): raise Invalid('dangerous SQL operation')
    # Pass 112: validate transactional engines, columns and replay primary key.
    contract_path=Path(__file__).resolve().parents[1]/'pass112/validate_transaction_schema.py'
    spec=importlib.util.spec_from_file_location('pass112_transaction_schema',contract_path)
    contract=importlib.util.module_from_spec(spec)
    spec.loader.exec_module(contract)
    try: contract.verify(schema_text)
    except contract.ContractError as exc: raise Invalid('transaction schema contract: '+str(exc)) from exc
    schema_tables=CREATE.findall(schema_text)
    if len(schema_tables)!=38 or len(set(schema_tables))!=38 or not NATIVE_REQUIRED.issubset(schema_tables) or not LEDGER_REQUIRED.issubset(schema_tables):
        raise Invalid('expected 36 native and 2 canonical ledger CREATE statements')
    insert_tables=SEED_INSERT.findall(seed_text)
    if set(insert_tables)!=ALLOWED_SEEDS or len(insert_tables)!=6:
        raise Invalid('seed table allowlist mismatch')
    if re.search(r'(?i)\b(?:UPDATE|ALTER|REPLACE|LOAD\s+DATA)\b',seed_text):
        raise Invalid('seed contains unsupported mutation')
    fixtures={}
    for kind in ('lifecycle','concurrency'):
        p=Path(fixture_dir)/f'{kind}.json'
        try: fixture=json.loads(checked(p))
        except (UnicodeError,json.JSONDecodeError) as exc: raise Invalid('invalid fixture '+kind) from exc
        if fixture.get('database')!=EXPECTED_DB or fixture.get('environment')!='disposable':
            raise Invalid('fixture targets an unexpected database')
        if fixture.get('tenant',{}).get('authenticated_owner_id')!=1:
            raise Invalid('fixture owner mismatch')
        for section in ('create','reschedule'):
            entry=fixture.get(section)
            if not isinstance(entry,dict) or entry.get('staff_ids')!=[1] or entry.get('duration_minutes')!=60 or entry.get('timezone')!='Australia/Melbourne':
                raise Invalid('fixture staff/duration/timezone mismatch')
            try: start=datetime.strptime(entry['start_at'],FMT).replace(tzinfo=ZoneInfo('Australia/Melbourne'))
            except (KeyError,TypeError,ValueError) as exc: raise Invalid('invalid appointment datetime') from exc
            reference=now or datetime.now(ZoneInfo('Australia/Melbourne'))
            if reference.tzinfo is None: raise Invalid('reference time must include timezone')
            if start <= reference + timedelta(hours=24) or start > reference + timedelta(days=90):
                raise Invalid('fixture appointment expired, too near or excessively far in future')
            if start.weekday()>4 or not (8<=start.hour<17) or start.minute!=0 or start.second!=0:
                raise Invalid('fixture appointment outside synthetic Mon-Fri 08:00-18:00 schedule')
            fixtures[f'{kind}.{section}']=start
    for key in ('create','reschedule'):
        a,b=fixtures[f'lifecycle.{key}'],fixtures[f'concurrency.{key}']
        if abs((a-b).total_seconds())<3600: raise Invalid('lifecycle/concurrency windows collide')
    for kind in ('lifecycle','concurrency'):
        if fixtures[f'{kind}.reschedule']<=fixtures[f'{kind}.create']:
            raise Invalid('reschedule must occur after creation')
    if manifest_path:
        try: manifest=json.loads(checked(manifest_path))
        except (UnicodeError,json.JSONDecodeError) as exc: raise Invalid('bad provenance manifest') from exc
        if manifest.get('approved_for_import') is not False or manifest.get('live_mariadb_evidence') is not False:
            raise Invalid('untrusted provenance state')
        expected=manifest.get('output_sha256',{})
        for label,raw in [('cleanto-plus-ledger.REVIEW.sql',schema),('seed-and-schedule.REVIEW.sql',seed)]:
            if expected.get(label)!=hashlib.sha256(raw).hexdigest(): raise Invalid('provenance hash mismatch: '+label)
        for kind in ('lifecycle','concurrency'):
            raw=checked(Path(fixture_dir)/f'{kind}.json')
            if expected.get(f'{kind}.json')!=hashlib.sha256(raw).hexdigest(): raise Invalid('fixture provenance mismatch: '+kind)
    return {'status':'INPUT_PREFLIGHT_ONLY','native_table_count':36,'canonical_ledger_tables':2,'seed_table_count':6,'appointment_windows':4,'import_executed':False,'release_authorized':False}

def main():
    p=argparse.ArgumentParser();p.add_argument('--schema',required=True);p.add_argument('--seed',required=True);p.add_argument('--fixtures',required=True);p.add_argument('--manifest');args=p.parse_args()
    try: print(json.dumps(validate(args.schema,args.seed,args.fixtures,args.manifest),sort_keys=True))
    except (Invalid,OSError) as exc: print('BLOCKED: '+str(exc),file=sys.stderr);return 78
    return 0

if __name__=='__main__': sys.exit(main())
