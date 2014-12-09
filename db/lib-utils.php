<?php
// utilities for JHDB contribution/upload


function expand_to_URL($ThisURL)
{ // make URL by adding Base URL if needed   (db stores relative path assumed)
	if (substr($ThisURL, 0, 7)=="http://")  return $ThisURL;    //  fully qualified URL already, don't mess with it
	if ($ThisURL[0]=="/") return  SiteBaseURL.substr($ThisURL,1); // drop leading / 
	return EntityContentBaseURL.$ThisURL;    // relative to Entity directory
}

function log_upload($Query)
{ //  log contributors' uploads for future debugging or hacking
	global $debug_log;
	$LogString	= $Query;
	if ($debug_log>0) $LogString .= "|".implode(",", $_POST);
	
	db_sql("INSERT INTO tblUploadLogs SET
			ContributorID ='',
			UploadDate		= NOW(),
			IP				= '{$_SERVER['REMOTE_ADDR']}',
			Log				= '".addslashes($LogString)."'
			");
	
} // end log_upload 


function get_EntityFullName_from_GIID($GIID)
{ // assemble human name (musician, etc.) from tracing GIID to Entiry record
	$ParentGalleryID 	= db_sfq("SELECT ParentGalleryID FROM tblGalleryItems WHERE ID='{$GIID}'");
	$EntityID			= db_sfq("SELECT EntityID FROM tblGalleries WHERE ID = '{$ParentGalleryID}'");
	$FullName			= db_sfq("SELECT CONCAT(FName,' ',LName) FROM tblEntities WHERE ID = '{$EntityID}'");
	return $FullName;
}// end get_EntityFullName_from_GIID

 
function get_EntityPrimaryGIID_from_GIID($GIID)
{ // trace thru tables to get Entity PrimaryGIID
	$ParentGalleryID 	= db_sfq("SELECT ParentGalleryID FROM tblGalleryItems WHERE ID='{$GIID}'");
	$EntityID			= db_sfq("SELECT EntityID FROM tblGalleries WHERE ID = '{$ParentGalleryID}'");
	$PrimaryGIID		= db_sfq("SELECT PrimaryGIID FROM tblEntities WHERE ID = '{$EntityID}'");
//echo "$ParentGalleryID ; $EntityID ; $PrimaryGIID ; <br>\n";
	return $PrimaryGIID;
}// end get_EntityFullName_from_GIID

 
function convert_field_to_sql_date($Date)
{  //  null, YYYY, mm-dd-yyyy, mm/dd/yyyy    other formats MAY be converted properly, this is not the definitive conversion utility.
	$Date = trim($Date);
	$Date = str_replace(" ","",$Date);
//echo "1Date=$Date; strlen=".strlen($Date)."<br>\n";
	if (strlen($Date)==0) return "0000-00-00";  // was null
	if (strlen($Date)==4) return "{$Date}-00-00"; // assume all digits

//echo "2Date=$Date;<br>\n";
	if (strlen($Date)!=10) echo"Warning '$Date' needs to be in the format dd/mm/yyyy<br>";
	$Date = date('Y-m-d', strtotime(str_replace('-', '/', $Date)));

//echo "3Date=$Date;<br>\n";
	return $Date;
}// end convert_field_to_sql_date


function MoveToCurrentEntityDirectory($SubDir, $PostName/* file path or URL */, $CopyRename='rename', $Suffix='', $MaxH='', $MaxW='')
{ // uploaded image or URL image   file is placed in the Entity's path   image/directory-- returns target URL
echo "<br> MoveToCurrentEntityDirectory($SubDir, $PostName, $CopyRename, $Suffix, $MaxH, $MaxW)<br>\n";
	
	if($CopyRename!='URL' AND $_FILES[$PostName]['error'] != 0) 
		die("Error in uploading file. <a href='http://php.net/manual/en/features.file-upload.errors.php'>{$_FILES[$PostName]['error']}</a> [lib-utils]");
	if (!$_SESSION['CurrentEntityID']) die("<strong>Internal Error: Entity (musician) ID is not set, cannot proceed with File upload operation</strong> [lib-utils]");
	$EntityDirectoryPath 	= db_sfq("SELECT DirectoryPath FROM tblEntities WHERE ID ={$_SESSION['CurrentEntityID']} ");
	$TargetDirPath			= EntityContentPHPBasePath."musicians/{$EntityDirectoryPath}/{$SubDir}/";
	if (!file_exists($TargetDirPath)) 
	{
		mkdir($TargetDirPath, 0777); chmod($TargetDirPath, 0777); // directory is there		
		//chown($TargetDirPath, ServerAccountUsername);
	}  
	
	$SourceFileName			= strtolower($_FILES[$PostName]['name']);
	if ($CopyRename=='URL') $SourceFileName = array_shift(explode('?', basename($PostName/*URL*/)));

// next 12 or so lines are to avoid duplicate filenames
	$TempArray 				= explode(".", $SourceFileName);
	$FileExtension 			= end($TempArray);
	if (substr_count($SourceFileName,".")!=1) echo "Warning: image filename {$SourceFileName} does not contain exactly one period.<br />\n";
	$FileNameNoExt			= $TempArray[0];   //  ASSUMPTION: no extra periods in filename
	$TargetPathAndName		= $TargetDirPath.$FileNameNoExt.$Suffix.".".$FileExtension;  // $Suffix is for possible _t thumbnail designation
	$Count = 0; 
	$Filename	= $SourceFileName;  // init for the likely case that it doesn't already exist and does not need _2  suffix
	while (file_exists($TargetPathAndName)) 
	{
		$Count++;   
		$TargetPathAndName 	= $TargetDirPath.($FileName=$FileNameNoExt.$Suffix."_".$Count.".".$FileExtension);
	}// end while
if (file_exists($TargetPathAndName)) echo"Internal error: file still/already exists $TargetPathAndName 	 [lib-utils]<br>\n";
	$TargetURL				= "{$EntityContentBaseURL}musicians/{$EntityDirectoryPath}/{$SubDir}/{$Filename}";
	$SourceTempFile			= $_FILES[$PostName]['tmp_name'];
echo " 	$EntityDirectoryPath; $TargetPathAndName; $TargetURL;	$SourceTempFile;";

	if ($CopyRename=='rename')
		$Result = rename($SourceTempFile, $TargetPathAndName);
	  elseif ($CopyRename=='copy')  // COPY
	  	$Result = copy($SourceTempFile, $TargetPathAndName);
	  elseif ($CopyRename=='URL')
	  	$Result = copy($PostName/*URL*/, $TargetPathAndName);
	if (!$Result) 
	{
		die ("<strong>Internal Error  </strong> [lib-utils] for uploaded file {$CopyRename} from temp directory (temp dir perm=".sprintf('%o', fileperms($SourceTempFile))."; dir owner=".fileowner($SourceTempFile).") to musician's directory(dir perm=".sprintf('%o', fileperms($TargetDirPath))."; dir owner=".fileowner($TargetDirPath)."): <br /><strong>{$CopyRename}</strong>({$SourceTempFile}{$PostName}, {$TargetPath}{$Filename}) <br />\n");
		
	}
	 chmod($TargetPathAndName, 0777);
	 //chown($TargetPathAndName, ServerAccountUsername);
	 
	 // only valid for images!  ($MaxH is null for MP3s)
	 if ($MaxH) // use H as test, W would be there too    -- not applicable for other than IMAGES
	 { // this is done at end where the file can be re written (because if URL, cannot re write URL source)
		 resize_image('max', $TargetPathAndName, $TargetPathAndName, $MaxH, $MaxW);
	 }
	 
	//return array($TargetURL, $TargetPathAndName);  // turns out path never needed
	return $TargetURL;
} // end MoveToCurrentEntityDirectory


function get_default_upload_type_from_GalleryID( $GalleryID )
{  // Translate upload enumerated type ID
	$GIContentTypeID 	= db_sfq("SELECT GIContentTypeID FROM tblGalleries WHERE ID={$GalleryID}");
	
	switch ($GIContentTypeID)
	{
	  CASE GIContentType_SubGallery:		$UploadType 	= "SubGallery"; break;
	  CASE GIContentType_Image:				$UploadType 	= "Image";		 break;
	  CASE GIContentType_ImageWithPlayer:	$UploadType 	= "Image";		 break;
	  CASE GIContentType_Audio:				$UploadType 	= "MP3";		 break;
	  CASE GIContentType_AudioWithPlayer:	$UploadType 	= "MP3";		 break;
	  CASE ContentType_Video:				$UploadType 	= "YouTubeURL";	 break;
	  CASE GIContentType_VideoEmbedYouTube:	$UploadType 	= "YouTubeURL";	 break;
	  CASE GIContentType_ContentHTML:		$UploadType 	= "HTML";		 break;
	  CASE GIContentType_ContentText:		$UploadType 	= "PlainText";	 break;
	  CASE GIContentType_RemoteURL:			$UploadType 	= "MiscURL";	 break;
	  CASE GIContentType_JHDBURL:			$UploadType 	= "MiscURL";	 break;
	  CASE GIContentType_JHDBContentFile:	$UploadType 	= "MiscURL";	 break;
	  
	  default: 								$UploadType 	= "";  			 break;
	}// end switch
	
//echo "<br> get_default_upload_type_from_GalleryID( $GalleryID ) = $GIContentTypeID --> $UploadType <br>";
		return $UploadType;
	
}// end get_default_upload_type_from_GalleryID




function form_directory_name_and_mkdir( $EntityFName, $EntityLName, $EntityCategoryPath="musicians" )
{	//------------------ form (unique) dir name (from "LName_FName")  and make new directory  for /contents/musicians/...  ------------------------------

// defaults to musician Entities for now,  however called with "events" or "media" or "collections"  could construct name and directory for those too if ever implemented

// NO provision for odd characters
	$EntityDirectoryName 	= $EntityLName;
	if ($EntityFName) $EntityDirectoryName .= "_".$EntityFName;
	$EntityDirectoryName 	= stripslashes($EntityDirectoryName);
	$EntityDirectoryName 	= str_replace("'","-", $EntityDirectoryName);
	$EntityDirectoryName 	= str_replace("‘","-", $EntityDirectoryName);	
	$EntityDirectoryName 	= str_replace("`","-", $EntityDirectoryName);	
	$EntityDirectoryName 	= str_replace("’","-", $EntityDirectoryName);	
	$EntityDirectoryName 	= str_replace(" ","", $EntityDirectoryName);	
	$EntityDirectoryName 	= strtolower($EntityDirectoryName);
	$DirAppendCount = 2; $DirAppend = "";

	if (file_exists(EntityContentPHPBasePath."{$EntityCategoryPath}/{$EntityDirectoryName}"))   // dir already exists 
	{
		while (file_exists(EntityContentPHPBasePath."{$EntityCategoryPath}/{$EntityDirectoryName}_{$DirAppendCount}"))  $DirAppend = "_".$FileAppendCount++;
	}
	$EntityDirectoryName 	.= $DirAppend;  //$Dir_Append is either null or remains with the last tested non-existent filename_N
	$EntityDirectoryPHPPath = EntityContentPHPBasePath."{$EntityCategoryPath}/{$EntityDirectoryName}";   // full path
	$EntityDirectoryURL	 	= EntityContentBaseURL."{$EntityCategoryPath}/{$EntityDirectoryName}";  // full path
echo "<br>attempting: mkdir($EntityDirectoryPHPPath); <br>\n";
	if (!file_exists($EntityDirectoryPHPPath))
		{mkdir($EntityDirectoryPHPPath, 0777); chmod($EntityDirectoryPHPPath, 0777); /*chown( $EntityDirectoryPHPPath, ServerAccountUsername);*/}// create the musician's content directory

	return array( $EntityDirectoryName,  $EntityDirectoryPHPPath, $EntityDirectoryURL );
}// end function form_directory_name_and_mkdir




function create_main_musician_GI_and_gallery($CurrentEntityID) 
{   // creates new Gallery Record and sets SESSION variables to "select gallery"

	// This fn Depends on SESSION vars to be correct (for this new Entity), rather than looking up in tblEntities
	list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('MainCollectionGalleryItem');	
	$NextGalleryItemID				= find_first_free_ID( "tblGalleryItems", $RangeMin, $RangeMax);
	list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('MusicianBandMainGallery');	
	$NextGalleryID					= find_first_free_ID( "tblGalleries", $RangeMin, $RangeMax);
	$EntityName	= (strlen($_SESSION['CurrentEntityFName'])>0? "{$_SESSION['CurrentEntityFName']} ":"").  /* FName may be Null */
						"{$_SESSION['CurrentEntityLName']}";
			
	db_sql($Q1="INSERT INTO tblGalleries SET 
				ID				= '{$NextGalleryID}',
				GIContentTypeID = ".GIContentType_SubGallery.",
				GalleryUsageTypeID = '99',
				EntityID		= '{$CurrentEntityID}', 
				Title			= '{$EntityName} Collection',
				ContributorID	= '{$_SESSION['CurrentContributorID']}',
				Online			= 1
			");
	$ThisGalleryID = db_mysqli_insert_id();	
		
	db_sql($Q2="INSERT INTO tblGalleryItems SET
				ID 				= '{$NextGalleryItemID}',
				ParentGalleryID	= '2', /*Top-level MUSICIANS COLLECTION*/
				SubGalleryID	= '{$ThisGalleryID}',
				GIContentTypeID	= ".GIContentType_SubGallery.",
				PageTitle		= UPPER('{$EntityName} COLLECTION'),
				MenuTitle		= '{$EntityName} Collection',
				Online			= 1					
			  ");
	$ThisGalleryItemID = db_mysqli_insert_id();
	
	log_upload( " create_main_musician_GI_and_gallery($CurrentEntityID); $Q1; $Q2;");
	
	echo "<br>Created '{$EntityName}' top-level collection Gallery[{$ThisGalleryID}] (within the top-level JHDB MUSICIANS Collection) with corresponding GalleryItemID[{$ThisGalleryItemID}]<br>";
	
	
	
	// SET SESSION VARIABLES
	//   none???
	
	return array($ThisGalleryItemID, $ThisGalleryID);  // primary Entity GalleryItemID, GalleryID
	
} // end create_main_musician_GI_and_gallery
				
				// --------------------  If Musician, 
				
function create_default_musician_GI_and_subgalleries($MusicianMainGalleryID)  //Create  standard subgalleries underneath "main" MusicianMainGalleryID
{
	// Create GalleryItems within MusicianMainGalleryID  for: Bio, Images, Audio, Video
	//   and then SubGalleries for Images and Audio and Video
	//  Deal with server directories (existence, creation, permissions) at upload time (back in main code)
	
	// This fn Depends on SESSION vars to be correct (for this new Entity), rather than looking up in tblEntities


echo "<br> create_default_musician_GI_and_subgalleries($MusicianMainGalleryID) <br>";


//--------------------------------- GALLERY ITEMS FIRST    then   SUBGALLERIES  (for each) --------------------------------------  
  	list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('GalleryItem');	
	$NextGalleryItemID				= find_first_free_ID( "tblGalleryItems", $RangeMin, $RangeMax);
  	list($SGRangeMin, $SGRangeMax) 	= get_ID_range_for_GalleryType('SubGallery');	
	$NextGalleryID					= find_first_free_ID( "tblGalleries", $SGRangeMin, $SGRangeMax);
	
	$EntityName	= (strlen($_SESSION['CurrentEntityFName'])>0? "{$_SESSION['CurrentEntityFName']} ":"").  /* FName may be Null */
						"{$_SESSION['CurrentEntityLName']}";
	
	
	// DEFAULT BIO/DESCRIPTION LEAFNODE PAGE
      if (isset($_POST['CreateBioItem']))   // Bio is only a GalleryItem (no child SubGallery)
	  {
		  db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					GIContentTypeID	= '".GIContentType_JHDBContentFile."',
					PageTitle		= 'Bio for {$EntityName}',
					MenuTitle		= 'Biography',
					URL				= '".PathToBioComingSoonContent."',
					Online			= 1,
					Sort			= 100					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
			db_sql("UPDATE tblEntities SET BioDescrGIID='{$ThisGalleryItemID}' WHERE ID = '{$_SESSION['CurrentEntityID']}' ");

			echo "<br>Created Biography GalleryItem[{$ThisGalleryItemID}] (within the top-level Musician Gallery) - please fill in details in subsequent steps<br>";
			$NextGalleryItemID++;  // incr for next Gallery Item so we don't have to re calculate (not that it's huge CPU usage)
			// No subgallery for Bio
	  } // end	 CreateBioItem  



	  
	  if (isset($_POST['CreateDescrItem']))   // only Bio OR Descr   (both at once not needed)(if needed, remove IF and warning, should work)
	  {
		if (isset($_POST['CreateBioItem'])) echo "Warning: CreateDescrItem <strong>Description</strong> ignored, since you also requested <strong>Biography</strong>. Contact JHDB admins to create additional Gallery Items (for Descriptions, etc.)<br>";
		else
		{ // continue CreateDescrItem
		  db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					GIContentTypeID	= ".GIContentType_JHDBContentFile /* leaf node */.",
					PageTitle		= 'Description of {$EntityName}',
					MenuTitle		= 'Description',
					URL				= '".PathToBioComingSoonContent."',
					Online			= 1,
					Sort			= 200					
				  ");	
			$ThisGalleryItemID = db_mysqli_insert_id();
			
			db_sql("UPDATE tblEntities SET BioDescrGIID='{$ThisGalleryItemID}' WHERE ID = '{$_SESSION['CurrentEntityLName']}' ");
			
			echo "<br>Created Description GalleryItem[{$ThisGalleryItemID}] (within the top-level Musician/Group Gallery) - please fill in details in subsequent steps<br>";	
	
			$NextGalleryItemID++;  // incr for next Gallery Item
			// No subgallery for Descr
		} // end CreateDescrItem
	  } // end	 CreateBioItem    
	  
	  
	  
	  
	  // DEFAULT PHOTO GALLERY
      if (isset($_POST['CreateImageGallery']))  // GalleryItem AND (empty) SubGallery
	  {
		   			// Create corresponding Image SubGallery                    
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery /* of GI Images */.",
					GalleryUsageTypeID = '2',
					Title			= '{$_POST['ImageGalleryName']}',
					Summary			= '{$_POST['ImageGalleryName']}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 300					
				  ");
			$ThisSubGalleryID = db_mysqli_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					PageTitle		= '{$_POST['ImageGalleryName']}',
					MenuTitle		= '{$_POST['ImageGalleryName']}',
					Online			= 1,
					Sort			= 300					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
			echo "<br>Created Photo GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			
			echo "<br>Created Image SubGallery[{$ThisSubGalleryID}]  (image collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
			
	  } // end	CreateImageGallery 
	  
	  
	  
	  // ADDITIONAL IMAGE GALLERY  CreateAdditionalPhotoGallery1
	  if (isset($_POST['CreateAdditionalPhotoGallery1']))  // GalleryItem AND (empty) SubGallery
	  {
		   			// Create corresponding Image SubGallery                    
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					GalleryUsageTypeID = '2',
					Title			= '{$_POST['CreateAdditionalPhotoGallery1Name']}',
					Summary			= '{$_POST['CreateAdditionalPhotoGallery1Name']}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 300					
				  ");
			$ThisSubGalleryID = db_mysqli_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					PageTitle		= '{$_POST['CreateAdditionalPhotoGallery1Name']}',
					MenuTitle		= '{$_POST['CreateAdditionalPhotoGallery1Name']}',
					Online			= 1,
					Sort			= 300					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
//echo "<br>Created {$_POST['CreateAdditionalPhotoGallery1Name']} Photo GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			
echo "<br>Created {$_POST['CreateAdditionalPhotoGallery1Name']} Image SubGallery[{$ThisSubGalleryID}]  (image collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
	  } // end	CreateImageGallery 
	  
	  
	  
	  
	  if (isset($_POST['CreateAudioGallery']))
	  {
		// Create corresponding Audio SubGallery  <-- default normal audio gallery                    
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery /* of MP3s */.",
					GalleryUsageTypeID = '4' /* guessing MP3s are music samples */,
					Title			= '{$_POST['AudioGalleryName']}',
					Summary			= '{$_POST['AudioGalleryName']}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 400					
				  ");
			$ThisSubGalleryID = db_mysqli_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					PageTitle		= '{$_POST['AudioGalleryName']}',
					MenuTitle		= '{$_POST['AudioGalleryName']}',
					Online			= 1,
					Sort			= 400					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
			echo "<br>Created Audio GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			

			echo "<br>Created Audio SubGallery[{$ThisSubGalleryID}]  (MP3 collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
	  } // end	CreateAudioGallery     
	  
	  
	  
	  
	  
	  //CreateAdditionalAudioGallery1    
      if (isset($_POST['CreateAdditionalAudioGallery1']))
	  {
		// Create ADDITIONAL Audio SubGallery                     
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					GalleryUsageTypeID = '5' /* guessing interviews */,
					Title			= '{$_POST['CreateAdditionalAudioGallery1Name']}',
					Summary			= '{$_POST['CreateAdditionalAudioGallery1Name']}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 400					
				  ");
			$ThisSubGalleryID = db_mysqli_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					PageTitle		= '{$_POST['CreateAdditionalAudioGallery1Name']}',
					MenuTitle		= '{$_POST['CreateAdditionalAudioGallery1Name']}',
					Online			= 1,
					Sort			= 400					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
			echo "<br>Created {$_POST['CreateAdditionalAudioGallery1Name']} Audio GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 

echo "<br>Created {$_POST['CreateAdditionalAudioGallery1Name']} Audio SubGallery[{$ThisSubGalleryID}]  (MP3 collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
			
	  } // end	CreateAdditionalAudioGallery     
	  
	  
	  
      if (isset($_POST['CreateVideoGallery']))
	  {
		  // Create corresponding Video SubGallery                     
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					GalleryUsageTypeID = '6',
					Title			= '{$_POST['YouTubeGalleryName']}',
					Summary			= '{$_POST['YouTubeGalleryName']}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 400					
				  ");
			$ThisSubGalleryID = db_mysqli_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					PageTitle		= '{$_POST['YouTubeGalleryName']}',
					MenuTitle		= '{$_POST['YouTubeGalleryName']}',
					Online			= 1,
					Sort			= 400					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
			echo "<br>Created Video GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			

			echo "<br>Created Video SubGallery[{$ThisSubGalleryID}]  (Youtube collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
	  } // end CreateVideoGallery
      
 if (isset($_POST['CreateAdditionalYouTubeGallery1']))
	  {
		// Create ADDITIONAL Video SubGallery                     
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					GalleryUsageTypeID = '6',
					Title			= '{$_POST['CreateAdditionalYouTubeGallery1Name']}',
					Summary			= '{$_POST['CreateAdditionalYouTubeGallery1Name']}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 400					
				  ");
			$ThisSubGalleryID = db_mysqli_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".GIContentType_SubGallery.",
					PageTitle		= '{$_POST['CreateAdditionalYouTubeGallery1Name']}',
					MenuTitle		= '{$_POST['CreateAdditionalYouTubeGallery1Name']}',
					Online			= 1,
					Sort			= 400					
				  ");
			$ThisGalleryItemID = db_mysqli_insert_id();
			echo "<br>Created {$_POST['CreateAdditionalYouTubeGallery1Name']} YouTube GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 

echo "<br>Created {$_POST['CreateAdditionalYouTubeGallery1Name']} YouTube SubGallery[{$ThisSubGalleryID}]  (Video collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
			
	  } // end	AdditionalCreateVideoGallery    
	  
	  
	//  WRONG   DELETE    return $ThisGalleryItemID; // primary GIID for tblEntities    
} // end create_default_musician_GI_and_subgalleries




function get_ID_range_for_GalleryType($GalleryTypeID)
{
	$RangeMin	= RangeMinGalleryItemsID; // defaults
	$RangeMax 	= RangeMaxGalleryItemsID;
  
	switch ($GalleryTypeID)
	{
		case 'BaseGallery': // 
			$RangeMin = RangeMinCollectionGalleryID; // base (top-level) collections created by JHDB admins only (MUSICIANS, MEDIA, EVENTS, etc.)
			$RangeMax = RangeMaxCollectionGalleryID;
		break; // end case BaseGallery
		case 'MusicianBandMainGallery': // 
			$RangeMin = RangeMinEntityGalleryID; 	 // musician/band collections IDs for tblGalleries that are associated with ENTITIES
			$RangeMax = RangeMaxEntityGalleryID;
		break; // end case MusicianBandMainGallery
		case 'SubGallery':	// 
			$RangeMin = RangeMinSubGalleryID; 		 // supporting sub galleries 
			$RangeMax = RangeMaxSubGalleryID;
		break; // end case SubGallery
		case 'GalleryItem':	// 
			$RangeMin = RangeMinGalleryItemsID; 	 // GalleryItem Table
			$RangeMax = RangeMaxGalleryItemsID;
		break; // end case GalleryItem
		case 'MainCollectionGalleryItem':	// 
			$RangeMin = RangeMinEntityGalleryItemsID; 	 // Musician (main collection) GalleryItem Table
			$RangeMax = RangeMaxEntityGalleryItemsID;
		break; // end case MainColelctionGalleryItem
		case 'Entity':	// 
			$RangeMin = RangeMinEntityID; 	 	     // Entity Table
			$RangeMax = RangeMaxEntityID;
		break; // end case Entity
		
	}// end switch GalleryType
	
	return array($RangeMin, $RangeMax );
} // end get_ID_range_for_GalleryType










//===============================================  DATABASE HELPERS  =========================================

function get_subgallery_crumb_menu($ThisGalleryItemID)
{ // if more than one child for the ParentGallery, display in menu.  These are siblings at this level.  (if deeper than the convention of one level down from the Entity's Collection, it will show siblings at that deeper level)
echo "\n<!-- get_subgallery_crumb_menu($ThisGalleryItemID) -->\n";
	$Count 				= 0;
	$Menu 				= "";
	list($ParentGalleryID, $SubGalleryID) 	= db_sfq($Q="SELECT ParentGalleryID, SubGalleryID FROM tblGalleryItems WHERE ID='{$ThisGalleryItemID}' ");
	if ($ParentGalleryID <10) $UseThisGalleryID = $SubGalleryID;  // use children siblings 
	  else $UseThisGalleryID = $ParentGalleryID; // use siblings of *this* GalleryID
	if ($ParentGalleryID ==1) return "";   //top-level collections have no sub crumb menu
	$ChildGIResults 	= db_sql($QQ="SELECT ID FROM tblGalleryItems WHERE ParentGalleryID='{$UseThisGalleryID}' ");
echo "\n<!-- $ParentGalleryID= {$Q} ;  $QQ -->\n";
	if (mysqli_num_rows($ChildGIResults)<=1)  return "";  // if just a single child (no siblings), no need to display itself.
	while ($NavRow = mysqli_fetch_assoc($ChildGIResults))
	{
		if ($Count>0) $Menu .= "&nbsp;&nbsp;-&nbsp;&nbsp;";
		$Menu .= gallery_link_from_GalleryItemID($NavRow['ID'], /*include HTML <a> tag*/true ) ;
		$Count++;
	}// end while
	
	return $Menu;
} // end get_subgallery_crumb_menu



function find_top_level_galleryID($CurrentGIID)
{ // note this may not be used any more
	$GalID =  db_sfq("SELECT ParentGalleryID FROM tblGalleryItems WHERE ID='{$CurrentGIID}'");
	if (!($GalID>0)) {echo "Warning: find_top_level_galleryID( {$CurrentGIID} ) No associated ParentGallery found; please report to JHDB admins<br>\n"; return 2;}
	while ($GalID>=Max_ID_TopLevel_Galleries) // looking for Gal ID==2,3,4,5 (but not ==1 homepage)
	{
		$TempID = db_sfq("SELECT ParentGalleryID FROM tblGalleryItems WHERE SubGalleryID='$ID' LIMIT 1"); // LIMIT just in case
		if ($TempID=="" OR $TempID==$GalID) {echo "Error: recursion for find_top_level_galleryID( {$CurrentGIID} ) ID={$GalID}  [lib-utils]<br>\n"; return $GalID; }
//	echo " <br>$CurrentID--TempID=$TempID;-- ";
		if ($TempID==1) exitloop; // ID==1 is Home
		if ($TempID) $GalID=$TempID;
	}
	
	return $GalID;
} // end find_top_level_galleryID



function gallery_thumb_URL_from_itemID($GIID, $IncludeHTMLTag=0 )   ///////  FIXME    NOT finished
{
/*
possible db field contents:
	http://fullUrl - pass along as-is
	/images/filename.jpg  - path starting at site root
	musicians/name/images/bio.jpg  -  path starting at  siteroot/content
	
try for tblGalleries  field first, if NULL, then tblGalleryItems
*/
	
	 
	list($GIThumbURL, $URL, $SubGalleryID)  = db_sfq("SELECT ThumbURL, URL, SubGalleryID FROM tblGalleryItems WHERE ID = {$GIID}");
//echo "<!--  list($GIThumbURL, $URL, $SubGalleryID)  = db_sfq(SELECT ThumbURL, URL, SubGalleryID FROM tblGalleryItems WHERE ID = {$GIID}); -->\n";
	$GalThumbURL = "";
	if ($SubGalleryID>0)
		$GalThumbURL = db_sfq("SELECT ThumbURL FROM tblGalleries WHERE ID = '{$SubGalleryID}'");
	
	if (substr($GIThumbURL,0,7) !="http://" AND strlen($GIThumbURL) >0) 
	  $GIThumbURL  = 
		(($GIThumbURL[0]=="/")? SiteBaseURL.substr($GIThumbURL,1)/*drop leading / */ : EntityContentBaseURL.$GIThumbURL);
	if (substr($GalThumbURL,0,7)!="http://" AND strlen($GalThumbURL)>0) 
	  $GalThumbURL = 
		(($GalThumbURL[0]=="/")? SiteBaseURL.substr($GalThumbURL,1)/*drop leading / */ : EntityContentBaseURL.$GalThumbURL);

//echo "<!-- GalThumbURL=$GalThumbURL; GIThumbURL=$GIThumbURL; \n -->";
	if ($GalThumbURL =="" AND $GIThumbURL == "") return NoImageAvailableURL;
	
	if (!$GalThumbURL) return $GIThumbURL;
	
	return $GalThumbURL;  // Gallery THumb image preferred
	
} // end gallery_thumb_URL_from_itemID



function gallery_link_from_GalleryItemID($GIID, $IncludeHTMLTag=false )
{ //  construct a link URL to the GalleryID that is pointed to by the given $GalleryItemID (if this is a leaf node, 
	list( $GIContentTypeID, $SubGalleryID, $GalleryItemURL, $PageTitle, $MenuTitle) = db_sfq($Q="SELECT GIContentTypeID, SubGalleryID, URL, PageTitle, MenuTitle FROM tblGalleryItems WHERE ID = {$GIID}");

//echo "\n<!-- gallery_link_from_GalleryItemID($GIID): $Q  -->\n";
	
	$ResultURL 		= GalleryBaseURL;   
	$Target			= ""; // possibly open in new window (if the full HTML tag is requested
	if ($GIContentTypeID==GIContentType_SubGallery)		$ResultURL .=	"?gi={$GIID}";	
	if ($GIContentTypeID==GIContentType_RemoteURL)		{$ResultURL	=	$GalleryItemURL; $Target="target='_blank'";} // we lose control with remote URLs
	if ($GIContentTypeID==GIContentType_JHDBURL)		{$ResultURL	=	$GalleryItemURL; }
	if ($GIContentTypeID==GIContentType_JHDBContentFile)$ResultURL .=	"?gi={$GIID}&u={$GalleryItemURL}";	

	if ($IncludeHTMLTag)
	{
		$ResultTitle = $PageTitle;  // serves as default Menu Title
		if ($MenuTitle) $ResultTitle = $MenuTitle;  // override if provided in GalleryItem record
		$ResultHTMLTag = "<a href='{$ResultURL}' {$Target} >{$ResultTitle}</a>";
		return $ResultHTMLTag;
	} else
	{ // no HTML tag format, just raw http:// link
		return $ResultURL;
	}
}// end gallery_link_from_GalleryItemID



function find_first_free_ID($TableName, $RangeMin, $RangeMax)  // this is used to manage the ID range conventions when adding new records in most tables
{	// no error checking for any anomalies such as the Range is already used up; assumes range min < max
	
	$ExistingCount	= db_sfq("SELECT ID FROM {$TableName} 
						WHERE ID>={$RangeMin} AND ID<={$RangeMax} "); 
	$MaxUsedID 		= db_sfq("SELECT ID FROM {$TableName} 
						WHERE ID>={$RangeMin} AND ID<={$RangeMax}
						ORDER BY ID DESC LIMIT 1");
	
	echo "<br>find_first_free_ID($TableName, $RangeMin, $RangeMax);  MaxUsedID =$MaxUsedID  <br>";
	
	if ($ExistingCount==0) return $RangeMin;  // none previously existed
	
	if ($MaxUsedID=="")   // could be NULL because range is already "full"
		return $RangeMax;    // guess/punt
		else return $MaxUsedID+1;  // next one to be used
	
	
}  //  end find_first_free_ID



function entity_file_path_from_EntityID($EntityID)   // PHP file path
{  // no error chking, assume EntityID is valid

	
	$EntityLName = db_sfq("SELECT LName FROM tblEntities WHERE ID='{$EntityID}'");
	return EntityContentPHPBasePath."/{$EntityLName}";
}// end entity_file_path_from_EntityID



//   Note the following may be unused, replaced by MoveToCurrentEntityDirectory
function UploadToEntityDirectory($SourceFilePath, $EntityID, $SubDir='images')
{  // given tmp file from $_POST, copy to Entity's  image/  directory and return URL to it
	if (!file_exists($SourceFilePath)) die (" Image Upload failed to server temp directory {$FilePath}; (EntityID={$EntityID}). Please use BROWSER BACK to try again or contact JHDB administrator");
	if (!$EntityID>0) die (" During Image Upload {$SourceFilePath}:  Bad (EntityID={$EntityID})   Please use BROWSER BACK to try again or contact JHDB administrator");
	$EntityFilePath = entity_file_path_from_EntityID($EntityID);
	$FileName = var_dump(basename($SourceFilePath)); // filename.ext
	$EntityFile = $EntityFilePath.$FileName;
	if (file_exists($EntityFile))
	{
		$NewFileName = date_format("Y-m-d")."-".$EntityFile;
		echo "Information: Filename {$EntityFile} already exists;  naming new image upload to {$NewFileName} <br>";	
		$EntityFile = $NewFileName;
	}
	if (copy ($SourceFilePath, $EntityFile))  
	{
		echo "<br> Successful upload copy to {$EntityFile}<br>";
		return $EntityFile; 
	}
	  else  die("Contact JHDB administrator: File Copy failed copy($SourceFilePath, $EntityFile)");
	
} // end UploadToEntityImageDirectory



function excerpt_string($SourceString,$StartTag,$EndTag)
{
    $r = explode($StartTag, $SourceString);
    if (isset($r[1])){
        $r = explode($EndTag, $r[1]);
        return $r[0];
    }
    return '';
}

function display_code_comments($File, $Label)   // used in stylized code comments displayed in the help file.
{
	$StartTag = "#BEGIN {$Label}";
	$EndTag = "#END";  // #END {$Label}    optional label
	$Contents = file_get_contents($File);  // can be a fully expanded URL
	echo excerpt_string($Contents,$StartTag,$EndTag);
	
}// end function display_code_comments





//----------------------------- inherited functions, not debugged, may not be used? ------------------------------



function resize_image_max($image,$max_width,$max_height)   // note param order: W, H
{
    $w = imagesx($image); //current width
    $h = imagesy($image); //current height
    if ((!$w) || (!$h)) { $GLOBALS['errors'][] = 'Image couldn\'t be resized because it wasn\'t a valid image.  [lib-utils]'; return false; }

    if (($w <= $max_width) && ($h <= $max_height)) { return $image; } //no resizing needed
    
    //try max width first...
    $ratio = $max_width / $w;
    $new_w = $max_width;
    $new_h = $h * $ratio;
    
    //if that didn't work
    if ($new_h > $max_height) {
        $ratio = $max_height / $h;
        $new_h = $max_height;
        $new_w = $w * $ratio;
    }
    
    $new_image = imagecreatetruecolor ($new_w, $new_h);
    imagecopyresampled($new_image,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
    return $new_image;
} // end resize_image_max


function resize_image($method, $image_loc, $new_loc, $height, $width) // note param order:  H, W
{
    if (!is_array(@$GLOBALS['errors'])) { $GLOBALS['errors'] = array(); }
    
    if (!in_array($method,array('force','max','crop'))) { $GLOBALS['errors'][] = 'Invalid method selected.'; }
    
    if (!$image_loc) { $GLOBALS['errors'][] = 'No source image location specified.'; }
    else {
        if ((substr(strtolower($image_loc),0,7) == 'http://') || (substr(strtolower($image_loc),0,7) == 'https://')) { /*don't check to see if file exists since it's not local*/ }
        elseif (!file_exists($image_loc)) { $GLOBALS['errors'][] = 'Image source file does not exist.'; }
        $extension = strtolower(substr($image_loc,strrpos($image_loc,'.')));
        if (!in_array($extension,array('.jpg','.jpeg','.png','.gif','.bmp'))) { $GLOBALS['errors'][] = 'Invalid source file extension!'; }
    }
    
    if (!$new_loc) { $GLOBALS['errors'][] = 'No destination image location specified.'; }
    else {
        $new_extension = strtolower(substr($new_loc,strrpos($new_loc,'.')));
        if (!in_array($new_extension,array('.jpg','.jpeg','.png','.gif','.bmp'))) { $GLOBALS['errors'][] = 'Invalid destination file extension!'; }
    }

    $width = abs(intval($width));
    if (!$width) { $GLOBALS['errors'][] = 'No width specified!'; }
    
    $height = abs(intval($height));
    if (!$height) { $GLOBALS['errors'][] = 'No height specified!'; }
    
    if (count($GLOBALS['errors']) > 0) { image_echo_errors(); return false; }
    
    if (in_array($extension,array('.jpg','.jpeg'))) { $image = @imagecreatefromjpeg($image_loc); }
    elseif ($extension == '.png') { $image = @imagecreatefrompng($image_loc); }
    elseif ($extension == '.gif') { $image = @imagecreatefromgif($image_loc); }
    elseif ($extension == '.bmp') { $image = @imagecreatefromwbmp($image_loc); }
    
    if (!$image) { $GLOBALS['errors'][] = 'Image could not be generated!'; }
    else {
        $current_width = imagesx($image);
        $current_height = imagesy($image);
        if ((!$current_width) || (!$current_height)) { $GLOBALS['errors'][] = 'Generated image has invalid dimensions!'; }
    }
    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); image_echo_errors(); return false; }

    if ($method == 'force') { $new_image = resize_image_force($image,$width,$height); }
    elseif ($method == 'max') { $new_image = resize_image_max($image,$width,$height); }
    elseif ($method == 'crop') { $new_image = resize_image_crop($image,$width,$height); }
    
    if ((!$new_image) && (count($GLOBALS['errors'] == 0))) { $GLOBALS['errors'][] = 'New image could not be generated!'; }
    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); image_echo_errors(); return false; }
    
    $save_error = false;
    if (in_array($extension,array('.jpg','.jpeg'))) { imagejpeg($new_image,$new_loc) or ($save_error = true); }
    elseif ($extension == '.png') { imagepng($new_image,$new_loc) or ($save_error = true); }
    elseif ($extension == '.gif') { imagegif($new_image,$new_loc) or ($save_error = true); }
    elseif ($extension == '.bmp') { imagewbmp($new_image,$new_loc) or ($save_error = true); }
    if ($save_error) { $GLOBALS['errors'][] = 'New image could not be saved!'; }
    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); @imagedestroy($new_image); image_echo_errors(); return false; }

    imagedestroy($image);
//    imagedestroy($new_image);
    
    return true;
} // end resize_image


