var modStatic = {};
modStatic.initialized = 0;
modStatic.centreoptions = {};
modStatic.progoptions = {};

modStatic.init = function () {
	$.post('modules/devotee/moduleEntry.php', 
			{action:'getCentreProgramMeta'}, 
			function(data,status){
				if ( data.callstatus == "FAIL") {
					alert ("Server Problem : 1000105");
					return ;
				}

				modStatic.initialized = 1;
				var CentreOptions = "";
				var centreArray = data['centres'];

				for(var cname in centreArray) {
					var cid = centreArray[cname];
					CentreOptions = CentreOptions + "<option value=" + cid + ">" +  cname + "</option>";

					var ProgramOptions = "";
					var programArray = data['centre_programs'][cid];

					for(var pname in programArray) {
						var pid = programArray[pname];
						ProgramOptions = ProgramOptions + "<option value=" + pid + ">" +  pname + "</option>";
					}
					
					modStatic.progoptions[cid] = ProgramOptions; 
				}
				
				modStatic.centreoptions = CentreOptions;
				//alert("Data: " + modStatic.centreoptions + "\nStatus: " + status);
			  }, 
			'json');
};


