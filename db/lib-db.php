<?php

/*
Table of Contents

function db_sql($Query)
function db_SingleFieldQuery($query)    (db_sfq)
function db_mysqli_insert_id()
function db_mysqli_affected_rows()
function post_debug()

*/


require_once ("admin/config.php");

function db_sql ($Query)
{
	global  $db_MySQL_Link, $dbServer, $dbUsername, $dbPassword, $dbDatabase;
	

	if (!isset($db_MySQL_Link)):   // create connection if not already
		$db_MySQL_Link = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbDatabase);
		if (mysqli_connect_errno())
		{
			echo /*StyleSheet() .*/ "\n\n<div class=Error>";
			echo "Mysqli connect error:" . mysqli_connect_error();
			echo "</div>";
			exit;
		}
	endif;// create new link
	
	if(!$result = mysqli_query($db_MySQL_Link, $Query))
	{
			echo /*StyleSheet() .*/ "\n\n<div class=Error>";
			echo "<br><b>" . "sql error:" . "</b><br><br>
		            <strong>" . "query:" . "</strong><br> $Query<br><br>
		     " . mysqli_error($db_MySQL_Link);
			if(stristr($Query, "select ")) echo ".<br>" . "if you haven't set up";
			echo "</div>";
			echo "<a href=\"javascript:history.go(-1);\">" . "< back" . "</a>";
			print_r ($_SERVER); echo "<br>POST<br>"; print_r ($_POST); echo "<br>SESSION<br>"; /*print_r($_SESSION);*/ echo "<br>GET<br>";  print_r($_GET);echo "</xmp><br>";
		exit;
	}
	return $result;
}// end  function db_sql


function db_sfq($query) 
{
	$result = db_sql($query);

		if ($result) {
		$row	= mysqli_fetch_array($result);

		if ( mysqli_num_fields($result) >1) return $row;  // multiple fields were requested
		// else
		return $row[0]; // only one field was intended
	} else {
		return 0;
	}
}// end db_SingleFieldQuery


function db_mysqli_insert_id()
{
	global  $db_MySQL_Link;
	return mysqli_insert_id($db_MySQL_Link);
}

function db_mysqli_affected_rows()
{
	global  $db_MySQL_Link;
	return mysqli_affected_rows($db_MySQL_Link);
}

function post_debug()
{
echo "<strong>SERVER</strong><BR><pre>";print_r ($_SERVER); echo "</pre><br><strong>POST</strong><BR><pre>"; print_r ($_POST); echo "</pre><br><strong>SESSION</strong><BR><pre>"; print_r($_SESSION); echo "</pre><br><strong>GET</strong><BR><pre>";  print_r($_GET);echo "</pre><br>";
}


?>
