var roundtrip = {
		debugMode			:	true,
		renderFieldHook 	:	function(input){
									if(input.inputParams.name == 'uid' && typeof input.inputParams.value == 'undefined'){
										input.inputParams.value = this.createUniqueId();
									}
									return input;
								}
							
		,addFieldSetHook	:	function(fieldset){
									if(typeof fieldset['inputs'] !='undefined'){
										for(i = 0;i <  fieldset['inputs'].length;i++){
											fieldName =  fieldset['inputs'][i]['options']['name'];
											
											if (fieldName == 'relationName' || fieldName == 'propertyName') {
												fieldset['inputs'][i].setValue('');
											}
											else if (fieldName == 'uid') {
												//console.log('Old:' + fieldset['inputs'][i].getValue());
												fieldset['inputs'][i].setValue(this.createUniqueId());
												//console.log('New:' + fieldset['inputs'][i].getValue());
											}
										}
									}
								}
									
		,createUniqueId		:	function(){
									var d = new Date;
									return parseInt(d.getTime() * Math.random());
								}
		
		,updateEvtListener	:	function(params){
									//console.log('updateEvtListener called');
									if(typeof params[0] != 'object'){
										//console.log(params[1]);
									}
								}
		,onAddWire			:	function(e, params, terminal){
									var uid1 = this.getUidForTerminal(params[0].terminal1);
									var uid2 = this.getUidForTerminal(params[0].terminal2);

									this._debug('Wire added');
									if(Ext.get(params[0].terminal2.el).getAttribute('title') == 'SOURCES'){
										var moduleUID =  uid1;
										this._debug('45 moduleUID: ' + moduleUID);
										var relationUID = uid2
										this._debug('47 relationUID: ' + relationUID);
									}
									else {
										var moduleUID =  uid2
										this._debug('51 moduleUID: ' + moduleUID);
										var relationUID = uid1
										this._debug('53 relationUID: ' + relationUID);
									}
								}
		
		,onRemoveWire			:	function(e, params, terminal){
										var t1 = Ext.get(params[0].terminal1.el);
										var t2 = Ext.get(params[0].terminal2.el);
										this._debug(this.getUidForTerminal(params[0].terminal1));
										this._debug(this.getUidForTerminal(params[0].terminal2));
										this._debug('Wire removed');
										if(t1.getAttribute('title') == 'SOURCES'){
											var moduleUID =  t2.findParent("fieldset",10,true).query('div.hiddenField input')[0].value;
											this._debug('moduleUID: ' + moduleUID);
											var relationUID = t1.parent().query('div.hiddenField input')[0].value;
											this._debug('relationUID: ' + relationUID);
										}
										else {
											var moduleUID =  t1.findParent("fieldset",10,true).query('div.hiddenField input')[0].value;
											this._debug('moduleUID: ' + moduleUID);
											var relationUID = t2.parent().query('div.hiddenField input')[0].value;
											this._debug('relationUID: ' + relationUID);
										}
									}
		,onFieldRendered		:	function(fieldId){
										//this._debug('onFieldRendered called: ' + fieldId);
										var l = Ext.get(
											Ext.query('div#' + fieldId + '-label')
										);
										if(l && Ext.query('div#' + fieldId + '-desc').length){
											l.addListener(
												"mouseover",
												function(ev,target){
													roundtrip.showHelp(target,true);
												}
											);
											l.addListener(
													"mouseout",
													function(ev,target){
														roundtrip.showHelp(target,false);
													}
												);
											l.addClass('helpAvailable')
										}
									}
		,getUidForTerminal		:	function(terminal){
										var t = Ext.get(terminal.el);
										if(t.getAttribute('title') == 'SOURCES'){
											return t.parent().query('div.hiddenField input')[0].value;
										}
										else {
											return t.findParent("fieldset",10,true).query('div.hiddenField input')[0].value;
										}
		}
		,showHelp				:	function(targetEl,show){
										var descriptionElement = Ext.get(targetEl.id.replace('label','desc'));
										if(descriptionElement && descriptionElement.dom.innerHTML.length){
											if(show){
												descriptionElement.show();
											}
											else {
												descriptionElement.hide();
											}
										}
									}
		,_debug					:	function(o){
										if(!this.debugMode){
											return;
										}
										if(typeof console != 'undefined' && typeof console.log == 'function'){
											console.log(o);
										}
									}
}

var versionMap = {
    '6.0' : '6.0'
}

Ext.onReady(
    function() {
        Ext.get(Ext.query('select[name=targetVersion]')[0]).addListener(
            "change",
            function(ev,target){
                var updatedDependencies = '';
                var dependencies = Ext.query('textarea[name=dependsOn]')[0].value.split("\n");
                for(i=0;i<dependencies.length;i++) {
                    parts = dependencies[i].split('=>');
                    if(parts.length==2) {
                        if(parts[0].indexOf('fluid')> -1) {
                            parts[1] = versionMap[target.value];
                        }
                        if(parts[0].indexOf('extbase')> -1) {
                            parts[1] = versionMap[target.value];
                        }
                        if(parts[0].indexOf('typo3')> -1) {
                           parts[1] = target.value;
                        }
                        updatedDependencies += parts[0] + '=> ' + parts[1] + "\n";
                    }

                }
                Ext.query('textarea[name=dependsOn]')[0].value = updatedDependencies;
            }
         );
		Ext.get(Ext.query('body')[0]).addClass('yui-skin-sam');
    }
);