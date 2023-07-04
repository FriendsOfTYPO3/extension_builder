console.log("Hello from extensionbuilder.js");

import Notification from '@typo3/backend/notification.js';
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";

const handleSaveButton = () => {
  console.log("Close button clicked");
  displayRandomNotification();

  // {"id":4,"method":"saveWiring","params":{"language":"extbaseModeling","name":"test","working":"{\"modules\":[],\"properties\":{\"backendModules\":[],\"description\":\"\",\"emConf\":{\"category\":\"plugin\",\"custom_category\":\"\",\"dependsOn\":\"typo3 => 11.5.0-11.5.99\\n\",\"disableLocalization\":false,\"disableVersioning\":false,\"generateDocumentationTemplate\":false,\"generateEditorConfig\":false,\"generateEmptyGitRepository\":false,\"sourceLanguage\":\"en\",\"state\":\"alpha\",\"targetVersion\":\"11.5.0-11.5.99\",\"version\":\"1.0.0\"},\"extensionKey\":\"asdfasdf\",\"name\":\"test\",\"originalExtensionKey\":\"\",\"originalVendorName\":\"\",\"persons\":[],\"plugins\":[],\"vendorName\":\"Asdfa\"},\"wires\":[]}"},"version":"json-rpc-2.0"}
  let request = JSON.stringify({key1: 'value1', key2: 'value2'});

  let payload = {
    "id": 4,
    "method": "saveWiring",
    "params": {
      "language": "extbaseModeling",
      "name": "test",
      "working": "{\"modules\":[],\"properties\":{\"backendModules\":[],\"description\":\"\",\"emConf\":{\"category\":\"plugin\",\"custom_category\":\"\",\"dependsOn\":\"typo3 => 11.5.0-11.5.99\\n\",\"disableLocalization\":false,\"disableVersioning\":false,\"generateDocumentationTemplate\":false,\"generateEditorConfig\":false,\"generateEmptyGitRepository\":false,\"sourceLanguage\":\"en\",\"state\":\"alpha\",\"targetVersion\":\"11.5.0-11.5.99\",\"version\":\"1.0.0\"},\"extensionKey\":\"Test111\",\"name\":\"test\",\"originalExtensionKey\":\"\",\"originalVendorName\":\"\",\"persons\":[],\"plugins\":[],\"vendorName\":\"Asdfa\"},\"wires\":[]}"
    },
    "version": "json-rpc-2.0"
  };

  // console.log(TYPO3.settings.ajaxUrls);
  new AjaxRequest(TYPO3.settings.ajaxUrls.eb_dispatchRpcAction)
  // new AjaxRequest('https://httpbin.org/json')
  .post(JSON.stringify(payload))
  .headers({
    'Content-Type': 'application/json',
  })
  .then(async function (response) {
    console.log("Response from ajax request:");
    console.log(response);
  })
    .catch(function (error) {
        console.log("Error from ajax request:");
        console.log(error);
    });
}
const saveButton = document.querySelector('#eb-btn-save');
saveButton.addEventListener('click', handleSaveButton);


// Array of funny texts
const funnyTexts = [
  "Großartiger Scott! Diese Funktion ist noch nicht bereit. Komm später zurück, Marty!",
  "Wo wir hingehen, brauchen wir keine Funktionen... weil diese noch nicht implementiert ist!",
  "1.21 Gigawatt! Diese Funktion ist noch nicht fertig. Zeit, in die Zukunft zu reisen!",
  "Doc Brown arbeitet noch an dieser Funktion. Bitte warte, während er den Fluxkompensator einstellt!",
  "Die DeLorean-Zeitmaschine ist noch in der Werkstatt. Diese Funktion wird bald durch die Zeit reisen!",
  "Bist du sicher, dass du nicht in der falschen Zeitlinie bist? Diese Funktion existiert hier noch nicht!",
  "Hilfe, Doc! Ich habe auf den Button geklickt, aber die Funktion ist in einer alternativen Zeitlinie gefangen!",
  "Bitte warte, während wir genug Plutonium sammeln, um diese Funktion zu aktivieren!",
  "Achtung! Diese Funktion ist noch nicht bereit. Bitte stelle sicher, dass dein Hoverboard aufgeladen ist und versuche es später erneut!",
  "Die Uhr des Rathauses wurde vom Blitz getroffen und diese Funktion ist noch nicht bereit. Komm zurück, wenn die Zeitmaschine repariert ist!"
];

// Array of funny titles
const funnyTitles = [
  "Fluxkompensator!",
  "1.21 Gigawatt!",
  "Zeitreise-Fehler!",
  "Wo ist Marty?",
  "DeLorean defekt!",
  "Falsche Zeitlinie!",
  "Plutonium fehlt!",
  "Hoverboard-Alarm!",
  "Uhrenturm-Blitz!",
  "Doc Brown ruft!"
];

// Function to display random notification
const displayRandomNotification = () => {
  // Generate random indexes
  const randomTextIndex = Math.floor(Math.random() * funnyTexts.length);
  const randomTitleIndex = Math.floor(Math.random() * funnyTitles.length);
  // Select random text and title
  const randomText = funnyTexts[randomTextIndex];
  const randomTitle = funnyTitles[randomTitleIndex];
  // Display the notification
  Notification.warning(randomTitle, randomText);
};

const saveExtensionButton = document.querySelector('#saveExtension-button');
saveExtensionButton.addEventListener('click', displayRandomNotification);

const newExtensionButton = document.querySelector('#newExtension-button');
newExtensionButton.addEventListener('click', displayRandomNotification);

const loadExtensionButton = document.querySelector('#loadExtension-button');
loadExtensionButton.addEventListener('click', displayRandomNotification);

const showHelpButton = document.querySelector('#showHelp');
showHelpButton.addEventListener('click', displayRandomNotification);

const toggleAdvancedOptionsButton = document.querySelector('#toggleAdvancedOptions');
toggleAdvancedOptionsButton.addEventListener('click', displayRandomNotification);

