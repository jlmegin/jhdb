

<?php
# These functions regulate player control.

// $episodeList is an array of episodes.  Each episode is an array of YID, artist/date, and episode blurb.
$episodeList = array(
	1 => array(
		"yid" => "Uz9DCLM7tf0" ,
		"artist" => "Emil Haddad & Dick Odgren (11/20/93)" ,
		"blurb" => "The area's most popular jazz act since 1982 features the incomparable trumpet and flugelhorn playing of elder statesman Emil Haddad, a Worcester native who played in the big bands of New York in the 1940's. Emil also sings in his own inimitable style. Pianist Dick Odgren, who grew up in Auburn, Massachusetts, where he still lives, taught at Berklee College of Music for twelve years and has recorded with Mike Metheny on Impulse. Dick and Emil have an unmatched sound and synergy."), 
	2 => array(
		"yid" => "WEOUuhubKMs" ,
		"artist" => "Rich Falco Quartet (12/12/93)" ,
		"blurb" => "Director of Jazz Studies at Worcester Polytechnic Institute, guitarist Rich Falco and his group play mainstream, contemporary and Latin jazz with an acoustic emphasis. Trombone, bass, and drums round out the quartet. Two of Richard Jarvais' vocals are featured, including a spine tingling version of Billie Holiday's \"Don\'t Explain.\""),
	3 => array(
		"yid" => "iqEoN3gI7kQ" ,
		"artist" => "Made in the Shade (1/4/94)" ,
		"blurb" => "This very entertaining Dixieland group was formed in 1990 by a group of Berklee students. Since then, they've gone to Europe twice to perform their authentic New Orleans-style jazz, released a CD, and are now mainstays of the Boston music scene." ),
	4 => array(
		"yid" => "4KEG5rLNpY0" ,
		"artist" => "Jim Porcella & Jeff Holmes Big Band featuring Dick Johnson, Part I (1/8/94)" ,
		"blurb" => "Seasoned vocalist Porcella teams up with Holmes and Johnson for a live CD recording in November of '93 at Worcester's most famous jazz venue, the El Morocco restaurant. Professor of jazz studies at UMass/Amherst, pianist/trumpeter Holmes brings his band as well as his compositions and arrangements to the session. Featured is Concord Jazz recording artist and Artie Shaw Band leader, Dick Johnson on reeds. This show was one of five finalists in the nation for a localCableACE'93 award in the music category."),
	5 => array(
		"yid" => "vrdtamc_qDw" ,
		"artist" => "Jim Porcella & Jeff Holmes Big Band featuring Dick Johnson, Part II (1/8/94)" ,
		"blurb" => "More of the live CD recording, Part II showcases instrumental compositions by Jeff Holmes and includes two more vocals by Porcella, as well as outstanding solos by Johnson."),
	6 => array(
		"yid" => "kTLnV7TBMyA" ,
		"artist" => "Jane Miller Trio (1/28/94)" ,
		"blurb" => "Guitarist/composer and Berklee teacher Jane Miller, backed by Bob Simonelli on bass and Don Kirby on drums, performs standards as well as her own very creative compositions. Her first CD, Postcard, has received a lot of airplay on jazz radio stations in Boston and beyond, and she released her second on the Purple Rose label."),
);


// Reads the "episode" GET parameter and generates a player for that episode number.
function buildPlayer() {
	$epnum = $_GET['episode'];
	if (($epnum == NULL) || (getYID($epnum) == "")) { // if a valid video is not selected
		?> <div id="playerContainer"><div id="instructions">Choose a video below to begin!</div></div> <?
	} else { // if a video has been selected
		// display the player
  	    ?>
        <div id="playerContainer"><iframe width="700" height="525" src="http://www.youtube.com/embed/<? echo getYID($epnum); ?>?autohide=1&showinfo=0" frameborder="0" allowfullscreen></iframe></div>
        <div id="episodeTitle"><? echo "Title" ?></div><?
   
	}
}


// Consumes an episode number, and returns its Youtube ID.
function getYID($epnum) {
	global $episodeList; // pulls in $episodeList from outside the function
	return $episodeList[$epnum]['yid'];
}

?>







<div id="playerContainer"><? buildPlayer(); ?></div>

<?
foreach ($episodeList as $epnum => $episode) {
	if (getYID($epnum) != NULL) { // if this video exists
		// e.g. 1. Emil Haddad and Dick Odgren (11/20/93)<br>
		?><div class="episodeBox"><a href="?episode=<? echo $epnum ?>"><? echo $epnum ?>. <? echo $episode['artist'] ?></a></div><?
	}
}
?>