#!/usr/bin/env python3
import datetime as dt
import importlib.util
import json
from pathlib import Path
from zoneinfo import ZoneInfo
path=Path(__file__).with_name('build_review_bundle.py')
spec=importlib.util.spec_from_file_location('bundle',path);module=importlib.util.module_from_spec(spec);spec.loader.exec_module(module)
base=Path(__file__).resolve().parents[1]
inv=json.loads((base/'pass104/schema_inventory.json').read_text())
seed=(base/'pass105/synthetic-seed.REVIEW.sql').read_text()
now=dt.datetime(2026,11,10,8,0,tzinfo=ZoneInfo('Australia/Melbourne'))
a,b,sql,meta=module.build(inv,seed,now)
assert a['database']==b['database']==module.DATABASE
assert a['create']['customer']['email'].endswith('@example.invalid')
assert b['create']['customer']['email'].endswith('@example.invalid')
assert a['create']['start_at']!=b['create']['start_at']
assert a['reschedule']['start_at']!=b['reschedule']['start_at']
assert a['create']['timezone']==b['create']['timezone']=='Australia/Melbourne'
assert sql.count('INSERT INTO')==6
assert "ct_week_days_available" in sql
assert not meta['mariadb_import_executed'] and not meta['booking_commit_verified']
assert not meta['schedule_semantics_approved']
try:
 bad=json.loads(json.dumps(inv));next(t for t in bad['tables'] if t['name']=='ct_bookings')['columns'].remove('order_id');module.build(bad,seed,now)
 raise AssertionError('missing order column accepted')
except ValueError:pass
try:
 module.build(inv,seed+'\nINSERT INTO `ct_payments` VALUES (1);',now)
 raise AssertionError('transaction seed accepted')
except ValueError:pass
try:
 module.build(inv,seed.replace('INSERT INTO','INSERT INTO',1)+'\nINSERT INTO `ct_services` VALUES (1);',now)
 raise AssertionError('unexpected seed statement accepted')
except ValueError:pass
print('Pass 106: 12 schema/fixture/safety assertions passed')
