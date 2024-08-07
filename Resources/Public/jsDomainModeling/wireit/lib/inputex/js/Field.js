(function () {
  var inputEx = YAHOO.inputEx,
    Dom = YAHOO.util.Dom,
    lang = YAHOO.lang,
    util = YAHOO.util,
    Event = YAHOO.util.Event;

  /**
   * @class An abstract class that contains the shared features for all fields
   * @constructor
   * @param {Object} options Configuration object
   * <ul>
   *    <li>name: the name of the field</li>
   *    <li>required: boolean, the field cannot be null if true</li>
   *   <li>className: CSS class name for the div wrapper (default 'inputEx-Field')</li>
   *   <li>value: initial value</li>
   *   <li>parentEl: HTMLElement or String id, append the field to this DOM element</li>
   * </ul>
   */
  inputEx.Field = function (options) {
    if (!options) {
      var options = {};
    }

    // Set the default values of the options
    this.setOptions(options);

    // Call the render of the dom
    this.render();

    /**
     * @event
     * @param {Any} value The new value of the field
     * @desc YAHOO custom event fired when the field is "updated"<br /> subscribe with: this.updatedEvt.subscribe(function(e, params) { var value = params[0]; console.log("updated",value, this.updatedEvt); }, this, true);
     */
    this.updatedEvt = new util.CustomEvent("updated", this);

    // initialize behaviour events
    this.initEvents();

    // Set the initial value
    //   -> no initial value = no style (setClassFromState called by setValue)
    if (!lang.isUndefined(this.options.value)) {
      this.setValue(this.options.value, false);
    }

    // append it immediatly to the parent DOM element
    if (options.parentEl) {
      if (lang.isString(options.parentEl)) {
        Dom.get(options.parentEl).appendChild(this.getEl());
      } else {
        options.parentEl.appendChild(this.getEl());
      }
    }
  };

  inputEx.Field.prototype = {
    /**
     * Set the default values of the options
     * @param {Object} options Options object (inputEx inputParams) as passed to the constructor
     */
    setOptions: function (options) {
      /**
       * Configuration object to set the options for this class and the parent classes. See constructor details for options added by this class.
       */
      this.options = {};

      // Basic options
      this.options.name = options.name;
      this.options.value = options.value;
      this.options.id = options.id || Dom.generateId();
      this.options.label = options.label;
      this.options.description = options.description;
      this.options.helpLink = options.helpLink;

      // Define default messages
      this.options.messages = {};
      this.options.messages.required =
        options.messages && options.messages.required
          ? options.messages.required
          : inputEx.messages.required;
      this.options.messages.invalid =
        options.messages && options.messages.invalid
          ? options.messages.invalid
          : inputEx.messages.invalid;
      //this.options.messages.valid = (options.messages && options.messages.valid) ? options.messages.valid : inputEx.messages.valid;

      // Other options
      this.options.className = options.className
        ? options.className
        : "inputEx-Field";
      this.options.wrapperClassName = options.wrapperClassName
        ? options.wrapperClassName
        : "inputEx-fieldWrapper";
      this.options.required = lang.isUndefined(options.required)
        ? false
        : options.required;
      this.options.showMsg = lang.isUndefined(options.showMsg)
        ? false
        : options.showMsg;
      this.options.advancedMode = lang.isUndefined(options.advancedMode)
        ? false
        : options.advancedMode;
    },

    /**
     * Default render of the dom element. Create a divEl that wraps the field.
     */
    render: function () {
      // Create a DIV element to wrap the editing el and the image
      this.divEl = inputEx.cn("div", {
        className: this.options.wrapperClassName,
      });
      //qwertz
      if (this.options.id) {
        this.divEl.id = this.options.id;
      }
      if (this.options.required) {
        Dom.addClass(this.divEl, "inputEx-required");
      }
      if (this.options.advancedMode) {
        Dom.addClass(this.divEl, "advancedMode");
      }

      // Label element
      if (this.options.label) {
        var labelClassName = "";
        var linkEl;
        if (this.options.description) {
          labelClassName = "helpAvailable";
        }
        this.labelDiv = inputEx.cn("div", {
          id: this.divEl.id + "-label",
          className: "inputEx-label",
          for: this.divEl.id + "-field",
        });
        this.labelEl = inputEx.cn("label", { className: labelClassName });
        this.labelEl.appendChild(document.createTextNode(this.options.label));
        this.labelDiv.appendChild(this.labelEl);
        if (this.options.helpLink) {
          linkEl = inputEx.cn("a", {
            href: this.options.helpLink,
            target: "_blank",
            title: "Open TYPO3 documentation in new window",
          });
          linkEl.innerHTML =
            '<span class="t3js-icon icon icon-size-small icon-state-default" style="margin-left: 10px; margin-bottom:2px">' +
            '<span class="icon-markup">' +
            '<svg class="icon-color" style="width:12px; height:12px;"><use xlink:href="/_assets/1ee1d3e909b58d32e30dcea666dd3224/Icons/T3Icons/sprites/apps.svg#apps-toolbar-menu-help"></use></svg>' +
            "</span>" +
            "</span>";
          this.labelDiv.appendChild(linkEl);
        }
        this.divEl.appendChild(this.labelDiv);
      }

      if (this.options.description) {
        this.options.className += " helpAvailable";
      }

      this.fieldContainer = inputEx.cn("div", {
        className: this.options.className,
      }); // for wrapping the field and description

      // Render the component directly
      this.renderComponent();

      // Description
      if (this.options.description) {
        this.descriptionElement = inputEx.cn(
          "div",
          {
            id: this.divEl.id + "-desc",
            className: "inputEx-description",
          },
          null,
          this.options.description,
        );
        this.fieldContainer.appendChild(this.descriptionElement);
        Event.addListener(
          this.labelDiv,
          "mouseover",
          this.showDescription,
          this,
          true,
        );
        Event.addListener(
          this.labelDiv,
          "mouseout",
          this.hideDescription,
          this,
          true,
        );
      }

      this.divEl.appendChild(this.fieldContainer);

      // Insert a float breaker
      this.divEl.appendChild(inputEx.cn("div", null, { clear: "both" }, " "));
    },

    showDescription: function () {
      this.descriptionElement.style.display = "block";
    },
    hideDescription: function () {
      this.descriptionElement.style.display = "none";
    },

    /**
     * Fire the "updated" event (only if the field validated)
     * Escape the stack using a setTimeout
     */
    fireUpdatedEvt: function () {
      // Uses setTimeout to escape the stack (that originiated in an event)
      var that = this;
      setTimeout(function () {
        that.updatedEvt.fire(that.getValue(), that);
      }, 50);
    },

    /**
     * Render the interface component into this.divEl
     */
    renderComponent: function () {
      // override me
    },

    /**
     * The default render creates a div to put in the messages
     * @return {HTMLElement} divEl The main DIV wrapper
     */
    getEl: function () {
      return this.divEl;
    },

    /**
     * Initialize events of the Input
     */
    initEvents: function () {
      // override me
    },

    /**
     * Return the value of the input
     * @return {Any} value of the field
     */
    getValue: function () {
      // override me
    },

    /**
     * Function to set the value
     * @param {Any} value The new value
     * @param {boolean} [sendUpdatedEvt] (optional) Wether this setValue should fire the updatedEvt or not (default is true, pass false to NOT send the event)
     */
    setValue: function (value, sendUpdatedEvt) {
      // to be inherited

      // set corresponding style
      this.setClassFromState();

      if (sendUpdatedEvt !== false) {
        // fire update event
        this.fireUpdatedEvt();
      }
    },

    /**
     * Set the styles for valid/invalide state
     */
    setClassFromState: function () {
      // remove previous class
      if (this.previousState) {
        // remove invalid className for both required and invalid fields
        var className =
          "inputEx-" +
          (this.previousState == inputEx.stateRequired
            ? inputEx.stateInvalid
            : this.previousState);
        Dom.removeClass(this.divEl, className);
      }

      // add new class
      var state = this.getState();
      if (
        !(
          state == inputEx.stateEmpty &&
          Dom.hasClass(this.divEl, "inputEx-focused")
        )
      ) {
        // add invalid className for both required and invalid fields
        var className =
          "inputEx-" +
          (state == inputEx.stateRequired ? inputEx.stateInvalid : state);
        Dom.addClass(this.divEl, className);
      }

      if (this.options.showMsg) {
        this.displayMessage(this.getStateString(state));
      }

      this.previousState = state;
    },

    /**
     * Get the string for the given state
     */
    getStateString: function (state) {
      if (state == inputEx.stateRequired) {
        return this.options.messages.required;
      } else if (state == inputEx.stateInvalid) {
        return this.options.messages.invalid;
      } else {
        return "";
      }
    },

    /**
     * Returns the current state (given its value)
     * @return {String} One of the following states: 'empty', 'required', 'valid' or 'invalid'
     */
    getState: function () {
      // if the field is empty :
      if (this.isEmpty()) {
        return this.options.required
          ? inputEx.stateRequired
          : inputEx.stateEmpty;
      }
      return this.validate() ? inputEx.stateValid : inputEx.stateInvalid;
    },

    /**
     * Validation of the field
     * @return {Boolean} field validation status (true/false)
     */
    validate: function () {
      return true;
    },

    /**
     * Function called on the focus event
     * @param {Event} e The original 'focus' event
     */
    onFocus: function (e) {
      var el = this.getEl();
      Dom.removeClass(el, "inputEx-empty");
      Dom.addClass(el, "inputEx-focused");
    },

    /**
     * Function called on the blur event
     * @param {Event} e The original 'blur' event
     */
    onBlur: function (e) {
      Dom.removeClass(this.getEl(), "inputEx-focused");

      // Call setClassFromState on Blur
      this.setClassFromState();
    },

    /**
     * onChange event handler
     * @param {Event} e The original 'change' event
     */
    onChange: function (e) {
      this.fireUpdatedEvt();
    },

    /**
     * Close the field and eventually opened popups...
     */
    close: function () {},

    /**
     * Disable the field
     */
    disable: function () {},

    /**
     * Enable the field
     */
    enable: function () {},

    /**
     * Focus the field
     */
    focus: function () {},

    /**
     * Purge all event listeners and remove the component from the dom
     */
    destroy: function () {
      var el = this.getEl();

      // Unsubscribe all listeners on the updatedEvt
      this.updatedEvt.unsubscribeAll();

      // Remove from DOM
      if (Dom.inDocument(el)) {
        el.parentNode.removeChild(el);
      }

      // recursively purge element
      util.Event.purgeElement(el, true);
    },

    /**
     * Update the message
     * @param {String} msg Message to display
     */
    displayMessage: function (msg) {
      if (!this.fieldContainer) {
        return;
      }
      if (!this.msgEl) {
        this.msgEl = inputEx.cn("div", { className: "inputEx-message" });
        try {
          var divElements = this.divEl.getElementsByTagName("div");
          this.divEl.insertBefore(
            this.msgEl,
            divElements[
              divElements.length - 1 >= 0 ? divElements.length - 1 : 0
            ],
          ); //insertBefore the clear:both div
        } catch (e) {
          alert(e);
        }
      }
      this.msgEl.innerHTML = msg;
    },

    /**
     * Show the field
     */
    show: function () {
      this.divEl.style.display = "";
    },

    /**
     * Hide the field
     */
    hide: function () {
      this.divEl.style.display = "none";
    },

    /**
     * Clear the field by setting the field value to this.options.value
     * @param {boolean} [sendUpdatedEvt] (optional) Wether this clear should fire the updatedEvt or not (default is true, pass false to NOT send the event)
     */
    clear: function (sendUpdatedEvt) {
      this.setValue(
        lang.isUndefined(this.options.value) ? "" : this.options.value,
        sendUpdatedEvt,
      );
    },

    /**
     * Should return true if empty
     */
    isEmpty: function () {
      return this.getValue() === "";
    },
  };
})();
