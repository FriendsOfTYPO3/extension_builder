console.log("Hello from extensionbuilder.js");

import Notification from '@typo3/backend/notification.js';
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";

// const handleSaveButton = () => {
//   let payload = {
//     "id": 4,
//     "method": "saveWiring",
//     "params": {
//       "language": "extbaseModeling",
//       "name": "Name",
//       "working": {
//         "modules": [],
//         "properties": {
//           "backendModules": [],
//           "description": "Dies ist eine BEschreibung",
//           "emConf": {
//             "category": "plugin",
//             "custom_category": "",
//             "dependsOn": "typo3 => 11.5.0-11.5.99\n",
//             "disableLocalization": true,
//             "disableVersioning": true,
//             "generateDocumentationTemplate": true,
//             "generateEditorConfig": true,
//             "generateEmptyGitRepository": true,
//             "sourceLanguage": "en",
//             "state": "stable",
//             "targetVersion": "12.4.0-12.4.99",
//             "version": "1.0.0"
//           },
//           "extensionKey": "keeeey",
//           "name": "test",
//           "originalExtensionKey": "",
//           "originalVendorName": "",
//           "persons": [],
//           "plugins": [],
//           "vendorName": "Treupo"
//         },
//         "wires": []
//       }
//     },
//     "version": "json-rpc-2.0"
//   };
//
//   // "/typo3/ajax/extensionBuilder/dispatchRpcAction?token=2a99be62cb4753527b6ee62238a35ec4de0b991e"
//   // "/typo3/ajax/extensionBuilder/dispatchRpcAction?token=2a99be62cb4753527b6ee62238a35ec4de0b991e"
//   console.log(TYPO3.settings.ajaxUrls);
//
//   new AjaxRequest(TYPO3.settings.ajaxUrls.eb_dispatchRpcAction)
//       .post(JSON.stringify(payload), {
//         headers: {
//           'Content-Type': 'application/json',
//           "X-Requested-With": "XMLHttpRequest"
//         }}
//       )
//       .then(async function (response) {
//         Notification.success("Success", response.resolve().result);
//       })
//       .catch(function (error) {
//         console.log(error);
//         Notification.error("Error", "Your extension could not be saved.");
//       });
// }



// const saveExtensionButton = document.querySelector('#saveExtension-button');
// saveExtensionButton.addEventListener('click', handleSaveButton);

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

const handleTopBarButtons = () => {
  const title = "This is not yet working!"
  const text = "Please use the buttons underneath"
  Notification.error(title, text);
};

const slackButton = document.querySelector('#slack-button');
slackButton.addEventListener('click', handleTopBarButtons);

const reportBugButton = document.querySelector('#bug-button');
reportBugButton.addEventListener('click', handleTopBarButtons);

const documentationButton = document.querySelector('#documentation-button');
documentationButton.addEventListener('click', handleTopBarButtons);

const sponsorButton = document.querySelector('#sponsor-button');
sponsorButton.addEventListener('click', handleTopBarButtons);

const toggleAdvancedOptionsButton = document.querySelector('#toggleAdvancedOptions');
toggleAdvancedOptionsButton.addEventListener('click', displayRandomNotification);

const saveButton = document.querySelector('#saveExtension-button');
saveButton.addEventListener('click', handleTopBarButtons);
