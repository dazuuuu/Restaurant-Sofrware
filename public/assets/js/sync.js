let syncInFlight = false;

async function apiCall(action, payload, deviceToken) {
    const cleanPayload = {};
    Object.entries({ action, ...payload }).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            cleanPayload[key] = value;
        }
    });
    const body = new URLSearchParams(cleanPayload);
    const response = await fetch('api.php', {
        method: 'POST',
        headers: deviceToken ? { 'X-Device-Token': deviceToken } : {},
        body,
    });
    const json = await response.json();
    if (!response.ok || !json.ok) {
        const err = new Error(json.error || 'Request failed');
        err.status = response.status;
        err.permanent = response.status >= 400 && response.status < 500 && response.status !== 401 && response.status !== 429;
        throw err;
    }
    return json.data;
}

async function reconcile(action, payload, data) {
    if (action === 'order-create' && data.order) {
        await posDb.orders.update(payload.client_id, {
            id: data.order.id,
            order_code: data.order.order_code,
            status: data.order.status,
            total_amount: data.order.total_amount,
        });
    }

    if (action === 'order-add-item' && data.order) {
        const orderLocalKey = data.order.client_id || payload.order_client_id;
        await posDb.orders.update(orderLocalKey, {
            id: data.order.id,
            status: data.order.status,
            total_amount: data.order.total_amount,
        });

        if (data.items) {
            await posDb.order_items.where('order_local_key').equals(orderLocalKey).delete();
            for (const item of data.items) {
                await posDb.order_items.put({ ...item, local_key: 'srv_' + item.id, order_local_key: orderLocalKey });
            }
        }
    }

    if ((action === 'order-complete' || action === 'kitchen-mark-ready') && data.order) {
        const orderLocalKey = data.order.client_id || payload.order_client_id;
        if (orderLocalKey) {
            await posDb.orders.update(orderLocalKey, {
                id: data.order.id,
                status: data.order.status,
            });
        }
    }

    if (action === 'booking-create' && data.booking) {
        await posDb.bookings.update(payload.client_id, {
            id: data.booking.id,
            booking_code: data.booking.booking_code,
            status: data.booking.status,
        });
    }
}

async function flushSyncQueue() {
    if (syncInFlight || !navigator.onLine) {
        return;
    }
    syncInFlight = true;

    try {
        const session = await getSession();
        const deviceToken = session ? session.deviceToken : null;

        const pending = await posDb.sync_queue.where('status').equals('pending').sortBy('localId');
        for (const entry of pending) {
            try {
                const data = await apiCall(entry.action, entry.payload, deviceToken);
                await reconcile(entry.action, entry.payload, data);
                await posDb.sync_queue.delete(entry.localId);
            } catch (err) {
                if (err.permanent) {
                    await posDb.sync_queue.update(entry.localId, {
                        status: 'failed',
                        error: err.message,
                    });
                    continue;
                }
                // Network/server error: stop here, preserve order, retry later.
                console.warn('Sync paused, will retry:', entry.action, err.message);
                break;
            }
        }
    } finally {
        syncInFlight = false;
        broadcastPendingCount();
    }
}

async function broadcastPendingCount() {
    const count = await posDb.sync_queue.where('status').equals('pending').count();
    const badge = document.getElementById('sync-status');
    if (badge) {
        badge.textContent = count > 0 ? count + ' change' + (count === 1 ? '' : 's') + ' pending sync' : '';
        badge.classList.toggle('d-none', count === 0);
    }
    document.dispatchEvent(new CustomEvent('pos-sync-queue-changed', { detail: { count } }));
}

window.posSync = {
    flush: flushSyncQueue,
    pendingCount: () => posDb.sync_queue.where('status').equals('pending').count(),
};

window.addEventListener('online', flushSyncQueue);
document.addEventListener('DOMContentLoaded', () => {
    flushSyncQueue();
    broadcastPendingCount();
});
