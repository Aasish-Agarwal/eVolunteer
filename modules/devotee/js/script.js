var modDevotee = {};

$( document ).ajaxError(function() {
  //alert( "Triggered ajaxError handler." );
});

function ajaxError(jqXHR, textStatus, errorThrown) {
     //   alert('$.post error: ' + textStatus + ' : ' + errorThrown);
};


modDevotee.__intLoadProgramCallBack = function (data, textStatus, jqXHR) {
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000101");
		return ;
	}

	var ProgramOptions = "";
	
	var programArray = data['programs'];
	for(var prop in programArray) {
		ProgramOptions = ProgramOptions + "<option value=" + programArray[prop] + ">" +  prop + "</option>";
	}

	$("select#Program").html(ProgramOptions);
};
modDevotee.__intLoadAttribCallBack = function (data, textStatus, jqXHR) {
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000105");
		return ;
	}

	var SelectOptions = "";
	
	var dataArray = data['attributes'];
	
    for (var i = 0; i < dataArray.length; i++) {
		SelectOptions = SelectOptions + "<option value=" + dataArray[i].dbname + ">" +  dataArray[i].property + "</option>";
    }

	$("select#Attribute").html(SelectOptions);
};

// this method is called when chart is first inited as we listen for "dataUpdated" event
function zoomChart() {
    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
	modDevotee.chart.zoomToIndexes(modDevotee.chartData.length - 40, modDevotee.chartData.length - 1);
}

modDevotee.__intGenChart = function (chartData, categoryField, valueField, targetname, target , targetContainer ) {
    var chart;

	chart = new AmCharts.AmSerialChart();
    chart.dataProvider = chartData;
    chart.categoryField = categoryField;
    chart.startDuration = 1;
    

    modDevotee.chart = chart;
    modDevotee.chartData = chartData;
    
    // listen for "dataUpdated" event (fired when chart is rendered) and call zoomChart method when it happens
    chart.addListener("dataUpdated", zoomChart);
    
    // AXES
    // category
    var categoryAxis = chart.categoryAxis;
    categoryAxis.labelRotation = 90;
    categoryAxis.gridPosition = "start";

    // value
    // in case you don't want to change default settings of value axis,
    // you don't need to create it, as one value axis is created automatically.

    // GRAPH
    var graph = new AmCharts.AmGraph();
    graph.valueField = valueField;
    graph.balloonText = "[[category]]: [[value]]";
    graph.type = "column";
    graph.title = "Country";

    graph.lineAlpha = 0;
    graph.fillAlphas = 0.8;

    chart.addGraph(graph);

    $(targetContainer).show();
    $(target).show();

    // CURSOR
    chartCursor = new AmCharts.ChartCursor();
    chartCursor.cursorPosition = "mouse";
    chart.addChartCursor(chartCursor);

    // SCROLLBAR
    var chartScrollbar = new AmCharts.ChartScrollbar();
    chartScrollbar.graph = graph;
    chartScrollbar.scrollbarHeight = 10;
    chartScrollbar.color = "#000000";
    chartScrollbar.autoGridCount = true;
    //chart.addChartScrollbar(chartScrollbar);
    
    chart.write(targetname);
    
};

