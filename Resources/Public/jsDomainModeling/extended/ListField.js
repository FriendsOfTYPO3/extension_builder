/****************************************************
 * 		overwrite the default listField to enable
 * 		value manipulation after a "Add" event
 * 		(see line
 **************************************************/
(function() {

	   var inputEx = YAHOO.inputEx, lang = YAHOO.lang, Event = YAHOO.util.Event, Dom = YAHOO.util.Dom;
	/**
	 * @class   Meta field to create a list of other fields
	 * @extends inputEx.Field
	 * @constructor
	 * @param {Object} options Added options:
	 * <ul>
	 *   <li>sortable: Add arrows to sort the items if true (default false)</li>
	 *   <li>elementType: an element type definition (default is {type: 'string'})</li>
	 *   <li>useButtons: use buttons instead of links (default false)</li>
	 *   <li>unique: require values to be unique (default false)</li>
	 *   <li>listAddLabel: if useButtons is false, text to add an item</li>
	 *   <li>listRemoveLabel: if useButtons is false, text to remove an item</li>
	 * </ul>
	 */
	inputEx.ListField = function(options) {

	   /**
	    * List of all the subField instances
	    */
	   this.subFields = [];

	   inputEx.ListField.superclass.constructor.call(this, options);
	};
	lang.extend(inputEx.ListField,inputEx.Field,
	/**
	 * @scope inputEx.ListField.prototype
	 */
	{
		// reference to the last copied fieldset
		lastCopiedFieldSet : {},
		/**
		 * Set the ListField classname
		 * @param {Object} options Options object (inputEx inputParams) as passed to the constructor
		 */
		setOptions: function(options) {
		   inputEx.ListField.superclass.setOptions.call(this, options);

		   this.options.className = options.className ? options.className : 'inputEx-Field inputEx-ListField';

		   this.options.sortable = lang.isUndefined(options.sortable) ? false : options.sortable;
		   this.options.elementType = options.elementType || {type: 'string'};
		   this.options.useButtons = lang.isUndefined(options.useButtons) ? false : options.useButtons;
		   this.options.unique = lang.isUndefined(options.unique) ? false : options.unique;

		   this.options.listAddLabel = options.listAddLabel || inputEx.messages.listAddLink;
		   this.options.listRemoveLabel = options.listRemoveLabel || inputEx.messages.listRemoveLink;
		},

		/**
		 * Render the addButton
		 */
		renderComponent: function() {

		   // Add element button
		   if(this.options.useButtons) {
		      this.addButton = inputEx.cn('img', {src: inputEx.spacerUrl, className: 'inputEx-ListField-addButton'});
		      this.fieldContainer.appendChild(this.addButton);
	      }

		   // List label
		   this.fieldContainer.appendChild( inputEx.cn('span', null, {marginLeft: "4px"}, this.options.listLabel) );

		   // Div element to contain the children
		   this.childContainer = inputEx.cn('div', {className: 'inputEx-ListField-childContainer'});
		   this.fieldContainer.appendChild(this.childContainer);

		   // Add link
		   if(!this.options.useButtons) {
		      this.addButton = inputEx.cn('a', {className: 'inputEx-List-link'}, null, this.options.listAddLabel);
		      this.fieldContainer.appendChild(this.addButton);
	      }
		},

		/**
		 * Handle the click event on the add button
		 */
		initEvents: function() {
		   Event.addListener(this.addButton, 'click', this.onAddButton, this, true);
		},

		/**
	    * Validate each field
	    * @returns {Boolean} true if all fields validate, required fields are not empty and unique constraint (if specified) is not violated
	    */
	   validate: function() {
	      var response = true;
	      var uniques = {};

	      // Validate all the sub fields
	      for (var i = 0 ; i < this.subFields.length && response; i++) {
	         var input = this.subFields[i];
	         input.setClassFromState(); // update field classes (mark invalid fields...)
	         var state = input.getState();
	         if( state == inputEx.stateRequired || state == inputEx.stateInvalid ) {
	            response = false; // but keep looping on fields to set classes
	         }
	         if(this.options.unique) {
	            var hash = lang.dump(input.getValue());
	            //logDebug('listfied index ',i, 'hash', hash);
	            if(uniques[hash]) {
	               response = false;    // not unique
	            } else {
	               uniques[hash] = true;
	            }
	          }
	      }
	      return response;
	   },

		/**
		 * Set the value of all the subfields
		 * @param {Array} value The list of values to set
		 * @param {boolean} [sendUpdatedEvt] (optional) Wether this setValue should fire the updatedEvt or not (default is true, pass false to NOT send the event)
		 */
		setValue: function(value, sendUpdatedEvt) {

		   if(!lang.isArray(value) ) {
		      // TODO: throw exceptions ?
		      return;
		   }

		   // Set the values (and add the lines if necessary)
		   for(var i = 0 ; i < value.length ; i++) {
		      if(i == this.subFields.length) {
		         this.addElement(value[i]);
		      }
		      else {
		         this.subFields[i].setValue(value[i], false);
		      }
		   }

		   // Remove additional subFields
		   var additionalElements = this.subFields.length-value.length;
		   if(additionalElements > 0) {
		      for(var i = 0 ; i < additionalElements ; i++) {
		         this.removeElement(value.length);
		      }
		   };

		   inputEx.ListField.superclass.setValue.call(this, value, sendUpdatedEvt);
		},

		/**
		 * Return the array of values
		 * @return {Array} The array
		 */
		getValue: function() {
		   var values = [];
		   for(var i = 0 ; i < this.subFields.length ; i++) {
		      values[i] = this.subFields[i].getValue();
		   }
		   return values;
		},

		/**
		 * Adds an element
		 * @param {Any} The initial value of the subfield to create
		 * @return {inputEx.Field} SubField added instance
		 */
		addElement: function(value) {

		   // Render the subField
		   var subFieldEl = this.renderSubField(value);

		   // Adds it to the local list
		   this.subFields.push(subFieldEl);

		   return subFieldEl;
		},

		/**
		 * Add a new element to the list and fire updated event
		 * @param {Event} e The original click event
		 */
		onAddButton: function(e) {
		   Event.stopEvent(e);
		   // Add a field with no value:
		   var subFieldEl = this.addElement(),
			   fieldset,
			   copiedFieldSet = this.lastCopiedFieldSet;
			if(typeof copiedFieldSet['inputs'] !='undefined'){
				for(i = 0;i <  copiedFieldSet['inputs'].length;i++){
					fieldName =  copiedFieldSet['inputs'][i]['options']['name'];
					if (fieldName == 'relationName' || fieldName == 'propertyName'|| fieldName == 'propertyDescription') {
						copiedFieldSet['inputs'][i].setValue('');
					} else if (fieldName == 'uid') {
						copiedFieldSet['inputs'][i].setValue(parseInt(new Date().getTime() * Math.random(), 10));
					} else if (fieldName == 'propertyType') {
						fieldset = copiedFieldSet.fieldset;
						fieldset.removeAttribute('class');
						Dom.addClass(fieldset, copiedFieldSet['inputs'][i].getValue());
					} else if (fieldName == 'advancedSettings') {
						if (copiedFieldSet.options.value) {
							fieldset = copiedFieldSet.fieldset;
							fieldset.removeAttribute('class');
							Dom.addClass(fieldset, copiedFieldSet.options.value.relationType);
						}
					}
				}
			}

		   // Focus on this field
		   subFieldEl.focus();

		   // Fire updated !
		   this.fireUpdatedEvt();
		},

		/**
		 * Adds a new line to the List Field
	 	 * @param {Any} The initial value of the subfield to create
		 * @return  {inputEx.Field} instance of the created field (inputEx.Field or derivative)
		 */
		renderSubField: function(value) {

		   // Div that wraps the deleteButton + the subField
		   var newDiv = inputEx.cn('div');

		   // Delete button
		   if(this.options.useButtons) {
		      var delButton = inputEx.cn('img', {src: inputEx.spacerUrl, className: 'inputEx-ListField-delButton'});
		      Event.addListener( delButton, 'click', this.onDelete, this, true);
		      newDiv.appendChild( delButton );
	      }

		   // Instanciate the new subField
		   var opts = lang.merge({}, this.options.elementType);
		   if(!opts.inputParams) opts.inputParams = {};
		   if(!lang.isUndefined(value)) opts.inputParams.value = value;

		   // IS: Quick hack to get the list element number from within the field and its subfields.
		   document.currentListElementNumber = this.subFields.length;

		   var el = inputEx.buildField(opts);

		   /****  set a reference to the copy to enable value manipulation ****/
		   this.lastCopiedFieldSet = el;
		   var subFieldEl = el.getEl();

		   Dom.setStyle(subFieldEl, 'margin-left', '4px');
		   Dom.setStyle(subFieldEl, 'float', 'left');
		   newDiv.appendChild( subFieldEl );

		   // Subscribe the onChange event to resend it
		   el.updatedEvt.subscribe(this.onChange, this, true);

		   // Arrows to order:
		   if(this.options.sortable) {
		      var arrowUp = inputEx.cn('div', {className: 'inputEx-ListField-Arrow inputEx-ListField-ArrowUp t3js-icon icon icon-size-small '});
		      arrowUp.innerHTML = '<span class="icon-markup"> <img src="./sysext/core/Resources/Public/Icons/T3Icons/actions/actions-move-up.svg" width="16" height="16"> </span>';
		      Event.addListener(arrowUp, 'click', this.onArrowUp, this, true);
		      var arrowDown = inputEx.cn('div', {className: 'inputEx-ListField-Arrow inputEx-ListField-ArrowDown t3js-icon icon icon-size-small '});
			   arrowDown.innerHTML = '<span class="icon-markup"> <img src="./sysext/core/Resources/Public/Icons/T3Icons/actions/actions-move-down.svg" width="16" height="16"> </span>';
		      Event.addListener(arrowDown, 'click', this.onArrowDown, this, true);
		      newDiv.appendChild( arrowUp );
		      newDiv.appendChild( arrowDown );
		   }

		   // Delete link
		   if(!this.options.useButtons) {
		      var delButton = inputEx.cn('div', {className: 'inputEx-List-link t3js-icon icon icon-size-small icon-state-default t3-icon-edit-delete deleteButton icon-actions-edit-delete'}, null, this.options.listRemoveLabel);
			  delButton.innerHTML = '<span class="icon-markup"><img src="./sysext/core/Resources/Public/Icons/T3Icons/actions/actions-edit-delete.svg" width="16" height="16"></span>';
		      Event.addListener( delButton, 'click', this.onDelete, this, true);
		      newDiv.appendChild( delButton );
	      }

		   // Line breaker
		   newDiv.appendChild( inputEx.cn('div', null, {clear: "both"}) );

		   this.childContainer.appendChild(newDiv);

		   return el;
		},

		/**
		 * Switch a subField with its previous one
		 * Called when the user clicked on the up arrow of a sortable list
		 * @param {Event} e Original click event
		 */
		onArrowUp: function(e) {
		   var childElement = Event.getTarget(e).parentNode.parentNode.parentNode;

		   var previousChildNode = null;
		   var nodeIndex = -1;
		   for(var i = 1 ; i < childElement.parentNode.childNodes.length ; i++) {
		      var el=childElement.parentNode.childNodes[i];
		      if(el == childElement) {
		         previousChildNode = childElement.parentNode.childNodes[i-1];
		         nodeIndex = i;
		         break;
		      }
		   }

		   if(previousChildNode) {
		      // Remove the line
		      var removedEl = this.childContainer.removeChild(childElement);

		      // Adds it before the previousChildNode
		      var insertedEl = this.childContainer.insertBefore(removedEl, previousChildNode);

		      // Swap this.subFields elements (i,i-1)
		      var temp = this.subFields[nodeIndex];
		      this.subFields[nodeIndex] = this.subFields[nodeIndex-1];
		      this.subFields[nodeIndex-1] = temp;

		      // Color Animation
		      if(this.arrowAnim) {
		         this.arrowAnim.stop(true);
		      }
		      this.arrowAnim = new YAHOO.util.ColorAnim(insertedEl, {backgroundColor: { from: '#eeee33' , to: '#eeeeee' }}, 0.4);
		      this.arrowAnim.onComplete.subscribe(function() { Dom.setStyle(insertedEl, 'background-color', ''); });
		      this.arrowAnim.animate();

		      // Fire updated !
		      this.fireUpdatedEvt();
		   }
		},

		/**
		 * Switch a subField with its next one
		 * Called when the user clicked on the down arrow of a sortable list
		 * @param {Event} e Original click event
		 */
		onArrowDown: function(e) {
		   var childElement = Event.getTarget(e).parentNode.parentNode.parentNode;

		   var nodeIndex = -1;
		   var nextChildNode = null;
		   for(var i = 0 ; i < childElement.parentNode.childNodes.length ; i++) {
		      var el=childElement.parentNode.childNodes[i];
		      if(el == childElement) {
		         nextChildNode = childElement.parentNode.childNodes[i+1];
		         nodeIndex = i;
		         break;
		      }
		   }

		   if(nextChildNode) {
		      // Remove the line
		      var removedEl = this.childContainer.removeChild(childElement);
		      // Adds it after the nextChildNode
		      var insertedEl = Dom.insertAfter(removedEl, nextChildNode);

		      // Swap this.subFields elements (i,i+1)
		      var temp = this.subFields[nodeIndex];
		      this.subFields[nodeIndex] = this.subFields[nodeIndex+1];
		      this.subFields[nodeIndex+1] = temp;

		      // Color Animation
		      if(this.arrowAnim) {
		         this.arrowAnim.stop(true);
		      }
		      this.arrowAnim = new YAHOO.util.ColorAnim(insertedEl, {backgroundColor: { from: '#eeee33' , to: '#eeeeee' }}, 1);
		      this.arrowAnim.onComplete.subscribe(function() { Dom.setStyle(insertedEl, 'background-color', ''); });
		      this.arrowAnim.animate();

		      // Fire updated !
		      this.fireUpdatedEvt();
		   }
		},

		/**
		 * Called when the user clicked on a delete button.
		 * @param {Event} e The original click event
		 */
		onDelete: function(e) {

		   Event.stopEvent(e);

		   // Get the wrapping div element
		   var elementDiv = Event.getTarget(e).parentNode.parentNode.parentNode;

		   // Get the index of the subField
		   var index = -1;

		   var subFieldEl = elementDiv.childNodes[this.options.useButtons ? 1 : 0];
		   for(var i = 0 ; i < this.subFields.length ; i++) {
		      if(this.subFields[i].getEl() == subFieldEl) {
		         index = i;
		         break;
		      }
		   }

		   // Remove it
		   if(index != -1) {
		      this.removeElement(index);
		   }

		   // Fire the updated event
		   this.fireUpdatedEvt();
		},

		/**
		 * Remove the line from the dom and the subField from the list.
		 * @param {integer} index The index of the element to remove
		 */
		removeElement: function(index) {
		   var elementDiv = this.subFields[index].getEl().parentNode;
			var fieldToRemove = this.subFields[index];
			if (fieldToRemove.inputs) {
				for(var i = 0; i < fieldToRemove.inputs.length; i++) {
					if(fieldToRemove.inputs[i].terminal !== undefined) {
						fieldToRemove.inputs[i].terminal.removeAllWires();
					}
				}
			}

		   this.subFields[index] = undefined;
		   this.subFields = inputEx.compactArray(this.subFields);

		   // Remove the element
		   elementDiv.parentNode.removeChild(elementDiv);
		}

	});

	/**
	 * Register this class as "list" type
	 */
	inputEx.registerType("list", inputEx.ListField);


	inputEx.messages.listAddLink = TYPO3.settings.extensionBuilder._LOCAL_LANG.add;
	inputEx.messages.listRemoveLink = TYPO3.settings.extensionBuilder._LOCAL_LANG.remove;

	})();
