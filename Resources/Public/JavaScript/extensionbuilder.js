define([
  'jquery',
  'TYPO3/CMS/ExtensionBuilder/Contrib/vue',
  'TYPO3/CMS/Backend/Notification',
  'TYPO3/CMS/Backend/Modal'
], function ($, Vue, Notification, Modal, App, createApp) {

  /* Check if tag with id #modeller is available */
  if (!document.getElementById('modeller')) {
    // If not, return
    return;
  }

  // const app = createApp(App);

  // console.log(app);
  const vue = new Vue({
    el: '#modeller',
    methods: {
      consoleTest: function() {
        const today = new Date();
        const date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
        const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        const dateTime = date +' '+ time;
        console.log(dateTime);
      }
    }
  });

  // TEMP TODO: remove
  console.log(vue);
});
