/*
phpmybookmarks
Copyright (C) 2013  Jacob Zelek <jacob@jacobzelek.com>

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

function Display() {}

Display.set_main = function(html)
{
	$("#main").html(html);
}

// Clear entire display area
Display.clear = function()
{
	$("#search_term").html("");
	$("#status").html("");
}

// Display error in status area
Display.error = function(error)
{
	// Place a status with a gif to indicate bookmark is being processed
	$("#status").html("<div class=\"alert alert-danger\">" +
						"<strong>" + error + "</strong>" +
						"<button type=\"button\" class=\"close\" " +
						"data-dismiss=\"alert\">&times;</button></div>");
}

Display.add_status = function(id, text)
{
	// Place a status with a gif to indicate bookmark is being processed
	$("#status").append("<div id=\"status_" + hex_sha1(id) + "\" " +
						"class=\"alert alert-info\">" +
						"<img src=\"img/loading.gif\" > " + text +
						"</div>");
}

Display.remove_status = function(id)
{
	$("#status_" + hex_sha1(id)).remove();
}

Display.clear_all_status = function()
{
	$("#status").html();
}

//Prompt user for string
Display.prompt_user = function(text)
{
	var response = "";
	
	// Repeat prompt until valid input given
	do
	{
		response = prompt(text, "");
	}
	while(response == "" || response == null);
	
	return response;
}