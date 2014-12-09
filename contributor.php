<? 
session_start();
require("db/lib-db.php");     			 // includes db_sfq   (single field query)
require("db/lib-JHDB-definitions.php");
require("lib-utils.php");
require("db/lib-form-util.php");  


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JHDB Upload Interface</title>
<style>
.StepTitle
{
	color: yellow;
	font-weight:bold;
	font-size:120%;
	background-color:#009;
}
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
</style>
</head>

<body>
<strong>Contributor Upload Page</strong><br />

<? //----------------------------------------------- Retrieve Persistent Current IDs ---------------------------------------

//post_debug();

if (!$_SESSION['CurrentContributorID']) echo "Not Logged In Yet";

// DropDown SELECT caused EntityID to change
if ($_POST['EntityID'])  	
	{ // EntityID changed
		list($_SESSION['CurrentEntityFName'],$_SESSION['CurrentEntityLName']) = 
			db_sfq("SELECT FName, LName FROM tblEntities WHERE ID={$_POST['EntityID']} ");  
		if ($_POST['EntityID']!=$_SESSION['CurrentEntityID']) 
			$_SESSION['CurrentGalleryID']=$_SESSION['CurrentGalleryTitle']=$_SESSION['CurrentUploadType']="";  
		$_SESSION['CurrentEntityID'] 		= $_POST['EntityID'];	
	} // end EntityID

// DropDown SELECT caused GalleryID to change
if ($_POST['GalleryID'])  	
	{ // GalleryID changed
		if ($_POST['GalleryID']!=$_SESSION['CurrentGalleryID']) $_SESSION['CurrentUploadType']="";	
		$_SESSION['CurrentGalleryID'] 		= $_POST['GalleryID'];	
		$_SESSION['CurrentGalleryTitle']	 = db_sfq("SELECT Title FROM tblGalleries WHERE ID={$_POST['GalleryID']} ");  
	} // end GalleryID

// DropDown SELECT caused UploadType to change
if ($_POST['UploadType'])  	{$_SESSION['CurrentUploadType'] 	= $_POST['UploadType'];	}

// Was there a SUBMIT BUTTON Command?
$Command = $_POST['Command'];  // Switching Entities and Galleries:  The existance of the POST variable above serves as the "Command" to change IDs
echo "<br>$Command<br>";

