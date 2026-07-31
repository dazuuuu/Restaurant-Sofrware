const posDb = new Dexie('pos_offline');

posDb.version(1).stores({
    session: 'key',
    pin_cache: 'userId',
    staff_roster: 'id, role',
    menu_items: 'id, category',
    services_cache: 'id, category',
    orders: 'local_key, id, client_id, status, table_number',
    order_items: 'local_key, order_local_key, order_id, order_client_id',
    bookings: 'local_key, id, client_id, status',
    sync_queue: '++localId, status, created_at',
});

function uuid() {
    if (window.crypto && window.crypto.randomUUID) {
        return window.crypto.randomUUID();
    }
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}

async function getSession() {
    return posDb.session.get('current');
}

async function setSession(session) {
    return posDb.session.put({ key: 'current', ...session });
}

async function clearSession() {
    return posDb.session.delete('current');
}

async function enqueueSync(action, payload) {
    const localId = await posDb.sync_queue.add({
        action,
        payload,
        status: 'pending',
        attempts: 0,
        created_at: new Date().toISOString(),
    });
    if (window.posSync && typeof window.posSync.flush === 'function') {
        window.posSync.flush();
    }
    return localId;
}

async function cacheReferenceData({ menu_items, services, staff_roster }) {
    if (menu_items) {
        await posDb.menu_items.clear();
        await posDb.menu_items.bulkPut(menu_items);
    }
    if (services) {
        await posDb.services_cache.clear();
        await posDb.services_cache.bulkPut(services);
    }
    if (staff_roster) {
        await posDb.staff_roster.clear();
        await posDb.staff_roster.bulkPut(staff_roster);
    }
}
