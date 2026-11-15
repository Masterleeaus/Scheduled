#!/usr/bin/env python3
"""Fail-closed checks for distinct historical and canonical ledger layouts."""
import argparse
from pathlib import Path

def audit(old,canonical,native):
    assert 'command_hash' in old and 'status ENUM' in old
    assert 'fingerprint' in canonical and 'state VARCHAR' in canonical
    assert 'command_hash' not in canonical and 'status ENUM' not in canonical
    assert 'ENGINE=InnoDB' in canonical
    assert 'function insert_schedule_weekly()' in native
    assert '"Monday"' in native and '"Sunday"' in native
    return {'fresh_ledger_sql':'integration/schema/pass12_ledger_canonical.mysql.sql',
            'legacy_ledger_sql':'DO_NOT_APPLY: integration/schema/pass05_reservation_ledger.mysql.sql',
            'existing_ledger_migration':'BLOCKED: inspect columns and preserve rows manually',
            'schedule_weekdays':'1=Monday through 7=Sunday from Cleanto source',
            'runtime_certification':False}
if __name__=='__main__':
    p=argparse.ArgumentParser();p.add_argument('schema_dir');p.add_argument('native_source');a=p.parse_args()
    d=Path(a.schema_dir)
    import json
    print(json.dumps(audit((d/'pass05_reservation_ledger.mysql.sql').read_text(),(d/'pass12_ledger_canonical.mysql.sql').read_text(),Path(a.native_source).read_text()),indent=2))
