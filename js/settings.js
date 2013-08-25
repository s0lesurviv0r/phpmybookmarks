function Settings() {}

Settings.display = function()
{
	var html = '';
	
	html += '<div class="container">';
	html += '<form id="change_pass" class="form-signin" method="POST">';
	html += '<h2 class="form-signin-heading">Change Password</h2>';
	//html += '<input id="pass_old" type="password" class="input-block-level" placeholder="Old Password">';
	html += '<input id="new_pass" type="password" class="input-block-level" placeholder="New Password">';
	html += '<input id="new_pass_confirm" type="password" class="input-block-level" placeholder="Confirm New Password">';
	html += '<button class="btn btn-large" type="submit">Change Password</button>';
	html += '</form>';
	html += '</div>';
    
    Display.set_main(html);
    
    $("#change_pass").submit(function(e)
    {
    	e.preventDefault();
    	
    	var new_pass = $("#new_pass").val();
    	var new_pass_confirm = $("#new_pass_confirm").val();
    	
    	var request_data = {
				action: "change_pass",
				new_pass: new_pass,
				new_pass_confirm: new_pass_confirm
			};

		Server.send_request(function(response)
		{
			location.reload();
		},
		request_data);
    });
}