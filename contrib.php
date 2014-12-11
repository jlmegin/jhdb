<? 
// JHDB - webforms for contributors to upload MUSICIANS bio, image, MP3
// forms for contributors along with in this same file the processing for those forms [one file for easier mgmt]
// even tho all the forms are on the "same" page, they are (1) revealed as needed/enabled;  (2) scrolled-to for the next step (depending on the previous submit)

session_start();
require("db/lib-db.php");     			 
require("db/lib-JHDB-definitions.php");
require("db/lib-utils.php");
require("db/lib-form-util.php");  
$ScrollToAnchor					= ""; // scroll down in page after processing, to reveal next step in upload process
$ErrorOverrideScrollToAnchor	= ""; // in case error was triggered, don't scroll down

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
/* Set the size and font of the tab widget */
.tabGroup {
    font: 10pt arial, verdana;
    width: 100%;
    height: 100%;
}
 
/* Configure the radio buttons to hide off screen */
.tabGroup > input[type="radio"] {
    position: absolute;
    left:-100px;
    top:-100px;
}
 
/* Configure labels to look like tabs */
.tabGroup > input[type="radio"] + label {
    /* inline-block such that the label can be given dimensions */
    display: inline-block;
 
    /* A nice curved border around the tab */
    border: 1px solid black;
    border-radius: 5px 5px 0 0;
    -moz-border-radius: 5px 5px 0 0;
    -webkit-border-radius: 5px 5px 0 0;
     
    /* the bottom border is handled by the tab content div */
    border-bottom: 0;
 
    /* Padding around tab text */
    padding: 5px 10px;
 
    /* Set the background color to default gray (non-selected tab) */
    background-color:#ddd;
}
 
/* Focused tabs need to be highlighted as such */
.tabGroup > input[type="radio"]:focus + label {
    border:1px dashed black;
}
 
/* Checked tabs must be white with the bottom border removed */
.tabGroup > input[type="radio"]:checked + label {
    background-color:white;
    font-weight: bold;
    border-bottom: 1px solid white;
    margin-bottom: -1px;
}
 
/* The tab content must fill the widgets size and have a nice border */
.tabGroup > div {
    display: none;
    border: 1px solid black;
    background-color: white;
    padding: 10px 10px;
    height: 100%;
    overflow: auto;
     
    box-shadow: 0 0 20px #444;
    -moz-box-shadow: 0 0 20px #444;
    -webkit-box-shadow: 0 0 20px #444;
     
    border-radius: 0 5px 5px 5px;
    -moz-border-radius: 0 5px 5px 5px;
    -webkit-border-radius: 0 5px 5px 5px;
}
 
/* This matchs tabs displaying to thier associated radio inputs */
.tab2:checked ~ .tab2, .tab3:checked ~ .tab3, .tab4:checked ~ .tab4, .tab5:checked ~ .tab5, .tab6:checked ~ .tab6 {
    display: block;
}
</style>
<script type="text/javascript">
function scrollTo(hashLabel) {
    location.hash = "#" + hashLabel;
}
</script>

<? function scrollTo($HashLabel) { echo "\n<script type='text/javascript'> scrollTo('{$HashLabel}');</script>\n"; } ?>



<script type="text/javascript">
	$("#CreateAdditionalPhotoGallery1Name").keyup(function()
	{
		$(this).val()==""?$("#CreateAdditionalPhotoGallery1").attr("checked",false):$("#CreateAdditionalPhotoGallery1").attr("checked",true)
	})
	
	$("#CreateAdditionalAudioGallery1Name").keyup(function()
	{
		$(this).val()==""?$("#CreateAdditionalAudioGallery1").attr("checked",false):$("#CreateAdditionalAudioGallery1").attr("checked",true)
	})
</script>

</head>

<body>






<?

//----------------------------------------------- Retrieve Persistent Current IDs ---------------------------------------

//post_debug();



if (!$_SESSION['CurrentContributorID']) echo "Not Logged In Yet";

// non-Commands  EntityID and GalleryID  onChange selections   are not posted in the $Command variable
$LastCommand 				= $_SESSION['ThisCommand'];
$ThisCommand 				= $_SESSION['ThisCommand'] = $_POST['Command'];  // currently processing THIS COMMAND
if ($ThisCommand=="Submit Name") $LastCommand = ""; // null out since they're starting over/anew
$_SESSION['LastCommand'] 	= $LastCommand;
/*  States (steps) for entry by contributor:  (These are the values of $_POST['Command'] when each step is SUBMITted)
	(SAVE) Submit Name     (old:  contribute_i)
		   Submit Bio Info
		   Submit Gallery Info
			Associate SubGallery  (contributors would not use this)
			Submit YouTube Info
			Submit Image for Upload
			Submit MP3 File for Upload
	 
*/





// =================================================== PROCESS POSTs =======================================================
// DropDown SELECT caused EntityID to change
if ($_POST['EntityID'])  	
	{ // EntityID changed
		list($FName,$LName) = 
			db_sfq("SELECT FName, LName FROM tblEntities WHERE ID='{$_POST['EntityID']}' "); 
		$_SESSION['CurrentEntityFName']=stripslashes($FName); $_SESSION['CurrentEntityLName']=stripslashes($LName);			
		$_SESSION['CurrentEntityName']		= stripslashes(($FName?$FName." ":"").$LName);  //  using $_SESSION  mysteriously did not work,  no time to debug

 
		if ($_POST['EntityID']!=$_SESSION['CurrentEntityID']) 
			$_SESSION['CurrentGalleryID']=$_SESSION['CurrentGalleryTitle']=$_SESSION['CurrentUploadType']="";  
		$_SESSION['CurrentEntityID'] 		= $_POST['EntityID'];	
		$_SESSION['ThisCommand'] = "Select Entity: {$_SESSION['CurrentEntityLName']}";
		$ScrollToAnchor	= "NewGallery";
	} // end EntityID



// DropDown SELECT caused GalleryID to change
if ($_POST['GalleryID']>0)  // changed to an existing gallery 	
	{ // GalleryID changed

		if ($_POST['GalleryID']!=$_SESSION['CurrentGalleryID']) $_SESSION['CurrentUploadType'] = "";	
		$_SESSION['CurrentGalleryID'] 		= $_POST['GalleryID'];	
		$_SESSION['CurrentGalleryTitle']	= db_sfq("SELECT Title FROM tblGalleries WHERE ID={$_POST['GalleryID']} ");
		$_SESSION['ThisCommand'] 			= "Select Gallery: {$_SESSION['CurrentGalleryTitle']}";
		$_SESSION['CurrentUploadType']		= get_default_upload_type_from_GalleryID($_SESSION['CurrentGalleryID']);
		
		$ScrollToAnchor	= "Uploads";   // Next step after Gallery Selection
	} // end GalleryID>0
	
if ($_POST['GalleryID']==-1)  // wants to create new gallery 	
	{ 
		$_SESSION['CurrentUploadType'] = $_SESSION['CurrentGalleryID'] = $_SESSION['CurrentGalleryTitle']	= "";
		$ScrollToAnchor	= "NewGallery";   // Next step after Gallery Selection 
	} // end GalleryID


// DropDown SELECT caused UploadType to change
if ($_POST['UploadType'])  	
	{
		$_SESSION['CurrentUploadType'] 	= $_POST['UploadType'];	
		$ScrollToAnchor	= "SelectGallery";   // Next step after Gallery Selection 
	}

//post_debug();


// Was there a SUBMIT BUTTON Command?
$Command = $_POST['Command'];  // Switching Entities and Galleries:  The existance of the POST variable above serves as the "Command" to change IDs
//echo "<br>1++++++++++++++++++++++++++++++++++++++++++++++<br>";

