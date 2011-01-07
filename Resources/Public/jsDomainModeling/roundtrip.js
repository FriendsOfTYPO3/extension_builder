var roundtrip = {
		debugMode			:	false,
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
									var t1 = Ext.get(params[0].terminal1.el);
									var t2 = Ext.get(params[0].terminal2.el);
									this._debug('Wire added');
									if(t1.getAttribute('title') == 'SOURCES'){
										var moduleUID =  t2.parent().parent().parent().query('div.hiddenField input')[0].value;
										this._debug('moduleUID: ' + moduleUID);
										var relationUID = t1.parent().query('div.hiddenField input')[0].value;
										this._debug('relationUID: ' + relationUID);
									}
									else {
										var moduleUID =  t1.parent().parent().parent().query('div.hiddenField input')[0].value;
										this._debug('moduleUID: ' + moduleUID);
										var relationUID = t2.parent().query('div.hiddenField input')[0].value;
										this._debug('relationUID: ' + relationUID);
									}
								}
		
		,onRemoveWire			:	function(e, params, terminal){
										var t1 = Ext.get(params[0].terminal1.el);
										var t2 = Ext.get(params[0].terminal2.el);
										this._debug('Wire removed');
										if(t1.getAttribute('title') == 'SOURCES'){
											var moduleUID =  t2.parent().parent().parent().query('div.hiddenField input')[0].value;
											this._debug('moduleUID: ' + moduleUID);
											var relationUID = t1.parent().query('div.hiddenField input')[0].value;
											this._debug('relationUID: ' + relationUID);
										}
										else {
											var moduleUID =  t1.parent().parent().parent().query('div.hiddenField input')[0].value;
											this._debug('moduleUID: ' + moduleUID);
											var relationUID = t2.parent().query('div.hiddenField input')[0].value;
											this._debug('relationUID: ' + relationUID);
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

