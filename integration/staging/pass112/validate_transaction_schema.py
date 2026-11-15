#!/usr/bin/env python3
"""Conservative static contract verification for *fresh* Cleanto acceptance SQL.

No SQL is executed. This is NOT evidence of a working MariaDB schema, an upgraded
Cleanto installation, or a booking commit. SQL outside the supported CREATE syntax
is rejected for the four required transactional tables.
"""
import argparse
import json
import re
from pathlib import Path

class ContractError(ValueError):
    pass

REQUIRED = {
    'ct_bookings': {
        'id': r'^int\s*\(\s*\d+\s*\)\s+not\s+null\s+auto_increment\b',
        'order_id': r'^(?:bigint|int)\s*\(\s*\d+\s*\)\s+not\s+null\b',
        'client_id': r'^(?:bigint|int)\s*\(\s*\d+\s*\)\s+not\s+null\b',
        'booking_date_time': r'^datetime\s+not\s+null\b',
        'booking_status': r'^enum\s*\(',
        'staff_ids': r'^varchar\s*\(\s*160\s*\)(?:\s|$)',
    },
    'ct_order_client_info': {
        'id': r'^int\s*\(\s*\d+\s*\)\s+not\s+null\s+auto_increment\b',
        'order_id': r'^int\s*\(\s*\d+\s*\)\s+not\s+null\b',
        'client_email': r'^varchar\s*\(\s*200\s*\)\s+not\s+null\b',
        'client_phone': r'^varchar\s*\(\s*15\s*\)\s+not\s+null\b',
        'client_personal_info': r'^text\s+not\s+null\b',
    },
    'titan_booking_orders': {
        'id': r'^bigint\s+unsigned\s+not\s+null\s+auto_increment\s+primary\s+key\b',
    },
    'titan_booking_requests': {
        'request_key': r'^varchar\s*\(\s*96\s*\).*?\bnot\s+null\s+primary\s+key\b',
        'fingerprint': r'^char\s*\(\s*64\s*\)',
        'state': r'^varchar\s*\(\s*16\s*\)\s+not\s+null\b',
        'booking_id': r'^bigint\s+unsigned\b',
    },
}
HEADER = re.compile(r'\bCREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-z_][a-z0-9_]*)`?\s*\(', re.I)
IDENT = re.compile(r'^`?([a-z_][a-z0-9_]*)`?\s+(.+)$', re.I | re.S)


def scan_until_close(sql, start):
    """Return the end of the outermost (...) respecting SQL strings/backticks."""
    depth, quote, escaped = 1, None, False
    for i in range(start, len(sql)):
        c = sql[i]
        if quote:
            if escaped:
                escaped = False
            elif c == '\\':
                escaped = True
            elif c == quote:
                if i + 1 < len(sql) and sql[i + 1] == quote:
                    continue
                quote = None
        elif c in ("'", '"', '`'):
            quote = c
        elif c == '(':
            depth += 1
        elif c == ')':
            depth -= 1
            if depth == 0:
                return i
    raise ContractError('unclosed CREATE TABLE parentheses')


def split_definitions(body):
    parts, start, depth, quote, escaped = [], 0, 0, None, False
    for i, c in enumerate(body):
        if quote:
            if escaped:
                escaped = False
            elif c == '\\':
                escaped = True
            elif c == quote:
                quote = None
        elif c in ("'", '"', '`'):
            quote = c
        elif c == '(':
            depth += 1
        elif c == ')':
            depth -= 1
            if depth < 0:
                raise ContractError('invalid definition nesting')
        elif c == ',' and depth == 0:
            parts.append(body[start:i].strip())
            start = i + 1
    if depth or quote:
        raise ContractError('invalid definition syntax')
    parts.append(body[start:].strip())
    if not all(parts):
        raise ContractError('empty schema definition')
    return parts


def create_statements(sql):
    statements = {}
    for m in HEADER.finditer(sql):
        name = m.group(1).lower()
        close = scan_until_close(sql, m.end())
        # Limit engine detection strictly to the tail of this CREATE statement.
        end = sql.find(';', close)
        if end < 0 or end - close > 400:
            raise ContractError('missing/oversized CREATE tail: ' + name)
        tail = sql[close + 1:end]
        engine = re.search(r'\bENGINE\s*=\s*([a-z0-9_]+)\b', tail, re.I)
        if name in statements:
            raise ContractError('duplicate CREATE TABLE: ' + name)
        if not engine:
            raise ContractError('missing storage engine: ' + name)
        statements[name] = (split_definitions(sql[m.end():close]), engine.group(1).lower())
    return statements


def verify(sql):
    statements = create_statements(sql)
    for table, required in REQUIRED.items():
        if table not in statements:
            raise ContractError('missing booking table: ' + table)
        entries, engine = statements[table]
        if engine != 'innodb':
            raise ContractError('nontransactional booking table: ' + table)
        columns, keys = {}, []
        for entry in entries:
            if re.match(r'^(?:PRIMARY\s+KEY|UNIQUE\s+(?:KEY|INDEX)|KEY|INDEX|CONSTRAINT|FOREIGN\s+KEY)\b', entry, re.I):
                keys.append(entry)
                continue
            m = IDENT.match(entry)
            if not m:
                raise ContractError('unrecognized column in ' + table)
            name, definition = m.group(1).lower(), m.group(2)
            if name in columns:
                raise ContractError('duplicate column ' + table + '.' + name)
            columns[name] = definition
        for name, pattern in required.items():
            if not re.search(pattern, columns.get(name, ''), re.I | re.S):
                raise ContractError('incompatible column ' + table + '.' + name)
        key_name = 'request_key' if table == 'titan_booking_requests' else 'id'
        inline = bool(re.search(r'\bPRIMARY\s+KEY\b', columns[key_name], re.I))
        separate = any(re.fullmatch(r'PRIMARY\s+KEY\s*\(\s*`?' + key_name + r'`?\s*\)(?:\s+USING\s+BTREE)?', k, re.I) for k in keys)
        if not (inline or separate):
            raise ContractError('missing standalone primary key: ' + table + '.' + key_name)
    return {
        'status': 'STATIC_SCHEMA_CONTRACT_ONLY',
        'transactional_tables_verified': len(REQUIRED),
        'mysql_import_executed': False,
        'booking_transaction_verified': False,
        'release_authorized': False,
    }


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--schema', required=True, type=Path)
    args = parser.parse_args()
    try:
        data = args.schema.read_bytes()
        if not 10 <= len(data) <= 2_000_000:
            raise ContractError('SQL input outside reviewed bounds')
        print(json.dumps(verify(data.decode('utf-8')), sort_keys=True))
    except (OSError, UnicodeError, ContractError) as exc:
        parser.exit(78, 'BLOCKED: ' + str(exc) + '\n')

if __name__ == '__main__':
    main()
