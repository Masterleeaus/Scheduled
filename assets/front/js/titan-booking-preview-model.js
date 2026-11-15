/* Read-only catalogue and slot response validation. No booking writes are exposed. */
(function (root, factory) {
  'use strict';
  var api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  if (root) root.TitanBookingPreviewModel = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';
  function id(value) { return Number.isSafeInteger(value) && value > 0; }
  function label(value) { return typeof value === 'string' && value.trim().length > 0 && value.length <= 180; }
  function rows(value, max) { return Array.isArray(value) && value.length <= max; }
  function catalogue(data) {
    if (!data || data.preview_only !== true || data.booking_writes_enabled !== false || !rows(data.services, 250) || !rows(data.methods, 1000) || !rows(data.units, 2000) || !rows(data.staff, 500)) throw new Error('Invalid preview catalogue');
    var seen = {}, methods = {}, services = {};
    ['services','methods','units','staff'].forEach(function (kind) {
      seen[kind] = {};
      data[kind].forEach(function (row) {
        if (!row || !id(row.id) || !label(row.label) || seen[kind][row.id]) throw new Error('Invalid '+kind+' entry');
        seen[kind][row.id] = true;
        if (kind === 'services') services[row.id] = true;
        if (kind === 'methods') {
          if (!id(row.service_id) || !services[row.service_id]) throw new Error('Orphan pricing method');
          methods[row.id] = true;
        }
        if (kind === 'units' && (!id(row.method_id) || !methods[row.method_id])) throw new Error('Orphan pricing unit');
      });
    });
    return data;
  }
  function slots(data) {
    if (!data || data.preview_only !== true || data.booking_writes_enabled !== false || data.appointment_created !== false || !rows(data.available_starts, 120)) throw new Error('Unsafe preview response');
    var seen = {};
    return data.available_starts.map(function (value) {
      if (typeof value !== 'string' || value.length > 48 || !/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(?:[+-]\d\d:\d\d|Z)$/.test(value) || !Number.isFinite(Date.parse(value)) || seen[value]) throw new Error('Invalid slot');
      seen[value] = true;
      return value;
    });
  }
  function dateWithinWindow(value, today, days) {
    if (typeof value !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(value)) return false;
    var date = new Date(value+'T12:00:00Z');
    if (!Number.isFinite(date.getTime()) || date.toISOString().slice(0,10) !== value) return false;
    var start = new Date(today+'T12:00:00Z');
    return date >= start && (date-start) <= days*86400000;
  }
  // Determine a booking calendar day in the selected business timezone instead of
  // the visitor's device timezone. Never infer a time zone from an offset alone.
  function todayInZone(timezone, now) {
    if(typeof timezone!=='string'||!timezone||timezone.length>80) throw new Error('Invalid business timezone');
    var stamp=now===undefined?new Date():new Date(now);
    if(!Number.isFinite(stamp.getTime()))throw new Error('Invalid clock');
    var dateParts=new Intl.DateTimeFormat('en-CA',{timeZone:timezone,year:'numeric',month:'2-digit',day:'2-digit'}).formatToParts(stamp);
    var values={};dateParts.forEach(function(p){if(['year','month','day'].indexOf(p.type)!==-1)values[p.type]=p.value;});
    if(!values.year||!values.month||!values.day)throw new Error('Invalid timezone date');
    return values.year+'-'+values.month+'-'+values.day;
  }
  function readableBookingMode(commitEnabled){
    return commitEnabled===true?'Reservation requests available':'Availability check only';
  }
  return { catalogue: catalogue, slots: slots, dateWithinWindow: dateWithinWindow, todayInZone: todayInZone, readableBookingMode:readableBookingMode };
});
