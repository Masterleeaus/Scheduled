#!/usr/bin/env python3
import sys,unittest,tempfile,json
from pathlib import Path
sys.path.insert(0,str(Path(__file__).parent))
from inventory_native_upgrades import generate as upgrades
from generate_synthetic_seed import generate as seed, ROWS
ROOT=Path(__file__).resolve().parents[3]
class Test(unittest.TestCase):
 def test_upgrade_inventory(self):
  with tempfile.TemporaryDirectory() as d:
   r=upgrades(ROOT/'private/cleanto/class_configure.php',ROOT/'private/cleanto/objects/class_version_update.php',ROOT/'integration/staging/pass104/schema_inventory.json',d)
   self.assertEqual(len(r['sources']),2)
   self.assertGreaterEqual(r['sources'][0]['statement_count'],30)
   self.assertGreaterEqual(r['sources'][1]['statement_count'],50)
   self.assertTrue(any(x['operation']=='ALTER' for x in r['sources'][1]['statements']))
   self.assertEqual(r['classification'],'REVIEW_ONLY_NOT_APPLIED')
   self.assertTrue(Path(d,'upgrade_inventory.json').is_file())
 def test_seed_tables_and_columns(self):
  sql=seed(ROOT/'integration/staging/pass104/schema_inventory.json')
  self.assertEqual(sql.count('INSERT INTO'),len(ROWS))
  self.assertIn('ct_services_methods_units_rate',sql)
  self.assertNotIn('ct_bookings`',sql)
  self.assertNotIn('ct_payments`',sql)
 def test_fail_closed_on_invalid_columns(self):
  with tempfile.TemporaryDirectory() as d:
   p=Path(d,'schema.json');p.write_text(json.dumps({'tables':[{'name':'ct_services','columns':['id']}]}))
   with self.assertRaises(ValueError):seed(p)
if __name__=='__main__':unittest.main(verbosity=2)
