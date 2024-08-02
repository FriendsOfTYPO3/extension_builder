console.log("example.js");

if(typeof extbaseModeling_wiringEditorLanguage !== 'undefined') {
  var extbaseModeling_wiringEditorLanguage = {
    parentEl: 'domainModelEditor',
    languageName: 'extbaseModeling',
    smdUrl: TYPO3.settings.ajaxUrls['ExtensionBuilder::wiringEditorSmdEndpoint'],
    layerOptions: {},
    modules: []
  };
}

console.log(TYPO3);
console.log(TYPO3.settings.extensionBuilder._LOCAL_LANG.add);
console.log(YAHOO);
console.log("extbaseModeling_wiringEditorLanguage", extbaseModeling_wiringEditorLanguage);
console.log("-------");
