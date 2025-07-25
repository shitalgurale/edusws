// Version 2.0 - Updated for EduSWS
importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyCpupDhdB59yZXp4MomhsVUlP6jv765tAA",
  authDomain: "edusws-40024.firebaseapp.com",
  projectId: "edusws-40024",
  storageBucket: "edusws-40024.firebasestorage.app",
  messagingSenderId: "427836051235",
  appId: "1:427836051235:web:526c48822593460e84c696"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// ✅ Background Push Notification Handling
messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message:', payload);

  const notificationTitle = payload.notification?.title || 'EduSWS Notification';
  const notificationOptions = {
    body: payload.notification?.body || '',
    icon: '/icons/icon-192x192.png',
    data: payload.data || {}
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// ✅ Force Update Logic
self.addEventListener('install', (event) => {
  self.skipWaiting(); // Activate updated SW immediately
});

self.addEventListener('activate', (event) => {
  clients.claim(); // Take control of all tabs
});

// ✅ Optional fetch event (fallback)
self.addEventListener('fetch', (event) => {
  // Avoid fetch-related crashes
});
