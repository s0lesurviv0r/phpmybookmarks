<?php

/*
 * @author Stackoverflow user: Stephen Watkins
 * @ref http://stackoverflow.com/questions/4356289/php-random-string-generator
 */
function get_random_string($length = 10)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$random_string = '';
	for ($i = 0; $i < $length; $i++)
	{
		$random_string .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $random_string;
}

function get_title_from_url($url)
{
	$html = file_get_contents($url);

	preg_match("/<title>(.*)<\/title>/siU", $html, $matches);

	$title = preg_replace('/\s+/', ' ', $matches[1]);
	$title = trim($title);
	$title = strip_tags($title);
	
	return (empty($title)) ? false : $title;
}

/*
 * @ref http://corpocrat.com/2009/02/28/php-how-to-get-domain-hostname-from-url/
 */
function get_domain_from_url($url)
{
	$domain = parse_url($url);
	
	if(!empty($domain["host"]))
	{
		return $domain["host"];
	}
	else
	{
		return $domain["path"];
	}
}