modDevotee.__intLoadAbsenteeCallBack = function (data, textStatus, jqXHR) {
    $("#chartdivAct").siblings().hide();
	$("#DrillDownDiv").hide();
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000107");
		return ;
	}

	$(modDevotee._frmResult).show();

	var chartData1 = data.attendence;

	$(modDevotee._frmResult).html('<font size="4">Devotees That Attended Their Last Class</font><hr>');
	$(modDevotee._frmResult).append('<font size="2">');

	// $retval[] = array('event_date' => $event_date, 'absentees' => $absentees , 'Date' => $event_date_pr);
	
    for (var i = 0; i < chartData1.length; i++) {

        var a = $('<a>');
        a.attr('title', chartData1[i].absentees);
        a.attr('category', 'attendence');
        a.attr('centre_id', data.centre_id);
        a.attr('absentees', chartData1[i].absentees);
        a.attr('prog_id', data.prog_id);
        a.attr('print_date', chartData1[i].Date);
        a.attr('event_date', chartData1[i].event_date);
        a.text(chartData1[i].Date);
        a.addClass('devAbsenteeDrillDown_class');
        $(modDevotee._frmResult).append(a);
        $(modDevotee._frmResult).append(":    " + chartData1[i].absentees );
        $(modDevotee._frmResult).append("<br><br>");
    }
	$(modDevotee._frmResult).append('</font>');

    $( '.devAbsenteeDrillDown_class' ).click(function() {
   	   
	   var centre_id = $(this).attr('centre_id');
   	   var prog_id = $(this).attr('prog_id');
   	   var event_date = $(this).attr('event_date');
   	   var absentees = $(this).attr('absentees');
   	   var print_date = $(this).attr('print_date');
   	   
   	   if ( absentees < 1) {
   		$("#DrillDownDiv").show();
   	   	   $("#DrillDownDiv").html("No devotees absent since " + print_date);
   		   return;
   	   }
   	   var content = "";
   	   content += "<br>event_date: " + event_date;
   	   content += "<br><hr>";

   	   $.post('modules/devotee/moduleEntry.php', {action:'getAbsenteeDev', 
   		centre_id:centre_id,prog_id:prog_id,event_date:event_date}, 
   		   			modDevotee.__intFillDevDrillDown, 'json');
   	   $("#DrillDownDiv").html(content);
   	 });

	modDevotee.__intGenChart (chartData1,"Date","absentees", "chartdivAct", "#chartdivAct" , "#feature-right") ;
};

modDevotee.__intLoadAttendenceCallBack = function (data, textStatus, jqXHR) {
    $("#chartdivAct").siblings().hide();
	$("#DrillDownDiv").hide();
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000103");
		return ;
	}

	$(modDevotee._frmResult).show();

	var chartData1 = data.attendence;

	$(modDevotee._frmResult).html('<font size="4">Attendence Details</font><hr>');
	$(modDevotee._frmResult).append('<font size="2">');

    for (var i = 0; i < chartData1.length; i++) {

        var a = $('<a>');
        a.attr('title', chartData1[i].Count);
        a.attr('category', 'attendence');
        a.attr('centre_id', data.centre_id);
        a.attr('prog_id', data.prog_id);
        a.attr('event_id', chartData1[i].event_id);
        a.text(chartData1[i].Date);
        a.addClass('devEventDrillDown_class');
        $(modDevotee._frmResult).append(a);
        $(modDevotee._frmResult).append(":    " + chartData1[i].Count );
        $(modDevotee._frmResult).append("<br><br>");
    }
	$(modDevotee._frmResult).append('</font>');

    $( '.devEventDrillDown_class' ).click(function() {
   	   
   	   var event_id = $(this).attr('event_id');
   	   var category = $(this).attr('category');

   	   var content = "";
   	   content += "<br>event_id: " + event_id;
   	   content += "<br>event_id: " + category;
   	   content += "<br><hr>";

   	   $.post('modules/devotee/moduleEntry.php', {action:'getEventDev', 
   		   			event_id:event_id}, 
   		   			modDevotee.__intFillDevDrillDown, 'json');
   	   $("#DrillDownDiv").html(content);
   	 });

	modDevotee.__intGenChart (chartData1,"Date","Count", "chartdivAct", "#chartdivAct" , "#feature-right") ;
};

