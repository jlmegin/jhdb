<?php
// This script and data application were generated by AppGini 5.30
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/tblAccessions.php");
	include("$currDir/tblAccessions_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('tblAccessions');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "tblAccessions";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`tblAccessions`.`ID`" => "ID",
		"IF(    CHAR_LENGTH(`tblEntities1`.`ID`) || CHAR_LENGTH(`tblEntities1`.`FName`), CONCAT_WS('',   `tblEntities1`.`ID`, ': ', `tblEntities1`.`FName`), '') /* EntityID */" => "EntityID",
		"IF(    CHAR_LENGTH(`tblSubGalleryTypes1`.`ID`) || CHAR_LENGTH(`tblSubGalleryTypes1`.`Name`), CONCAT_WS('',   `tblSubGalleryTypes1`.`ID`, ': ', `tblSubGalleryTypes1`.`Name`), '') /* MediumTypeID */" => "MediumTypeID",
		"IF(    CHAR_LENGTH(`tblContentTypes1`.`ID`) || CHAR_LENGTH(`tblContentTypes1`.`Name`), CONCAT_WS('',   `tblContentTypes1`.`ID`, ': ', `tblContentTypes1`.`Name`), '') /* ContentTypeID */" => "ContentTypeID",
		"`tblAccessions`.`CaptionText`" => "CaptionText",
		"`tblAccessions`.`URL`" => "URL",
		"`tblAccessions`.`SourceID`" => "SourceID",
		"`tblAccessions`.`Credits`" => "Credits",
		"IF(    CHAR_LENGTH(`tblContributors1`.`ID`) || CHAR_LENGTH(`tblContributors1`.`LName`), CONCAT_WS('',   `tblContributors1`.`ID`, ': ', `tblContributors1`.`LName`), '') /* ContributorID */" => "ContributorID",
		"`tblAccessions`.`StyleID`" => "StyleID",
		"`tblAccessions`.`CountryID`" => "CountryID",
		"`tblAccessions`.`CreationDate`" => "CreationDate",
		"`tblAccessions`.`AccessionEntryDate`" => "AccessionEntryDate",
		"`tblAccessions`.`ApprovalID`" => "ApprovalID",
		"`tblAccessions`.`Comments`" => "Comments",
		"concat('<img src=\"', if(`tblAccessions`.`Online`, 'checked.gif', 'checkednot.gif'), '\" border=\"0\" />')" => "Online"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`tblAccessions`.`ID`',
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => '`tblAccessions`.`SourceID`',
		8 => 8,
		9 => 9,
		10 => '`tblAccessions`.`StyleID`',
		11 => '`tblAccessions`.`CountryID`',
		12 => '`tblAccessions`.`CreationDate`',
		13 => '`tblAccessions`.`AccessionEntryDate`',
		14 => '`tblAccessions`.`ApprovalID`',
		15 => 15,
		16 => '`tblAccessions`.`Online`'
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`tblAccessions`.`ID`" => "ID",
		"IF(    CHAR_LENGTH(`tblEntities1`.`ID`) || CHAR_LENGTH(`tblEntities1`.`FName`), CONCAT_WS('',   `tblEntities1`.`ID`, ': ', `tblEntities1`.`FName`), '') /* EntityID */" => "EntityID",
		"IF(    CHAR_LENGTH(`tblSubGalleryTypes1`.`ID`) || CHAR_LENGTH(`tblSubGalleryTypes1`.`Name`), CONCAT_WS('',   `tblSubGalleryTypes1`.`ID`, ': ', `tblSubGalleryTypes1`.`Name`), '') /* MediumTypeID */" => "MediumTypeID",
		"IF(    CHAR_LENGTH(`tblContentTypes1`.`ID`) || CHAR_LENGTH(`tblContentTypes1`.`Name`), CONCAT_WS('',   `tblContentTypes1`.`ID`, ': ', `tblContentTypes1`.`Name`), '') /* ContentTypeID */" => "ContentTypeID",
		"`tblAccessions`.`CaptionText`" => "CaptionText",
		"`tblAccessions`.`URL`" => "URL",
		"`tblAccessions`.`SourceID`" => "SourceID",
		"`tblAccessions`.`Credits`" => "Credits",
		"IF(    CHAR_LENGTH(`tblContributors1`.`ID`) || CHAR_LENGTH(`tblContributors1`.`LName`), CONCAT_WS('',   `tblContributors1`.`ID`, ': ', `tblContributors1`.`LName`), '') /* ContributorID */" => "ContributorID",
		"`tblAccessions`.`StyleID`" => "StyleID",
		"`tblAccessions`.`CountryID`" => "CountryID",
		"`tblAccessions`.`CreationDate`" => "CreationDate",
		"`tblAccessions`.`AccessionEntryDate`" => "AccessionEntryDate",
		"`tblAccessions`.`ApprovalID`" => "ApprovalID",
		"`tblAccessions`.`Comments`" => "Comments",
		"`tblAccessions`.`Online`" => "Online"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`tblAccessions`.`ID`" => "ID",
		"IF(    CHAR_LENGTH(`tblEntities1`.`ID`) || CHAR_LENGTH(`tblEntities1`.`FName`), CONCAT_WS('',   `tblEntities1`.`ID`, ': ', `tblEntities1`.`FName`), '') /* EntityID */" => "EntityID",
		"IF(    CHAR_LENGTH(`tblSubGalleryTypes1`.`ID`) || CHAR_LENGTH(`tblSubGalleryTypes1`.`Name`), CONCAT_WS('',   `tblSubGalleryTypes1`.`ID`, ': ', `tblSubGalleryTypes1`.`Name`), '') /* MediumTypeID */" => "MediumTypeID",
		"IF(    CHAR_LENGTH(`tblContentTypes1`.`ID`) || CHAR_LENGTH(`tblContentTypes1`.`Name`), CONCAT_WS('',   `tblContentTypes1`.`ID`, ': ', `tblContentTypes1`.`Name`), '') /* ContentTypeID */" => "ContentTypeID",
		"`tblAccessions`.`CaptionText`" => "CaptionText",
		"`tblAccessions`.`URL`" => "URL",
		"`tblAccessions`.`SourceID`" => "SourceID",
		"`tblAccessions`.`Credits`" => "Credits",
		"IF(    CHAR_LENGTH(`tblContributors1`.`ID`) || CHAR_LENGTH(`tblContributors1`.`LName`), CONCAT_WS('',   `tblContributors1`.`ID`, ': ', `tblContributors1`.`LName`), '') /* ContributorID */" => "ContributorID",
		"`tblAccessions`.`StyleID`" => "StyleID",
		"`tblAccessions`.`CountryID`" => "CountryID",
		"`tblAccessions`.`CreationDate`" => "CreationDate",
		"`tblAccessions`.`AccessionEntryDate`" => "AccessionEntryDate",
		"`tblAccessions`.`ApprovalID`" => "ApprovalID",
		"`tblAccessions`.`Comments`" => "Comments",
		"`tblAccessions`.`Online`" => "Online"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`tblAccessions`.`ID`" => "ID",
		"IF(    CHAR_LENGTH(`tblEntities1`.`ID`) || CHAR_LENGTH(`tblEntities1`.`FName`), CONCAT_WS('',   `tblEntities1`.`ID`, ': ', `tblEntities1`.`FName`), '') /* EntityID */" => "EntityID",
		"IF(    CHAR_LENGTH(`tblSubGalleryTypes1`.`ID`) || CHAR_LENGTH(`tblSubGalleryTypes1`.`Name`), CONCAT_WS('',   `tblSubGalleryTypes1`.`ID`, ': ', `tblSubGalleryTypes1`.`Name`), '') /* MediumTypeID */" => "MediumTypeID",
		"IF(    CHAR_LENGTH(`tblContentTypes1`.`ID`) || CHAR_LENGTH(`tblContentTypes1`.`Name`), CONCAT_WS('',   `tblContentTypes1`.`ID`, ': ', `tblContentTypes1`.`Name`), '') /* ContentTypeID */" => "ContentTypeID",
		"`tblAccessions`.`CaptionText`" => "CaptionText",
		"`tblAccessions`.`URL`" => "URL",
		"`tblAccessions`.`SourceID`" => "SourceID",
		"`tblAccessions`.`Credits`" => "Credits",
		"IF(    CHAR_LENGTH(`tblContributors1`.`ID`) || CHAR_LENGTH(`tblContributors1`.`LName`), CONCAT_WS('',   `tblContributors1`.`ID`, ': ', `tblContributors1`.`LName`), '') /* ContributorID */" => "ContributorID",
		"`tblAccessions`.`StyleID`" => "StyleID",
		"`tblAccessions`.`CountryID`" => "CountryID",
		"`tblAccessions`.`CreationDate`" => "CreationDate",
		"`tblAccessions`.`AccessionEntryDate`" => "AccessionEntryDate",
		"`tblAccessions`.`ApprovalID`" => "ApprovalID",
		"`tblAccessions`.`Comments`" => "Comments",
		"concat('<img src=\"', if(`tblAccessions`.`Online`, 'checked.gif', 'checkednot.gif'), '\" border=\"0\" />')" => "Online"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'EntityID' => 'EntityID', 'MediumTypeID' => 'MediumTypeID', 'ContentTypeID' => 'ContentTypeID', 'ContributorID' => 'ContributorID');

	$x->QueryFrom="`tblAccessions` LEFT JOIN `tblEntities` as tblEntities1 ON `tblEntities1`.`ID`=`tblAccessions`.`EntityID` LEFT JOIN `tblSubGalleryTypes` as tblSubGalleryTypes1 ON `tblSubGalleryTypes1`.`ID`=`tblAccessions`.`MediumTypeID` LEFT JOIN `tblContentTypes` as tblContentTypes1 ON `tblContentTypes1`.`ID`=`tblAccessions`.`ContentTypeID` LEFT JOIN `tblContributors` as tblContributors1 ON `tblContributors1`.`ID`=`tblAccessions`.`ContributorID` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowMassDelete = false;
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 0;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 20;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "tblAccessions_view.php";
	$x->RedirectAfterInsert = "tblAccessions_view.php?SelectedID=#ID#";
	$x->TableTitle = "tblAccessions";
	$x->TableIcon = "table.gif";
	$x->PrimaryKey = "`tblAccessions`.`ID`";
	$x->DefaultSortField = '1';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth   = array(  150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150, 150);
	$x->ColCaption = array("ID", "EntityID", "MediumTypeID", "ContentTypeID", "CaptionText", "URL", "SourceID", "Credits", "ContributorID", "StyleID", "CountryID", "CreationDate", "AccessionEntryDate", "ApprovalID", "Comments", "Online");
	$x->ColFieldName = array('ID', 'EntityID', 'MediumTypeID', 'ContentTypeID', 'CaptionText', 'URL', 'SourceID', 'Credits', 'ContributorID', 'StyleID', 'CountryID', 'CreationDate', 'AccessionEntryDate', 'ApprovalID', 'Comments', 'Online');
	$x->ColNumber  = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16);

	$x->Template = 'templates/tblAccessions_templateTV.html';
	$x->SelectedTemplate = 'templates/tblAccessions_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `tblAccessions`.`ID`=membership_userrecords.pkValue and membership_userrecords.tableName='tblAccessions' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `tblAccessions`.`ID`=membership_userrecords.pkValue and membership_userrecords.tableName='tblAccessions' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`tblAccessions`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: tblAccessions_init
	$render=TRUE;
	if(function_exists('tblAccessions_init')){
		$args=array();
		$render=tblAccessions_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: tblAccessions_header
	$headerCode='';
	if(function_exists('tblAccessions_header')){
		$args=array();
		$headerCode=tblAccessions_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: tblAccessions_footer
	$footerCode='';
	if(function_exists('tblAccessions_footer')){
		$args=array();
		$footerCode=tblAccessions_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>