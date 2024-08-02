// InputEx needs a correct path to this image
console.log("extensibionbuilder.js");

if(typeof extbaseModeling_wiringEditorLanguage !== 'undefined') {
  var extbaseModeling_wiringEditorLanguage = {
    parentEl: 'domainModelEditor',
    languageName: 'extbaseModeling',
    smdUrl: TYPO3.settings.ajaxUrls['ExtensionBuilder::wiringEditorSmdEndpoint'],
    layerOptions: {},
    modules: []
  };
}

// console.log("extbaseModeling.js", extbaseModeling_wiringEditorLanguage);

// if(typeof YAHOO !== 'undefined')
//   console.log(YAHOO);
// else
//   console.log('YAHOO is undefined');
//
// if(typeof TYPO3 !== 'undefined')
//   console.log(TYPO3);
// else
//   console.log('TYPO3 is undefined');
//
// if(typeof inputEx !== 'undefined')
//   console.log(inputEx);
// else
//   console.log('inputEx is undefined');

console.log("--------");

inputEx.spacerUrl = TYPO3.settings.extensionBuilder.publicResourcesUrl + '/jsDomainModeling/wireit/lib/inputex/images/space.gif';

// console.log(inputEx.spacerUrl);

document.addEventListener('DOMContentLoaded', function() {
  // Annahme: WireIt und extbaseModeling_wiringEditorLanguage sind bereits definiert oder werden woanders importiert
  const editor = new WireIt.WiringEditor(extbaseModeling_wiringEditorLanguage);

  // Hier müssen Sie die initialWarnings als normales JavaScript-Array definieren
  const initialWarnings = [
    "TODO: Warnung 1",
    "TODO: Warnung 2",
    "TODO: Warnung 3"
  ];

  if (initialWarnings.length > 0) {
    editor.alert('Warning', initialWarnings.join('<br />'));
  }
});
