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