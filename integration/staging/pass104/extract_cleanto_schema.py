#!/usr/bin/env python3
"""Read-only extractor for literal Cleanto 8.5 CREATE TABLE strings.
Does not evaluate PHP, run installer commands, or fabricate table definitions.
Generated DDL is a review candidate, not evidence of a successful migration.
"""
import argparse, hashlib, json, re
from pathlib import Path
PATTERN = re.compile(r'"(CREATE TABLE(?: IF NOT EXISTS)?\s+`(ct_[a-z0-9_]+)`[\s\S]*?)"\s*;', re.I)
TABLE = re.compile(r'CREATE TABLE(?: IF NOT EXISTS)?\s+`(ct_[a-z0-9_]+)`', re.I)
COLUMN = re.compile(r'^\s*`([^`]+)`\s+', re.M)

def extract(source):
    matches=list(PATTERN.finditer(source))
    if len(matches)!=36:
        raise ValueError(f'Unexpected literal table count {len(matches)} (expected 36): review original installer')
    output=[]
    for m in matches:
        sql=m.group(1).strip()
        name=m.group(2)
        if '$' in sql or '\0' in sql or '\\"' in sql:
            raise ValueError(f'Dynamic or escaped DDL in {name}')
        if not sql.endswith(';'): sql+=';'
        if not re.search(r'ENGINE\s*=\s*(InnoDB|MyISAM)', sql, re.I):
            raise ValueError(f'Unknown storage engine for {name}')
        output.append((name,sql))
    names=[x[0] for x in output]
    if len(set(names))!=len(names): raise ValueError('Duplicate table name')
    required={'ct_bookings','ct_order_client_info','ct_payments','ct_services','ct_services_method','ct_service_methods_units','ct_services_methods_units_rate','ct_users'}
    if not required.issubset(names): raise ValueError('Critical booking table absent')
    return output

def produce(source_path, out_dir):
    raw=Path(source_path).read_bytes()
    source=raw.decode('utf-8','replace')
    rows=extract(source)
    out=Path(out_dir);out.mkdir(parents=True,exist_ok=True)
    script='-- REVIEW CANDIDATE: Cleanto installer literal CREATE TABLE declarations only.\n-- NOT equivalent to an installed/upgraded Cleanto 8.5 schema.\n-- Missing installer ALTERs, seed data and version migrations must be reviewed.\n-- SOURCE SHA256 '+hashlib.sha256(raw).hexdigest()+'\n\n'
    script+='\n\n'.join('-- '+name+'\n'+statement for name,statement in rows)+'\n'
    (out/'cleanto-8.5-installer-tables.REVIEW.sql').write_text(script)
    inventory={ 'source':'private/cleanto/class_configure.php', 'source_sha256':hashlib.sha256(raw).hexdigest(), 'table_count':len(rows), 'tables':[{'name':name,'columns':COLUMN.findall(sql)} for name,sql in rows], 'limitations':['Only literal CREATE TABLE statements; not ALTER statements or installer upgrades','No seed rows or database transaction executed','Do not promote or import into production without schema review']}
    (out/'schema_inventory.json').write_text(json.dumps(inventory,indent=2)+'\n')
    return inventory

if __name__=='__main__':
    ap=argparse.ArgumentParser();ap.add_argument('installer_file');ap.add_argument('output_dir');a=ap.parse_args()
    print(json.dumps({'count':produce(a.installer_file,a.output_dir)['table_count']}))
