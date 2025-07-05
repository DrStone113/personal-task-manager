// Firebase Messaging Service Worker

self.addEventListener("push", (event) => {
    const notif = event.data.json().notification;

    self.waitUntil(registration.showNotification(notif.title , {
        body: notif.body,
        icon: notif.image,
        data: {
            url: notif.click_action
        }
    }));
});

self.addEventListener("notificationclick", (event) => {
    event.waitUntil(clients.openWindow(event.notification.data.url));
});