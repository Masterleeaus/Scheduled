/* Same-origin Laravel booking transport. Does not grant booking authority. */
(function (global) {
  'use strict';
  function token(root) {
    var element = root && root.querySelector ? root.querySelector('input[name="_token"]') : null;
    var meta = global.document && global.document.querySelector ? global.document.querySelector('meta[name="csrf-token"]') : null;
    var value = (element && element.value) || (meta && meta.getAttribute('content')) || '';
    if (typeof value !== 'string' || !/^[A-Za-z0-9]{20,256}$/.test(value)) throw new Error('Booking session token unavailable');
    return value;
  }
  function options(root, payload, signal) {
    var headers = {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token(root)};
    var opts = {method:'POST', credentials:'same-origin', cache:'no-store', headers:headers, body:JSON.stringify(payload)};
    if (signal) opts.signal = signal;
    return opts;
  }
  function checkedResponse(response) {
    if (!response || !response.ok) throw new Error('Booking service unavailable');
    return response.json();
  }
  function confirmed(data) {
    if (!data || data.confirmed !== true || data.payment_settled !== false ||
        typeof data.booking_reference !== 'string' || !/^[0-9]{1,20}$/.test(data.booking_reference)) {
      throw new Error('Booking confirmation proof unavailable');
    }
    return data.booking_reference;
  }
  global.TitanBookingTransport = {options:options, checkedResponse:checkedResponse, confirmed:confirmed};
  if (typeof module !== 'undefined' && module.exports) module.exports = global.TitanBookingTransport;
})(typeof window !== 'undefined' ? window : globalThis);
