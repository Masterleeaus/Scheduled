#!/usr/bin/env python3
"""Produce fresh, synthetic booking acceptance fixtures from reviewed Pass 108 inputs.

No network, database or production writes. The resulting bundle is NOT approved
for import or release, and is verified against the existing Pass 110 preflight.
"""
import argparse
import hashlib
import importlib.util
import json
import os
import tempfile
from datetime import datetime, timedelta
from pathlib import Path
from zoneinfo import ZoneInfo

ROOT=Path(__file__).resolve().parents[3]
SOURCE=ROOT/'integration/staging/pass108/review_outputs'
PREFLIGHT=ROOT/'integration/staging/pass110/validate_inputs.py'
FILES=('cleanto-plus-ledger.REVIEW.sql','seed-and-schedule.REVIEW.sql','lifecycle.json','concurrency.json')
ZONE=ZoneInfo('Australia/Melbourne')


def sha(blob):return hashlib.sha256(blob).hexdigest()


def read_reviewed(source):
    """Verify all original Pass 108 outputs before deriving fresh fixtures."""
    source=Path(source)
    manifest=json.loads((source/'REVIEW_MANIFEST.json').read_text())
    if manifest.get('approved_for_import') is not False or manifest.get('live_mariadb_evidence') is not False:
        raise ValueError('untrusted source approval fields')
    original={name:(source/name).read_bytes() for name in FILES}
    for name,blob in original.items():
        if manifest.get('output_sha256',{}).get(name)!=sha(blob):
            raise ValueError('reviewed source hash mismatch: '+name)
    return manifest,original


def next_business_day(day):
    d=day+timedelta(days=1)
    while d.weekday()>4:d+=timedelta(days=1)
    return d


def build(source,dest,now=None):
    source=Path(source);dest=Path(dest)
    stamp=now or datetime.now(ZONE)
    if stamp.tzinfo is None:raise ValueError('current time must be timezone-aware')
    stamp=stamp.astimezone(ZONE)
    manifest,original=read_reviewed(source)
    # Fixed 10:00/14:00 local time avoids ambiguous DST transition hours.
    day=stamp.date()+timedelta(days=7)
    while day.weekday()>4:day=next_business_day(day)
    second=next_business_day(day)
    output={k:original[k] for k in FILES[:2]}
    for kind,hour in (('lifecycle',10),('concurrency',14)):
        obj=json.loads(original[f'{kind}.json'])
        for field,when in (('create',day),('reschedule',second)):
            obj[field]['start_at']=when.strftime('%Y-%m-%d')+f' {hour:02d}:00:00'
        output[f'{kind}.json']=(json.dumps(obj,indent=2,sort_keys=True)+'\n').encode()
    # Keep explicit negative release markers even when the data passes preflight.
    result=dict(manifest)
    result.update(status='REVIEW_ONLY_REFRESHED_NOT_EXECUTED',approved_for_import=False,
                  live_mariadb_evidence=False,migrations_reconciled=False,
                  fixture_generated_at=stamp.isoformat(),
                  refreshed_fixture_dates=[str(day),str(second)],
                  output_sha256={name:sha(blob) for name,blob in output.items()})
    output['REVIEW_MANIFEST.json']=(json.dumps(result,indent=2,sort_keys=True)+'\n').encode()
    if dest.exists() and any(dest.iterdir()):raise ValueError('destination must be empty: '+str(dest))
    dest.mkdir(parents=True,exist_ok=True)
    try:
        for name,blob in output.items():
            target=dest/name
            with target.open('xb') as f:f.write(blob)
            os.chmod(target,0o600)
        spec=importlib.util.spec_from_file_location('pass110_validation',PREFLIGHT)
        mod=importlib.util.module_from_spec(spec);spec.loader.exec_module(mod)
        verification=mod.validate(dest/FILES[0],dest/FILES[1],dest,dest/'REVIEW_MANIFEST.json',stamp)
        if verification.get('status')!='INPUT_PREFLIGHT_ONLY':raise ValueError('unexpected validation status')
    except Exception:
        for name in (*FILES,'REVIEW_MANIFEST.json'):
            (dest/name).unlink(missing_ok=True)
        raise
    return {'status':'REVIEW_ONLY_REFRESHED_NOT_EXECUTED','dates':result['refreshed_fixture_dates'],
            'preflight':verification,'release_authorized':False}


def main():
    p=argparse.ArgumentParser();p.add_argument('--source',type=Path,default=SOURCE)
    p.add_argument('--out',required=True,type=Path);args=p.parse_args()
    print(json.dumps(build(args.source,args.out),indent=2))

if __name__=='__main__':main()
