@auth
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js"></script>
<script>
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

  const isStandalone =
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true;

  console.log('🔍 isStandalone:', isStandalone);
  console.log('🔍 serviceWorker in navigator:', 'serviceWorker' in navigator);

  if ('serviceWorker' in navigator) {
    Notification.requestPermission().then(permission => {
      console.log('🔐 Notification permission:', permission);

      if (permission !== 'granted') {
        alert("Please allow notifications to enable push messages.");
        console.warn('🚫 Notification permission not granted');
        return;
      }

      navigator.serviceWorker.ready.then((registration) => {
        console.log('✅ Service Worker is ready');

        fetch('/sanctum/csrf-cookie').then(() => {
          console.log('🧾 CSRF cookie set. Requesting FCM token...');

          messaging.getToken({
            vapidKey: 'BMkQG08hKewq6EntrMX6tn0DRwgZtcKQmeO8_URBDnKoUY4brqfMjmZqnmcfg5X7ISPklwxAEa6tRUpUa9WsS84',
            serviceWorkerRegistration: registration,
            forceRefresh: true
          }).then((token) => {
            if (token) {
              console.log('🎯 FCM Token:', token);

              fetch('/store-fcm-token', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ token }),
                credentials: 'same-origin'
              }).then(res => res.json()).then(res => {
                console.log('📬 Token sent to server successfully:', res);
              }).catch(error => {
                console.error('❌ Error sending token to server:', error);
              });

            } else {
              console.warn('⚠️ No FCM token returned');
            }
          }).catch(err => {
            console.error('❌ FCM getToken error:', err);
          });

        }).catch(err => {
          console.error('❌ Error fetching CSRF cookie:', err);
        });

      }).catch(err => {
        console.error('❌ Service Worker not ready:', err);
      });

    }).catch(err => {
      console.error('❌ Notification permission request error:', err);
    });
  } else {
    console.warn('🛑 Service Worker not supported in this browser');
  }

  // Foreground message handling
  messaging.onMessage((payload) => {
    console.log('🔔 Foreground push received:', payload);
    alert(payload.notification?.title + "\n" + payload.notification?.body);
  });
</script>
@endauth