if ($Command)
{
	echo "<br>Command just completed: <em>$Command</em><br>";
	
	switch ($Command)
	{
		case "Delete Entity &amp; ALL Associated Records &amp; Galleries":
			echo "DELETE goes here<br>";
			
		break;  // end case DeSelect
		
		
		case "DeSelect Entity & Gallery Selections":
			$_SESSION['CurrentEntityFName']=$_SESSION['CurrentEntityLName']=$_SESSION['CurrentEntityName']=$_SESSION['CurrentEntityID']=$_SESSION['CurrentUploadType']=$_SESSION['CurrentGalleryID']=$_SESSION['CurrentGalleryTitle']="";
		break;  // end case DeSelect
	
	
	
	
	
	
		//----------------------------------------- New/Existing Entity Name -------------------------------
		// Step just submitted: Entity Name/Dates; Next Step Bio/Descr
		case "Submit Name":    // Note: creates Entity record AND an associated top-level Gallery AND **FOUR** ubGalleries (if Musician)
		
			if (!$_POST['NewEntityLName']) die("<br/> <br/><strong>A Name Is Required</strong> - please <a href='http://jazzhistorymuseum.org/contrib.php'>click here</a> and enter required 'Last Name' (or band/venue name)");
			if ($_SESSION['CurrentEntityID']>0)
		// -------------------------------------------EXISTING--------------------------------------
			{ // Edit the existing name  from POST vars
		//  NOTE:   DOES NOT change directory name (path) when last name changes -- do this manually AND update path record field
				db_sql($Q1="UPDATE tblEntities 
						SET 
							FName			='{$_POST['NewEntityFName']}', 
							LName			='{$_POST['NewEntityLName']}',
							EntityTypeID 	='{$_POST['EntityTypeID']}'
							/*DirectoryPath  NOT updated*/
						WHERE ID='{$_SESSION['CurrentEntityID']}'
						");
//echo "<br>after UPDATE: ".db_sfq("SELECT LName FROM tblEntities WHERE ID=10007") ."<br>";

				log_upload( " Entity: Edit Name; $Q1; ");

				$_SESSION['CurrentEntityFName']		= stripslashes($_POST['NewEntityFName']);
				$_SESSION['CurrentEntityLName']		= stripslashes($_POST['NewEntityLName']);
						
				$_SESSION['CurrentEntityName']		= stripslashes(($_POST['NewEntityFName']?$_POST['NewEntityFName']." ":"").$_POST['NewEntityLName']);

				$_SESSION['CurrentGalleryID']		= $_SESSION['CurrentGalleryTitle'] = $_SESSION['CurrentUploadType']	= ""; // reset to be sure
				$ScrollToAnchor	= "EntityBio"; 
			} // end edit existing
			else
			
			
			
			
		// -------------------------------------------NEW ENTITY--------------------------------------
			{  // create new record & ID   from POST vars;  and   main directory for content/musicians  and  entityname/images
				$NextEntityID						= find_first_free_ID( "tblEntities", RangeMinEntityID, RangeMaxEntityID);
				$_POST['NewEntityFName']			= trim($_POST['NewEntityFName']);
				$_POST['NewEntityLName']			= trim($_POST['NewEntityLName']);
				$_SESSION['CurrentEntityFName']		= stripslashes($_POST['NewEntityFName']);
				$_SESSION['CurrentEntityLName']		= stripslashes($_POST['NewEntityLName']);
				
				$_SESSION['CurrentEntityName']		= stripslashes(($_POST['NewEntityFName']?$_POST['NewEntityFName']." ":"").$_POST['NewEntityLName']);
				list( $EntityDirectoryName,  $EntityDirectoryPHPPath, $EntityDirectoryURL )= 
					form_directory_name_and_mkdir( $_POST['NewEntityFName'], $_POST['NewEntityLName']);  // fn call takes care of stripslashes
				
				mkdir($EntityDirectoryPHPPath."/images");  chmod($EntityDirectoryPHPPath."/images", 0777);
				
				log_upload( " Entity: New musician Directory (& images/); $EntityDirectoryPHPPath; ");
						
				$EntityResults 	= db_sql($Q1="INSERT INTO tblEntities SET 
										ID 				= '{$NextEntityID}',
										FName			= '{$_POST['NewEntityFName']}', 
										LName			= '{$_POST['NewEntityLName']}',
										DirectoryPath	= '{$EntityDirectoryName}',
										EntityTypeID 	= '{$_POST['EntityTypeID']}',
										CreationDate	= NOW(),
										Online			= 1
										 "); //  OTHER FIELDS?   FIXME
										 
				
				$_SESSION['CurrentEntityID']		= db_mysqli_insert_id(); // new ID from insert

				$_SESSION['CurrentGalleryID']		= $_SESSION['CurrentGalleryTitle'] = $_SESSION['CurrentUploadType']	= ""; // reset to be sure
				
				log_upload( " Entity: New Name; $Q1; ");
				// --------------------  Create default top-level Gallery
				list($PrimaryEntityGIID, $PrimaryEntityGalleryID) = create_main_musician_GI_and_gallery($_SESSION['CurrentEntityID']);  // creates new Gallery Record and sets SESSION variables to "select gallery"
				
				// --------------------  If Musician, Create Four standard subgalleries underneath
				
				create_default_musician_GI_and_subgalleries($PrimaryEntityGalleryID);
				db_sql("UPDATE tblEntities SET PrimaryGIID = '{$PrimaryEntityGIID}' WHERE ID = '{$_SESSION['CurrentEntityID']}' ");

echo " <br>PrimaryEntityGIID=$PrimaryEntityGIID ; EntityID= {$_SESSION['CurrentEntityID']}  PrimaryEntityGalleryID=$PrimaryEntityGalleryID; **********";

				$ScrollToAnchor	= "EntityBio"; 
			} // end else create new
		break;  // end case Submit Name







		//-----------------------------------------    upload BIO/DESCR INFO      -------------------------------
		// Step just submitted: "Submit Bio Info"
		// Note: if there are BOTH Bio AND Descr, NOT IMPLEMENTED
		
		case "Submit Bio Info": 
			// record Birth dates/etc in Entity Record
			
			//  NOT IMPLEMENTED for: EDIT *existing* bio, etc.
			
			if (!$_SESSION['CurrentEntityID'])  // needs session variable to be en force
			 { echo "Warning: (Internal Error [contrib]) Bio info posted with no CurrentEntityID, this update ignored. EntityID[{$_SESSION['CurrentEntityID']}]<br>\n"; break;}
			
			
			
			//------------------------------- locate directory path ------------------------------
			$EntityDirectoryName 	= db_sfq("SELECT DirectoryPath FROM tblEntities WHERE ID = '{$_SESSION['CurrentEntityID']}' ");
			$EntityDirectoryPHPPath = EntityContentPHPBasePath."musicians/{$EntityDirectoryName}";   // full path
			$EntityDirectoryURL	 	= EntityContentBaseURL."musicians/{$EntityDirectoryName}";  // full path
			$EntityDirectoryBaseRoot= EntityContentBaseRoot."musicians/{$EntityDirectoryName}";  //   /content/musicians.,,,

			if (!file_exists($EntityDirectoryPHPPath))
				echo "Error [contrib]: $EntityDirectoryPHPPath directory doesn't exist for Entity[{$_SESSION['CurrentEntityID']}];  contact JHDB admin with this message (contribute.php) ";

			

			
			$BirthDate 		= convert_field_to_sql_date($_POST['EntityBirthDate']);  //if year only, YYYY-00-00
			$DeathDate 		= convert_field_to_sql_date($_POST['EntityDeathDate']);  // if null, returns null
			$TargetBioImageURL 	= $ImageCaption = $BioFileURL = "";
//post_debug(); 	
echo " <br>Image Files (may be null): {$_POST['EntityBioImageFile']}; {$_FILES['EntityBioImageFile']['name']} <br>\n";		
			if ($_FILES['EntityBioImageFile']['error']==0 OR $_POST['EntityBioImageSourceURL'])
			{  //process bio/descr image
/*				
$imgname     = $_FILES['img'] ['name'] ;
$imgsize     = $_FILES['img'] ['size'] ;
$imgtmpname  = $_FILES['img'] ['tmp_name'] ;
$imgtype     = $_FILES['img'] ['type'] ;
*/
//echo "<br>attempting: mkdir($EntityDirectoryPHPPath/images); <br> \n";
				if (!file_exists("$EntityDirectoryPHPPath/images"))  echo "Warning [contrib]: image directory does not exist {$EntityDirectoryPHPPath}/images; please send this error message to JHDB admins<br>"; 
					//{mkdir("$EntityDirectoryPHPPath/images", 0777); chmod("$EntityDirectoryPHPPath/images", 0777); /*chown("$EntityDirectoryPHPPath/images", ServerAccountUsername);*/} // create the musician's content directory

				//$TargetImageFilePath 	= "{$EntityDirectoryPHPPath}/images/BioImage.jpg";   // FIXME  change to using MoveToCurrentEntityDirectory
				//$TargetBioImageURL	= "{$EntityDirectoryURL}/images/BioImage.jpg";
				if ($_POST['EntityBioImageSourceURL'])
				{ //  URL was given as image source
					// need error check for URL syntax, existance
					if ($_FILES['EntityBioImageFile']['error']==0) echo "WARNING [contrib]: bio image file upload ignored because URL was given<br>";
					$TargetBioImageURL = MoveToCurrentEntityDirectory('images', $_POST['EntityBioImageSourceURL'], 'URL', '', MaxThumbSizeH, MaxThumbSizeW );
				} else  // it was a file upload
				{
					// from the file upload, capture the resulting URL pointer
					$TargetBioImageURL = MoveToCurrentEntityDirectory('images', 'EntityBioImageFile', 'rename', '', MaxThumbSizeH, MaxThumbSizeW );
				}
				 
				//chown( $TargetImageFilePath, ServerAccountUsername);
				$ImageCaption			= $_POST['EntityBioImageCaption'];
			} // end EntityBioImageFile
			
			
			
			if ($_FILES['EntityThumbImageForGalleryMarquis']["error"]==0 OR $_POST['EntityThumbURLForGalleryMarquis'])
			{  //process bio thumb image (or URL to image)
				
				if ($_POST['EntityThumbURLForGalleryMarquis'])
				{ //  URL was given as image source
					// need error check for URL syntax, existance
					if ($_FILES['EntityThumbImageForGalleryMarquis']["error"]==0) echo "WARNING [contrib]: bio marquis image file upload ignored because URL was given<br>";
					$MarquisThumbImageURL = MoveToCurrentEntityDirectory('images', $_POST['EntityThumbURLForGalleryMarquis'], 'URL', '', MaxMarquisSizeH, MaxMarquisSizeW );
				} else  // it was a file upload
				{
					// from the file upload, capture the resulting URL pointer
					$MarquisThumbImageURL = MoveToCurrentEntityDirectory('images', 'EntityThumbImageForGalleryMarquis', 'rename', '', MaxMarquisSizeH, MaxMarquisSizeW );
				}
				
				// the above resize is for FIT-WITHIN max H & W.  this wide-aspect-ratioed layout area would likely need a designer to select and crop a best showcase/marquis image snippet for this purpose

				
				$PrimaryGIID		= db_sfq("SELECT PrimaryGIID 
											FROM tblEntities WHERE ID = '{$_SESSION['CurrentEntityID']}' ");
				$PrimaryGalleryID	= db_sfq($Q3="SELECT SubGalleryID FROM tblGalleryItems WHERE ID='{$PrimaryGIID}'");
				if ($PrimaryGalleryID)  // NOTE:  this is BIO UPLOAD, but using the Marquis thumb image for a higher level than JUST the BIO   gallery item and the main musician collection marquis thumb
				{
					$CurrentGIMarguisThumbURL = db_sfq("SELECT ThumbURL FROM tblGalleryItems WHERE ID= '{$PrimaryGIID}'");
					if (!$CurrentGIMarguisThumbURL)  // if already there, don't replace it
					db_sql($Q4="UPDATE tblGalleryItems SET ThumbURL = '$MarquisThumbImageURL'  
							WHERE ID= '{$PrimaryGIID}' ");	  // this is the one used for displaying thumbnails	
					db_sql($Q2="UPDATE tblGalleries SET ThumbURL = '$MarquisThumbImageURL'  
							WHERE ID= '{$PrimaryGalleryID}' ");   // this may have been deprecated, but post anyway
				}
				 else log_upload("ERROR [contrib]: No SubGallery associated with GI for Entity:".$_SESSION['CurrentEntityID']." when trying to update gallery marquis (wide) thumbnail: ".$Q3."; ".$Q2);
				 log_upload("Bio Marquis Thumb for mus Directory Gallery: $Q2; $Q3; $Q4;" );
			} // end EntityBioImageFile			
			
			
			if (strlen(trim($_POST['EntityBioDescription']))>0)   //  Bio text was entered
			{  //process bio/descr text
//				if (""!=db_sfq("SELECT  *BioURL* FROM tblEntities WHERE ID='{$_SESSION['CurrentEntityID']}' ")) echo "WARNING: Bio info updating/editing not yet supported, please contact JHDB admins, this bio update was ignored<br>";
//				else
				{ //  create new
					$BioText 		= trim($_POST['EntityBioDescription']);
					if($BioText != strip_tags($BioText))  $FileExt = "html";  else  $FileExt = "txt";
					$BioFilePath 	= "{$EntityDirectoryPHPPath}/bio.{$FileExt}";
					$BioFileURL		= "{$EntityDirectoryURL}/bio.{$FileExt}";
					$BioFileRoot	= "{$EntityDirectoryBaseRoot}/bio.{$FileExt}";

//	echo "<br>BIO: $BioFilePath; $BioFileURL; <br>\n";
					$BioFile = fopen($BioFilePath, "w") or die("Error [contrib]: Bio Create File: Unable to open file: {$BioFilePath}");
					fwrite($BioFile, $BioText);
					fclose($BioFile);
					
					log_upload( " Bio: create new bio text/html file:{$BioFileURL}; ");

				}// end create new
				
			  }// end EntityBioDescription
	
			db_sql($Q1="UPDATE tblEntities SET
						Birth 			= '{$BirthDate}',
						Death 			= '{$DeathDate}',
						/*DirectoryPath = '{$EntityDirectoryName}',   should have already been done */
						ImageURL		= '{$TargetBioImageURL}',
						ImageCaption	= '{$ImageCaption}'
					WHERE ID = '{$_SESSION['CurrentEntityID']}'
			");

			log_upload( " Bio Submit; $Q1; $Q2 ");
			
//echo "<br>BioFileURL=$BioFileURL; MarquisThumbImageURL=$MarquisThumbImageURL;   $Q1;$Q2<br>\n";
				
			//check to see if Bio/Descr GalleryItem record exists (from Step 1a auto-creation of GalleryItems and SubGalleries)
				
			//$ThisPrimaryGIID = db_sfq("SELECT PrimaryGIID FROM tblEntities WHERE ID={$_SESSION['CurrentEntityID']} ");	// get (hopefully) newly added Musician Main GalleryID
			//$_SESSION['CurrentGalleryID'] = db_sql("SELECT SubGalleryID FROM tblGalleryItems WHERE ID = '{$ThisPrimaryGIID}'");
			$ThisBioGalleryItemID = db_sfq("SELECT BioDescrGIID FROM tblEntities WHERE ID='{$_SESSION['CurrentEntityID']}'");
echo "<!-- *** ThisBioGalleryItemID= $ThisBioGalleryItemID = db_sfq('SELECT BioDescrGIID FROM tblEntities WHERE ID='{$_SESSION['CurrentEntityID']}''); -->\n";
			if (!$ThisBioGalleryItemID) 
			{  // Gal Item for Bio didn't prev exist, so insert new GI
				list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('GalleryItem');	
				$NextGalleryItemID				= find_first_free_ID( "tblGalleryItems", $RangeMin, $RangeMax);
		 		  
				db_sql($Q1= "INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$PrimaryGalleryID}',
					SubGalleryID	= NULL,  /* none: bio page is a leaf node */
					GIContentTypeID	= '".GIContentType_JHDBContentFile."', 
					URL				= '{$BioFileRoot}',
					ThumbURL		= '{$MarquisThumbImageURL}',
					PageTitle		= 'Bio for {$_SESSION['CurrentEntityName']}',
					MenuTitle		= 'Biography',
					Online			= 1,
					Sort			= 100					
				  ");
				$ThisBioGalleryItemID	= db_mysqli_insert_id();
				  
				db_sql("UPDATE tblEntities SET BioDescrGIID	= '{$ThisBioGalleryItemID}' 
				  			WHERE ID='{$_SESSION['CurrentEntityID']}'");  // new GI was added
				  
				log_upload( " Bio Create New Gal Item[{$ThisGalleryItemID}]; $Q1; ");
			}// end  create new
			elseif ($ThisBioGalleryItemID>0)
			{ // exactly one bio found, UPDATE the GI record

				db_sql($Q1= "UPDATE tblGalleryItems SET
		  			ParentGalleryID	= '{$PrimaryGalleryID}',
					SubGalleryID	= NULL,  /* none: bio page is a leaf node */
					GIContentTypeID	= '".GIContentType_JHDBContentFile."', 
					URL				= '{$BioFileRoot}',
					ThumbURL		= '{$MarquisThumbImageURL}',
					PageTitle		= 'Bio for {$_SESSION['CurrentEntityName']}',
					MenuTitle		= 'Biography',
					Online			= 1,
					Sort			= 100	
		 			WHERE ID = '{$ThisBioGalleryItemID}' /* existing bio GIID */				
				  ");
				log_upload( " Bio Update existing Gal Item[{$ThisBioGalleryItemID}]; $Q1; ");

			} // end  Update GI
//			elseif ($Count>1)
//				 echo "Warning: More than one({$Count}) GalleryItem records found for Bio/Descr; Contact JHDB admins to include your information for GalleryItem[{$ThisBioGalleryItemID}], where Primary Entity GalleryID[{$PrimaryGalleryID}]<br>";
		  
			
			
			
			//  if there were no galleries, scroll to  NewGallery;  if image or audio, scroll to SelectGallery (and pre select that gallery)
			$ScrollToAnchor	= "NewGallery";
			/* FIXME  
				$_SESSION['CurrentGalleryID']		=  
				$_SESSION['CurrentGalleryTitle']	= db_sfq("SELECT Title FROM tblGalleries WHERE ID={$_POST['GalleryID']} ");
			if (count_toplevel_galleries_for_EntityID($_SESSION['CurrentEntityID']) >0 )   
					$ScrollToAnchor	= "SelectGallery"; 
			*/
			
			$ScrollToAnchor	= "SelectGallery";   //  assume for now (one or more exist) ...
		break;  // end case Submit Bio Info









		//----------------------------------------- Edit or Create New Gallery Name -------------------------------
		// Step just submitted: "Submit Gallery Info"
		case "Submit Gallery Info": 
			// EXISTING GALLERY CHOSEN
			if ($_SESSION['CurrentGalleryID']>0)
			{ // ----------- Edit the existing name  from POST vars
				db_sql($Q1="UPDATE tblGalleries 
						SET Title='{$_POST['NewGalleryTitle']}' 
						WHERE ID='{$_SESSION['CurrentGalleryID']}' ");
				$_SESSION['CurrentGalleryTitle']	= $_POST['NewGalleryTitle'];
				$_SESSION['CurrentUploadType']		= ""; // reset to be sure
				
				log_upload( " Gallery: Edit Name; $Q1; ");
			} // end edit existing
			else
			{  // CREATE NEW Gallery record & ID   from POST vars
			
				list($RangeMin, $RangeMax) =  get_ID_range_for_GalleryType($_POST['GalleryType']); // GalleryType contains Textual parameter type
				
				$NextGalleryID	= find_first_free_ID( "tblGalleries", $RangeMin, $RangeMax);
				$GalleryResults = db_sql($Q1="INSERT INTO tblGalleries SET 
						ID				= '{$NextGalleryID}',
						Title			= '{$_POST['NewGalleryTitle']}',
						Summary			= '{$_POST['GallerySummary']}',
						EntityID 		= '{$_SESSION['CurrentEntityID']}',
						GIContentTypeID	= '{$_POST['GIContentTypeID']}',
						GalleryUsageTypeID= '{$_POST['GalleryUsageTypeID']}',
						ContributorID	= '{$_SESSION['ContribbutorID']}',
						Online		= 1
					"); //  OTHER FIELDS?   FIXME
				if (!$GalleryResults) die(" [contrib] Database gallery insert failed:  Title='{$_POST['NewGalleryTitle']}");
				$_SESSION['CurrentGalleryID']		= db_mysqli_insert_id(); // new ID from insert
				$_SESSION['CurrentGalleryTitle']	= $_POST['NewGalleryTitle'];
				$_SESSION['CurrentUploadType']		= ""; // reset, to be sure
				
				log_upload( " Gallery: Create New; $Q1; ");
			} // end else create new
			$ScrollToAnchor	= "Uploads"; 
		break;   // end case Gallery


		
		
		case "Associate SubGallery":  //----------------------------------------- Add SubGallery GalleryItem -------------------------------
		// error check
			$SubGalleryResults = db_sql($Q1="INSERT INTO tblGalleryItems SET 
					GIContentTypeID		= '".GIContentType_SubGallery."',
					SubGalleryID 		= '{$_POST['AssociateSubGalleryID']}',
					CaptionText			= '{$_POST['SubGalleryCaption']}',
					ThumbURL 			= '{$ThumbFilePath}',
					PageTitle 			= '{$_POST['SubGalleryPageTitle']}',
					ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}',
					Online				= 1
					");
				if ($YouTubeResults==true) echo "Database update success for {$_POST['YouTubeURL']}"; else echo "Database update failed for {$_POST['YouTubeURL']}";
			$ScrollToAnchor	= "Uploads"; 
			log_upload( " Gallery: Associate SubGallery; $Q1; ");
		break; // end CASE SubGallery
		
		


		
		case "Submit YouTube Info":  //----------------------------------------- Capture YouTube info  -------------------------------
			if (!$_POST['YouTubeURL'])  echo "<strong>Please enter your YouTube URL web address and re submit</strong>  [contrib]<br>";
			else
			{ // URL ready to upload, NO ERROR CHECKING DONE ON SYNTAX
				// Caption and Thumbnail are optional
				$ImageFileURL = "";
				/*   NO THUMB NEEDED nor IMPLEMENTED 
				if ($_POST['YouTubeThumbImageURL'])
				{ //  URL was given as image source
					// need error check for URL syntax, existance
					if ($_FILES['YouTubeThumbImage']["error"]==0) echo "WARNING: YouTube thumb image file upload ignored because URL was given<br>";
					$ImageFileURL = MoveToCurrentEntityDirectory('images', $_POST['YouTubeThumbImageURL'], 'URL', '', MaxThumbSizeH, MaxThumbSizeW );
				} 
				 elseif ($_FILES['YouTubeThumbImage']["error"]==0) // it was a file upload
				{
					$ImageFileURL = MoveToCurrentEntityDirectory('images', 'YouTubeThumbImage', 'rename', '', MaxThumbSizeH, MaxThumbSizeW );
				}// end elseif
				*/
				
				$YouTubeResults = db_sql($Q1="INSERT INTO tblGalleryItems SET 
					GIContentTypeID		= '".GIContentType_VideoEmbedYouTube."',
					URL 				= '".trim($_POST['YouTubeURL'])."',
					CaptionText			= '".trim($_POST['YouTubeURLCaption'])."',
					ThumbURL 			= '{$ImageFileURL}',
					PageTitle 			= '".trim($_POST['YouTubePageTitle'])."',
					ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}',
					Online				= 1
					");
				if ($YouTubeResults==true) echo "Database update success for {$_POST['YouTubeURL']}"; else echo "Database update failed for {$_POST['YouTubeURL']}   [contrib]";
			}// end else
			$ScrollToAnchor	= "Uploads"; 
			log_upload( " Upload: Submit YouTube Info; $Q1; ");
		break; // end CASE YouTube





//-----------------------------------------  Upload Image & info  -------------------------------
		case "Submit Image for Upload":  
/*		
			$temp = explode(".", strtolower($_FILES["ImageFile"]["name"]));
			$ImageFileExtension = end($temp);

			if (!in_array($ImageFileExtension, $AllowedImageUploadExts)) die("Error: The file extension ({$ImageFileExtension}) for Image file ({$_FILES['ImageFile']['name']}) is not an extension of type ".implode(", ",$AllowedImageUploadExts)."<br>");
			if ($_FILES['ImageFile']['error']!=0)  
			{
				echo "<strong>Please select an image file from your local disk and re submit</strong><br>";
				print_r($_FILES['ImageFile']);
			}


				$ImageFilePath = "";

				if ($_FILES['ImageFile']['type']!='image/jpeg') echo" WARNING: Found: {$_FILES['ImageFile']['type']}; file {$_FILES['ImageFile']['name']} is not the proper image format.  Please upload a file of type JPEG (.jpg) <br>";
*/
			
//echo "<br>attempting: MoveToCurrentEntityDirectory('images', {$_FILES['ImageFile']['name']});  type = {$_FILES['ImageFile']['type']}  <br>";

				// XX $ImageFileTempSourcePath = $_FILES['ImageFile']['tmp_name'];



			if ($_POST['ImageFileURL'])
			{ //  URL was given as image source
				// need error check for URL syntax, existance
				if ($_FILES['ImageFile']["error"]==0) echo "WARNING [contrib]: image file upload ignored because URL was given<br>";
				$ImageFileURL 		= MoveToCurrentEntityDirectory('images', $_POST['ImageFileURL'], 'URL', '', MaxImageSizeHGallery, MaxImageSizeWGallery );
				$ImageFileThumbURL 	=  MoveToCurrentEntityDirectory('images', $_POST['ImageFileURL'], 'URL', '', MaxThumbSizeH, MaxThumbSizeW );
			} 
			 elseif ($_FILES['ImageFile']["error"]==0) // it was a file upload
			{
				$ImageFileURL 		= MoveToCurrentEntityDirectory('images', 'ImageFile', 'rename', '', MaxImageSizeHGallery, MaxImageSizeWGallery );
				$ImageFileThumbURL 	= MoveToCurrentEntityDirectory('images', 'ImageFile', 'rename', '_t'/* suffix for thumb */, MaxThumbSizeH, MaxThumbSizeW ); 
			}// end elseif
				
			$ImageGIResults = db_sql($Q1="INSERT INTO tblGalleryItems SET 
				GIContentTypeID		= '".GIContentType_ImageWithPlayer."',
				URL 				= '{$ImageFileURL}',
				CaptionText			= '{$_POST['ImageCaption']}',
				ThumbURL 			= '{$ImageFileThumbURL}',
				PageTitle 			= 'Image for {$_SESSION['CurrentEntityName']}',
				ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}',
				Online				= 1
				");
				
			log_upload( " Upload: Submit Image; $Q1; ");
			if ($ImageGIResults==true) echo "Database update success for {$_FILES['ImageFile']['name']}"; else echo "Database update failed for {$_FILES['ImageFile']['name']}   [contrib]";
				
			$ScrollToAnchor	= "Uploads";
		break; // end CASE Images	
		
		
		
		
			
//-----------------------------------------  Upload MP3 & info  -------------------------------		
		case "Submit MP3 File for Upload":  
		
		
			if ($_POST['AudioFileURL'])
			{ //  URL was given as MP3 source
				// need error check for URL syntax, existance
				if ($_FILES['AudioFile']["error"]==0) echo "WARNING: MP3 file upload ignored because URL was given  [contrib]<br>";
				$AudioFileURL = MoveToCurrentEntityDirectory('audio', $_POST['AudioFileURL'], 'URL' ); // no file type sanity check
			} 
			 elseif ($_FILES['AudioFile']["error"]==0) // it was a file upload
			{
				$temp = explode(".", $_FILES["AudioFile"]["name"]);
				$MP3FileExtension = strtolower(end($temp));
				if (!in_array($MP3FileExtension, $AllowedAudioUploadExts)) die("Error: The file extension ({$MP3FileExtension}) for Audio file ({$_FILES['AudioFile']['name']}) is not an extension of type ".implode(", ",$AllowedAudioUploadExts)." [contrib]<br>");
				if ($_FILES['AudioFile']['error']!=0)  
				{
					echo "<strong>Please select an audio file from your local disk and re submit</strong> [contrib]<br>";
					print_r($_FILES['AudioFile']);
				}
				
				if ($_FILES['AudioFile']['type']!='audio/mpg') echo" WARNING: Found: {$_FILES['AudioFile']['type']}; file {$_FILES['AudioFile']['name']} is not the proper audio format.  Please upload a file of type MP3 (.mp3)  [contrib]<br>";
				
				$AudioFileURL = MoveToCurrentEntityDirectory('audio', 'AudioFile' );
			}// end elseif
		
			$AudioGIResults = db_sql($Q1="INSERT INTO tblGalleryItems SET 
				GIContentTypeID		= '".GIContentType_AudioWithPlayer."',
				URL 				= '{$AudioFileURL}',
				CaptionText			= '{$_POST['AudioCaption']}',
				ThumbURL 			= '',
				PageTitle 			= 'Audio for {$_SESSION['CurrentEntityName']}',
				ParentGalleryID		= '{$_SESSION['CurrentGalleryID']}',
				Online				= 1
				");
			log_upload( " Upload: Submit MP3; $Q1; ");
			if ($ImageGIResults==true) echo "Database update success for {$_FILES['AudioFile']['name']}"; else echo "Database update failed for {$_FILES['AudioFile']['name']} [contrib]";

			$ScrollToAnchor	= "Uploads"; 
		

		break; // end CASE Audio
		

	}// end switch
}// end process Command action

// ======================================================= END POSTs PROCESSING  ===============================================











// =========================================================== MAINLINE FORMs ================================================
?>

<!--<table width="906" border="0">
  <tr>
    <td colspan="3"><em>Logged in:</em> (contributor name will be added here)<br /> Scroll to: <?=$ScrollToAnchor	?>     <br />
</td>
  </tr>
  <tr>
    <td colspan="3">
    <? if($_SESSION['CurrentEntityID']>0)
	{?><form action="" method="POST"> <input type="submit" name="Command" value="DeSelect Entity &amp; Gallery Selections"/> <br />
     <em>Admin usage only:</em>  <input type="submit" name="Command" value="Delete Entity &amp; ALL Associated Records &amp; Galleries" onClick="alert('DANGER: Confirm This MAJOR Deletion (to cancel, use BROWSER BACK');" /> <span style="color:red;font-weight:bold;"><-- Delete: <?=$_SESSION['CurrentEntityName']?></span></form>
    <p>
      <? } else {?>
      <strong>Please start</strong> by telling us a little about what you wish to contribute by <br />
      first identifying the performer/band/venue/artist/poet and then
      <br />
      entering the most common <strong>name</strong> of the performer/band/venue/etc. and then<br />
      selecting the <strong>category</strong> that best describes this  entry.<br />
<? } ?>
    </p></td>
  </tr>
</table>-->
<h1>Jazz History Database Contributions Page</h1>
<p>To begin, please enter the name of the new artist, event, or other subject you would like to contribute to, or select an existing one from the list.


<? //-------------------------------  Step 1a Add/pick the artist  -------------------------  ?>
<div class="tab1">
<table>
  <tr>

    <td width="410" valign="top">
     <form action=""  method="POST">
	 <? if ($_SESSION['CurrentEntityID']>0) {?><em><strong>EDIT EXISTING</strong> Name of Artist </em> <? }else{?>
	 <br />
	 <em><strong>Enter New Artist Name</strong></em> 
	 <? }?><span style='color:grey;'><br />
      (If NOT a person, then leave FirstName blank)</span><br />      
      <table width="400" border="0" <?=($_SESSION['CurrentEntityID']>0 ?"":"bgcolor='#FFFF00'")?> >
      <tr>
        <td><strong>First Name</strong> <br />
          <input type="text" name="NewEntityFName" size="30" value="<?=$_SESSION['CurrentEntityFName']?>" /></td>
        <td><strong>Last Name</strong><br />
          <input type="text" name="NewEntityLName" size="30" value="<?=$_SESSION['CurrentEntityLName']?>" /></td>
        </tr>
      <tr>
        <td colspan="2"><br />
          <strong>Which of the following best describes the new contribution:</strong><br />
          <?  
		  $CategoryResults = db_sql("SELECT * FROM tblEntityTypes WHERE Online=1");
		  while ($Row=mysqli_fetch_assoc($CategoryResults))
		    {
			  $ID 		= $Row['ID'];
			  $Category = $Row['Category'];
			  $ThisCategorySelected = "";
			  if ((!($_SESSION['CurrentEntityID']>0) AND $ID==1) OR 
			        ($_SESSION['CurrentEntityID']>0 AND 
			      $ID==db_sfq("SELECT EntityTypeID FROM tblEntities WHERE ID={$_SESSION['CurrentEntityID']}")))
				   $ThisCategorySelected = " checked ";
		  ?>
          <label><input type="radio" name="EntityTypeID"  value="<?=$ID?>"  <?=$ThisCategorySelected?>/><?=$Category?></label><br />
          <? }// end while each category  ?>
        </td>
        </tr>
        
 
        
  <?/* if(!($_SESSION['CurrentEntityID']>0))
//NOTE: THIS CHUNK OF CODE IS COMMENTED OUT BECAUSE WE DONT WANT TO DISPLAY IT
//BUT I SUSPECT SOME VARAIABLES ARE SET BASED OFF WHAT THE USER WOULD SUBMIT HERE
//THAT COULD BE IMPORTANT FOR THE REST OF THE SITE TO FUNCTION, but who knows cause PHP is bad
	{?>      
      <tr>
        <td colspan="2"><p>&nbsp;</p>
          <p>For new entries, please check below which you will be uploading/entering for the following types of items:<br />
            <span style="color:grey;">(if checked, SubGalleries will be automatically created)</span></p>
          <p>
            <input name="CreateBioItem" type="checkbox" id="CreateBioItem" value="1" checked="checked" />
            <label for="CreateDescrItem">Biography  of Musician/Artist/Poet</label> 
            &nbsp;<br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(or...) 
            <input name="CreateDescrItem" type="checkbox" id="CreateDescrItem" value="1" />
            <label for="CreateDescrItem">Description  of Group/Band/Venue</label><br />
			<input name="CreateImageGallery" type="checkbox" id="CreateImageGallery" value="1" checked="checked" />
            <label for="CreateImageGallery">Image Gallery named(*)</label> 
            <input type="text" name="ImageGalleryName" size="20" id="ImageGalleryName" value="Photo Gallery" />
            <br />
            <input name="CreateAudioGallery" type="checkbox" id="CreateAudioGallery" value="1" />
            <label for="CreateAudioGallery">Audio Gallery named</label>
            <input type="text" name="AudioGalleryName" size="20" id="AudioGalleryName" value="MP3 Gallery" />
            <br />
            <input name="CreateVideoGallery" type="checkbox" id="CreateVideoGallery" value="1" />
            <label for="CreateVideoGallery">YouTube Video Gallery named</label>
            <input type="text" name="YouTubeGalleryName" size="20" id="YouTubeGalleryName" value="Video Gallery" />
            <br />
            

            <br />
            <input name="CreateAdditionalPhotoGallery1" type="checkbox" id="CreateAdditionalPhotoGallery1" value="1" />
            <label for="CreateAdditionalPhotoGallery1">Additional Photo Gallery</label>
            named:
            <input type="text" name="CreateAdditionalPhotoGallery1Name" size="20" id="CreateAdditionalPhotoGallery1Name" />
            <br />
            <br />
            <input name="CreateAdditionalAudioGallery1" type="checkbox" id="CreateAdditionalAudioGallery1" value="1" />
            <label for="CreateAdditionalAudioGallery1">Additional Audio Gallery</label>
            named:
            <input type="text" name="CreateAdditionalAudioGallery1Name" size="20" id="CreateAdditionalAudioGallery1Name" />
            <br />
            <br />
            <input name="CreateAdditionalYouTubeGallery1" type="checkbox" id="CreateAdditionalYouTubeGallery1" value="1" />
            <label for="CreateAdditionalYouTubeGallery1">Additional YouTube Gallery</label>
            named:
            <input type="text" name="CreateAdditionalYouTubeGallery1Name" size="20" id="CreateAdditionalYouTubeGallery1Name" />
            </p>
          <blockquote>
            <p>(*) <em>Names are used in various places, including <br />
              Navigation Menus (please keep them short)
              </em><br />
            </p>
          </blockquote></td>
      </tr>
    <? } */?> 
      
      <tr>
        <td colspan="2"><input type="submit" name="Command" value="Submit Name" />
          <? if ($_SESSION['CurrentEntityID']>0) {?><--Save EDITED Name/Category<br />
          <span style="color:grey"><em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Contact JHDB admin to delete entries)</em></span>
          <? }else{ ?><--Save new Name & Category<? }?>
        </td>
        </tr>
 
    </table>
    </form>
    </td>
    <td width="1">&nbsp;</td>
    <td width="481" valign="top"><? if ($_SESSION['CurrentEntityID']>0) {?><em>Choose an  Existing Name &nbsp;&nbsp;&nbsp;(or add a new Name on the left)<br />
        <span style="color:grey;">(select from this list of existing names)</span></em><? }else{?><em> .. OR Select an Existing Name<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(for uploads)</em> <? }?><br />
<? display_dropdown ( /*HTMLID*/'EntityID', /*Table*/'tblEntities', /*InputName*/'EntityID', /*OnChangeSubmit*/true, /*CheckOnline*/false, /*DisplayCurrent*/true, /*CurrentID*/$_SESSION['CurrentEntityID'], /*QueryTable*/'', /*QueryWhere*/'', /*OrderBy */'LName',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'Select Musician/Venue', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'ID', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
 /*DisplayListField*/'LName', /*ValueField*/'ID', /*HideIfNone*/false, /*Anchor*/'', /*$FormTags*/true, /*OptionValueForUnselected*/ -1 ); ?>
</td>
  </tr>
  <tr>
    <td colspan="3" >&nbsp;</td>
  </tr>
  </table>
</div>

  <div class="tabGroup">
    <input type="radio" name="tabGroup1" id="rad2" class="tab2" checked="checked"/>
    <label for="rad2">Biography</label>
     
    <input type="radio" name="tabGroup1" id="rad3" class="tab3"/>
    <label for="rad3">Galleries</label>

<input type="radio" name="tabGroup1" id="rad4" class="tab4"/>
    <label for="rad4">Images</label>

<input type="radio" name="tabGroup1" id="rad5" class="tab5"/>
    <label for="rad5">Audio</label>

<input type="radio" name="tabGroup1" id="rad6" class="tab6"/>
    <label for="rad6">Video</label>


  
  
<? //-------------------------------  Step 1b Biography  -------------------------  ?>

<? //if ($_SESSION['CurrentEntityID']>0) { ?>
<div class="tab2">
<table>
  <tr>
    <td height="53" colspan="3" class="StepTitle"><a name="EntityBio" id="EntityBio"></a>Step 1b - Enter Description/Biography for <?=$_SESSION['CurrentEntityName'] ?>  &nbsp;&nbsp;&nbsp;&nbsp;&lt;-- <em>NOTE TO STAFF:</em> &nbsp;Step 1b WILL NOT SHOW ONCE ENTERED</td>
  </tr>
  <tr>
    <td colspan="3" >
     <form action=""  method="POST" enctype="multipart/form-data">
       <p><strong>Birthdate or Start date for <span class="StepTitle">
         <?=$_SESSION['CurrentEntityName'] ?>
         </span>:</strong><br>
         <i>Leave blank if unknown. Format: MM/DD/YYYY or YYYY</i><br />
         <input type="text" name="EntityBirthDate" size="10" value="<?=$EntityBirthDate?>" />
         <br /><br />
         
         <strong>Deathdate or End date:</strong><br>
         <i>Leave blank if not applicable. Format: MM/DD/YYYY or YYYY</i> <br />
         <input type="text" name="EntityDeathDate" size="10" value="<?=$EntityDeathDate?>" />
         <br />
         <br /><br />
         
         <strong>Biographical-Portrait Image</strong> (if available):<br>
         <i>To choose a file to upload, click the 'Choose File' button on the right. You must have permission to do so from the copyright owner. </i>
         <input type='file' name='EntityBioImageFile' id="EntityBioImageFile" >
         <br />
         OR URL: 
         <input type="text" name="EntityBioImageSourceURL" size="100" value="" />
         <br />
         <br />
         <br />
         <strong>Biographical-Portrait Caption</strong> (<i>If applicable</i>)<br />
         
         <textarea cols='100' rows='4' name="EntityBioImageCaption" colspan ='2' 
       placeholder="This information could include photographer name, date taken, location, and the names of anyone else in the photo. Limit 500 characters.">
    </textarea>
         <br />
         <br />
         
         <strong>Biography or Descriptive Text</strong><br>
         <textarea cols='100' rows='10' name="EntityBioDescription" 
       placeholder="Enter information about  <?=$_SESSION['CurrentEntityName'] ?>. You may list any sources (if applicable) below the main body of text. You will be cited as the author of this entry. Limit 10,000 characters">
    </textarea>
         <br />
         <br />
       </p>
       <p><strong>Optional Image</strong> to be used as thumbnail representation for the top-level Musician Collection:<br />
         <i>To choose a file to upload, click the 'Choose File' button on the right. You must have permission to do so from the copyright owner. <br />
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;300px W&nbsp; &nbsp;X &nbsp;&nbsp;110px H<br />
         </i>
         <input type='file' name='EntityThumbImageForGalleryMarquis' id="EntityThumbImageForGalleryMarquis" />
         <br />
         OR URL:
         <input type="text" name="EntityThumbURLForGalleryMarquis" size="100" value="" />
         <br />
         <br />
         <br />
         <input type="submit" name="Command" value="Submit Bio Info" /> 
         <-- Save above descriptive information for  	<strong>
          <?=$_SESSION['CurrentEntityName'] ?>
           </strong>
         <br />
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:grey"><em>To edit or delete, please contact a JHDB administrator</em></span></p>
     </form>
    </td>
  </tr>
  <tr>
  <td colspan="3" >
    
    </td>
  </tr>
  <tr>
    <td colspan="3" >&nbsp;</td>
  </tr>
  </table>
</div>
<? //} // End IF for displaying Step 1b  ?>
  
  
  
  
  
  
<? //-------------------------------  Step 2a Galleries (It will probably end up being easier to just ignore galleries, we can automattically generate a master gallery when an artist is made, and automattically select that gallery for the sake of the rest of the website functioning  -------------------------  ?>

<? //if ($_SESSION['CurrentEntityID']>0){ ?> 
<div class="tab3">
<table>
  <tr>
    <td height="49" colspan="3"  class="StepTitle"><a name="NewGallery" id="NewGallery"></a>Step 2a - Add a New Gallery (Collection) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;( &nbsp;<span style="color:red;font-size:110%;">OR</span>&nbsp; go to Step 3 for uploading)<br /></td>
  </tr>

  <tr>
    <td colspan="3" >A <strong>Gallery</strong> is a "collection" of Museum Items (Pictures, MP3s, etc.) for this Musician/Band/Venue<br />
Please enter a <strong>Gallery Title</strong>, which serves as a Label/Title for this Collection or Grouping<br />
and a <strong>Type of Gallery</strong> and a <strong>Gallery Summary</strong> (sentence/phrase/short paragraph) of what this Gallery Contains.<br /><br />
</td>
  </tr>
  <tr>
   <td colspan="3" >
   <form action=""  method="POST">
   
        <p>
          <? if ($_SESSION['CurrentGalleryID']>0)   
	           {
			?>
          Edit Existing Gallery Title:
          <? }else{?>
          Enter <strong>New Gallery Title</strong>:
          <? }?>
          <br />      
          
          <input type="text" name="NewGalleryTitle" size="30" value="<?=$_SESSION['CurrentGalleryTitle']?>" />
          <br />
          <br />
          
          
          <? if (!$_SESSION['CurrentGalleryID']>0)   
	{?>
          <strong>Type of New Gallery</strong>: &nbsp;(*)<br />
          <select name="GalleryType">
            <option value="SubGallery" selected="selected">Sub Gallery for <?=$_SESSION['CurrentEntityName']?> </option>
            <!--option value="MusicianBandMainGallery">Top-level/Primary Musician/Band Collection for <?=$_SESSION['CurrentEntityName']?> </option>
            <option value="BaseGallery">Base Collections (for JHDB Administrator Usage Only)</option-->
          </select> 
         <br />
<span style="font-size:75%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(*) 
         A <strong>SubGallery</strong> is used to group items together, such as Images,  Audio MP3 files, YouTube Videos</span><br />
          <br />
          Contents Type for New Gallery:<br />
<? display_dropdown ( /*HTMLID*/'GIContentTypeID', /*Table*/'tblGIContentTypes', /*InputName*/'GIContentTypeID', /*OnChangeSubmit*/false, /*CheckOnline*/false, /*DisplayCurrent*/false, /*CurrentID*/'', /*QueryTable*/'', /*QueryWhere*/'AND ID<8 AND ID>1 AND  ID!=2*ROUND(ID/2)' /*odd   might just us  'ID IN (3,5,7)' */, /*OrderBy */'ID',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'Select Content Type', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
 /*DisplayListField*/'Name', /*ValueField*/'ID', /*HideIfNone*/false, /*Anchor*/'', /*$FormTags*/false, /*OptionValueForUnselected*/ -1 ); ?>
<br /><br />

			Usage Type (context):<br />
<? display_dropdown ( /*HTMLID*/'GalleryUsageTypeID', /*Table*/'tblGalleryUsageTypes', /*InputName*/'GalleryUsageTypeID', /*OnChangeSubmit*/false, /*CheckOnline*/false, /*DisplayCurrent*/false, /*CurrentID*/'', /*QueryTable*/'', /*QueryWhere*/'AND ID<=10' /*odd   might just us  'ID IN (3,5,7)' */, /*OrderBy */'ID',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'Select Usage Type', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
 /*DisplayListField*/'Name', /*ValueField*/'ID', /*HideIfNone*/false, /*Anchor*/'', /*$FormTags*/false, /*OptionValueForUnselected*/ -1 ); ?>
          <br />


        </p>
        <table width="505" border="0">
          <tr>
            <td width="134" align="right"><strong>Gallery Summary<br />
             </strong> <span style="font-size:80%;">(optional)</span></td>
            <td width="361"><textarea name="GallerySummary" cols="80" rows="3"></textarea></td>
          </tr>
        </table>
    <br />

    <? }// end !CurrentGalleryID ?>     
	
    <input type="submit" name="Command" value="Submit Gallery Info" />
    
&lt;-- Click to Submit the above New Gallery Information
   <br />
   <br />
   </form>
   </td>
 </tr>
</table>
</div>
 <? //}// end EntityID ?>
 
 
 
 
 
 
 
 
<? //-------------------------------  Step 2b Images  -------------------------  ?>

<? //if ($_SESSION['CurrentEntityID']>0 
//			AND   0 != db_sfq("SELECT COUNT(ID) FROM tblGalleries WHERE EntityID={$_SESSION['CurrentEntityID']}"))
//{ ?>
<div class="tab4">
<table>
<tr>
    <td align="right">Image File Selection for Upload Into <br />
<?=$_SESSION['CurrentEntityName']?>'s Image SubGallery: <?=$_SESSION['CurrentGalleryTitle']?><br />
</td>
    <td></td>
    <td></td>
  </tr>
  <tr><td colspan="3">
  
<form action=""  method="POST" enctype="multipart/form-data">
<table>
  <tr>
    <td align="right"><strong>Caption</strong><br /> (HTML permitted)</td><td></td>
    <td><textarea name="ImageCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <tr>
    <td align="right"><strong>Upload Image File</strong> (JPEG format)</td><td></td>
    <td><input type="file" name="ImageFile" size="60" /> <-- file to upload<br />
      OR URL:
      <input type="text" name="ImageFileURL" size="100" value="" id="ImageFileURL" /></td>
  </tr>
  <tr>
    <td align="right">Upload Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit Image for Upload"/>
    </td>
  </tr>
  </table>
</form>
</td></tr>

</table>
<!--<table>
 <tr>
    <td height="49" colspan="3"  class="StepTitle"><a name="SelectGallery" id="SelectGallery"></a>Step 2b - Select an Existing Gallery for Uploading</td>
  </tr>
  <tr>
     <td colspan="3" valign="top"> 
       <? if (db_sfq("SELECT COUNT(ID) FROM tblGalleries WHERE EntityID='{$_SESSION['CurrentEntityID']}' ") ==0) {?>
       <em>(No Galleries for this Musician/Group Exist Yet)</em>
       <?
	 } else 
	   { if ($_SESSION['CurrentGalleryID']>0) {?>
       <em>Current Gallery Selected for Uploading in Step 3: <?=$_SESSION['CurrentGalleryTitle']?><br />
       <span style="color:grey;">(To change Galleries or add a new Gallery Title, Select from below dropdown list )</span></em>
       <? }else{?>
       <em>Select Existing Gallery</em>
       <? }}?><br />
  <? display_dropdown ( /*HTMLID*/'GalleryID', /*Table*/'tblGalleries', /*InputName*/'GalleryID', /*OnChangeSubmit*/true, /*CheckOnline*/false, /*DisplayCurrent*/true, /*CurrentID*/$_SESSION['CurrentGalleryID'], /*QueryTable*/'', /*QueryWhere*/" AND EntityID='{$_SESSION['CurrentEntityID']}'",
 /*OrderBy */'Title',  /*Repeater*/'', /*UnselectedDefaultTextLabel*/'Click to Select Existing Gallery/Collection, or Enter New Gallery Name in Step 2a ', /*OnlyOneFlag*/false/*don't use with OnChange*/, /*JSParams*/'', /*LimitLabelChars */ 60, /*DisplayPrefixID*/'ID', /*ComboText1A*/'', /*ComboQuery1*/'', /*ComboText1C*/'', /*DebugFlag*/false,
  /*DisplayListField*/'Title', /*ValueField*/'ID', /*HideIfNone*/true, /*Anchor*/'', /*$FormTags*/true, /*OptionValueForUnselected*/ -1 ); ?>
<br />
<span style="color:grey;font-style:italic">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(To Add a New Gallery, Choose Enter New Gallery Name at the top of the above dropdown menu list)</span>
</td>
  </tr>
  <tr>
    <td colspan="3"><br />
    <span style="font-size:75%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Show/Hide Current  Contents (GalleryItems) for this Selected Gallery <a href="<?="{$GalleryDisplayBaseURL}/dump.php?d=1&g={$_SESSION['CurrentGalleryID']}" ?>" target="db_details"><?=($_SESSION['CurrentGalleryTitle'])?></a> (opens in new window)</span></td>
  </tr>
</table>-->
</div>
 <? //}// end EntityID>0  for step 2b ?>
 
 
 
 
 
 <? //-------------------------------  Step 4 Audio  -------------------------  ?>

<? //if ($_SESSION['CurrentEntityID']>0 AND $_SESSION['CurrentGalleryID']>0)
//{ 
	$CurrentUploadType = $_SESSION['CurrentUploadType'];
	if (substr($_SESSION['ThisCommand'],0,14)=="Select Gallery")
	{// pre determine the type of gallery and initialize the upload type
		list($GalleryUsageTypeID, $GIContentTypeID) = db_sfq("SELECT GalleryUsageTypeID, GIContentTypeID FROM tblGalleries WHERE ID = '{$_SESSION['CurrentGalleryID']}' ");
	}

?> 
 <!--tr>
   <td colspan="3" >&nbsp; <?="$Command;$ThisCommand;$LastCommand;GalID:{$_SESSION['CurrentGalleryID']};UsageType:$GalleryUsageTypeID; GIContentTypeID=$GIContentTypeID;"?></td>
 </tr-->
<div class="tab5">
<table>
<tr>
    <td  colspan="3">MP3 File Selection for Upload
    <form action=""  method="POST" enctype="multipart/form-data">
<table>
  <tr>
    <td align="right"><strong>Caption</strong><br /> (HTML permitted)</td><td></td>
    <td><textarea name="AudioCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <tr>
    <td align="right"><strong>Upload Audio File</strong> (MP3 format)</td><td></td>
    <td><input type="file" name="AudioFile" size="60" /> <-- file to upload<br />
      OR URL:
      <input type="text" name="AudioFileURL" size="100" value="" id="AudioFileURL" /></td>
  </tr>
  <tr>
    <td align="right">Upload Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit MP3 File for Upload"/>
    </td>
  </tr>
  </table>
</form>

</table>
<!--<table>
 <tr>
    <td height="33" colspan="3" class="StepTitle"><a name="Uploads" id="Uploads"></a>Step 3 - Upload/Add an Item into 
      <?=$_SESSION['CurrentEntityName'] ?>
    Gallery: &quot;<?=$_SESSION['CurrentGalleryTitle']?>&quot;</td>
  </tr>
  <tr>
    <td> <!-- some options below are not really "uploads",  All are types of GalleryItems -->
    <form id='UploadType' name='UploadType' method='post' action='' >
    <select name='UploadType'  OnChange='this.form.submit();' >
        <option value='' >Select Type of Upload for New GalleryItem</option>
        <option value='HTML' 		<?= ($CurrentUploadType=="HTML")		?"Selected=Selected":"" ?> >Paste HTML</option>
        <option value='PlainText' 	<?= ($CurrentUploadType=="PlainText")	?"Selected=Selected":"" ?> >Paste Plain Text</option>
        <option value='Image' 		<?= ($CurrentUploadType=="Image")		?"Selected=Selected":"" ?> >Image File</option>
        <option value='MP3' 		<?= ($CurrentUploadType=="MP3")			?"Selected=Selected":"" ?> >MP3 File</option>
        <option value='YouTubeURL' 	<?= ($CurrentUploadType=="YouTubeURL")	?"Selected=Selected":"" ?> >YouTube Video URL</option>
        <option value='MiscURL' 	<?= ($CurrentUploadType=="MiscURL")		?"Selected=Selected":"" ?> >URL</option>
        <option value='SubGallery' 	<?= ($CurrentUploadType=="SubGallery")	?"Selected=Selected":"" ?> >SubGallery</option>
	</select>  </form>
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
 <? //} ?> 
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


  <tr>
    <td align="right">Image File Selection for Upload Into <br />
<?=$_SESSION['CurrentEntityName']?>'s Image SubGallery: <?=$_SESSION['CurrentGalleryTitle']?><br />
</td>
    <td></td>
    <td></td>
  </tr>
  <tr><td colspan="3">
  
<form action=""  method="POST" enctype="multipart/form-data">
<table>
  <tr>
    <td align="right"><strong>Caption</strong><br /> (HTML permitted)</td><td></td>
    <td><textarea name="ImageCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <tr>
    <td align="right"><strong>Upload Image File</strong> (JPEG format)</td><td></td>
    <td><input type="file" name="ImageFile" size="60" /> <-- file to upload<br />
      OR URL:
      <input type="text" name="ImageFileURL" size="100" value="" id="ImageFileURL" /></td>
  </tr>
  <tr>
    <td align="right">Upload Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit Image for Upload"/>
    </td>
  </tr>
  </table>
</form>
</td></tr>
<? break;
   case "MP3":
?> 
  <tr>
    <td  colspan="3">MP3 File Selection for Upload
    <form action=""  method="POST" enctype="multipart/form-data">
<table>
  <tr>
    <td align="right"><strong>Caption</strong><br /> (HTML permitted)</td><td></td>
    <td><textarea name="AudioCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <tr>
    <td align="right"><strong>Upload Audio File</strong> (MP3 format)</td><td></td>
    <td><input type="file" name="AudioFile" size="60" /> <-- file to upload<br />
      OR URL:
      <input type="text" name="AudioFileURL" size="100" value="" id="AudioFileURL" /></td>
  </tr>
  <tr>
    <td align="right">Upload Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit MP3 File for Upload"/>
    </td>
  </tr>
  </table>
</form>
    
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

	  <br /></td>
  	</tr>
    <tr>
    <td align="right">SubGallery Title to Display</td>
    <td></td>
    <td><input type="text" name="SubGalleryPageTitle" size="60" id="SubGalleryPageTitle" /></td>
 	 </tr>
    <tr>
    <td align="right">Caption</td><td></td>
    <td><textarea name="SubGalleryCaption" cols="60" rows="3"></textarea>
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
  <form action=""  method="POST" enctype="multipart/form-data">
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
    <td><textarea name="YouTubeURLCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <!--   UNNEEDED in current display template technique   tr>
    <td align="right">Upload Thumbnail Image File</td><td></td>
    <td><input type="file" name="YouTubeThumbImage" size="60" id="YouTubeThumbImage" />
      <br />
      OR URL:
      <input type="text" name="YouTubeThumbImageURL" size="100" value="" id="YouTubeThumbImageURL" />    </td>
  </tr -->
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
</table>-->
</div>
<div class="tab6">

<? //---------------------------------------------------- Video ------------------------------------------------------- ?>

<form action=""  method="POST" enctype="multipart/form-data">
<table>
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
    <td><textarea name="YouTubeURLCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <!--   UNNEEDED in current display template technique   tr>
    <td align="right">Upload Thumbnail Image File</td><td></td>
    <td><input type="file" name="YouTubeThumbImage" size="60" id="YouTubeThumbImage" />
      <br />
      OR URL:
      <input type="text" name="YouTubeThumbImageURL" size="100" value="" id="YouTubeThumbImageURL" />    </td>
  </tr -->
  <tr>
    <td align="right">Submit Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit YouTube Info"/>
    </td>
  </tr>
</table>
  </form>

</div>


</div>
<!-- <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /> -->
<? if ($ErrorOverrideScrollToAnchor) scrollTo($ErrorOverrideScrollToAnchor);  // if error was thrown somewhere, use this one
	elseif ($ScrollToAnchor) scrollTo($ScrollToAnchor); 
?>

</body>
</html>
