<?
/*
function get_enum_name ($Table, $ID, $TextForNone='<i>(none)</i>', $TextForNotFound = '<i>(not found)</i>')
function display_dropdown (...)
function display_checkbox ($Field, $CurrentValue)
function form_encode($string)
*/


function get_enum_name ($Table, $ID, $TextForNone='<i>(none)</i>', $TextForNotFound = '<i>(not found)</i>')
// get_enum_name (/*Table*/, /*ID*/, /*TextForNone*/'<i>(none)</i>', /*TextForNotFound*/'<i>(not found)</i>');
{
	// returns text string name (essentially a macro)
	if (!$ID) return $TextForNone;
	$Label = db_SingleFieldQuery ("SELECT Name FROM $Table WHERE ID=$ID");
	if (!$Label) $Label = $TextForNotFound;
	return $Label;
} // end get_enum_name


// July 2014
function display_dropdown ( $HTMLID='', $Table, $InputName, $OnChangeSubmit=true, $CheckOnline=false, $DisplayCurrent=false, $CurrentID=0, $QueryTable='', $QueryWhere='', $OrderBy = 'ID',  $Repeater='', $UnselectedDefaultTextLabel='', $OnlyOneFlag=false/*don't use with OnChange*/, $JSParams='', $LimitLabelChars = 60, $DisplayPrefixID='ID', $ComboText1A='', $ComboQuery1='', $ComboText1C='', $DebugFlag=false, $DisplayListField='Name', $ValueField='ID', $HideIfNone=false, $Anchor='', $FormTags=1, $OptionValueForUnselected='' )
 //display_dropdown ( /*HTMLID*/'', /*Table*/'', /*InputName*/'', /*OnChangeSubmit*/true, /*CheckOnline*/false, /*DisplayCurrent*/false, /*CurrentID*/0, /*QueryTable*/'', /*QueryWhere*/'', /*OrderBy */'ID',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/true, /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false, /*DisplayListField*/'Name', /*ValueField*/'ID', /*HideIfNone*/false, /*Anchor*/'', /*$FormTags*/1, /*OptionValueForUnselected*/ '' )
{ 
/*
	CheckOnline:	  Table has a field called Online - if not online,   (offline)
	DisplayCurrent:   Puts in "selected" parameter  when ID matches $CurrentID
	CurrentID		  Initial ID value for current value "selected"  (0 usually disables the feature)
	OnChangeSubmit:  (1) wraps SELECT in a FORM     (2) adds parameter  OnChange Submit()
	Repeater:		  pass thru <input hidden>  fields to keep   Current* variables connected across POSTs
	OnlyOneFlag:	  If true, then for the case of only one result, display that result as the default "selected" -- do not use with OnChangeSubmit, since you can never submit 
	JSParams:		  don't use OnChange with OnChangeSumbit
	LimitLabelChars	  Max # chars displayed in dropdown labels (excluding (online/offline) indication, and (ID) )
	DisplayPrefixID		  CHANGED 3/24/10: OLD: Insert (ID) prior to each label   NEW:  if '' then include nothing, else include the field as given (to get the ID use the value 'ID';  for SKU, use 'SKU' )
	ComboText1A,C	  Insert this text just after each label, and after results of Query1
	ComboQuery1		  Insert the results of this query after Text1  (do not perform Query if null)
	DisplayListField  In the dropdown list, show this field contents as the items to be selected - assumes this table  thus  X.Name (if the db tbl uses a different descr name like SName, then use 'SName' as this param value
	ValueField		  Normally ID, but the <option value>  may need to be SKU for instance.   IF NOT ID, then check other field combinations to make sure this works
	HideIfNone		  If no matches, do not print the dropdown list at all, return 0
	Anchor			  Adds this #label as a POST action URL suffix to position upon return to <a name>

	returns number of rows (options) found
*/

	if (!$UnselectedDefaultTextLabel) $UnselectedDefaultTextLabel = "SELECT $InputName";
	
	//X.$DisplayListField AS DisplayName, 
	$Query = "SELECT DISTINCT X.*, X.$ValueField AS ValueField  FROM $Table AS X $QueryTable WHERE 1=1 ".($CheckOnline?" AND X.Online='1'":"") ; //. ($DisplayCurrent?" AND ":"")
	
	$Query .= " ".$QueryWhere ;  
	$Query .= " ORDER BY $OrderBy";   //   actual param might need  X.
	if ($DebugFlag) echo $Query."<br>";
	$Result = db_sql ($Query); // all artists even if offline
	$Num_Rows = mysqli_num_rows($Result);
	if ($Num_Rows==1  AND  $OnlyOneFlag) $OnlyOne = true; else $OnlyOne = false;   // when only one, then cannot continue because no OnChange can occur
	
	if ($Num_Rows==0 AND $HideIfNone) return 0; // don't print anything
	
	if ($OnChangeSubmit AND $FormTags) echo "\n<form id='$InputName' name='$InputName' method='post' action='".$_SERVER["PHP_SELF"].($Anchor?"#$Anchor":"")."' >";
    echo "<select name='$InputName'  ".($OnChangeSubmit?"OnChange='this.form.submit();'":"") . "$JSParams" .
		($HTMLID?'ID=\"$HTMLID\"':'').">";   //  not yet needed: id='$InputName'    careful for duplicate dropdowns on same page
     
	if ($Num_Rows==0)
		echo "<option value='' >NO $InputName Table Entries Found</option>";
		else
		echo "<option value='{$OptionValueForUnselected}' ".(($CurrentID=='')? "selected" : "").">$UnselectedDefaultTextLabel</option>";
	while ($Row = mysqli_fetch_assoc($Result)) 
	{
		$ID = $Row['ID'];
		$ValueFieldValue = $Row['ValueField'];  // Normally 'ID'
		$QQ= "$ComboQuery1";
		echo $ComboQuery1 ."---- ".$QQ ."<br>\n";
		if ($ComboQuery1)  $ComboText1B = db_SingleFieldQuery ("$ComboQuery1$ID" );
		echo "<option value='$ValueFieldValue' ".(($CurrentID==$ID or $OnlyOne )? "selected" : "").">".
			($DisplayPrefixID?"<span style='color:#333333'>[".$Row["$DisplayPrefixID"]."]</span> ":"").  // splice in ID or SKU or other field value first [in brackets]
			substr($Row["$DisplayListField"],0,$LimitLabelChars).
			(($CheckOnline AND !$Row['Online']  )?"(offline)":"").
			"$ComboText1A$ComboText1B$ComboText1C</option>";
	} // end while

 	echo "</select> ";
	if ($OnChangeSubmit AND $FormTags) echo "$Repeater </form>";
	
	return $Num_Rows;

} // end display_dropdown
	
function display_checkbox ($FieldName, $CurrentValue, $Label='', $ValueIfChecked='1')
{ ?>
<?=$Label?"<label>":""?><input type="checkbox" name="<?=$FieldName?>"  value="<?=$ValueIfChecked?>" <?=($CurrentValue==$ValueIfChecked?"checked":"")?> /><?=$Label?><?=$Label?"</label>":""?>
<?
}// end display_checkbox


function form_encode($string)
{
	return str_replace("&amp;", "&", (htmlentities(stripslashes($string), ENT_QUOTES)));
}

?>