modDevotee.__intFillDevDrillDown = function (data, textStatus, jqXHR) {
    $("#chartdivAct").siblings().hide();
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000106");
		return ;
	}

	if ( data.action == "getRegDevCntreProgAttrib" ) {
	    var completelink = "<A href='modules/devotee/moduleEntry.php?action=getDevExport&centre_id=" + data.centre_id +
	    			"&prog_id=" + data.prog_id + 
					"&category=" + 'attribute' + 
					"&attribute=" + data.attribute + 
					"&attribval=" + data.attribval + 
	    			"&period=" + data.period + 
	    			"&export_type=basic'>Distribution Export</A>";
	    
		$("#feature-mini-left").html(completelink);
		$("#feature-mini-left").show();
	}

	if ( data.action == "getAbsenteeDev" ) {
	    var completelink = "<A href='modules/devotee/moduleEntry.php?action=getDevExport&centre_id=" + data.centre_id +
					"&prog_id=" + data.prog_id + 
					"&event_date=" + data.event_date + 
					"&category=" + 'absentees' + 
	    			"&period=" + data.period + 
	    			"&export_type=basic'>Absentees Export</A>";
	    
		$("#feature-mini-left").html(completelink);
		$("#feature-mini-left").show();
	}
	
	if ( data.action == "getEventDev" ) {
	    var allDevotees = "<A href='modules/devotee/moduleEntry.php?action=getDevExport"+
					"&event_id=" + data.event_id + 
					"&category=" + 'attendence' + 
	    			"&export_type=basic'>Export All Devotees</A>";
	    
	    var newDevotees = "<A href='modules/devotee/moduleEntry.php?action=getDevExport"+
		"&event_id=" + data.event_id + 
		"&category=" + 'firsttimers' + 
		"&export_type=basic'>Export First Timers</A>";

	    $("#feature-mini-left").html(allDevotees + "<span>   </span>" + newDevotees);
		$("#feature-mini-left").show();
	}
	
	
	$("#DrillDownDiv").show();
	
	
	var individuals = data.individuals;

    var content = '<font size="2"><table border id="mygrid">';
    
	content +=  '<thead><tr>';
	content +=  '<td> Id </td>';
	content +=  '<td> Name </td>';
	content +=  '<td> InitiatedName </td>';
	content +=  '<td> Contact </td>';
	content +=  '<td> AlternateContact </td>';
	content +=  '<td> Mail </td>';
	content +=  '<td> Address </td>';
	content +=  '<td> SpouseName </td>';
	content +=  '<td> Gender </td>';
	content +=  '</tr></thead><tbody>';

    for (var i = 0; i < individuals.length; i++) {
    	content +=  '<tr>';
    	content +=  '<td>' + individuals[i].Id + '</td>';
    	content +=  '<td>' + individuals[i].Name + '</td>';
    	content +=  '<td>' + individuals[i].InitiatedName + '</td>';
    	content +=  '<td>' + individuals[i].Contact + '</td>';
    	content +=  '<td>' + individuals[i].AlternateContact + '</td>';
    	content +=  '<td>' + individuals[i].Mail + '</td>';
    	content +=  '<td>' + individuals[i].Address + '</td>';
    	content +=  '<td>' + individuals[i].SpouseName + '</td>';
    	content +=  '<td>' + individuals[i].Gender + '</td>';
    	
    	content +=  '</tr>';
    }
	content += "</tbody></table></font><hr>";
	$("#DrillDownDiv").html(content);

};

modDevotee.__intShowDistCallBack = function (data, textStatus, jqXHR) {
	$("#DrillDownDiv").hide();
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000104");
		return ;
	}

	$(modDevotee._frmResult).show();
	var chartData1 = data.distribution;

    var a = $('<a>');
    a.attr('title', data.unspecified + ' Devotees' );
    a.attr('category', 'centreprogram');
    a.attr('attribute', data.attribute);
    a.attr('centre_id', data.centre_id);
    a.attr('prog_id', data.prog_id);
    a.attr('period', data.period);
    a.attr('attribval', "null");
    a.text(data.unspecified + ' Devotees' );
    a.addClass('devDrillDown_class');

	$(modDevotee._frmResult).html('<font size="4">' + data.attribute + ' is not set for ' );
    $(modDevotee._frmResult).append(a);
    $(modDevotee._frmResult).append('</font><hr>');
	
	$(modDevotee._frmResult).append('<font size="2">');

    for (var i = 0; i < chartData1.length; i++) {
        var a = $('<a>');
        a.attr('title', chartData1[i].Count);
        a.attr('category', 'centreprogram');
        a.attr('attribute', data.attribute);
        a.attr('centre_id', data.centre_id);
        a.attr('prog_id', data.prog_id);
        a.attr('period', data.period);
        a.attr('attribval', chartData1[i].Property);
        a.text(chartData1[i].Property);
        a.addClass('devDrillDown_class');
        $(modDevotee._frmResult).append(a);
        $(modDevotee._frmResult).append(":   " + chartData1[i].Count );
        $(modDevotee._frmResult).append("<br><br>");
    }
	$(modDevotee._frmResult).append('</font>');

    $( '.devDrillDown_class' ).click(function() {
  	   
  	   var attribute = $(this).attr('attribute');
  	   var centre_id = $(this).attr('centre_id');
  	   var prog_id = $(this).attr('prog_id');
  	   var attribval = $(this).attr('attribval');
  	   var period = $(this).attr('period');

  	   var content = "<br>attribute: " + $(this).attr('attribute');
  	   content += "<br>centre_id: " + $(this).attr('centre_id');
  	   content += "<br>prog_id: " + $(this).attr('prog_id');
  	   content += "<br>attribval: " + $(this).attr('attribval');
  	   content += "<br><hr>";
  	   
  	   
  	   $.post('modules/devotee/moduleEntry.php', {action:'getRegDevCntreProgAttrib', 
  		   			attribute:attribute,centre_id:centre_id,prog_id:prog_id,attribval:attribval,period:period}, 
  		   			modDevotee.__intFillDevDrillDown, 'json');
  	   $("#DrillDownDiv").html(content);
  	 });

    $("#chartdivAct").siblings().hide();
    modDevotee.__intGenChart (chartData1,"Property","Count", "chartdivAct", "#chartdivAct" , "#feature-right") ;
};

