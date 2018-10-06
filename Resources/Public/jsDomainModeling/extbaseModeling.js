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
			if ($(selectElement).parent().hasClass('isDependant')) {
				return;
			}
			var fieldset = $(selectElement).parents('fieldset').first();
			if (selectElement.name == 'relationType') {
				// relations
				fieldset = $(fieldset).parents('fieldset').first();
				var renderTypeSelect = fieldset.find("select[name='renderType']").first();
				updateRenderTypeOptions(selectElement.value, renderTypeSelect);
			}
			fieldset.attr('class', '');
			fieldset.addClass(selectElement.value);
		}


		function updateRenderTypeOptions (selectedRelationType, renderTypeSelect) {
			renderTypeSelect.find("option").hide();
			var optionValueMap = {
				'zeroToOne': ["selectSingle", "inline"],
				'manyToOne': ["selectSingle"],
				'zeroToMany': ["inline"],
				'manyToMany': ["selectMultipleSideBySide", "selectSingleBox", "selectCheckBox"]
			};
			var validOptions = optionValueMap[selectedRelationType];

			$.each(validOptions, function(i, e) {
				renderTypeSelect.find("option[value='" + e + "']").show();
			});
			if (validOptions.indexOf(renderTypeSelect.val()) < 0) {
				renderTypeSelect.val(validOptions[0]);
			}

		}

		inputEx.SelectField.prototype.onChange = function (evt) {
			addFieldsetClass(evt.target);
		};

		/**
		 * add the selected propertyType as classname to all propertyGroup fieldsets
		 */
		WireIt.WiringEditor.prototype.onPipeLoaded = function () {
			var propertyTypeSelects = $('.propertyGroup select');
			if (propertyTypeSelects) {
				propertyTypeSelects.each(function (index, el) {
					addFieldsetClass(el);
				});
			}
			var relationTypeSelects = $('.relationGroup select');
			if (relationTypeSelects) {
				relationTypeSelects.each(function (index, el) {
					addFieldsetClass(el);
				});
			}
		};
})();



YAHOO.util.Event.onAvailable('extensionDependencies-field', function () {
	/**
	 * Update dependencies in textarea
	 */
	$('#targetVersionSelector-field').onchange =
	function (event) {
		var updatedDependencies = '';
		var dependenciesField = $('extensionDependencies-field');
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

	$('body').addClass('yui-skin-sam');
	$('.t3js-module-docheader-bar-buttons').show();
	if (window.top.location.href === window.location.href) {
		$("#opennewwindow").hide();
	}
	var advancedMode = false;
	$('#toggleAdvancedOptions').click(
	function () {
		if (!advancedMode) {
			$('#domainModelEditor').addClass('showAdvancedOptions');
			$('#toggleAdvancedOptions .simpleMode').hide();
			$('#toggleAdvancedOptions .advancedMode').show();
			advancedMode = true;
		} else {
			$('#domainModelEditor').removeClass('showAdvancedOptions');
			$('#toggleAdvancedOptions .simpleMode').show();
			$('#toggleAdvancedOptions .advancedMode').hide();
			advancedMode = false;
		}
		return false;
	});
});