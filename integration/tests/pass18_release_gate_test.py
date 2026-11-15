import importlib.util, tempfile, unittest, json
from pathlib import Path
base=Path(__file__).parents[1]/'release'
def load(name):
 s=importlib.util.spec_from_file_location(name,base/(name+'.py'));m=importlib.util.module_from_spec(s);s.loader.exec_module(m);return m
class GateTests(unittest.TestCase):
 def test_reject_empty(self):
  r=load('acceptance_gate').evaluate({'gates':{}});self.assertFalse(r['production_ready']);self.assertEqual(6,len(r['blocked_gates']))
 def test_reject_claim_without_artifact(self):
  g={x:{'passed':True,'executed_at':'2026-11-11T00:00:00Z','environment':'staging','artifact_sha256':'a'*64,'operator':'qa','evidence_path':'/no/such/file'} for x in load('acceptance_gate').GATES};self.assertFalse(load('acceptance_gate').evaluate({'gates':g})['production_ready'])
 def test_detect_missing_application(self):
  with tempfile.TemporaryDirectory() as d:
   r=load('preflight').scan(Path(d));self.assertEqual('FAIL',r['source_gate'])
 def test_example_is_fail_closed(self):
  r=load('acceptance_gate').evaluate(json.loads((base/'acceptance_evidence.example.json').read_text()));self.assertEqual(6,len(r['blocked_gates']))
if __name__=='__main__':unittest.main()