modDevotee.__intSuportConfDownload = function (centre_id , prog_id , category) {
    var a = $('<a>');
    a.attr('title', 'CSV file to be used for marking attendence');
    a.attr('category', category);
    a.attr('centre_id', centre_id);
    a.attr('prog_id', prog_id);
    a.text('Attendence Entry Sheet');
    a.addClass('devDrillDown_class');
    $(modDevotee._frmResult).append(chartData1[i].Property + ": ");
};


modDevotee.__intLoadCentresCallBack = function (data, textStatus, jqXHR) {
	if ( data.callstatus == "FAIL") {
		alert ("Server Problem : 1000102");
		return ;
	}
	
	
	var CentreProgHTML = '<fieldset><legend>Centre and Program:</legend>'; 
	CentreProgHTML += "<p>Centre: <SELECT id=\"Centre\" name='Centre' style='width:200px;'></SELECT> Program <SELECT id=\"Program\" name='Program' style='width:200px;'></SELECT><p>";
	CentreProgHTML += '</fieldset>'; 
	CentreProgHTML += '<p><div id="divSectReports">'; 

	CentreProgHTML += '<fieldset><legend>Reports:</legend>'; 
	
	CentreProgHTML += '<p><SELECT id="Period" name="Period" style="width:200px;"></SELECT> '; 
	CentreProgHTML += '<p>Property: <SELECT id="Attribute" name="Attribute" style="width:200px;"></SELECT> <input id = "actShowDistribution" type="submit" value="Distribution"/>'; 
	CentreProgHTML += '<p><input id = "actShowAttendence" type="submit" value="Attendence"/>'; 
	CentreProgHTML += '<input id = "actShowAbsentees" type="submit" value="Absentees"/><p>'; 
	
	CentreProgHTML += '</fieldset>'; 
	CentreProgHTML += '</div>'; 

	CentreProgHTML += '<p><div id="divSectDwownload"></div>'; 

	//CentreProgHTML +=  "<p><A href='modules/devotee/dummy.php?centre_id=5&prog_id=10&export_type=basic'>download</A>";

	
	$(modDevotee._frmBody).append(CentreProgHTML);
	$(modDevotee._frmBody).append("<br><p><hr><br>");

	var PeriodOptions = "";
	PeriodOptions = PeriodOptions + '<option value=1>Last 1 Month</option>';
	PeriodOptions = PeriodOptions + '<option value=2>Last 2 Months</option>';
	PeriodOptions = PeriodOptions + '<option value=3>Last 3 Months</option>';
	PeriodOptions = PeriodOptions + '<option value=6>Last 6 Months</option>';
	PeriodOptions = PeriodOptions + '<option value=0>All</option>';
	$("select#Period").html(PeriodOptions);
	
	var CentreOptions = "";
	
	var centreArray = data['centres'];
	for(var prop in centreArray) {
		CentreOptions = CentreOptions + "<option value=" + centreArray[prop] + ">" +  prop + "</option>";
	}

	// $("select#Centre").html(CentreOptions);
	$("select#Centre").html(modStatic.centreoptions);
	
	
	$.post('modules/devotee/moduleEntry.php', {action:'getLstTypeDevAttrib'}, modDevotee.__intLoadAttribCallBack, 'json');

    id = $("select#Centre option:selected").attr('value');
//	$.post('modules/devotee/moduleEntry.php', {action:'getPrograms', id:id}, modDevotee.__intLoadProgramCallBack, 'json');
	$("select#Program").html(modStatic.progoptions[id]);

	$("#actShowDistribution").click(function(){
        prog_id = $("select#Program option:selected").attr('value');
        centre_id = $("select#Centre option:selected").attr('value');
        attribute = $("select#Attribute option:selected").attr('value');
        period = $("select#Period option:selected").attr('value');
        $("#feature-right").hide();
    	$.post('modules/devotee/moduleEntry.php', {action: 'getDistribution',prog_id:prog_id,centre_id:centre_id,attribute:attribute,period:period}, modDevotee.__intShowDistCallBack, 'json');
    }); 
	
	$("#actShowAbsentees").click(function(){
        prog_id = $("select#Program option:selected").attr('value');
        centre_id = $("select#Centre option:selected").attr('value');
        period = $("select#Period option:selected").attr('value');
        $("#feature-right").hide();
        $.post('modules/devotee/moduleEntry.php', {action: 'getAttendenceTrends',prog_id:prog_id,centre_id:centre_id,numEvents:24,period:period}, modDevotee.__intLoadAbsenteeCallBack, 'json');
    }); 

	$("#actShowAttendence").click(function(){
        prog_id = $("select#Program option:selected").attr('value');
        centre_id = $("select#Centre option:selected").attr('value');
        period = $("select#Period option:selected").attr('value');
        $("#feature-right").hide();
        $.post('modules/devotee/moduleEntry.php', {action: 'getAttendence',prog_id:prog_id,centre_id:centre_id,numEvents:24,period:period}, modDevotee.__intLoadAttendenceCallBack, 'json');
    }); 

    $("select#Centre").change(function(){
        id = $("select#Centre option:selected").attr('value');
    	//$.post('modules/devotee/moduleEntry.php', {action:'getPrograms', id:id}, modDevotee.__intLoadProgramCallBack, 'json');
    	$("select#Program").html(modStatic.progoptions[id]);
	});

    $("select#Period").change(function(){
        prog_id = $("select#Program option:selected").attr('value');
        centre_id = $("select#Centre option:selected").attr('value');
        period = $("select#Period option:selected").attr('value');
        
        var basicLink = "<A href='modules/devotee/moduleEntry.php?action=getDevExport&centre_id=" + centre_id + "&prog_id=" + prog_id + "&period=" + period + "&export_type=basic'>Basic</A>";

        //$("#divSectDwownload").html('<fieldset><legend>Downloads:</legend><table border><tr><td> ' + basicLink + "</td><td>" + completelink + "</td></tr></table></fieldset>");
        $("#divSectDwownload").html('<fieldset><legend>Downloads:</legend><table border><tr><td> ' + basicLink + "</td><td>" + "</td></tr></table></fieldset>");
	});
    
    $("select#Program").change(function(){
        prog_id = $("select#Program option:selected").attr('value');
        centre_id = $("select#Centre option:selected").attr('value');
        period = $("select#Period option:selected").attr('value');
        
        var basicLink = "<A href='modules/devotee/moduleEntry.php?action=getDevExport&centre_id=" + centre_id + "&prog_id=" + prog_id + "&period=" + period + "&export_type=basic'>Basic</A>";
        

        //$("#divSectDwownload").html('<fieldset><legend>Downloads:</legend><table border><tr><td> ' + basicLink + "</td><td>" + completelink + "</td></tr></table></fieldset>");
        $("#divSectDwownload").html('<fieldset><legend>Downloads:</legend><table border><tr><td> ' + basicLink + "</td><td>" + "</td></tr></table></fieldset>");
	});

};

