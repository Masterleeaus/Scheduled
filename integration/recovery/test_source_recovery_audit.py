"""Regression tests for the non-destructive ZIP comparison and template audit."""
import importlib.util, json, os, tempfile, unittest, zipfile
from pathlib import Path
p=Path(__file__).with_name('source_recovery_audit.py')
spec=importlib.util.spec_from_file_location('recovery',p)
m=importlib.util.module_from_spec(spec);spec.loader.exec_module(m)
class RecoveryTests(unittest.TestCase):
 def create(self,files):
  fp=tempfile.NamedTemporaryFile(suffix='.zip',delete=False);fp.close()
  with zipfile.ZipFile(fp.name,'w') as z:
   for name,body in files.items():z.writestr(name,body)
  self.addCleanup(lambda:os.unlink(fp.name))
  return fp.name
 def test_identical_has_no_missing_files(self):
  a=m.members(self.create({'core/a.php':'a'}));self.assertEqual(m.compare(a,a)['historical_only_count'],0)
 def test_detect_missing_historical_file(self):
  a=m.members(self.create({'core/a.php':'a'})); b=m.members(self.create({'core/a.php':'a','core/b.php':'b'}))
  self.assertEqual(m.compare(a,b)['historical_only'],['core/b.php'])
 def test_changed_file_is_not_counted_as_missing(self):
  a=m.members(self.create({'core/a.php':'new'}));b=m.members(self.create({'core/a.php':'old'}))
  self.assertEqual(m.compare(a,b)['changed_common_count'],1)
  self.assertEqual(m.compare(a,b)['historical_only_count'],0)
 def test_reject_traversal(self):
  with self.assertRaises(ValueError):m.members(self.create({'../bad':'x'}))
 def test_reject_duplicate_members(self):
  path=tempfile.NamedTemporaryFile(suffix='.zip',delete=False);path.close();self.addCleanup(lambda:os.unlink(path.name))
  import warnings
  with warnings.catch_warnings():
   warnings.simplefilter('ignore')
   with zipfile.ZipFile(path.name,'w') as z:z.writestr('x','1');z.writestr('x','2')
  with self.assertRaises(ValueError):m.members(path.name)
 def test_template_invalid_accent(self):
  doc={'headline':'a','tagline':'b','accent':'invalid','services':[],'faqs':[]}
  f=self.create({'core/app/Services/Sites/Presets/test.json':json.dumps(doc)})
  class Attached(dict):pass
  d=Attached(m.members(f));d.path=f
  self.assertIn('Invalid accent',m.template_audit(d)[0]['issues'])
if __name__=='__main__':unittest.main()
