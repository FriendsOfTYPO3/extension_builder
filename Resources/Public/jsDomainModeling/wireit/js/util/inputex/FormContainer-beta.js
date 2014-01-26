// Comment by Ingmar Schlecht, 4. March 2010: The cloneObject function is needed later in this file, in order to make the back reference
// from the subfields to the container work, which is necessary for the positioning etc. of the wires of subfields

// Clone function for all objects. From: http://my.opera.com/GreyWyvern/blog/show.dml/1725165
// This is needed for function renderForm() in wireit/js/util/inputex/FormContainer-beta.js, where the option object needs to be cloned
/**
Object.prototype.cloneObject = function() {
	var newObj = (this instanceof Array) ? [] : {};
	for (i in this) {
		if (i == 'cloneObject') continue;
		if (this[i] && typeof this[i] == "object") {
			newObj[i] = this[i].cloneObject();
		} else newObj[i] = this[i]
	}
	return newObj;
};
 */

/**
 * extending objects prototype causes error in ExtJS
 * since there is an unfiltered for key in object call...
 * @param obj
 * @returns {Array}
 */
function cloneObject(obj) {
	var newObj = (obj instanceof Array) ? [] : {};
	for (var i in obj) {
		if (i == 'cloneObject') continue;
		if (obj[i] && typeof obj[i] == "object") {
			newObj[i] = cloneObject(obj[i]);
		} else newObj[i] = obj[i];
	}
	return newObj;
}


/**
 * Class used to build a container with inputEx forms
 * @class FormContainer
 * @namespace WireIt
 * @extends WireIt.Container
 * @constructor
 * @param {Object}   options  Configuration object (see properties)
 * @param {WireIt.Layer}   layer The WireIt.Layer (or subclass) instance that contains this container
 */
WireIt.FormContainer = function(options, layer) {
	WireIt.FormContainer.superclass.constructor.call(this, options, layer);
};

YAHOO.lang.extend(WireIt.FormContainer, WireIt.Container, {

	/**
	 * @method setOptions
	 */
	setOptions: function(options) {
		WireIt.FormContainer.superclass.setOptions.call(this, options);

		this.options.legend = options.legend;
		this.options.collapsible = options.collapsible;
		this.options.fields = options.fields;
	},

	/**
	 * The render method is overrided to call renderForm
	 * @method render
	 */
	render: function() {
		WireIt.FormContainer.superclass.render.call(this);
		this.renderForm();
	},

	/**
	 * Render the form
	 * @method renderForm
	 */
	renderForm: function() {

		// IS:
		// Clone field options, so we have our own copy here.
		this.options = cloneObject(this.options);

		this.setBackReferenceOnFieldOptionsRecursively(this.options.fields);

		var groupParams = {parentEl: this.bodyEl, fields: this.options.fields, legend: this.options.legend, collapsible: this.options.collapsible};
		this.form = new YAHOO.inputEx.Group(groupParams);
	},

	/**
	 * When creating wirable input fields, the field configuration (inputParams) must have a reference to the current container (this is used for positionning).
	 * For complex fields (like object or list), the reference is set recursively AFTER the field creation.
	 * @method setBackReferenceOnFieldOptionsRecursively
	 */
	setBackReferenceOnFieldOptionsRecursively: function(fieldArray) {
		for (var i = 0; i < fieldArray.length; i++) {
			var inputParams = fieldArray[i].inputParams;
			inputParams.container = this;

			// Checking for group sub elements
			if (inputParams.fields && typeof inputParams.fields == 'object') {
				this.setBackReferenceOnFieldOptionsRecursively(inputParams.fields);
			}

			// Checking for list sub elements
			if (inputParams.elementType) {
				inputParams.elementType.inputParams.container = this;

				// Checking for group elements within list elements
				if (inputParams.elementType.inputParams.fields && typeof inputParams.elementType.inputParams.fields == 'object') {
					this.setBackReferenceOnFieldOptionsRecursively(inputParams.elementType.inputParams.fields);
				}
			}
		}
	},

	/**
	 * @method getValue
	 */
	getValue: function() {
		return this.form.getValue();
	},

	/**
	 * @method setValue
	 */
	setValue: function(val) {
		this.form.setValue(val);
	}

});
