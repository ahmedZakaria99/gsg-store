import {initializeApp} from "firebase/app";
import {getAnalytics} from "firebase/analytics";
import {getMessaging, getToken, onMessage} from "firebase/messaging";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyCvUeYqw1JoohTIA3WcpmN3yy8XgDoIpNI",
    authDomain: "gsg-test-afb93.firebaseapp.com",
    projectId: "gsg-test-afb93",
    storageBucket: "gsg-test-afb93.appspot.com",
    messagingSenderId: "540443733882",
    appId: "1:540443733882:web:c4dfcf15d193e41e0930c3",
    measurementId: "G-XCJE306TP9"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const messaging = getMessaging();
getToken(messaging, {vapidKey: 'BFUI_gyMGbcUwmrkAeQQRLbld2xnBSmouruCQTyN6G6wEMK2iVn7v1ZKTuagIhwob023Q7MqndBOH9SGtjOl9qM'}).then((currentToken) => {
    if (currentToken) {
        // Send the token to your server and update the UI if necessary
        console.log(currentToken);
    } else {
        // Show permission request UI
        console.log('No registration token available. Request permission to generate one.');
        // ...
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
    // ...
});
onMessage(messaging, (payload) => {
    alert();
    console.log('Message received. ', payload);
    // ...
});


