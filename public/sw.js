self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        //notifications aren't supported or permission not granted!
        return;
    }
    if (e.data) {
        var msg = e.data.json();

        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon,
            actions: msg.actions,
            badge: msg.badge
        }));
    }
});

self.addEventListener('notificationclick', function (event) {
    if (event.action != '') {
        self.clients.openWindow(event.action);
    }
});
