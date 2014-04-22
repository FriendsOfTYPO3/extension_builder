var extbaseModeling_wiringEditorLanguage = {
	parentEl: 'domainModelEditor',
	languageName: "extbaseModeling",
	smdUrl: TYPO3.settings.ajaxUrls['ExtensionBuilder::wiringEditorSmdEndpoint'],
	layerOptions: {
	},
	modules: []
};

YAHOO.util.Event.onDOMReady(function () {
	var inputEx = YAHOO.inputEx, Event = YAHOO.util.Event, lang = YAHOO.lang, dom = YAHOO.util.Dom;

	function addFieldsetClass (selectElement) {
		var fieldset = dom.getAncestorByTagName(selectElement, 'fieldset');
		fieldset.removeAttribute('class');
		dom.addClass(fieldset, selectElement.value);
	}

	inputEx.SelectField.prototype.onChange = function (evt) {
		addFieldsetClass(dom.get(evt.target));
	};

	/**
	 * add the selected propertyType as classname to all propertyGroup fieldsets
	 */
	WireIt.WiringEditor.prototype.onPipeLoaded = function () {
		var propertyTypeSelects = $$('.propertyGroup select');
		if (propertyTypeSelects) {
			propertyTypeSelects.each(function (el) {
				addFieldsetClass(dom.get(el));
			});
		}
	};

	$('typo3-mod-php').addClassName('yui-skin-sam');

	/**
	 * Update dependencies in textarea
	 */
	$$('select[name=targetVersion]')[0].onchange =
	function (event) {
		var updatedDependencies = '';
		var dependencies = $$('textarea[name=dependsOn]')[0].value.split("\n");
		for (i = 0; i < dependencies.length; i++) {
			parts = dependencies[i].split('=>');
			if (parts[0].indexOf('typo3') > -1) {
				updatedDependencies += 'typo3 => ' + event.target.value + "\n";
			} else {
				updatedDependencies += dependencies[i] + "\n";
			}

		}
		$$('textarea[name=dependsOn]')[0].value = updatedDependencies;
	};

	var advancedMode = false;
	$('toggleAdvancedOptions').onclick =
	function (ev, target) {
		if (!advancedMode) {
			$('domainModelEditor').addClassName('showAdvancedOptions');
			$$('#toggleAdvancedOptions .simpleMode')[0].style.display = 'none';
			$$('#toggleAdvancedOptions .advancedMode')[0].style.display = 'inline';
			advancedMode = true;
		} else {
			$('domainModelEditor').removeClassName('showAdvancedOptions');
			$$('#toggleAdvancedOptions .simpleMode')[0].style.display = 'inline';
			$$('#toggleAdvancedOptions .advancedMode')[0].style.display = 'none';
			advancedMode = false;
		}
		return false;
	};
});