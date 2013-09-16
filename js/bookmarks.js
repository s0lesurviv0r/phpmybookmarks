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

function Bookmark()
{
	this.id = 0;
	this.url = "";
	this.title = "";
	this.tags = Array();
}

Bookmark.prototype.load = function(bookmark)
{
	this.id = bookmark.id;
	this.url = bookmark.url;
	this.title = bookmark.title;
	this.tags = bookmark.tags;
}

Bookmark.prototype.get_html = function()
{
	var html = "";

	html += '<tr>';
	html += '<td>';

	html += '<a class="btn btn-mini edit-tags" id="edit_tag_' +
	this.id + '" href="#"><i class="icon-tag"></i></a>&nbsp;';
	
	html += '<a class="btn btn-mini remove" id="remove_' + this.id +
			'" href="#"><i class="icon-remove"></i></a>&nbsp;&nbsp;';

	html += '<a target="_NEW" class="title" href="' + this.url + ' ">';
	html += this.title;
	html += '</a>';
	
	for(var i=0; i < this.tags.length; i++)
	{
		html += "&nbsp;&nbsp;" + Tags.get_html(this.tags[i], "tag");
	}
	
	html += '</td>';
	html += '</tr>';

	return html;
}

function Bookmarks() { }

Bookmarks.init = function()
{
	Bookmarks.current_id = 0;

	Bookmarks.count = 0;
	Bookmarks.bookmarks = Array();
	Bookmarks.more = false;

	Bookmarks.tags = Array();
	Bookmarks.search = "";
	Bookmarks.limit = 100;
}

Bookmarks.remove_tag = function(tag)
{
	if(Bookmarks.tags.in_array(tag, false))
	{
		Bookmarks.tags.remove(
			Bookmarks.tags.indexOf(tag));
	}
}

Bookmarks.clear_search = function()
{
	Bookmarks.limit = 100;
	Bookmarks.search = "";
	Bookmarks.tags = Array();
} 

Bookmarks.fetch = function()
{
	var request_data = {
						action: "get_bookmarks",
						limit: Bookmarks.limit,
						search: Bookmarks.search,
						tags: Bookmarks.tags.join(":")
						};
	
	Server.send_request(function(response)
	{
		Bookmarks.more = response.more;
		Bookmarks.count = response.result_count;
		Bookmarks.bookmarks = Array();
		
		var count = response.bookmarks.length;
		
		for(var i=0; i < count; i++)
		{
			var bookmark = new Bookmark();
			bookmark.load(response.bookmarks[i]);
			Bookmarks.bookmarks.push(bookmark);
		}
		
		Bookmarks.display();
	},
	request_data);
}

/**
 * Queries for latest id. If newer bookmarks exist, update screen
 * 
 * @todo If new items query and add only new items without full refresh
 */
Bookmarks.refresh = function()
{
	var request_data = {
				action: "get_current_id"
			};

	Server.send_request(function(response)
	{
		if(response.id != Bookmarks.current_id)
		{
			Bookmarks.current_id = response.id;
			Bookmarks.fetch();
		}
	},
	request_data);
}

/*
 * Display current bookmarks
 */
Bookmarks.display = function()
{	
	var html = "";

	// If there are search or tag terms
	if(Bookmarks.search !="" ||
			Bookmarks.tags != "")
	{
	
		html += "<div class=\"well\">";
		html += "<small>Search Parameters (Click to unselect)</small><br />";

		if(Bookmarks.search != "")
		{
			html += "<a class=\"label clear-search\">" +
				"Search: " +
				Bookmarks.search + "</a>&nbsp;&nbsp;";
		}
		
		if(Bookmarks.tags != "")
		{
			for(var i=0; i < this.tags.length; i++)
			{
				html += Tags.get_html(this.tags[i], "clear-tag") + "&nbsp;&nbsp;";
			}
		}
		
		html += "</div>";
	}
	
	if(Bookmarks.count > 0)
	{	
		html += '<table class="table table-hover table-condensed"><tbody>';
		
		for(i=0; i < Bookmarks.bookmarks.length; i++)
		{
			html += Bookmarks.bookmarks[i].get_html();
		}

		html += '</tbody></table>';
		
		html += '<a id="bookmark_ending" rel="bookmark_ending"></a>';
	}
	else
	{
		html += '<strong>No Bookmarks Found!</strong>';
	}
	
	// Display bookmarks HTML
	Display.set_main(html);
	
	/**
	 * Loads more bookmarks when bottom of page is reached
	 */
	$("#bookmark_ending").waypoint(function()
	{
		if(Bookmarks.more)
		{
			Bookmarks.limit = Bookmarks.limit + 100;
			Bookmarks.fetch();
		}
	}, { offset: '-25%' });
	
	/**
	 * Handles remove bookmark button
	 */
	$(".remove").click(
	function(e)
	{
		e.preventDefault();

		// Get id of bookmark
		bookmark_id = $(this).attr('id').split("_")[1];
		element_id = $(this).attr('id');
		
		var request_data = {
				action: "remove_bookmark",
				id: bookmark_id
				};

		Server.send_request(function(response)
		{
			//@todo Tie into new update system
			$("#" + element_id).parent()
				.parent().remove();
		},
		request_data);
	});
	
	/**
	 * Handles edit tags button
	 */
	$(".edit-tags").click(
	function(e)
	{
		e.preventDefault();

		// Get id of bookmark
		bookmark_id = $(this).attr('id').split("_")[2];
		
		Tag_Editor.display(bookmark_id);
	});
	
	/**
	 * Handles tag clicks
	 */
	$(".tag").click(function(e)
	{
		e.preventDefault();

		tag = $(this).attr("tag");
		
		if(!Bookmarks.tags.in_array(tag, false))
		{
			Bookmarks.tags.push(tag);
			Bookmarks.fetch();
		}
	});
	
	/**
	 * Handles tag clicks in search parameters
	 */
	$(".clear-tag").click(function(e)
	{
		e.preventDefault();

		tag = $(this).attr("tag");
		
		if(Bookmarks.tags.in_array(tag, false))
		{
			Bookmarks.remove_tag(tag);
			Bookmarks.fetch();
		}
	});
	
	/**
	 * Handles tag clicks in search parameters
	 */
	$(".clear-search").click(function(e)
	{
		e.preventDefault();
		
		Bookmarks.search = "";
		Bookmarks.fetch();
	});
}