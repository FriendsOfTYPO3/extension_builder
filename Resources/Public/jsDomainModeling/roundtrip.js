var roundtrip = {
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
}

