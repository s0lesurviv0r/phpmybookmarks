function Server() {}

Server.send_request = function(callback, data)
{
	jQuery.ajax("ajax.php", { dataType: 'json', data: data, type: 'POST' })
	.done(function(response)
	{
		if(response.success)
		{
			callback(response.data);
		}
		else
		{
			Display.error(response.reason_readable);
		}
	})
	.fail(function()
	{
		Display.error("Server unreachable. " +
					"Possibly a network failure.");
	});
}