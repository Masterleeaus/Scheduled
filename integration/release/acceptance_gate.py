#!/usr/bin/env python3
"""Fail-closed production acceptance gate: no score or PASS from source presence alone.
Requires independently authored, verifiable evidence records for the 6 external integration gates.
"""
import argparse, json, sys
from datetime import datetime, timezone
from pathlib import Path
GATES={
 'mariadb_migration': 'Laravel and Cleanto migrations applied and rollback tested on real MariaDB',
 'booking_concurrency':'Real concurrent reservations: one commit for overlapping slot, idempotent retry',
 'template_browser':'All 15 websites + 10 landings: editing, saving, reload and publishing browser verified',
 'ai_provider':'Configured provider: AI copy, branding approval, generated image and persistence verified',
 'stripe_settlement':'Stripe test mode: checkout, signed webhook, provider readback, refunds reconciled',
 'tenant_isolation':'Authenticated tenant A/B cross-resource access and domain tests passed',
}
def evaluate(report):
    results={};issues=[]
    for name,description in GATES.items():
        evidence=report.get('gates',{}).get(name,{})
        required=('passed','executed_at','environment','artifact_sha256','operator','evidence_path')
        missing=[f for f in required if not evidence.get(f)]
        if evidence.get('passed') is not True or missing:
            results[name]={'accepted':False,'missing_fields':missing,'description':description}
            issues.append(name)
        else:
            try:
                dt=datetime.fromisoformat(evidence['executed_at'].replace('Z','+00:00'))
                fresh=(datetime.now(timezone.utc)-dt.astimezone(timezone.utc)).days<=30
            except Exception:fresh=False
            sha=evidence.get('artifact_sha256','')
            artifact=Path(evidence['evidence_path'])
            if not (fresh and len(sha)==64 and all(c in '0123456789abcdef' for c in sha.lower()) and artifact.exists()):
                results[name]={'accepted':False,'description':description,'reason':'Evidence stale, invalid hash or missing file'};issues.append(name)
            else:
                import hashlib
                actual=hashlib.sha256(artifact.read_bytes()).hexdigest()
                ok=actual.lower()==sha.lower()
                results[name]={'accepted':ok,'description':description,'reason':'Validated evidence hash' if ok else 'Evidence hash mismatch'}
                if not ok:issues.append(name)
    return {'schema':'plusagency.acceptance.v1','production_ready':not issues,'blocked_gates':issues,'gates':results}
def main():
    p=argparse.ArgumentParser();p.add_argument('--evidence',required=True);p.add_argument('--output');a=p.parse_args()
    result=evaluate(json.loads(Path(a.evidence).read_text()))
    output=json.dumps(result,indent=2)+'\n';print(output,end='')
    if a.output:Path(a.output).write_text(output)
    return 0 if result['production_ready'] else 3
if __name__=='__main__':sys.exit(main())
