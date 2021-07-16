/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');
   
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
            apiKey: "AIzaSyB5YP6u4hQ4AZGjCYfhigtJ8TsTQ5YnCOA",
            authDomain: "atrium-food.firebaseapp.com",
            databaseURL: "https://atrium-food.firebaseio.com",
            projectId: "atrium-food",
            storageBucket: "atrium-food.appspot.com",
            messagingSenderId: "617478703147",
            appId: "1:617478703147:web:1ea403e673707a80d6026e",
            measurementId: "G-02LQ64PHCG"
    });
  
/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "/itwonders-web-logo.png",
    };
  
    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});