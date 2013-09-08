/*
phpmybookmarks
Copyright (C) 2013  Jacob Zelek

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

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