if ($Command)
{
	switch ($Command)
	{
		case "DeSelect Entity & Gallery Selections":
			$_SESSION['CurrentEntityFName']=$_SESSION['CurrentEntityLName']=$_SESSION['CurrentEntityID']=$_SESSION['CurrentUploadType']=$_SESSION['CurrentGalleryID']=$_SESSION['CurrentGalleryTitle']="";
		break;  // end case DeSelect
		
		case "Submit Entity Name": //----------------------------------------- New Entity Name -------------------------------
			if ($_SESSION['CurrentEntityID']>0)
			{ // Edit the existing name  from POST vars
//echo  "db_sql(UPDATE tblEntities SET FName='{$_POST['NewEntityFName']}', LName='{$_POST['NewEntityLName']}' WHERE ID='{$_SESSION['CurrentEntityID']}' )<br><br>";
 //echo "<br>before UPDATE: ".db_sfq("SELECT LName FROM tblEntities WHERE ID=10007") ." ;  LName='{$_POST['NewEntityLName']}'<br>";
				db_sql("UPDATE tblEntities 
						SET FName='{$_POST['NewEntityFName']}', LName='{$_POST['NewEntityLName']}' 
						WHERE ID='{$_SESSION['CurrentEntityID']}' ");
//echo "<br>after UPDATE: ".db_sfq("SELECT LName FROM tblEntities WHERE ID=10007") ."<br>";

				$_SESSION['CurrentEntityFName']		= $_POST['NewEntityFName'];
				$_SESSION['CurrentEntityLName']		= $_POST['NewEntityLName'];
				$_SESSION['CurrentGalleryID']		= $_SESSION['CurrentGalleryTitle'] = $_SESSION['CurrentUploadType']	= ""; // reset to be sure
			} // end edit existing
			else
			{  // create new record & ID   from POST vars
				$NextEntityID	= find_first_free_ID( "tblEntities", RangeMinEntityID, RangeMaxEntityID);
echo "INSERT INTO tblEntities SET 
										ID 		= '{$NextEntityID}',
										FName	='{$_POST['NewEntityFName']}', 
										LName	='{$_POST['NewEntityLName']}'  ";
				
				$EntityResults 	= db_sql("INSERT INTO tblEntities SET 
										ID 		= '{$NextEntityID}',
										FName	='{$_POST['NewEntityFName']}', 
										LName	='{$_POST['NewEntityLName']}'  "); //  OTHER FIELDS?   FIXME
				$_SESSION['CurrentEntityID']		= mysql_insert_id(); // new ID from insert
				$_SESSION['CurrentEntityFName']		= $_POST['NewEntityFName'];
				$_SESSION['CurrentEntityLName']		= $_POST['NewEntityLName'];
				$_SESSION['CurrentGalleryID']		= $_SESSION['CurrentGalleryTitle'] = $_SESSION['CurrentUploadType']	= ""; // reset to be sure
			} // end else create new
		break;  // end case Entity

		
		case "Submit Gallery Name":  //----------------------------------------- Edit or Create New Gallery Name -------------------------------
			if ($_SESSION['CurrentGalleryID']>0)
			{ // ----------- Edit the existing name  from POST vars
				db_sql("UPDATE tblGalleries 
						SET Title='{$_POST['NewGalleryTitle']}' 
						WHERE ID='{$_SESSION['CurrentGalleryID']}' ");
				$_SESSION['CurrentGalleryTitle']	= $_POST['NewGalleryTitle'];
				$_SESSION['CurrentUploadType']		= ""; // reset to be sure
			} // end edit existing
			else
			{  // ----------- create new Gallery record & ID   from POST vars
			
				$RangeMin	= RangeMinGalleryItemsID; // defaults
				$RangeMax 	= RangeMaxGalleryItemsID;

				switch ($_POST['GalleryType'])
				{
					case 1: // 
						$RangeMin = RangeMinCollectionGalleryID; // base collections
						$RangeMax = RangeMaxCollectionGalleryID;
					break; // end case 1
					case 2: // 
						$RangeMin = RangeMinEntityGalleryID; 	// musician/band collections
						$RangeMax = RangeMaxEntityGalleryID;
					break; // end case 2
					case 3:	// 
						$RangeMin = RangeMinGalleryItemsID; 	// supporting sub galleries
						$RangeMax = RangeMaxGalleryItemsID;
					break; // end case 3
				}// end switch GalleryType
				
				$NextGalleryID	= find_first_free_ID( "tblGalleries", $RangeMin, $RangeMax);
				$GalleryResults = db_sql("INSERT INTO tblGalleries SET 
						ID			= '{$NextGalleryID}',
						Title		= '{$_POST['NewGalleryTitle']}',
						Summary		= '{$_POST['GallerySummary']}',
						EntityID 	= '{$_SESSION['CurrentEntityID']}'
					"); //  OTHER FIELDS?   FIXME
				if (!$GalleryResults) die("Database gallery insert failed:  Title='{$_POST['NewGalleryTitle']}");
				$_SESSION['CurrentGalleryID']		= mysql_insert_id(); // new ID from insert
				$_SESSION['CurrentGalleryTitle']	= $_POST['NewGalleryTitle'];
				$_SESSION['CurrentUploadType']		= ""; // reset, to be sure
			} // end else create new
		break;   // end case Gallery
		
		case "Submit YouTube Info":  //----------------------------------------- Capture YouTube info  -------------------------------
			if (!$_POST['YouTubeURL'])  echo "<strong>Please enter your YouTube URL web address and re submit</strong><br>";
			else
			{ // URL ready to upload, NO ERROR CHECKING DONE ON SYNTAX
				// Caption and Thumbnail are optional
				$ThumbFilePath = "";
				if ($_POST['YouTubeThumbFile'])  $ThumbFilePath = UploadToEntityDirectory($_POST['file']);
				
				$YouTubeResults = db_sql("INSERT INTO tblGalleryItems SET 
					GIContentTypeID		= 'SubGalleryType_VideoEmbedYouTube',
					URL 				= '{$_POST['YouTubeURL']}',
					CaptionText			= '{$_POST['YouTubeURLCaption']}',
					ThumbURL 			= '{$ThumbFilePath}',
					PageTitle 			= '{$_POST['YouTubePageTitle']}',
					ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}'
					");
				if ($YouTubeResults==true) echo "Database update success for {$_POST['YouTubeURL']}"; else echo "Database update failed for {$_POST['YouTubeURL']}";
			}// end else

		break; // end CASE YouTube
		
		
		case "Associate SubGallery":  //----------------------------------------- Add SubGallery GalleryItem -------------------------------
		// error check
			$SubGalleryResults = db_sql("INSERT INTO tblGalleryItems SET 
					GIContentTypeID		= 'SubGalleryType_SubGallery',
					SubGalleryID 		= '{$_POST['AssociateSubGalleryID']}',
					CaptionText			= '{$_POST['SubGalleryCaption']}',
					ThumbURL 			= '{$ThumbFilePath}',
					PageTitle 			= '{$_POST['SubGalleryPageTitle']}',
					ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}'
					");
				if ($YouTubeResults==true) echo "Database update success for {$_POST['YouTubeURL']}"; else echo "Database update failed for {$_POST['YouTubeURL']}";
		break; // end CASE SubGallery
		
		
		
		case "Submit Image for Upload":  //-----------------------------------------  Upload Image & info  -------------------------------
			if (!$_POST['ImageFile'])  echo "<strong>Please select an image file from your local disk and re submit</strong><br>";
			else
			{ // URL ready to upload, NO ERROR CHECKING DONE ON SYNTAX
				// Caption and Thumbnail are optional
				$ImageFilePath = "";
				if ($_POST['ImageFile'])  $ImageFilePath = UploadToEntityDirectory($_POST['ImageFile']);
				
				$ImageGIResults = db_sql("INSERT INTO tblGalleryItems SET 
					GIContentTypeID		= 'SubGalleryType_ImageWithPlayer',
					URL 				= '{$_POST['ImageURL']}',
					CaptionText			= '{$_POST['ImageCaption']}',
					ThumbURL 			= '{$ImageFilePath}',
					PageTitle 			= '{$_POST['ImagePageTitle']}',
					ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}'
					");
				if ($ImageGIResults==true) echo "Database update success for {$_POST['ImageFile']}"; else echo "Database update failed for {$_POST['ImageFile']}";
			}// end else

		break; // end CASE YouTube		
		
		
	}// end switch
}// end process Command action

//echo "<br><br>"; post_debug();

?>

<table width="807" border="0">
  <tr>
    <td colspan="3">Logged in: xxx<br />
</td>
  </tr>
  <tr>
    <td colspan="3"><form action="" method="POST"> <input type="submit" name="Command" value="DeSelect Entity &amp; Gallery Selections"/> </form>&nbsp;</td>
  </tr>
  <tr>
    <td height="37" colspan="3" class="StepTitle">Step 1 - Identify Entity (Performer, Band, Composer, Venue, Media, etc.)</td>
  </tr>
  <tr>
    <td width="243" valign="top"><? if ($_SESSION['CurrentEntityID']>0) {?><em>Selected Existing   &nbsp;&nbsp;&nbsp;(or add new --&gt; )<br /><span style="color:grey;">(click to change)</span></em><? }else{?><em>Select Existing</em> <? }?><br />
<? display_dropdown ( /*HTMLID*/'EntityID', /*Table*/'tblEntities', /*InputName*/'EntityID', /*OnChangeSubmit*/true, /*CheckOnline*/false, /*DisplayCurrent*/true, /*CurrentID*/$_SESSION['CurrentEntityID'], /*QueryTable*/'', /*QueryWhere*/'', /*OrderBy */'LName',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'Select Musician/Venue', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'ID', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
 /*DisplayListField*/'LName', /*ValueField*/'ID', /*HideIfNone*/false, /*Anchor*/'', /*$FormTags*/true, /*OptionValueForUnselected*/ -1 ); ?>
</td>
    <td width="1">&nbsp;</td>
    <td valign="top"> <? if ($_SESSION['CurrentEntityID']>0) {?><em>Edit Existing Name of Musician/Group </em> <? }else{?><em>Enter New Musician/Group Name</em> <? }?><span style='color:grey;'>(If NOT a person, then leave FirstName blank)</span><br />      <table width="400" border="0">
      <tr>
      <form action=""  method="POST">
        <td>First Name (if a person)<br />          <input type="text" name="NewEntityFName" size="30" value="<?=$_SESSION['CurrentEntityFName']?>" /></td>
        <td>Last Name or Group Name<br />          <input type="text" name="NewEntityLName" size="30" value="<?=$_SESSION['CurrentEntityLName']?>" /></td>
        <td><br />
          <input type="submit" name="Command" value="Submit Entity Name" />
        	<!-- persistent/cumulative IDs are passed thru session variables -->
            </td>
       </form>
      </tr>
    </table></td>
  </tr>
<? if ($_SESSION['CurrentEntityID']>0)
{ ?>
 <tr>
   <td colspan="3" >&nbsp;</td>
 </tr>
 <tr>
    <td height="49" colspan="3"  class="StepTitle">Step 2 - Identify Gallery/Collection <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    (Label/Title for Grouping/Category of Museum Items for this Musician/Band/Venue)</td>
  </tr>
  <tr>
     <td width="243" valign="top"> 
	 <? if (db_sfq("SELECT COUNT(ID) FROM tblGalleries WHERE EntityID='{$_SESSION['CurrentEntityID']}' ") ==0) {?>
	   <em>(No Galleries for this Musician/Group Exist Yet)</em>
       <?
	 } else 
	   { if ($_SESSION['CurrentGalleryID']>0) {?>
       <em>Selected Existing <br />
       <span style="color:grey;">(click to change or add Gallery)</span></em>
       <? }else{?>
       <em>Select Existing</em>
       <? }}?><br />
<? display_dropdown ( /*HTMLID*/'GalleryID', /*Table*/'tblGalleries', /*InputName*/'GalleryID', /*OnChangeSubmit*/true, /*CheckOnline*/false, /*DisplayCurrent*/true, /*CurrentID*/$_SESSION['CurrentGalleryID'], /*QueryTable*/'', /*QueryWhere*/" AND EntityID='{$_SESSION['CurrentEntityID']}'",
 /*OrderBy */'Title',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'Click to Select Existing Gallery/Collection, or Enter New --> ', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'ID', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
  /*DisplayListField*/'Title', /*ValueField*/'ID', /*HideIfNone*/true, /*Anchor*/'', /*$FormTags*/true, /*OptionValueForUnselected*/ -1 ); ?>
</td>
	<form action=""  method="POST">
    <td width="1">&nbsp;</td>
    <td width="549" valign="top">
    <? if (!$_SESSION['CurrentGalleryID']>0)   
	{?>
	<em>Choose Type of New Gallery:</em><select name="GalleryTypeID">
		<option value="2">Top-level/Primary Musician/Band Collection for <?=$_SESSION['CurrentEntityLName']?></option>
		<option value="3">Supporting Sub Gallery for <?=$_SESSION['CurrentEntityLName']?></option>
	  	<option value="1">Base Collections (JHDB Admin Usage Only)</option>
    </select> <br />
    <table width="505" border="0">
      <tr>
        <td width="134" align="right">Gallery Summary</td>
        <td width="361"><textarea name="GallerySummary" cols="50" rows="3"></textarea></td>
      </tr>
  </table>
    <br />

    <? }?>     
	<? if ($_SESSION['CurrentGalleryID']>0)   
	{?><em>Edit Existing Gallery Title</em><? }else{?><em>Enter New Gallery/Collection Title:</em><? }?><br />      

    <input type="text" name="NewGalleryTitle" size="30" value="<?=$_SESSION['CurrentGalleryTitle']?>" /><br />
    
 
    <input type="submit" name="Command" value="Submit Gallery Name" /></td>
    </form>
  </tr>
  <tr>
    <td colspan="3"><br />
    Show/Hide Current  Contents (GalleryItems) for this Selected Gallery <a href="<?="{$GalleryDisplayBaseURL}/dump.php?d=1&g={$_SESSION['CurrentGalleryID']}" ?>" target="db_details"><?=($_SESSION['CurrentGalleryTitle'])?></a> (opens in new window)</td>
  </tr>
<? } ?>
<? if ($_SESSION['CurrentEntityID']>0 AND $_SESSION['CurrentGalleryID']>0)
{ ?> 
 <tr>
   <td colspan="3" >&nbsp;</td>
 </tr>
 <tr>
    <td height="33" colspan="3" class="StepTitle">Step 3 - Add a GalleryItem in Gallery &quot;<?=$_SESSION['CurrentGalleryTitle']?>&quot;</td>
  </tr>
  <tr>
    <td> <!-- some options below are not really "uploads",  All are types of GalleryItems -->
    <form id='UploadType' name='UploadType' method='post' action='/contributor.php' >
    <select name='UploadType'  OnChange='this.form.submit();' >
        <option value='' >Select New GalleryItem Type</option>
        <option value='HTML' 		<?= ($_SESSION['CurrentUploadType']=="HTML")		?"Selected=Selected":"" ?> >Paste HTML</option>
        <option value='PlainText' 	<?= ($_SESSION['CurrentUploadType']=="PlainText")	?"Selected=Selected":"" ?> >Paste Plain Text</option>
        <option value='Image' 		<?= ($_SESSION['CurrentUploadType']=="Image")		?"Selected=Selected":"" ?> >Image File</option>
        <option value='MP3' 		<?= ($_SESSION['CurrentUploadType']=="MP3")			?"Selected=Selected":"" ?> >MP3 File</option>
        <option value='YouTubeURL' 	<?= ($_SESSION['CurrentUploadType']=="YouTubeURL")	?"Selected=Selected":"" ?> >YouTube Video URL</option>
        <option value='SubGallery' 	<?= ($_SESSION['CurrentUploadType']=="SubGallery")	?"Selected=Selected":"" ?> >SubGallery</option>
	</select>  </form>
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
 <? } ?> 
<? if ($_SESSION['CurrentEntityID']>0 AND $_SESSION['CurrentGalleryID']>0 AND $_SESSION['CurrentUploadType']!="")
{ 
  switch ($_SESSION['CurrentUploadType'])
  {
	  case "HTML":
?> 
  <tr>
    <td>HTML</td><td></td>
    <td>
    </td>
  </tr>
<? break;
   case "PlainText":
?> 
  <tr>
    <td>Plain Text</td><td></td>
    <td>
    </td>
  </tr>
<? break;
   case "Image":
?> 

   <form action=""  method="POST">
  <tr>
    <td align="right">Image File Selection for Upload Into <br />
<?=$_SESSION['CurrentEntityLName']?>'s Image SubGallery: <?=$_SESSION['CurrentGalleryTitle']?><br />
(Make sure you have created an Image SubGallery First)</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td align="right">Caption</td><td></td>
    <td><input type="text" name="ImageCaption" size="60" />
    </td>
  </tr>
  <tr>
    <td align="right">Upload Image File</td><td></td>
    <td><input type="file" name="ImageFile" size="60" />
    </td>
  </tr>
  <tr>
    <td align="right">Submit Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit Image for Upload"/>
    </td>
  </tr>
  </form>
<? break;
   case "MP3":
?> 
  <tr>
    <td >MP3 File Selection for Upload</td><td></td>
    <td>
    </td>
  </tr>
<? break;
   case "SubGallery":
?> 
<form action=""  method="POST">
  <tr>
    <td colspan="3" ><p><strong>Associate SubGallery:<br />
      </strong>Define a Gallery Name first, then associate that Gallery as a SubGallery of this selected Parent Gallery:<strong>
<?=$_SESSION['CurrentGalleryTitle']?>
      </strong><br />
    To delete or edit, please contact a JHDB admin. If you need to insert a subgallery that is from another Entity (musician/band),<br />
    Please contact a JHDB admin.
    </p></td>
  </tr>
  <tr>
    <td height="31" align="right" >Parent Gallery</td>
    <td></td>
    <td><strong>
      <?=$_SESSION['CurrentGalleryTitle']?>
    </strong></td>
  </tr>
  <tr>
	<td height="31" align="right" > Choose Existing (Sub)Gallery<br />
	</td><td></td>
    <td><? display_dropdown ( /*HTMLID*/'AssociateSubGalleryID', /*Table*/'tblGalleries', /*InputName*/'AssociateSubGalleryID', /*OnChangeSubmit*/false, /*CheckOnline*/false, /*DisplayCurrent*/false, /*CurrentID*/'', /*QueryTable*/'', 
	/*QueryWhere*/" AND EntityID='{$_SESSION['CurrentEntityID']}'  AND ID != '{$_SESSION['CurrentGalleryID']}' ", // no recursion for gallery
	/*OrderBy */'Title',  /*Repeater*/'', /*DefaultTextLabel*/'Select Musician/Venue', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'ID', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
 /*DisplayListField*/'Title', /*ValueField*/'ID', /*HideIfNone*/false, /*Anchor*/'', /*$FormTags*/true ); ?>

	</td>
  	</tr>
    <tr>
    <td align="right">SubGallery Title to Display</td>
    <td></td>
    <td><input type="text" name="SubGalleryPageTitle" size="60" id="SubGalleryPageTitle" /></td>
 	 </tr>
    <tr>
    <td align="right">Caption</td><td></td>
    <td><input type="text" name="SubGalleryCaption" size="60" />
    </td>
  	</tr>  
    <tr>
    <td align="right">Add Chosen SubGallery--><br />
      to Parent Gallery:<br />
      <?=$_SESSION['CurrentGalleryTitle']?></td><td></td>
    <td valign="top"><input type="submit" name="Command" value="Associate SubGallery"/>
    </td>
 	</tr>
</form>
<? break;
   case "YouTubeURL":
?> 
  <form action=""  method="POST">
  <tr>
    <td align="right">Video Title to Display</td>
    <td></td>
    <td><input type="text" name="YouTubePageTitle" size="60" id="YouTubePageTitle" /></td>
  </tr>
  <tr>
    <td align="right">YouTube URL Entry</td><td></td>
    <td><input type="text" name="YouTubeURL" size="60" /> 
      <em>(required) </em></td>
  </tr>
  <tr>
    <td align="right">Caption</td><td></td>
    <td><input type="text" name="YouTubeURLCaption" size="60" />
    </td>
  </tr>
  <tr>
    <td align="right">Upload Thumbnail Image File</td><td></td>
    <td><input type="file" name="YouTubeURLFile" size="60" />
    </td>
  </tr>
  <tr>
    <td align="right">Submit Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit YouTube Info"/>
    </td>
  </tr>
  </form>
<? break;
  }// end switch
?> 
  
<? }// end if $_SESSION...  ?>
</table>



</body>
</html>