modDevotee.ProgramSummary = function  (frmContainer,frmBody,frmProgress,frmResult) {
	$("#DrillDownDiv").hide();
	$(frmContainer).show();
	$(frmContainer).siblings().hide();
	
	$(frmBody).show();
	$(frmBody).siblings().hide();

	modDevotee._frmBody = frmBody;
	modDevotee._frmResult = frmResult;

	$(modDevotee._frmBody).html("");

	$.post('modules/devotee/moduleEntry.php', {action: 'getCentres'}, modDevotee.__intLoadCentresCallBack, 'json');
};


modDevotee.registerDevotee = function (frmContainer,frmBody,frmProgress,frmResult) {
	
	modAuth._LoggedInUser
	
	modAuth._frmContainer = frmContainer;
	modAuth._frmBody = frmBody;
	modAuth._frmProgress = frmProgress;
	modAuth._frmResult = frmResult;
	
	$(frmContainer).show();
	$(frmContainer).siblings().hide();

	$(frmBody).show();
	$(frmBody).siblings().hide();

	$(frmBody).load("modules/devotee/add_devotee_form.php", {frmtype: 'contact'}, function(){
        var id = $("select#Centre option:selected").attr('value');
        modDevotee.populatePrograms (id);
		
		$("select#Centre").change(function(){
	        $("select#Program").attr("disabled","disabled");
	        $("select#Program").html("<option>wait...</option>");
	        id = $("select#Centre option:selected").attr('value');
	        modDevotee.populatePrograms (id);
		});

		$("#frmAddDevotee").bind('submit',function(event){
			$.ajax({
				type: "POST",
				url:$('#frmAddDevotee').attr('action'),
				  dataType: 'html',
				  data: $('#frmAddDevotee').serialize(),
				success: function(data){
					$(frmBody).show();
					$(frmBody).siblings().hide();
					$(frmBody).html(data);
					$("#frmDevoteeDetails").bind('submit',modDevotee.ShowDevoteeDetails);
				}
			});
			return false;
		});
	});
}

