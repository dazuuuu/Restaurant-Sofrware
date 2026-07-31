const OFFLINE_SESSION_TTL_MS = 24 * 60 * 60 * 1000;

async function sha256Hex(text) {
    const data = new TextEncoder().encode(text);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    return Array.from(new Uint8Array(hashBuffer)).map((b) => b.toString(16).padStart(2, '0')).join('');
}

const offlineAuth = {
    // Call right after a successful ONLINE staff login (the browser already has
    // the plaintext PIN from the form the user just submitted). Nothing PIN-derived
    // is ever sent to or received from the server.
    async cacheAfterOnlineLogin(user, pin, deviceToken, currencySymbol) {
        const salt = uuid();
        const pinHash = await sha256Hex(salt + ':' + pin);

        await posDb.pin_cache.put({ userId: user.id, salt, pinHash });
        await setSession({
            userId: user.id,
            fullName: user.full_name,
            role: user.role,
            deviceToken,
            currencySymbol: currencySymbol || 'Ksh',
            cachedAt: Date.now(),
        });
    },

    async tryOfflineUnlock(userId, pin) {
        const [session, pinRow] = await Promise.all([
            getSession(),
            posDb.pin_cache.get(userId),
        ]);

        if (!session || !pinRow || session.userId !== userId) {
            return { success: false, reason: 'No cached offline session for this user on this device.' };
        }

        if (Date.now() - session.cachedAt > OFFLINE_SESSION_TTL_MS) {
            return { success: false, reason: 'Offline session expired. Please connect to the internet and log in once to refresh it.' };
        }

        const candidateHash = await sha256Hex(pinRow.salt + ':' + pin);
        if (candidateHash !== pinRow.pinHash) {
            return { success: false, reason: 'Incorrect PIN.' };
        }

        return {
            success: true,
            user: { id: session.userId, full_name: session.fullName, role: session.role },
        };
    },

    async isSessionValid() {
        const session = await getSession();
        if (!session) {
            return false;
        }
        return Date.now() - session.cachedAt <= OFFLINE_SESSION_TTL_MS;
    },

    async getCachedUser() {
        const session = await getSession();
        if (!session) {
            return null;
        }
        return { id: session.userId, full_name: session.fullName, role: session.role, deviceToken: session.deviceToken };
    },

    async logout() {
        const session = await getSession();
        if (session) {
            await posDb.pin_cache.delete(session.userId);
        }
        await clearSession();
    },
};
