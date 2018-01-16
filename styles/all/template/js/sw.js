/* jshint browser:true, worker:true, es3:false, esversion:6 */
'use strict';

function urlB64ToUint8Array(base64String) {
	var padding = '='.repeat((4 - base64String.length % 4) % 4);
	var base64 = (base64String + padding)
		.replace(/\-/g, '+')
		.replace(/_/g, '/');

	var rawData = window.atob(base64);
	var outputArray = new Uint8Array(rawData.length);

	for (var i = 0; i < rawData.length; ++i) {
		outputArray[i] = rawData.charCodeAt(i);
	}
	return outputArray;
}

self.addEventListener('install', function(event) {
	event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function(event) {
	event.waitUntil(self.clients.claim());
});

self.addEventListener('notificationclick', function(event) {
	var notification = event.notification,
		data = notification.data;

	notification.close();

	if (data && data.url) {
		event.waitUntil(
			// Retrieve a list of the clients of this service worker.
			self.clients.matchAll({ type: 'window' }).then(function(clientList) {
				// If there is at least one client, focus it.
				if (clientList.length > 0) {
					clientList[0].navigate(data.url);
					return clientList[0].focus();
				}

				// Otherwise, open a new page.
				return self.clients.openWindow(data.url);
			})
		);
	}
});

self.addEventListener('push', function(event) {
	const data = event.data.json();
	const title = data.title;
	const options = {
		body: data.message,
		data: {url: data.url},
		icon: data.avatar,
		badge: data.badge,
		vibrate: [300, 100, 400],
		timestamp: data.time,
		requireInteraction: true
	};

	event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('pushsubscriptionchange', function(event) {
	if (!localStorage.pushSubscription) {
		return;
	}
	var pushSubscription = JSON.parse(localStorage.pushSubscription);
    const applicationServerKey = urlB64ToUint8Array(pushSubscription.serverKey);
    event.waitUntil(
        self.registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: applicationServerKey
        }).then(function(newSubscription) {
	        var subscriptionArray = JSON.parse(JSON.stringify(newSubscription)),
		        subscriptionString = 'endpoint=' + subscriptionArray.endpoint + '&keys[p256dh]=' + subscriptionArray.keys.p256dh + '&keys[auth]=' + subscriptionArray.keys.auth + '&subscription_id=' + pushSubscription.subscriptionID;

	        var fetchOptions = {
		        method: 'post',
		        headers: new Headers({
			        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			        'X-Requested-With': 'XMLHttpRequest'
		        }),
		        credentials: 'include',
		        body: subscriptionString
	        };
	        return fetch(pushSubscription.serverURL + 'subscribe', fetchOptions);
        }).then(function(response) {
	        return response.json();
        }).then(function(res) {
	        if (!res.id) {
		        return;
	        }
	        localStorage.pushSubscription = JSON.stringify({
		        serverKey: pushSubscription.serverKey,
		        serverURL: pushSubscription.serverURL,
		        subscriptionID: res.id
	        });
        })
    );
});

