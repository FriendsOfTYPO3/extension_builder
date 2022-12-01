/**
 * Module: @friendsoftypo3/extension-builder/ebshow
 * JavaScript code for extension "extension_builder" backend module
 */

import Notification from "@typo3/backend/notification.js";

class FlashMessageDemo {
  constructor() {
    var button_load_extension = document.getElementById('loadExtension-button');
    button_load_extension.addEventListener('click', function(event) {
      event.preventDefault();
      Notification.success('Info', 'This is not implemented yet', 5);
    });

    var button_save_extension = document.getElementById('saveExtension-button');
    button_save_extension.addEventListener('click', function(event) {
      event.preventDefault();
      Notification.success('Info', 'This is not implemented yet', 5);
    });

    var button_new_extension = document.getElementById('newExtension-button');
    button_new_extension.addEventListener('click', function(event) {
      event.preventDefault();
      Notification.success('Info', 'This is not implemented yet', 5);
    });

    var button_toggle_advanced_options = document.getElementById('toggleAdvancedOptions');
    button_toggle_advanced_options.addEventListener('click', function(event) {
      event.preventDefault();
      Notification.success('Info', 'This is not implemented yet', 5);
    });
  }
}

export default new FlashMessageDemo();
