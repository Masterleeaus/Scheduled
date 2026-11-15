#!/usr/bin/env python3
import json
import pathlib
import shutil
import sys
import tempfile
import unittest
from unittest.mock import patch

sys.path.insert(0,str(pathlib.Path(__file__).parent))
from build_candidate import assemble, ledger_ddl

SOURCE=pathlib.Path(__file__).resolve().parents[3]

class CandidateTests(unittest.TestCase):
    def test_canonical_contract(self):
        sql=ledger_ddl((SOURCE/'private/booking-sidecar/BookingWriteSchema.php').read_text())
        self.assertEqual(len(sql),2)
        self.assertIn('fingerprint',sql[1])
    def test_complete_bundle(self):
        with tempfile.TemporaryDirectory() as tmp:
            manifest=assemble(SOURCE,tmp)
            self.assertEqual(manifest['status'],'REVIEW_ONLY_NOT_EXECUTED')
            self.assertFalse(manifest['approved_for_import'])
            self.assertEqual(len(list(pathlib.Path(tmp).iterdir())),5)
            self.assertEqual((pathlib.Path(tmp)/'cleanto-plus-ledger.REVIEW.sql').read_text().count('CREATE TABLE IF NOT EXISTS titan_booking_'),2)
            self.assertEqual(json.loads((pathlib.Path(tmp)/'REVIEW_MANIFEST.json').read_text())['output_sha256']['lifecycle.json'],manifest['output_sha256']['lifecycle.json'])
    def test_reject_old_ledger(self):
        with self.assertRaises(ValueError):ledger_ddl("'CREATE TABLE IF NOT EXISTS titan_booking_requests (command_hash CHAR(64), status ENUM)' ")
    def test_reject_missing_native_table(self):
        self._tamper('integration/staging/pass104/cleanto-8.5-installer-tables.REVIEW.sql',lambda s:s.replace('CREATE TABLE IF NOT EXISTS `ct_bookings`','CREATE TABLE IF NOT EXISTS `ct_bookings_missing`',1))
    def test_reject_seed_real_booking(self):
        self._tamper('integration/staging/pass106/review_outputs/seed-and-schedule.REVIEW.sql',lambda s:s+'\nINSERT INTO `ct_bookings` VALUES (1);\n')
    def test_reject_unapproved_database(self):
        self._tamper('integration/staging/pass106/review_outputs/lifecycle.json',lambda s:s.replace('titan_booking_test_acceptance','production'))
    def test_reject_legacy_ledger_modification(self):
        self._tamper('integration/schema/pass05_reservation_ledger.mysql.sql',lambda s:s.replace('command_hash','fingerprint'))
    def test_reject_schema_contract_drift(self):
        self._tamper('private/booking-sidecar/BookingSchemaColumnContract.php',lambda s:s.replace("'booking_status'", "'booking_status_changed'",1))
    def _tamper(self,relative,fn):
        with tempfile.TemporaryDirectory() as tmp:
            root=pathlib.Path(tmp)
            files=['integration/staging/pass104/cleanto-8.5-installer-tables.REVIEW.sql','integration/staging/pass104/schema_inventory.json','integration/staging/pass106/review_outputs/seed-and-schedule.REVIEW.sql','integration/staging/pass106/review_outputs/lifecycle.json','integration/staging/pass106/review_outputs/concurrency.json','private/booking-sidecar/BookingWriteSchema.php','private/booking-sidecar/BookingSchemaColumnContract.php','integration/schema/pass05_reservation_ledger.mysql.sql']
            for f in files:
                to=root/f;to.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(SOURCE/f,to)
            target=root/relative;target.write_text(fn(target.read_text()))
            with self.assertRaises(ValueError):assemble(root,root/'out')

if __name__=='__main__':unittest.main(verbosity=2)
