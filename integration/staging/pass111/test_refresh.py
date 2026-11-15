#!/usr/bin/env python3
import importlib.util
import json
import tempfile
from datetime import datetime
from pathlib import Path
from zoneinfo import ZoneInfo

HERE=Path(__file__).resolve().parent
spec=importlib.util.spec_from_file_location('refresh',HERE/'refresh_fixtures.py')
m=importlib.util.module_from_spec(spec);spec.loader.exec_module(m)
passed=0

def check(cond,msg):
    global passed
    assert cond,msg
    passed+=1

for date in ('2026-11-10 11:00:00','2026-11-13 23:30:00','2026-11-14 09:00:00','2026-12-31 18:00:00','2027-04-03 12:00:00','2027-10-02 12:00:00'):
    with tempfile.TemporaryDirectory() as tmp:
        target=Path(tmp)/'fresh'
        stamp=datetime.fromisoformat(date).replace(tzinfo=ZoneInfo('Australia/Melbourne'))
        result=m.build(m.SOURCE,target,stamp)
        lc=json.loads((target/'lifecycle.json').read_text())
        co=json.loads((target/'concurrency.json').read_text())
        check(result['status']=='REVIEW_ONLY_REFRESHED_NOT_EXECUTED','safe status')
        check(result['preflight']['status']=='INPUT_PREFLIGHT_ONLY','preflight')
        check(lc['create']['start_at'][11:]=='10:00:00' and co['create']['start_at'][11:]=='14:00:00','distinct hours')
        check(lc['create']['start_at'][:10]==co['create']['start_at'][:10],'same day')
        check(lc['reschedule']['start_at']>lc['create']['start_at'],'later reschedule')
        check(result['release_authorized'] is False,'no release authority')
        check((target/'cleanto-plus-ledger.REVIEW.sql').read_bytes()==(m.SOURCE/'cleanto-plus-ledger.REVIEW.sql').read_bytes(),'schema unchanged')
        check((target/'seed-and-schedule.REVIEW.sql').read_bytes()==(m.SOURCE/'seed-and-schedule.REVIEW.sql').read_bytes(),'seed unchanged')
        try:m.build(m.SOURCE,target,stamp)
        except ValueError:passed+=1
        else:raise AssertionError('overwriting an existing bundle was allowed')
with tempfile.TemporaryDirectory() as tmp:
    source=Path(tmp)/'source';source.mkdir()
    for name in (*m.FILES,'REVIEW_MANIFEST.json'):(source/name).write_bytes((m.SOURCE/name).read_bytes())
    (source/'seed-and-schedule.REVIEW.sql').write_bytes(b'ALTER TABLE ct_bookings DROP COLUMN booking_status;')
    try:m.build(source,Path(tmp)/'output',datetime(2026,11,10,tzinfo=ZoneInfo('Australia/Melbourne')))
    except ValueError:passed+=1
    else:raise AssertionError('tampered source accepted')
print(f'PASS {passed}/{passed} fresh-fixture assertions')
