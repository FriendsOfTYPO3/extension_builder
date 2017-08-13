(function() {

	var inputEx = YAHOO.inputEx, lang = YAHOO.lang, Event = YAHOO.util.Event, Dom = YAHOO.util.Dom;

	/**
	 * @class Basic string field (equivalent to the input type "text")
	 * @extends inputEx.Field
	 * @constructor
	 * @param {Object} options Added options:
	 * <ul>
	 *	  <li>regexp: regular expression used to validate (otherwise it always validate)</li>
	 *   <li>size: size attribute of the input</li>
	 *   <li>maxLength: maximum size of the string field (no message display, uses the maxlength html attribute)</li>
	 *   <li>minLength: minimum size of the string field (will display an error message if shorter)</li>
	 *   <li>typeInvite: string displayed when the field is empty</li>
	 *   <li>readonly: set the field as readonly</li>
	 * </ul>
	 */
	inputEx.StringField = function(options) {
		inputEx.StringField.superclass.constructor.call(this, options);

		if (this.options.typeInvite) {
			this.updateTypeInvite();
		}
	};

	lang.extend(inputEx.StringField, inputEx.Field,
		/**
		 * @scope inputEx.StringField.prototype
		 */
		{
			/**
			 * Set the default values of the options
			 * @param {Object} options Options object (inputEx inputParams) as passed to the constructor
			 */
			setOptions: function(options) {
				inputEx.StringField.superclass.setOptions.call(this, options);
				this.options.regexp = options.regexp;
				this.options.size = options.size;
				this.options.maxLength = options.maxLength;
				this.options.minLength = options.minLength;
				this.options.typeInvite = options.typeInvite;
				this.options.readonly = options.readonly;
				this.options.forceLowerCase = options.forceLowerCase;
				this.options.forceAlphaNumeric = options.forceAlphaNumeric;
				this.options.forceAlphaNumericUnderscore = options.forceAlphaNumericUnderscore;
				this.options.noSpaces = options.noSpaces;
				this.options.ucFirst = options.ucFirst;
				this.options.lcFirst = options.lcFirst;
				this.options.firstCharNonNumeric = options.firstCharNonNumeric;
				this.options.advancedMode = options.advancedMode ? options.advancedMode : false;
				this.options.classname = options.classname;
				this.options.placeholder = options.placeholder;
			},


			/**
			 * Render an 'INPUT' DOM node
			 */
			renderComponent: function() {

				// This element wraps the input node in a float: none div
				this.wrapEl = inputEx.cn('div', {className: 'inputEx-StringField-wrapper'});

				// Attributes of the input field
				var attributes = {};
				attributes.type = 'text';
				attributes.id = this.divEl.id ? this.divEl.id + '-field' : YAHOO.util.Dom.generateId();
				if (this.options.size) attributes.size = this.options.size;
				if (this.options.name) attributes.name = this.options.name;
				if (this.options.placeholder) attributes.placeholder = this.options.placeholder;
				if (this.options.readonly) attributes.readonly = 'readonly';

				if (this.options.maxLength) attributes.maxLength = this.options.maxLength;

				// Create the node
				this.el = inputEx.cn('input', attributes);

				// Append it to the main element
				this.wrapEl.appendChild(this.el);
				Dom.addClass(this.divEl,'textfieldWrapper');
				if(this.options.advancedMode) {
				  Dom.addClass(this.divEl, "advancedMode");
			    }
				if(this.options.classname) {
				  Dom.addClass(this.divEl, this.options.classname);
				}
				this.fieldContainer.appendChild(this.wrapEl);
			},

			/**
			 * Register the change, focus and blur events
			 */
			initEvents: function() {
				Event.addListener(this.el, "change", this.onChange, this, true);

				if (YAHOO.env.ua.ie) { // refer to inputEx-95
					var field = this.el;
					new YAHOO.util.KeyListener(this.el, {keys:[13]}, {fn:function() {
						field.blur();
						field.focus();
					}}).enable();
				}

				Event.addFocusListener(this.el, this.onFocus, this, true);
				Event.addBlurListener(this.el, this.onBlur, this, true);

				Event.addListener(this.el, "keypress", this.onKeyPress, this, true);
				Event.addListener(this.el, "keyup", this.onKeyUp, this, true);
			},

			/**
			 * Return the string value
			 * @param {String} The string value
			 */
			getValue: function() {
				return (this.options.typeInvite && this.el.value == this.options.typeInvite) ? '' : this.el.value;
			},

			/**
			 * Function to set the value
			 * @param {String} value The new value
			 * @param {boolean} [sendUpdatedEvt] (optional) Wether this setValue should fire the updatedEvt or not (default is true, pass false to NOT send the event)
			 */
			setValue: function(value, sendUpdatedEvt) {
				this.el.value = value;
				this.el.setAttribute('title', value);
				// call parent class method to set style and fire updatedEvt
				inputEx.StringField.superclass.setValue.call(this, value, sendUpdatedEvt);
			},

			/**
			 * Uses the optional regexp to validate the field value
			 */
			validate: function() {
				var val = this.getValue();

				// empty field
				if (val == '') {
					// validate only if not required
					return !this.options.required;
				}

				// Check regex matching and minLength (both used in password field...)
				var result = true;

				// if we are using a regular expression
				if (this.options.regexp) {
					var reg = new RegExp(this.options.regexp);
					result = result && reg.test(val);
				}
				if (this.options.minLength) {
					result = result && val.length >= this.options.minLength;
				}
				if (this.options.forceAlphaNumeric) {
					var forceAlphaNumericRegex = new RegExp(/[^a-zA-Z0-9]/g);
					result = result && !forceAlphaNumericRegex.test(val);
				}
				if (this.options.forceAlphaNumericUnderscore) {
					var forceAlphaNumericUnderscoreRegex = new RegExp(/[^a-zA-Z0-9_]/g);
					result = result && !forceAlphaNumericUnderscoreRegex.test(val);
				}
				if (this.options.firstCharNonNumeric) {
					var firstCharNonNumericRegex = new RegExp(/(^[a-zA-Z])/);
					result = result && firstCharNonNumericRegex.test(val);
				}
				return result;
			},

			/**
			 * Disable the field
			 */
			disable: function() {
				this.el.disabled = true;
			},

			/**
			 * Enable the field
			 */
			enable: function() {
				this.el.disabled = false;
			},

			/**
			 * Set the focus to this field
			 */
			focus: function() {
				// Can't use lang.isFunction because IE >= 6 would say focus is not a function (IE says it's an object) !!
				if (!!this.el && !lang.isUndefined(this.el.focus)) {
					this.el.focus();
				}
			},

			/**
			 * Add the minLength string message handling
			 */
			getStateString: function(state) {
				if (state == inputEx.stateInvalid && this.options.minLength && this.el.value.length < this.options.minLength) {
					return inputEx.messages.stringTooShort[0] + this.options.minLength + inputEx.messages.stringTooShort[1];
				}
				return inputEx.StringField.superclass.getStateString.call(this, state);
			},

			/**
			 * Display the type invite after setting the class
			 */
			setClassFromState: function() {
				inputEx.StringField.superclass.setClassFromState.call(this);

				// display/mask typeInvite
				if (this.options.typeInvite) {
					this.updateTypeInvite();
				}
			},

			updateTypeInvite: function() {

				// field not focused
				if (!Dom.hasClass(this.divEl, "inputEx-focused")) {

					// show type invite if field is empty
					if (this.isEmpty()) {
						Dom.addClass(this.divEl, "inputEx-typeInvite");
						this.el.value = this.options.typeInvite;

						// important for setValue to work with typeInvite
					} else {
						Dom.removeClass(this.divEl, "inputEx-typeInvite");
					}

					// field focused : remove type invite
				} else {
					if (Dom.hasClass(this.divEl, "inputEx-typeInvite")) {
						// remove text
						this.el.value = "";

						// remove the "empty" state and class
						this.previousState = null;
						Dom.removeClass(this.divEl, "inputEx-typeInvite");
					}
				}
			},

			/**
			 * Clear the typeInvite when the field gains focus
			 */
			onFocus: function(e) {
				inputEx.StringField.superclass.onFocus.call(this, e);

				if (this.options.typeInvite) {
					this.updateTypeInvite();
				}
			},

			onKeyPress: function(e) {
				// override me
			},

			onKeyUp: function(e) {
				if (this.options.forceLowerCase) {
					this.el.value = this.el.value.toLowerCase();
				}
				if (this.options.noSpaces) {
					this.el.value = this.el.value.replace(/\s/g, '');
				}
				if (this.options.lcFirst || this.options.ucFirst) {
					var first = this.el.value.charAt(0);
					var tmp = this.el.value.substr(1);
					if (this.options.lcFirst) {
						first = first.toLowerCase();
					} else {
						first = first.toUpperCase();
					}
					this.el.value = first + tmp;
				}
			}

		}
	);


	inputEx.messages.stringTooShort = ["This field should contain at least "," numbers or characters"];

	/**
	 * Register this class as "string" type
	 */
	inputEx.registerType("string", inputEx.StringField);

})();
