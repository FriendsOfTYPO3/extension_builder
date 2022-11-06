define([
  'jquery',
  'TYPO3/EBT/ExtensionBuilder/Contrib/vue'
], function (
  $,
  Vue
) {
  if (!document.getElementById('modeller')) {
    return;
  }

  new Vue({
    el: '#modeller'
  });
});
