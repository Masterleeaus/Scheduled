import { test } from 'node:test';
import assert from 'node:assert/strict';
import { NonceStore } from '../src/nonce-store.js';

test('an issued nonce is consumed exactly once', () => {
  const store = new NonceStore({ now: () => 100 });
  const nonce = store.issue();
  assert.equal(store.consume(nonce), true);
  assert.equal(store.consume(nonce), false);
});

test('unknown and empty nonces are refused', () => {
  const store = new NonceStore({ now: () => 100 });
  assert.equal(store.consume('unknown'), false);
  assert.equal(store.consume(''), false);
  assert.equal(store.consume(undefined), false);
});

test('a nonce expires after its TTL and is never accepted late', () => {
  let t = 100;
  const store = new NonceStore({ ttlSeconds: 60, now: () => t });
  const nonce = store.issue();
  t = 160;
  assert.equal(store.consume(nonce), false);
  assert.equal(store.consume(nonce), false);
});

test('issued nonces are unique and TTL is capped at 300 seconds', () => {
  const store = new NonceStore({ now: () => 0 });
  assert.notEqual(store.issue(), store.issue());
  assert.throws(() => new NonceStore({ ttlSeconds: 301 }), RangeError);
  assert.throws(() => new NonceStore({ ttlSeconds: 0 }), RangeError);
});
