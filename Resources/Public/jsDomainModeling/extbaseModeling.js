var extbaseModeling_wiringEditorLanguage = {
  parentEl: 'domainModelEditor',
  languageName: 'extbaseModeling',
  smdUrl: TYPO3.settings.ajaxUrls['ExtensionBuilder::wiringEditorSmdEndpoint'],
  layerOptions: {},
  modules: []
};

/**
 * @see https://gist.github.com/ziggi/2f15832b57398649ee9b and jQuery source code
 * @param {String} selector
 * @returns {*[]}
 */
Element.prototype.parents = function (selector) {
  var elem = this,
    matched = [];

  while ((elem = elem.parentNode) && elem.nodeType !== Node.DOCUMENT_NODE) {
    if (elem.nodeType === Node.ELEMENT_NODE) {
      matched.push(elem);
    }
  }

  return matched.filter(function (match) {
    return match.matches(selector);
  });
};

(function(){
  var inputEx = YAHOO.inputEx;
  var renderFields = inputEx.Group.prototype.renderFields;

  /**
   * @param {Element} selectElement
   */
  function addFieldsetClass (selectElement) {
    if (YAHOO.util.Dom.get(selectElement).parentNode.classList.contains('isDependant')) {
      return;
    }
    var fieldset = $(selectElement).parents('fieldset').first();

    if (selectElement.name === 'relationType') {
      // relations
      var fieldSets = selectElement.parents('fieldset');
      if (fieldSets.length === 0) {
        return;
      }
      fieldset = $(fieldset).parents('fieldset').first();
      var renderTypeSelect = fieldset.find("select[name='renderType']").first();
      updateRenderTypeOptions(selectElement.value, renderTypeSelect);
    }

    fieldset.attr('class', '');
    fieldset.addClass(selectElement.value);
  }

  /**
   * @param {String} selectedRelationType
   * @param {Element} renderTypeSelect
   */
  function updateRenderTypeOptions (selectedRelationType, renderTypeSelect) {
    renderTypeSelect.find("option").hide();
    var optionValueMap = {
      'zeroToOne': ["selectSingle", "selectMultipleSideBySide", "inline"],
      'manyToOne': ["selectSingle", "selectMultipleSideBySide"],
      'zeroToMany': ["inline", "selectMultipleSideBySide"],
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

  /**
   * @param {Element} parentEl
   */
  inputEx.Group.prototype.renderFields = function(parentEl) {
    renderFields.call(this, parentEl);
    var selectElements = parentEl.querySelectorAll('fieldset select[name=relationType]');
    for (var i = 0; i < selectElements.length; i++) {
      // trigger options rendering & enabling for relationType selectors
      addFieldsetClass(selectElements.item(i));
    }
  };

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
  // Update dependencies in textarea
  YAHOO.util.Event.addListener('targetVersionSelector-field', 'change', function (event) {
    var dependenciesField = document.querySelector('#extensionDependencies-field');

    var dependencies = dependenciesField.value.split("\n").map(function (dependency) {
      var parts = dependency.split('=>');
      return parts[0].indexOf('typo3') > -1 ? 'typo3 => ' + event.target.value : dependency;
    });
    dependenciesField.value = dependencies.join("\n");
  });
});

YAHOO.util.Event.onAvailable('toggleAdvancedOptions', function () {
  if (window.top.location.href === window.location.href) {
    document.querySelector('#opennewwindow').style.display = 'none';
  }

  var advancedMode = false;
  YAHOO.util.Event.addListener('toggleAdvancedOptions', 'click', function () {
    if (!advancedMode) {
      YAHOO.util.Dom.addClass('domainModelEditor', 'showAdvancedOptions');
      document.querySelector('#toggleAdvancedOptions .simpleMode').style.display = 'none';
      document.querySelector('#toggleAdvancedOptions .advancedMode').style.display = 'inline';
      advancedMode = true;
    } else {
      YAHOO.util.Dom.removeClass('domainModelEditor', 'showAdvancedOptions');
      document.querySelector('#toggleAdvancedOptions .simpleMode').style.display = 'inline';
      document.querySelector('#toggleAdvancedOptions .advancedMode').style.display = 'none';
      advancedMode = false;
    }
    return false;
  });
});
