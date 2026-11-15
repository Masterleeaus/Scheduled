#!/usr/bin/env python3
"""Build a review-only, deterministic native Cleanto disposable database candidate.

Never migrates an existing database, runs SQL, or authorizes booking writes.
"""
import argparse
import hashlib
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
NATIVE_TABLE_PATTERN = re.compile(r'(?im)^CREATE TABLE (?:IF NOT EXISTS )?`([a-z0-9_]+)`\s*\(')
BANNED_SQL = re.compile(r'(?im)\b(?:DROP\s+(?:DATABASE|TABLE)|TRUNCATE|DELETE\s+FROM|UPDATE\s+\w+\s+SET|GRANT|CREATE\s+USER)\b')
EXPECTED_TABLES = ('ct_bookings', 'ct_order_client_info', 'ct_services', 'ct_admin_info', 'ct_week_days_available')


def sha(data):
    return hashlib.sha256(data).hexdigest()


def read(path):
    return Path(path).read_bytes()


def ledger_ddl(php):
    """Obtain only exact hardcoded canonical CREATE TABLE statements, never eval PHP."""
    statements = re.findall(r"'((?:CREATE TABLE IF NOT EXISTS titan_booking_(?:orders|requests) [^']+))'", php)
    if len(statements) != 2 or not statements[0].startswith('CREATE TABLE IF NOT EXISTS titan_booking_orders ') or not statements[1].startswith('CREATE TABLE IF NOT EXISTS titan_booking_requests '):
        raise ValueError('Canonical ledger statements differ from reviewed PHP contract')
    if 'fingerprint CHAR(64)' not in statements[1] or 'state VARCHAR(16)' not in statements[1] or 'command_hash' in statements[1]:
        raise ValueError('Old ledger layout or missing canonical columns')
    return statements


