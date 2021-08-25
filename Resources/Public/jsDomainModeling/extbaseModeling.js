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

    var fieldSets = selectElement.parents('fieldset');
    if (fieldSets.length === 0) {
      return;
    }
    if (selectElement.name === 'relationType') {
      // relations
      var fieldSet = fieldSets[0];
      var outerFieldSets = fieldSet.parents('fieldset');
      if (outerFieldSets.length === 0) {
        return;
      }
      fieldSet = outerFieldSets[0];
      var renderTypeSelect = fieldSet.querySelectorAll('select[name="renderType"]')[0];
      updateRenderTypeOptions(selectElement.value, renderTypeSelect);

      fieldSet.classList.value = '';
      fieldSet.classList.add(selectElement.value);
    }
  }

  /**
   * @param {String} selectedRelationType
   * @param {Element} renderTypeSelect
   */
  function updateRenderTypeOptions (selectedRelationType, renderTypeSelect) {
    renderTypeSelect.querySelectorAll('option').forEach(function (option, i) {
      option.style.display = 'none';
    });
    var optionValueMap = {
      'zeroToOne': ['selectSingle', 'selectMultipleSideBySide', 'inline'],
      'manyToOne': ['selectSingle', 'selectMultipleSideBySide'],
      'zeroToMany': ['inline', 'selectMultipleSideBySide'],
      'manyToMany': ['selectMultipleSideBySide', 'selectSingleBox', 'selectCheckBox']
    };
    var validOptions = optionValueMap[selectedRelationType];
    validOptions.forEach(function (e, i) {
      renderTypeSelect.querySelectorAll('option[value="' + e + '"]').forEach(function (option, i) {
        option.style.display = 'block';
      });
    });
    if (validOptions.indexOf(renderTypeSelect.value) < 0) {
      renderTypeSelect.value = validOptions[0];
    }
  }

  /**
   * @param {Element} parentEl
   */
  inputEx.Group.prototype.renderFields = function (parentEl) {
    renderFields.call(this, parentEl);
    parentEl.querySelectorAll('fieldset select[name="relationType"]').forEach(function (element, i) {
      // trigger options rendering & enabling for relationType selectors
      addFieldsetClass(element);
    });
  };

  inputEx.SelectField.prototype.onChange = function (evt) {
    addFieldsetClass(evt.target);
  };

  /**
   * add the selected propertyType as classname to all propertyGroup fieldsets
   */
  WireIt.WiringEditor.prototype.onPipeLoaded = function () {
    var propertyTypeSelects = document.querySelectorAll('.propertyGroup select');
    if (propertyTypeSelects.length > 0) {
      propertyTypeSelects.forEach(function (el, i) {
        addFieldsetClass(el);
      });
    }
    var relationTypeSelects = document.querySelectorAll('.relationGroup select');
    if (relationTypeSelects.length > 0) {
      relationTypeSelects.forEach(function (el, i) {
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