function image_echo_errors() 
{
    if (!is_array(@$GLOBALS['errors'])) { $GLOBALS['errors'] = array('Unknown error!'); }
    foreach ($GLOBALS['errors'] as $error) { echo '<p style="color:red;font-weight:bold;">Error: '.$error.'</p>'; }
} // end image_echo_errors








// permsBlurb: Consumes an optional name, and returns the permissions blurb using that name.
function permsBlurb($names=NULL) 
{
	if ($names != NULL) {
		echo "All materials are presented here with the express permission of their respective copyright owners and " . $names . ".  Materials may have been edited to remove content for which explicit permission has not been granted."; 
	} else if ($names == NULL) { // If no name is given, special case format.
		echo "All materials are presented here with the express permission of their respective copyright owners.  Materials may have been edited to remove content for which explicit permission has not been granted."; 
	}
} //end permsBlurb

// newStamp: Shorthand for a stamp in the new-stamp class, for marking new collections.
function newStamp() 
{
	echo "<div class=\"new-stamp\"><img src=\"/images/index-splashes/new.png\" /></div>" ;
} // end newStamp

// audioPlayer: Generates the audio player.  Requires the appropriate scripts to be included in the <head> of the file.
function audioPlayer() 
{
	echo "
	<div id=\"player\"></div>
	<script type=\"text/javascript\">
		var so = new SWFObject('http://www.jazzhistorydatabase.com/Scripts/jwplayer/player.swf','mpl','350','60','9');
		so.addParam('allowscriptaccess','always');
		so.addParam('allowfullscreen','false');
		so.addParam('wmode','transparent');
		so.addParam('flashvars','&type=sound&backcolor=521010&frontcolor=f5f4f4&lightcolor=eeeeee&screencolor=f5f4f4&skin=http://www.jazzhistorydatabase.com/Scripts/jwplayer/overlay.swf&bufferlength=2&volume=75&displayclick=none');
		so.write('player');
	</script>
" ;
}// end audioPlayer




?> 