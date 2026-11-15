#!/usr/bin/env python3
"""Native-source-backed checks on synthetic Cleanto fixture generation."""
import datetime as dt
import importlib.util
import json
from pathlib import Path
from zoneinfo import ZoneInfo
base=Path(__file__).resolve().parents[1]
spec=importlib.util.spec_from_file_location('bundle',base/'pass106/build_review_bundle.py')
mod=importlib.util.module_from_spec(spec);spec.loader.exec_module(mod)
inv=json.loads((base/'pass104/schema_inventory.json').read_text())
seed=(base/'pass105/synthetic-seed.REVIEW.sql').read_text()
zone=ZoneInfo('Australia/Melbourne')
count=0
for offset in range(14):
 now=dt.datetime(2026,11,9,12,0,tzinfo=zone)+dt.timedelta(days=offset)
 a,b,sql,meta=mod.build(inv,seed,now)
 for f in (a,b):
  for stage in ('create','reschedule'):
   day=dt.datetime.strptime(f[stage]['start_at'],'%Y-%m-%d %H:%M:%S').date()
   assert day.weekday()<5, (now,stage,day)
   assert day>now.date(),(now,stage,day)
   count+=1
  assert f['reschedule']['start_at']>f['create']['start_at']
 assert a['create']['start_at']!=b['create']['start_at']
 assert sql.count('INSERT INTO')==6
 assert meta['mariadb_import_executed'] is False
 count+=3
native=Path(__file__).with_name('native_schedule_source.php').read_text()
assert 'case 1:' in native and '"Monday"' in native
assert 'case 7:' in native and '"Sunday"' in native
assert 'function insert_schedule_weekly()' in native
assert '`week_id`,`weekday_id`' in native
count+=4
print('Pass 107 source-backed fixture assertions passed:', count)