modDevotee.ShowDevoteeDetails = function(event) {
	event.preventDefault();
	alert ("FOUND");
	$.ajax({
		type: "POST",
		url:$('#frmDevoteeDetails').attr('action'),
		  dataType: 'html',
		  data: $('#frmDevoteeDetails').serialize(),
		success: function(data){
			$(modAuth._frmBody).show();
			$(modAuth._frmBody).siblings().hide();
			$(modAuth._frmBody).html(data);
		}
	});
};

modDevotee.registerDevotee_dummy = function (frmContainer,frmBody,frmProgress,frmResult) {
	
	$(frmContainer).show();
	$(frmContainer).siblings().hide();

	$(frmBody).show();
	$(frmBody).siblings().hide();

	$(frmBody).load("modules/devotee/add_devotee_form.php", {frmtype: contact}, function(){

		$("#frmExcelUpload2").bind('submit',function(event){
			event.preventDefault();

			$(frmProgress).show();
			$(frmProgress).siblings().hide();

			$.ajax({
				type: "POST",
				url:$('#frmExcelUpload2').attr('action'),
				  dataType: 'html',
				  data: $('#frmExcelUpload2').serialize(),
				success: function(data){
					$(frmResult).show();
					$(frmResult).siblings().hide();
					$(frmResult).html(data);
				}
			});
			return false;
		});
	});
};


modDevotee.loadDevoteesFromExcel = function (frmContainer,frmBody,frmProgress,frmResult) {
	
	$(frmContainer).show();
	$(frmContainer).siblings().hide();

	$(frmBody).show();
	$(frmBody).siblings().hide();

	$(frmBody).load("modules/devotee/import_devotees_form.php");
}

modDevotee.loadAttendenceFromExcel = function (frmContainer,frmBody,frmProgress,frmResult) {
	
	$(frmContainer).show();
	$(frmContainer).siblings().hide();

	$(frmBody).show();
	$(frmBody).siblings().hide();

	$(frmBody).load("modules/devotee/import_attendence_form.php",{},function(){
        var id = $("select#Centre option:selected").attr('value');
        modDevotee.populatePrograms (id);
		
		$("select#Centre").change(function(){
	        $("select#Program").attr("disabled","disabled");
	        $("select#Program").html("<option>wait...</option>");
	        id = $("select#Centre option:selected").attr('value');
	        modDevotee.populatePrograms (id);
		});
	});
}

modDevotee.DisableDevoteeFromExcel = function (frmContainer,frmBody,frmProgress,frmResult) {
	
	$(frmContainer).show();
	$(frmContainer).siblings().hide();

	$(frmBody).show();
	$(frmBody).siblings().hide();

	$(frmBody).load("modules/devotee/disable_devotees_form.php",{},function(){
	});
}

modDevotee.populatePrograms = function (id) {
	 $.post("modules/devotee/getPrograms.php", {id:id}, function(data){
         $("select#Program").removeAttr("disabled");
         $("select#Program").html(data);
     });
	
}
