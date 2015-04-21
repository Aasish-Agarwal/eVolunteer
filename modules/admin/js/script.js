var modAdmin = {}

modAdmin.xmlLoad = function (inTagName,formId,retValDiv) {

	$(inTagName).siblings().hide();
	$(inTagName).show();
	$("#imgExcelFrm2Progress").hide();
	$(formId).show();
	$(retValDiv).hide();
	
	
		$(formId).bind('submit',function(event){
			$("#imgExcelFrm2Progress").show();
			$(formId).hide();

			$.ajax({
				type: "POST",
				url:$(formId).attr('action'),
				  dataType: 'html',
				  data: $(formId).serialize(),
				success: function(data){
					$(formId).hide();
					$("#imgExcelFrm2Progress").hide();
					$(retValDiv).html(data);
					$(retValDiv).show();
				}
			});
			return false;
		});
}

