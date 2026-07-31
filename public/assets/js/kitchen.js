async function cacheKitchenOrders(orders) {
    for (const o of orders) {
        const localKey = o.client_id || 'srv_' + o.id;
        const existing = await posDb.orders.get(localKey);
        if (!existing) {
            await posDb.orders.put({ ...o, local_key: localKey });
        }
    }
}

async function markOrderReadyOffline(orderId, clientId) {
    const localKey = clientId || 'srv_' + orderId;
    await posDb.orders.update(localKey, { status: 'ready' });
    await enqueueSync('kitchen-mark-ready', {
        order_id: orderId || undefined,
        order_client_id: clientId || undefined,
    });
}

function wireMarkReadyLinks(selector) {
    document.querySelectorAll(selector).forEach((link) => {
        link.addEventListener('click', async (e) => {
            if (navigator.onLine) {
                return; // online: let the normal server link navigation happen unchanged
            }
            e.preventDefault();
            const orderId = link.dataset.orderId ? Number(link.dataset.orderId) : null;
            const clientId = link.dataset.clientId || null;
            await markOrderReadyOffline(orderId, clientId);

            const card = link.closest('.col-md-6, .col-lg-4, .card-body');
            if (card && card.classList) {
                card.closest('.col-md-6, .col-lg-4')?.classList.add('d-none');
            }
            link.textContent = 'Marked ready (offline) — will sync';
            link.classList.add('disabled');
        });
    });
}

window.kitchenOffline = { cacheKitchenOrders, markOrderReadyOffline, wireMarkReadyLinks };
