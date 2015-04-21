//Listen when a button, with a class of "myButton", is clicked
//You can use any jQuery/JavaScript event that you'd like to trigger the call
//Send the AJAX call to the server
var modMain = {};

modMain.__intAuthStatCallBack = function (data, textStatus, jqXHR) {
	if ( data.IsVald == 1 ) {
		$("#username").html("Welcome " + data.User);
		$("#username").show();
		$("#authaction").text("LogOut");
		$("#authaction").click(modMain.handleAuthAction);
		modAuth.setUserName(data.User);
		return;
	}
	$("#username").hide();
	$("#authaction").text("LogIn");
	$("#authaction").click(modMain.handleAuthAction);
};
	
modMain.initAuthetication = function () {
	$.post('modules/auth/moduleEntry.php', {action:'getAuthStatus'}, modMain.__intAuthStatCallBack, 'json');
};

modMain.initStatic = function () {
	if ( modStatic.initialized == 0 ) {
		modStatic.init();
	}
};

/* ****************************************************
If #authaction is set to "LogIn"
	Call function to open the login dialogue
If #authaction is set to "Logout"
	Call function to logout user
	Hide username
*/

modMain.handleAuthAction = function () {
	if ( $("#authaction").html() == "LogIn" ) {
		modAuth.initAuth("#auth","#username","#authaction");
	} else {
		modAuth.logOut();
		$("#username").hide();
		$("#authaction").text("LogIn");
	}
};


//<![CDATA[
jQuery(document).ready(function($) {
	$('#flexme1').flexigrid({usepager : true,useRp : true,rp : 15,showTableToggleBtn : true});
	$('#flexme1').flexReload();
	$('#flexme1').hide();
	$('#divFlexme1').hide();
	
	$('#feature-left').children().hide();
	$('#feature-right').children().hide();
});
//]]>

function namespace(namespaceString) {
    var parts = namespaceString.split('.'),
        parent = window,
        currentPart = '';    
        
    for(var i = 0, length = parts.length; i < length; i++) {
        currentPart = parts[i];
        parent[currentPart] = parent[currentPart] || {};
        parent = parent[currentPart];
    }
    
    return parent;
};

$(document).ready(function(){
   $("#zone-bar li em").click(function() {
		var hidden = $(this).parents("li").children("ul").is(":hidden");

		$("#zone-bar>ul>li>ul").hide();
		$("#zone-bar>ul>li>a").removeClass();

		if (hidden) {
			$(this)
				.parents("li").children("ul").toggle()
				.parents("li").children("a").addClass("zoneCur");
			}
	   });

	$("#zone-bar li ul li").click(function() {
		var selectedoption = this.id;

		$("#zone-bar>ul>li>ul").hide();
		$("#zone-bar>ul>li>a").removeClass();
		
		modMain.initStatic();

		if ( selectedoption == "GuestRegistration" ) {
			//modAuth.initAuth("#feature-left","#username","#authaction");
			//modAdmin.userAdmin("#feature-left","#feature-mini-left","#feature-mini-right");
			// modDevotee.registerDevotee("#divDynaFrm1","#divDynaFrmBody1","#divDynaFrmProgress1","#divDynaFrmResult1") ;
			$("#feature-right").hide();
		}

		if ( selectedoption == "DevoteeUploadEXL" ) {
			//modAdmin.xmlLoad("#divExcelUploadForm2","#frmExcelUpload2","#divExcelFrm2Ret");
			modDevotee.loadDevoteesFromExcel("#divDynaFrm1","#divDynaFrmBody1","#divDynaFrmProgress1","#divDynaFrmResult1");
			$("#feature-right").hide();
		}

		if ( selectedoption == "AttendenceFormDnLoadEXL" ) {
			//modAdmin.xmlLoad("#divExcelUploadForm2","#frmExcelUpload2","#divExcelFrm2Ret");
			//modDevotee.dnloadAttendenceEntrySheet("#divDynaFrm1","#divDynaFrmBody1","#divDynaFrmProgress1","#divDynaFrmResult1");
			$("#feature-right").hide();
		}
		
		
		if ( selectedoption == "AttendenceUploadEXL" ) {
			//modAdmin.xmlLoad("#divExcelUploadForm2","#frmExcelUpload2","#divExcelFrm2Ret");
			modDevotee.loadAttendenceFromExcel("#divDynaFrm1","#divDynaFrmBody1","#divDynaFrmProgress1","#divDynaFrmResult1");
			$("#feature-right").hide();
		}

		if ( selectedoption == "DisableDevoteeEXL" ) {
			//modAdmin.xmlLoad("#divExcelUploadForm2","#frmExcelUpload2","#divExcelFrm2Ret");
			modDevotee.DisableDevoteeFromExcel("#divDynaFrm1","#divDynaFrmBody1","#divDynaFrmProgress1","#divDynaFrmResult1");
			$("#feature-right").hide();
		}

		if ( selectedoption == "ProgramSummary" ) {
			//modAdmin.xmlLoad("#divExcelUploadForm2","#frmExcelUpload2","#divExcelFrm2Ret");
			$("#feature-right").hide();
			modDevotee.ProgramSummary("#divDynaFrm1","#divDynaFrmBody1","#divDynaFrmProgress1","#divDynaFrmResult1");
		}
		
		
	});

	modMain.initAuthetication();
	$("#main-search").hide();

});




