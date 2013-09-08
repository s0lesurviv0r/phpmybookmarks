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

$(function()
{
	$("#tags").click(function(e)
	{
		e.preventDefault();
		Bookmarks.clear_search();
		Display.clear();
		Tags.display();
	});
			
	
	/**
	 * Handles logo button and timeline button
	 */
	$(".timeline").click(function(e)
	{
		e.preventDefault();
		Bookmarks.clear_search();
		Display.clear();
		Bookmarks.fetch();
		$("#search-text").val("");
	});
	
	$("#settings").click(function(e)
	{
		e.preventDefault();
		Bookmarks.clear_search();
		Display.clear();
		Settings.display();
	});
	
	/**
	 * Handles add bookmark button
	 *
	 * @todo Add only new items without full refresh
	 */
	$("#add").click(function()
	{
		url = $("#add-url").val();
		$("#add-url").val("");

		// Add status message
		Display.add_status(url, "<strong>Adding</strong> " + url);
		
		var request_data = {
							action: "add_bookmark",
							url: url
							};

		Server.send_request(function(response)
		{
			url = response.url;

			// If title was resolved and bookmark was added succesfully
			if(response.title != false)
			{
				Display.remove_status(url);
				Bookmarks.fetch();
			}
			// Title was not parsed so ask user for it
			else
			{
				title = Display.prompt_user("Please specify a title for " + url);
				
				var request_data = {
						action: "add_bookmark",
						url: url,
						title: title
						};

				Server.send_request(function(response)
				{
					Display.remove_status(url);
					Bookmarks.fetch();
				},
				request_data);
			}
		},
		request_data);
	});

	/**
	 * Handles search bookmark button
	 */
	$("#search").click(function()
	{
		Bookmarks.search = $("#search-titles").val();
		Display.clear();
		Bookmarks.fetch();
		$("#search-titles").val("");
	});

	/**
	 * Typeahead feature for search field
	 */	
	$("#search-titles").typeahead(
	{	
		source: function(query, process)
		{
			var id = "search-titles";
			
			var request_data = {
					element: id,
					typeahead: query,
					action: "get_typeahead_options"
					};

			return Server.send_request(function(response)
			{
				return process(response.options);
			},
			request_data);
		}
	});
	
	/**
	 * Typeahead feature for add new tag field
	 */	
	$("#search-tags").typeahead(
	{	
		source: function(query, process)
		{
			var id = "search-tags";
			
			var request_data = {
					element: id,
					typeahead: query,
					action: "get_typeahead_options"
					};

			return Server.send_request(function(response)
			{
				return process(response.options);
			},
			request_data);
		}
	});
	
	$("#add_tag").click(function()
	{
		var bookmark_id = Tag_Editor.get_bookmark_id();
		var tag = Tag_Editor.get_input();
		
		var request_data = {
				bookmark_id: bookmark_id,
				tag: tag,
				action: "add_tag"
				};

		Server.send_request(function(response)
		{
			// Clear tag input
			Tag_Editor.clear_input();
			
			// Add tag to list in editor
			Tag_Editor.add_tag(response.tag);
			
			// Update bookmarks display
			Bookmarks.fetch();
		},
		request_data);
	});
	
	$("#done_tagging").click(function(e)
	{
		e.preventDefault();
		Tag_Editor.clear();
	});
	
	// Initial display of bookmarks
	Bookmarks.init();
	Bookmarks.fetch();

	// Set timer to query for new bookmarks every 60 seconds
	//setInterval(function() { Bookmarks.refresh(); }, 60000);
});