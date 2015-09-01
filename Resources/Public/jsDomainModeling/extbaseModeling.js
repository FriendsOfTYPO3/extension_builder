var extbaseModeling_wiringEditorLanguage = {
	parentEl: 'domainModelEditor',
	languageName: "extbaseModeling",
	smdUrl: TYPO3.settings.ajaxUrls['ExtensionBuilder::wiringEditorSmdEndpoint'],
	layerOptions: {
	},
	modules: []
};

(function(){
	var inputEx = YAHOO.inputEx, Event = YAHOO.util.Event, lang = YAHOO.lang, dom = YAHOO.util.Dom;

		function addFieldsetClass (selectElement) {
			var fieldset = TYPO3.jQuery(selectElement).parent('fieldset');
			if (fieldset.hasClass('inputEx-Expanded')) {
				return;
			}
			fieldset.attr('class', '');
            fieldset.addClass(selectElement.value);
		}

		inputEx.SelectField.prototype.onChange = function (evt) {
			addFieldsetClass(evt.target);
		};

		/**
		 * add the selected propertyType as classname to all propertyGroup fieldsets
		 */
		WireIt.WiringEditor.prototype.onPipeLoaded = function () {
			var propertyTypeSelects = TYPO3.jQuery('.propertyGroup select');
			if (propertyTypeSelects) {
				propertyTypeSelects.each(function (index, el) {
					addFieldsetClass(el);
				});
			}
		};
})();



YAHOO.util.Event.onAvailable('extensionDependencies-field', function () {
	/**
	 * Update dependencies in textarea
	 */
    TYPO3.jQuery('#targetVersionSelector-field').onchange =
	function (event) {
		var updatedDependencies = '';
        var dependenciesField = TYPO3.jQuery('extensionDependencies-field');
		var dependencies = dependenciesField.value.split("\n");
		for (i = 0; i < dependencies.length; i++) {
			parts = dependencies[i].split('=>');
			if (parts[0].indexOf('typo3') > -1) {
				updatedDependencies += 'typo3 => ' + event.target.value + "\n";
			} else {
				updatedDependencies += dependencies[i] + "\n";
			}

		}
        dependenciesField.value = updatedDependencies;
	};
});

YAHOO.util.Event.onAvailable('toggleAdvancedOptions', function () {

    TYPO3.jQuery('#typo3-index-php').addClass('yui-skin-sam');

	var advancedMode = false;
	TYPO3.jQuery('#toggleAdvancedOptions').click(
	function () {
		if (!advancedMode) {
			TYPO3.jQuery('#domainModelEditor').addClass('showAdvancedOptions');
            TYPO3.jQuery('#toggleAdvancedOptions .simpleMode').hide();
            TYPO3.jQuery('#toggleAdvancedOptions .advancedMode').show();
			advancedMode = true;
		} else {
            TYPO3.jQuery('#domainModelEditor').removeClass('showAdvancedOptions');
            TYPO3.jQuery('#toggleAdvancedOptions .simpleMode').show();
            TYPO3.jQuery('#toggleAdvancedOptions .advancedMode').hide();
			advancedMode = false;
		}
		return false;
	});
});