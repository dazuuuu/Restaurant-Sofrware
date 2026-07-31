async function getCurrencySymbol() {
    const session = await getSession();
    return (session && session.currencySymbol) || 'Ksh';
}

function money(symbol, amount) {
    return symbol + Number(amount || 0).toFixed(2);
}

async function upsertOrderFromServer(order, items) {
    const localKey = order.client_id || 'srv_' + order.id;
    await posDb.orders.put({ ...order, local_key: localKey });
    await posDb.order_items.where('order_local_key').equals(localKey).delete();
    for (const item of items || []) {
        await posDb.order_items.put({ ...item, local_key: 'srv_' + item.id, order_local_key: localKey });
    }
    return localKey;
}

async function cancelPendingSyncByClientId(clientId) {
    const rows = await posDb.sync_queue.where('status').equals('pending').toArray();
    for (const row of rows) {
        if (row.payload && row.payload.client_id === clientId) {
            await posDb.sync_queue.delete(row.localId);
        }
    }
}

async function renderOrderWorkspace(containerSelector, orderLocalKey) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    const [order, items, menuItems, symbol] = await Promise.all([
        posDb.orders.get(orderLocalKey),
        posDb.order_items.where('order_local_key').equals(orderLocalKey).toArray(),
        posDb.menu_items.toArray(),
        getCurrencySymbol(),
    ]);
    if (!order) return;

    const itemRows = items.map((item) => {
        const canRemoveNow = true;
        return `<tr data-local-key="${item.local_key}">
            <td>${escapeHtml(item.name || '')}</td>
            <td>${item.quantity}</td>
            <td>${money(symbol, item.unit_price)}</td>
            <td>${money(symbol, item.subtotal)}</td>
            <td><small>${escapeHtml(item.special_instructions || '-')}</small></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-item-btn" ${canRemoveNow ? '' : 'disabled'}>Remove</button></td>
        </tr>`;
    }).join('');

    const menuOptions = menuItems.map((m) =>
        `<option value="${m.id}" data-price="${m.price}">${escapeHtml(m.name)} - ${money(symbol, m.price)}</option>`
    ).join('');

    container.innerHTML = `
        <div class="alert alert-warning">You're offline. Changes are saved on this device and will sync automatically once you're back online.</div>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <strong>${escapeHtml(order.order_code || 'New Order')}</strong> - Table ${escapeHtml(order.table_number)}
                <span class="text-muted">${escapeHtml(order.customer_name || 'Walk-in')}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Subtotal</th><th>Notes</th><th></th></tr></thead>
                        <tbody>${itemRows || '<tr><td colspan="6" class="text-muted">No items yet.</td></tr>'}</tbody>
                    </table>
                </div>
                <hr>
                <form class="add-item-form row g-2">
                    <div class="col-md-5">
                        <select class="form-select" name="menu_item_id" required>
                            <option value="">-- Select Item --</option>
                            ${menuOptions}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="quantity" value="1" min="1" max="99" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="instructions" placeholder="Special instructions">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">Add</button>
                    </div>
                </form>
                <div class="mt-3 h5">Total: <span class="order-total">${money(symbol, order.total_amount)}</span></div>
            </div>
        </div>
    `;

    container.querySelector('.add-item-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const menuItemId = Number(form.menu_item_id.value);
        const quantity = Number(form.quantity.value) || 1;
        const instructions = form.instructions.value;
        const menuItem = menuItems.find((m) => m.id === menuItemId);
        if (!menuItem) return;

        const itemLocalKey = uuid();
        const subtotal = menuItem.price * quantity;
        await posDb.order_items.put({
            local_key: itemLocalKey,
            order_local_key: orderLocalKey,
            menu_item_id: menuItemId,
            name: menuItem.name,
            quantity,
            unit_price: menuItem.price,
            subtotal,
            special_instructions: instructions,
        });
        const freshOrder = await posDb.orders.get(orderLocalKey);
        const newTotal = (freshOrder.total_amount || 0) + subtotal;
        await posDb.orders.update(orderLocalKey, { total_amount: newTotal });

        const isSynced = !!freshOrder.id;
        await enqueueSync('order-add-item', {
            order_id: isSynced ? freshOrder.id : undefined,
            order_client_id: freshOrder.client_id || orderLocalKey,
            client_id: itemLocalKey,
            menu_item_id: menuItemId,
            quantity,
            instructions,
        });

        renderOrderWorkspace(containerSelector, orderLocalKey);
    });

    container.querySelectorAll('.remove-item-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const row = btn.closest('tr');
            const itemLocalKey = row.getAttribute('data-local-key');
            const item = await posDb.order_items.get(itemLocalKey);
            if (!item) return;

            await posDb.order_items.delete(itemLocalKey);
            const freshOrder = await posDb.orders.get(orderLocalKey);
            await posDb.orders.update(orderLocalKey, {
                total_amount: Math.max(0, (freshOrder.total_amount || 0) - item.subtotal),
            });

            if (item.id) {
                await enqueueSync('order-remove-item', { item_id: item.id });
            } else {
                await cancelPendingSyncByClientId(itemLocalKey);
            }

            renderOrderWorkspace(containerSelector, orderLocalKey);
        });
    });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
}

window.waiterOrder = { renderOrderWorkspace, upsertOrderFromServer };
