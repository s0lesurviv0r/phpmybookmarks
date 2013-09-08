/*
 * @ref http://www.guyfromchennai.com/?p=28
 * @author Kumar S.
 * Returns true if the passed value is found in the
 * array. Returns false if it is not.
 */
Array.prototype.in_array = function(value, caseSensitive)
{
	for(var i=0; i < this.length; i++)
	{
		// use === to check for Matches. ie., identical (===),
		if(caseSensitive)
		{ //performs match even the string is case sensitive
			if(this[i].toLowerCase() == value.toLowerCase())
			{
				return true;
			}
		}
		else
		{
			if(this[i] == value)
			{
				return true;
			}
		}
	}
	
	return false;
};

/*
 * @author John Resig
 * @license MIT
 */
Array.prototype.remove = function(from, to)
{
	var rest = this.slice((to || from) + 1 || this.length);
	this.length = from < 0 ? this.length + from : from;
	return this.push.apply(this, rest);
};