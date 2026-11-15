#!/usr/bin/env python3
"""Inventory native Cleanto installer and historical upgrade SQL without executing PHP.
Review-only: historical ALTERs are not safe to replay in arbitrary order.
"""
import argparse,hashlib,json,re
from pathlib import Path
ASSIGN=re.compile(r'\$([A-Za-z_][A-Za-z_0-9]*)\s*=\s*(["\'])(.*?)\2\s*;',re.S)
SQL=re.compile(r'^\s*(ALTER\s+TABLE|CREATE\s+TABLE|INSERT\s+INTO|UPDATE\s+`?ct_|DELETE\s+FROM\s+`?ct_)\b',re.I)
TABLE=re.compile(r'(?:ALTER\s+TABLE|CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?|INSERT\s+INTO|UPDATE|DELETE\s+FROM)\s+`?(ct_[a-z0-9_]+)`?',re.I)
ADD=re.compile(r'\bADD\s+(?:COLUMN\s+)?`([a-z_0-9]+)`',re.I)
CHANGE=re.compile(r'\b(?:CHANGE|MODIFY)\s+(?:COLUMN\s+)?`([a-z_0-9]+)`',re.I)
DROP=re.compile(r'\bDROP\s+(?:COLUMN\s+)?`([a-z_0-9]+)`',re.I)
def inventory(php_path, base_columns):
    raw=Path(php_path).read_bytes();src=raw.decode('utf-8','replace')
    rows=[]
    for m in ASSIGN.finditer(src):
        value=m.group(3)
        if not SQL.search(value):continue
        operation=SQL.search(value).group(1).upper().split()[0]
        tbl=TABLE.search(value)
        if not tbl: continue
        table=tbl.group(1).lower()
        columns={'add':ADD.findall(value),'change':CHANGE.findall(value),'drop':DROP.findall(value)}
        already=sorted(set(x.lower() for x in columns['add']) & set(base_columns.get(table,[])))
        rows.append({'variable':m.group(1),'operation':operation,'table':table,
           'line':src.count('\n',0,m.start())+1,'sql_sha256':hashlib.sha256(value.encode()).hexdigest(),
           'columns':columns,'add_columns_already_in_base':already,
           'dynamic_or_escaped':bool(re.search(r'(?<!\\)\$[A-Za-z_]|\\["\']',value))})
    return {'file':str(php_path),'sha256':hashlib.sha256(raw).hexdigest(),'statement_count':len(rows),'statements':rows}
def generate(installer,updater,schema,outdir):
    cols={t['name']:t['columns'] for t in json.loads(Path(schema).read_text())['tables']}
    sources=[inventory(installer,cols),inventory(updater,cols)]
    result={'classification':'REVIEW_ONLY_NOT_APPLIED','sources':sources,
     'notes':['The baseline CREATE DDL may already include columns introduced by historical upgrades.',
      'SQL embedded in expressions or dynamically built strings is NOT automatically extracted.',
      'Do not replay this inventory against production or assume that version-specific upgrades are ordered.',
      'Foreign keys, state-dependent updates and data migrations require separate human review.']}
    out=Path(outdir);out.mkdir(parents=True,exist_ok=True)
    (out/'upgrade_inventory.json').write_text(json.dumps(result,indent=2)+'\n')
    return result
if __name__=='__main__':
    p=argparse.ArgumentParser();p.add_argument('installer');p.add_argument('updater');p.add_argument('schema_inventory');p.add_argument('output_dir');a=p.parse_args()
    result=generate(a.installer,a.updater,a.schema_inventory,a.output_dir)
    print(json.dumps({s['file']:s['statement_count'] for s in result['sources']}))