def assemble(root, output):
    root=Path(root)
    source_paths={
        'native': root/'integration/staging/pass104/cleanto-8.5-installer-tables.REVIEW.sql',
        'inventory': root/'integration/staging/pass104/schema_inventory.json',
        'seed': root/'integration/staging/pass106/review_outputs/seed-and-schedule.REVIEW.sql',
        'lifecycle': root/'integration/staging/pass106/review_outputs/lifecycle.json',
        'concurrency': root/'integration/staging/pass106/review_outputs/concurrency.json',
        'sidecar': root/'private/booking-sidecar/BookingWriteSchema.php',
        'contract': root/'private/booking-sidecar/BookingSchemaColumnContract.php',
        'legacy': root/'integration/schema/pass05_reservation_ledger.mysql.sql'
    }
    raw={k:read(p) for k,p in source_paths.items()}
    native=raw['native'].decode('utf-8')
    seed=raw['seed'].decode('utf-8')
    inv=json.loads(raw['inventory'])
    source_tables=NATIVE_TABLE_PATTERN.findall(native)
    inventory_tables={t['name']:set(t['columns']) for t in inv['tables']}
    if len(source_tables)!=36 or len(set(source_tables))!=36 or set(source_tables)!=set(inventory_tables):
        raise ValueError('Native SQL must match all 36 distinct inventoried tables')
    if not all(t in source_tables for t in EXPECTED_TABLES):
        raise ValueError('Native booking tables missing')
    if BANNED_SQL.search(native) or BANNED_SQL.search(seed):
        raise ValueError('Unapproved destructive or administrative SQL')
    if len(source_tables)!=36:
        raise ValueError('Unexpected native CREATE statements')
    if seed.count('INSERT INTO')!=6 or 'INSERT INTO `ct_bookings`' in seed or 'INSERT INTO `ct_payments`' in seed:
        raise ValueError('Synthetic seed contains unexpected writes')
    staff_schedule = re.search(r'(?is)INSERT INTO `ct_week_days_available`[^;]*;', seed)
    if not staff_schedule or len(re.findall(r"\(1,1,[1-5],'08:00:00','18:00:00','N','W'\)",staff_schedule.group()))!=5:
        raise ValueError('Staff schedule is not exactly five synthetic weekdays')
    for name in ('lifecycle','concurrency'):
        fixture=json.loads(raw[name]);
        if fixture.get('database')!='titan_booking_test_acceptance' or fixture.get('environment')!='disposable':
            raise ValueError('Fixture not scoped to disposable database')
        if fixture.get('tenant',{}).get('authenticated_owner_id')!=1:
            raise ValueError('Unapproved tenant fixture')
    if json.loads(raw['lifecycle'])['create']['start_at']==json.loads(raw['concurrency'])['create']['start_at']:
        raise ValueError('Acceptance windows collide')
    php=raw['sidecar'].decode('utf-8')
    canonical=ledger_ddl(php)
    old=raw['legacy'].decode('utf-8')
    if not ('command_hash' in old and 'status ENUM' in old and 'fingerprint' not in old):
        raise ValueError('Historical ledger provenance changed: stop before mixing schemas')
    contract=raw['contract'].decode('utf-8')
    # Fail closed if the contract changes before an engineer reviews the candidate.
    required={
        'ct_bookings':['id','order_id','client_id','booking_date_time','booking_status','staff_ids','service_id','method_id','method_unit_id','method_unit_qty'],
        'ct_order_client_info':['id','order_id','order_duration','client_name','client_email','client_phone'],
        'ct_service_methods_units':['id','services_id','methods_id','minlimit','maxlimit','base_price','status'],
        'ct_services_methods_units_rate':['id','units_id','rules','rates']}
    for table, columns in required.items():
        if not set(columns).issubset(inventory_tables.get(table,set())):
            raise ValueError('Native DDL missing booking writer columns in '+table)
        marker="'"+table+"'=>[" + ','.join("'"+c+"'" for c in columns)+']'
        if marker not in contract: raise ValueError('Writer schema contract changed: '+table)
    schema= native.rstrip()+"\n\n-- Fresh-test-only Titan booking sidecar tables; never apply to an existing tenant.\n"+'\n'.join(s+';' for s in canonical)+'\n'
    if len(NATIVE_TABLE_PATTERN.findall(schema))!=36 or len(re.findall(r'(?m)^CREATE TABLE IF NOT EXISTS titan_booking_',schema))!=2: raise ValueError('Expected 38 distinct CREATE statements')
    out=Path(output)
    out.mkdir(parents=True,exist_ok=True)
    outputs={'cleanto-plus-ledger.REVIEW.sql':schema.encode(), 'seed-and-schedule.REVIEW.sql':raw['seed'],
             'lifecycle.json':raw['lifecycle'],'concurrency.json':raw['concurrency']}
    evidence={'status':'REVIEW_ONLY_NOT_EXECUTED','native_tables':36,'canonical_ledger_tables':2,
              'approved_for_import':False,'migrations_reconciled':False,'live_mariadb_evidence':False,
              'missing_pass58':True,'schema_known_gaps':['Cleanto installer ALTER operations and version-specific updates not validated against MariaDB','No installed/upgraded native schema comparison','Historical ledger is incompatible and requires separate migration'],
              'source_sha256':{k:sha(v) for k,v in raw.items()},
              'output_sha256':{k:sha(v) for k,v in outputs.items()}}
    outputs['REVIEW_MANIFEST.json']=(json.dumps(evidence,indent=2,sort_keys=True)+'\n').encode()
    for name,data in outputs.items():
        (out/name).write_bytes(data)
    return evidence


def main():
    parser=argparse.ArgumentParser(); parser.add_argument('--root',type=Path,default=ROOT);parser.add_argument('--out',type=Path,required=True)
    opts=parser.parse_args(); print(json.dumps(assemble(opts.root,opts.out),indent=2))

if __name__=='__main__': main()
