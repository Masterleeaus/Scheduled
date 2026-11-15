#!/usr/bin/env python3
import importlib.util
import json
import tempfile
from datetime import datetime,timezone,timedelta
from pathlib import Path
from zoneinfo import ZoneInfo

P=Path(__file__).with_name('validate_inputs.py')
spec=importlib.util.spec_from_file_location('input_validator',P);m=importlib.util.module_from_spec(spec);spec.loader.exec_module(m)
APP=Path(__file__).resolve().parents[3]
SOURCE=APP/'integration/staging/pass108/review_outputs'

def test():
    with tempfile.TemporaryDirectory() as tmp:
        t=Path(tmp)
        for name in ('cleanto-plus-ledger.REVIEW.sql','seed-and-schedule.REVIEW.sql','lifecycle.json','concurrency.json','REVIEW_MANIFEST.json'):
            (t/name).write_bytes((SOURCE/name).read_bytes())
        schema=t/'cleanto-plus-ledger.REVIEW.sql';seed=t/'seed-and-schedule.REVIEW.sql';manifest=t/'REVIEW_MANIFEST.json'
        today=datetime(2026,11,10,11,0,tzinfo=ZoneInfo('Australia/Melbourne'))
        def check(ok=True,with_manifest=False,now=today):
            try: m.validate(schema,seed,t,manifest if with_manifest else None,now)
            except (m.Invalid,ValueError):
                if ok: raise
                return
            if not ok:raise AssertionError('unsafe fixture unexpectedly accepted')
        check(True,True)
        # Explicitly verify passing valid fixture and substantive negative mutations.
        raw=schema.read_bytes(); schema.write_bytes(raw+b'\nDROP TABLE `ct_bookings`;');check(False);schema.write_bytes(raw)
        schema.write_bytes(raw.replace(b'titan_booking_requests',b'titan_old_requests'));check(False);schema.write_bytes(raw)
        raw_seed=seed.read_text();seed.write_text(raw_seed+'\nINSERT INTO ct_bookings (id) VALUES (4);');check(False);seed.write_text(raw_seed)
        raw_con=(t/'concurrency.json').read_text(); d=json.loads(raw_con);d['database']='production';(t/'concurrency.json').write_text(json.dumps(d));check(False);(t/'concurrency.json').write_text(raw_con)
        d=json.loads(raw_con);d['create']['start_at']='2026-11-10 09:00:00';(t/'concurrency.json').write_text(json.dumps(d));check(False);(t/'concurrency.json').write_text(raw_con)
        d=json.loads(raw_con);d['create']['start_at']='2026-11-21 14:00:00';(t/'concurrency.json').write_text(json.dumps(d));check(False);(t/'concurrency.json').write_text(raw_con)
        d=json.loads(raw_con);d['create']['staff_ids']=[2];(t/'concurrency.json').write_text(json.dumps(d));check(False);(t/'concurrency.json').write_text(raw_con)
        d=json.loads(raw_con);d['create']['start_at']='2026-11-17 10:00:00';(t/'concurrency.json').write_text(json.dumps(d));check(False);(t/'concurrency.json').write_text(raw_con)
        check(False,now=datetime(2026,11,18,12,0,tzinfo=timezone.utc))
        # Manifest must detect a changed seed even if SQL mutation is otherwise permitted.
        seed.write_text(raw_seed+'\n-- modified\n');check(False,True);seed.write_text(raw_seed)
        check(True,True)
        print('PASS 11/11 staging input/preflight scenarios')
if __name__=='__main__': test()
