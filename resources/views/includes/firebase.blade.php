@auth
<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js"></script>

<!-- Big, Clean Enable Notifications Button -->
<button id="enableNotifications" style="
  display: none;
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 14px 24px;
  font-size: 16px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  cursor: pointer;
">
  🔔 Enable Notifications
</button>

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

const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isTWA = isStandalone && /android/i.test(navigator.userAgent);

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then((registration) => {
      console.log('✅ Service Worker registered');

    if (((isIOS && isStandalone) || isTWA) && Notification.permission !== 'granted') {
      document.getElementById('enableNotifications').style.display = 'block';
    
      document.getElementById('enableNotifications').addEventListener('click', () => {
        requestNotificationPermission(registration);
        document.getElementById('enableNotifications').style.display = 'none';
      });
    }
    else 
    {
        requestNotificationPermission(registration);
    }

    }).catch((err) => {
      console.error('❌ Service Worker registration failed:', err);
    });
}
 
  else {
    console.warn('🛑 Service Worker not supported in this browser');
  }

  function requestNotificationPermission(registration) {
    Notification.requestPermission().then(permission => {
      console.log('🔐 Notification permission:', permission);

      if (permission !== 'granted') {
        alert("Please allow notifications to enable push messages.");
        return;
      }

      // Get CSRF and then fetch the token
      fetch('/sanctum/csrf-cookie').then(() => {
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
      console.error('❌ Notification permission request error:', err);
    });
  }

  // Foreground message handler
  messaging.onMessage((payload) => {
    console.log('🔔 Foreground push received:', payload);
    alert(payload.notification?.title + "\n" + payload.notification?.body);
  });
</script>
@endauth
