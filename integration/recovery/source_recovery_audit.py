#!/usr/bin/env python3
"""Read-only source lineage and template-integrity audit for PlusAgency/Cleanto.
No extraction, execution, database modification or filesystem writes except the requested report.
"""
import argparse, collections, datetime, hashlib, json, os, re, sys, zipfile
from pathlib import PurePosixPath

def members(path):
    with zipfile.ZipFile(path) as z:
        invalid=z.testzip()
        if invalid: raise ValueError(f'ZIP CRC failed: {invalid}')
        result={}
        for i in z.infolist():
            if i.is_dir(): continue
            name=i.filename.replace('\\','/')
            p=PurePosixPath(name)
            if p.is_absolute() or '..' in p.parts or name in result:
                raise ValueError(f'Unsafe or duplicate archive member: {name}')
            result[name]={'crc32':f'{i.CRC:08x}','size':i.file_size}
        return result

def compare(current, historic):
    current_names=set(current); historic_names=set(historic)
    diff=sorted(k for k in current_names & historic_names if current[k] != historic[k])
    return {'historical_only':sorted(historic_names-current_names),
      'current_only_count':len(current_names-historic_names), 'changed_common':diff,
      'changed_common_count':len(diff),'historical_file_count':len(historic_names),
      'historical_only_count':len(historic_names-current_names)}

def template_audit(z):
    prefix='core/app/Services/Sites/Presets/'
    presets=sorted(k for k in z if k.startswith(prefix) and k.endswith('.json'))
    candidates={os.path.basename(k)[:-5]:k for k in presets}
    records=[]
    with zipfile.ZipFile(z.path) as archive:
        for key,path in sorted(candidates.items()):
            row={'key':key,'source':path,'valid_json':False,'top_level_fields':[], 'issues':[]}
            try:
                doc=json.loads(archive.read(path))
                if not isinstance(doc,dict): row['issues'].append('Not an object')
                else:
                    row['valid_json']=True; row['top_level_fields']=sorted(doc)
                    for req in ['headline','tagline','accent']:
                        if not isinstance(doc.get(req),str) or not doc[req].strip(): row['issues'].append('Missing '+req)
                    if not re.fullmatch('#[0-9a-fA-F]{6}',str(doc.get('accent',''))):row['issues'].append('Invalid accent')
                    for field in ('services','faqs'):
                        if not isinstance(doc.get(field),list):row['issues'].append('Missing '+field+' list')
            except (ValueError,UnicodeError) as exc: row['issues'].append('JSON error: '+str(exc))
            records.append(row)
    return records

def main():
    ap=argparse.ArgumentParser();ap.add_argument('current');ap.add_argument('--historical',nargs='*',default=[]);ap.add_argument('--output');args=ap.parse_args()
    current=members(args.current);audit={'schema_version':1,'generated_utc':datetime.datetime.now(datetime.timezone.utc).isoformat(),
      'current_archive':os.path.basename(args.current),'current_file_count':len(current),
      'historical_comparisons':{},'templates':[],'warnings':[]}
    for historic in args.historical:
        historical=members(historic)
        audit['historical_comparisons'][os.path.basename(historic)]=compare(current,historical)
    # Make path visible to template_audit without creating/extracting files
    class AttachedDict(dict):pass
    d=AttachedDict(current);d.path=args.current
    audit['templates']=template_audit(d)
    audit['template_count']=len(audit['templates'])
    audit['template_invalid_count']=sum(bool(x['issues']) for x in audit['templates'])
    code='core/app/Services/Sites/SiteDocument.php'
    with zipfile.ZipFile(args.current) as z:
        if code in current:
            document=z.read(code).decode('utf8','replace')
            audit['editor_schema']={'has_section_tree':bool(re.search(r"['\"]sections['\"]\s*=>",document)),
              'has_fixed_allowlist':"$allowed=" in document,
              'has_document_size_limit':('65535' in document)}
        imported_prefix='core/public/assets/sites/imported/'
        audit['imported_assets']={}
        for k in current:
            if k.startswith(imported_prefix):
                parts=k[len(imported_prefix):].split('/')
                if len(parts)>1 and parts[1]:audit['imported_assets'][parts[0]]=audit['imported_assets'].get(parts[0],0)+1
    payload=json.dumps(audit,indent=2,sort_keys=True)+'\n'
    if args.output:
        with open(args.output,'w',encoding='utf8') as f:f.write(payload)
    else:print(payload)
    return 0 if audit['template_invalid_count']==0 else 2
if __name__=='__main__':sys.exit(main())
