var modAuth = {};
modAuth.setUserName = function (username) {
	modAuth._LoggedInUser = username;
};

modAuth.isLoggedIn = 0;

modAuth.initAuth = function (inTagName,usernametag,authActionTag) {

	$(inTagName).siblings().hide();
	$(inTagName).show();

	$(inTagName).load("modules/auth/authenticate_form.php",{},function(){
		$("#authform").bind('submit',function(event){
			$.ajax({
				type: "POST",
				url:$('#authform').attr('action'),
				  dataType: 'json',
				  data: $('#authform').serialize(),
				success: function(data){
					if ( data.status == "FAIL") {
						alert("Login Failed - Please retry with correct credentials");
					} else {
						$('#authform').hide();
						$(usernametag).show();
						$(usernametag).text("Welcome " + data.username);
						$(authActionTag).text("LogOut");
						modAuth._LoggedInUser = data.username;
						modAuth.isLoggedIn = 1;
					}
				}
			});
			return false;
		});
	});
};

modAuth.logOut = function () {
	$.ajax({
		type: "GET",
		url:"modules/auth/authenticate.php",
		  dataType: 'json',
		  data: { status: "loggedout" },
		success: function(data){
			modAuth.isLoggedIn = 0;
			alert ("Good Bye " + modAuth._LoggedInUser + "!!!\n\nSee you again");
		}
	});
};

