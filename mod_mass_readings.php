<?php
/*
	Module    : "Mass Readings"
	Author    : Jason Clifford
	Version   : 1.0 23rd September 2010
	Copyright : (C) 2010 Jason Clifford
	Licence   : GPL v2 or later
	Email     : jason@ukcatholic.com
	Website   : http://www.ukcatholic.com/
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$lang = $params->get('language');
$type = $params->get('reading');
$get_timeout = $params->get('gettimeout');
$displayDay = $params->get('displayday');

if ( $displayDay == 'sunday' ) {
    $wday = date('w');
    if ( $wday == 0 ) {
	$disDay = date('Ymd');
    }
    elseif ( $wday == 6 && ( date('H') < 17 ) ) {
	$disDay = date('Ymd', time() - 86000*6);
    }
    elseif ( $wday == 6 && ( date('H') > 17 ) ) {
	# On Saturday evening we show the readings for the next day
	$disDay = date('Ymd', time() + 86000);
    }
    else {
	$disDay = date('Ymd', time() - 86000 * $wday);
    }
}
else {
    if (  $wday == 6 && ( date('H') > 17 ) ) {
	# On Saturday evening we show the readings for the next day
	$disDay = date('Ymd', time() + 86000);
    }
    $disDay = date('Ymd');
}

$arr = array("FR","PS","SR","GSP");
echo "<span='mass_readings'>";
foreach ($arr as $cont) {
    $url = "http://feed.evangelizo.org/reader.php?date=$disDay&type=$type&lang=$lang&content=$cont";
    ini_set('default_socket_timeout', $get_timeout);
    $h = fopen($url,"r");
    if (! $h ) {
	continue;
    }
    else {		
	while( ! feof($h) ) {
	    $a .= fgets($h);
	}
	
	if ( ! $previous ) {
	    echo $a."<br />";
	    $previous = $a;
	    continue;
	}

	if ( strcmp($previous,$a)  == 0 ) {
	    continue;
	}
	else {
	    $match = "/".preg_quote($previous)."/";
	    $previous = $a;
	    echo preg_replace($match, "", $a)."<br />";
	}
    }
}
if ( $displayDay != "sunday" && date('w') != 0 ) {
    # cannot specify the date for this so not for Sunday.
    $readurl = "http://www.dailygospel.org/ind-gospel-d.php?language=$lang&title_bold=1&typeRead=ALL&return_url=".$_SERVER['HTTP_HOST'];
    echo '<a href="'.$readurl.'" target="Mass Readings">Click here to read</a><br />';
}
echo "</span>";
?>
