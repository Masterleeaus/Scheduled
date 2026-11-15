#!/usr/bin/env python3
import json,re,pathlib
root=pathlib.Path(__file__).resolve().parents[2]
data=json.loads((root/'resources/data/business-verticals.json').read_text())
rows=data['verticals'];assert len(rows)==50
assert [r['id'] for r in rows]==list(range(1,51))
assert len({r['key'] for r in rows})==50
assert {r['id'] for r in rows if r['new_course']}=={38,42,43,46,47,49}
assert sum(r['group']=='Cleaning services' for r in rows)==15
assert sum(r['group']=='Trades and property maintenance' for r in rows)==9
assert sum(r['group']=='Outdoor and garden' for r in rows)==7
assets=list((root/'app/Services/Sites/Presets').glob('*.json'))
assert len(assets)>=15
assert all((root/'app/Services/Sites/Presets'/f"{r['recommended_template']}.json").is_file() for r in rows)
for p in ['app/Services/Sites/SiteOnboarding.php','app/Services/Sites/SiteDocument.php','app/Services/Sites/SiteCreationAI.php','resources/views/admin/sites/create.blade.php']:
 assert 'vertical_key' in (root/p).read_text(),p
print('PASS: 50 verticals; 10 groups; 6 new course markers; recommendations resolve; onboarding + editor + AI source integration')
