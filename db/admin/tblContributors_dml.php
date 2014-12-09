<?php

// Data functions for table tblContributors

// This script and data application were generated by AppGini 5.30
// Download AppGini for free from http://bigprof.com/appgini/download/

function tblContributors_insert(){
	global $Translation;

	if($_GET['insert_x']!=''){$_POST=$_GET;}

	// mm: can member insert record?
	$arrPerm=getTablePermissions('tblContributors');
	if(!$arrPerm[1]){
		return false;
	}

	$data['FName'] = makeSafe($_POST['FName']);
		if($data['FName'] == empty_lookup_value){ $data['FName'] = ''; }
	$data['LName'] = makeSafe($_POST['LName']);
		if($data['LName'] == empty_lookup_value){ $data['LName'] = ''; }
	$data['Email'] = makeSafe($_POST['Email']);
		if($data['Email'] == empty_lookup_value){ $data['Email'] = ''; }
	$data['PW'] = makeSafe($_POST['PW']);
		if($data['PW'] == empty_lookup_value){ $data['PW'] = ''; }
	$data['Organization'] = makeSafe($_POST['Organization']);
		if($data['Organization'] == empty_lookup_value){ $data['Organization'] = ''; }
	$data['ScopeID'] = makeSafe($_POST['ScopeID']);
		if($data['ScopeID'] == empty_lookup_value){ $data['ScopeID'] = ''; }
	$data['ParentID'] = makeSafe($_POST['ParentID']);
		if($data['ParentID'] == empty_lookup_value){ $data['ParentID'] = ''; }
	$data['AdminLevel'] = makeSafe($_POST['AdminLevel']);
		if($data['AdminLevel'] == empty_lookup_value){ $data['AdminLevel'] = ''; }
	$data['CreationDate'] = intval($_POST['CreationDateYear']) . '-' . intval($_POST['CreationDateMonth']) . '-' . intval($_POST['CreationDateDay']);
	$data['CreationDate'] = parseMySQLDate($data['CreationDate'], '');
	$data['ExpirationDate'] = intval($_POST['ExpirationDateYear']) . '-' . intval($_POST['ExpirationDateMonth']) . '-' . intval($_POST['ExpirationDateDay']);
	$data['ExpirationDate'] = parseMySQLDate($data['ExpirationDate'], '');
	$data['LastAccessDate'] = intval($_POST['LastAccessDateYear']) . '-' . intval($_POST['LastAccessDateMonth']) . '-' . intval($_POST['LastAccessDateDay']);
	$data['LastAccessDate'] = parseMySQLDate($data['LastAccessDate'], '');
	$data['Online'] = makeSafe($_POST['Online']);
		if($data['Online'] == empty_lookup_value){ $data['Online'] = ''; }

	// hook: tblContributors_before_insert
	if(function_exists('tblContributors_before_insert')){
		$args=array();
		if(!tblContributors_before_insert($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('insert into `tblContributors` set       `FName`=' . (($data['FName'] !== '' && $data['FName'] !== NULL) ? "'{$data['FName']}'" : 'NULL') . ', `LName`=' . (($data['LName'] !== '' && $data['LName'] !== NULL) ? "'{$data['LName']}'" : 'NULL') . ', `Email`=' . (($data['Email'] !== '' && $data['Email'] !== NULL) ? "'{$data['Email']}'" : 'NULL') . ', `PW`=' . (($data['PW'] !== '' && $data['PW'] !== NULL) ? "'{$data['PW']}'" : 'NULL') . ', `Organization`=' . (($data['Organization'] !== '' && $data['Organization'] !== NULL) ? "'{$data['Organization']}'" : 'NULL') . ', `ScopeID`=' . (($data['ScopeID'] !== '' && $data['ScopeID'] !== NULL) ? "'{$data['ScopeID']}'" : 'NULL') . ', `ParentID`=' . (($data['ParentID'] !== '' && $data['ParentID'] !== NULL) ? "'{$data['ParentID']}'" : 'NULL') . ', `AdminLevel`=' . (($data['AdminLevel'] !== '' && $data['AdminLevel'] !== NULL) ? "'{$data['AdminLevel']}'" : 'NULL') . ', `CreationDate`=' . (($data['CreationDate'] !== '' && $data['CreationDate'] !== NULL) ? "'{$data['CreationDate']}'" : 'NULL') . ', `ExpirationDate`=' . (($data['ExpirationDate'] !== '' && $data['ExpirationDate'] !== NULL) ? "'{$data['ExpirationDate']}'" : 'NULL') . ', `LastAccessDate`=' . (($data['LastAccessDate'] !== '' && $data['LastAccessDate'] !== NULL) ? "'{$data['LastAccessDate']}'" : 'NULL') . ', `Online`=' . (($data['Online'] !== '' && $data['Online'] !== NULL) ? "'{$data['Online']}'" : 'NULL'), $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"tblContributors_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID=db_insert_id(db_link());

	// hook: tblContributors_after_insert
	if(function_exists('tblContributors_after_insert')){
		$res = sql("select * from `tblContributors` where `ID`='" . makeSafe($recID) . "' limit 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID);
		$args=array();
		if(!tblContributors_after_insert($data, getMemberInfo(), $args)){ return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID); }
	}

	// mm: save ownership data
	sql("insert into membership_userrecords set tableName='tblContributors', pkValue='$recID', memberID='".getLoggedMemberID()."', dateAdded='".time()."', dateUpdated='".time()."', groupID='".getLoggedGroupID()."'", $eo);

	return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID);
}

function tblContributors_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('tblContributors');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='tblContributors' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='tblContributors' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: tblContributors_before_delete
	if(function_exists('tblContributors_before_delete')){
		$args=array();
		if(!tblContributors_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	// child table: tblContributors
	$res = sql("select `ID` from `tblContributors` where `ID`='$selected_id'", $eo);
	$ID = db_fetch_row($res);
	$rires = sql("select count(1) from `tblContributors` where `ParentID`='".addslashes($ID[0])."'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "tblContributors", $RetMsg);
		return $RetMsg;
	}elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["confirm delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "tblContributors", $RetMsg);
		$RetMsg = str_replace("<Delete>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['yes']."\" onClick=\"window.location='tblContributors_view.php?SelectedID=".urlencode($selected_id)."&delete_x=1&confirmed=1';\">", $RetMsg);
		$RetMsg = str_replace("<Cancel>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['no']."\" onClick=\"window.location='tblContributors_view.php?SelectedID=".urlencode($selected_id)."';\">", $RetMsg);
		return $RetMsg;
	}

	// child table: tblAccessions
	$res = sql("select `ID` from `tblContributors` where `ID`='$selected_id'", $eo);
	$ID = db_fetch_row($res);
	$rires = sql("select count(1) from `tblAccessions` where `ContributorID`='".addslashes($ID[0])."'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "tblAccessions", $RetMsg);
		return $RetMsg;
	}elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["confirm delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "tblAccessions", $RetMsg);
		$RetMsg = str_replace("<Delete>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['yes']."\" onClick=\"window.location='tblContributors_view.php?SelectedID=".urlencode($selected_id)."&delete_x=1&confirmed=1';\">", $RetMsg);
		$RetMsg = str_replace("<Cancel>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['no']."\" onClick=\"window.location='tblContributors_view.php?SelectedID=".urlencode($selected_id)."';\">", $RetMsg);
		return $RetMsg;
	}

	// child table: tblUploadLogs
	$res = sql("select `ID` from `tblContributors` where `ID`='$selected_id'", $eo);
	$ID = db_fetch_row($res);
	$rires = sql("select count(1) from `tblUploadLogs` where `ContributorID`='".addslashes($ID[0])."'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "tblUploadLogs", $RetMsg);
		return $RetMsg;
	}elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks){
		$RetMsg = $Translation["confirm delete"];
		$RetMsg = str_replace("<RelatedRecords>", $rirow[0], $RetMsg);
		$RetMsg = str_replace("<TableName>", "tblUploadLogs", $RetMsg);
		$RetMsg = str_replace("<Delete>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['yes']."\" onClick=\"window.location='tblContributors_view.php?SelectedID=".urlencode($selected_id)."&delete_x=1&confirmed=1';\">", $RetMsg);
		$RetMsg = str_replace("<Cancel>", "<input tabindex=\"2\" type=\"button\" class=\"button\" value=\"".$Translation['no']."\" onClick=\"window.location='tblContributors_view.php?SelectedID=".urlencode($selected_id)."';\">", $RetMsg);
		return $RetMsg;
	}

	sql("delete from `tblContributors` where `ID`='$selected_id'", $eo);

	// hook: tblContributors_after_delete
	if(function_exists('tblContributors_after_delete')){
		$args=array();
		tblContributors_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='tblContributors' and pkValue='$selected_id'", $eo);
}

function tblContributors_update($selected_id){
	global $Translation;

	if($_GET['update_x']!=''){$_POST=$_GET;}

	// mm: can member edit record?
	$arrPerm=getTablePermissions('tblContributors');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='tblContributors' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='tblContributors' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['FName'] = makeSafe($_POST['FName']);
		if($data['FName'] == empty_lookup_value){ $data['FName'] = ''; }
	$data['LName'] = makeSafe($_POST['LName']);
		if($data['LName'] == empty_lookup_value){ $data['LName'] = ''; }
	$data['Email'] = makeSafe($_POST['Email']);
		if($data['Email'] == empty_lookup_value){ $data['Email'] = ''; }
	$data['PW'] = makeSafe($_POST['PW']);
		if($data['PW'] == empty_lookup_value){ $data['PW'] = ''; }
	$data['Organization'] = makeSafe($_POST['Organization']);
		if($data['Organization'] == empty_lookup_value){ $data['Organization'] = ''; }
	$data['ScopeID'] = makeSafe($_POST['ScopeID']);
		if($data['ScopeID'] == empty_lookup_value){ $data['ScopeID'] = ''; }
	$data['ParentID'] = makeSafe($_POST['ParentID']);
		if($data['ParentID'] == empty_lookup_value){ $data['ParentID'] = ''; }
	$data['AdminLevel'] = makeSafe($_POST['AdminLevel']);
		if($data['AdminLevel'] == empty_lookup_value){ $data['AdminLevel'] = ''; }
	$data['CreationDate'] = intval($_POST['CreationDateYear']) . '-' . intval($_POST['CreationDateMonth']) . '-' . intval($_POST['CreationDateDay']);
	$data['CreationDate'] = parseMySQLDate($data['CreationDate'], '');
	$data['ExpirationDate'] = intval($_POST['ExpirationDateYear']) . '-' . intval($_POST['ExpirationDateMonth']) . '-' . intval($_POST['ExpirationDateDay']);
	$data['ExpirationDate'] = parseMySQLDate($data['ExpirationDate'], '');
	$data['LastAccessDate'] = intval($_POST['LastAccessDateYear']) . '-' . intval($_POST['LastAccessDateMonth']) . '-' . intval($_POST['LastAccessDateDay']);
	$data['LastAccessDate'] = parseMySQLDate($data['LastAccessDate'], '');
	$data['Online'] = makeSafe($_POST['Online']);
		if($data['Online'] == empty_lookup_value){ $data['Online'] = ''; }
	$data['selectedID']=makeSafe($selected_id);

	// hook: tblContributors_before_update
	if(function_exists('tblContributors_before_update')){
		$args=array();
		if(!tblContributors_before_update($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('update `tblContributors` set       `FName`=' . (($data['FName'] !== '' && $data['FName'] !== NULL) ? "'{$data['FName']}'" : 'NULL') . ', `LName`=' . (($data['LName'] !== '' && $data['LName'] !== NULL) ? "'{$data['LName']}'" : 'NULL') . ', `Email`=' . (($data['Email'] !== '' && $data['Email'] !== NULL) ? "'{$data['Email']}'" : 'NULL') . ', `PW`=' . (($data['PW'] !== '' && $data['PW'] !== NULL) ? "'{$data['PW']}'" : 'NULL') . ', `Organization`=' . (($data['Organization'] !== '' && $data['Organization'] !== NULL) ? "'{$data['Organization']}'" : 'NULL') . ', `ScopeID`=' . (($data['ScopeID'] !== '' && $data['ScopeID'] !== NULL) ? "'{$data['ScopeID']}'" : 'NULL') . ', `ParentID`=' . (($data['ParentID'] !== '' && $data['ParentID'] !== NULL) ? "'{$data['ParentID']}'" : 'NULL') . ', `AdminLevel`=' . (($data['AdminLevel'] !== '' && $data['AdminLevel'] !== NULL) ? "'{$data['AdminLevel']}'" : 'NULL') . ', `CreationDate`=' . (($data['CreationDate'] !== '' && $data['CreationDate'] !== NULL) ? "'{$data['CreationDate']}'" : 'NULL') . ', `ExpirationDate`=' . (($data['ExpirationDate'] !== '' && $data['ExpirationDate'] !== NULL) ? "'{$data['ExpirationDate']}'" : 'NULL') . ', `LastAccessDate`=' . (($data['LastAccessDate'] !== '' && $data['LastAccessDate'] !== NULL) ? "'{$data['LastAccessDate']}'" : 'NULL') . ', `Online`=' . (($data['Online'] !== '' && $data['Online'] !== NULL) ? "'{$data['Online']}'" : 'NULL') . " where `ID`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="tblContributors_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: tblContributors_after_update
	if(function_exists('tblContributors_after_update')){
		$res = sql("SELECT * FROM `tblContributors` WHERE `ID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['ID'];
		$args = array();
		if(!tblContributors_after_update($data, getMemberInfo(), $args)){ return; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='tblContributors' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function tblContributors_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('tblContributors');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	$AllowInsert = ($arrPerm[1] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}

	$filterer_ParentID = thisOr(undo_magic_quotes($_REQUEST['filterer_ParentID']), '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: ParentID
	$combo_ParentID = new DataCombo;
	// combobox: CreationDate
	$combo_CreationDate = new DateCombo;
	$combo_CreationDate->DateFormat = "mdy";
	$combo_CreationDate->MinYear = 1900;
	$combo_CreationDate->MaxYear = 2100;
	$combo_CreationDate->DefaultDate = parseMySQLDate('', '');
	$combo_CreationDate->MonthNames = $Translation['month names'];
	$combo_CreationDate->NamePrefix = 'CreationDate';
	// combobox: ExpirationDate
	$combo_ExpirationDate = new DateCombo;
	$combo_ExpirationDate->DateFormat = "mdy";
	$combo_ExpirationDate->MinYear = 1900;
	$combo_ExpirationDate->MaxYear = 2100;
	$combo_ExpirationDate->DefaultDate = parseMySQLDate('', '');
	$combo_ExpirationDate->MonthNames = $Translation['month names'];
	$combo_ExpirationDate->NamePrefix = 'ExpirationDate';
	// combobox: LastAccessDate
	$combo_LastAccessDate = new DateCombo;
	$combo_LastAccessDate->DateFormat = "mdy";
	$combo_LastAccessDate->MinYear = 1900;
	$combo_LastAccessDate->MaxYear = 2100;
	$combo_LastAccessDate->DefaultDate = parseMySQLDate('', '');
	$combo_LastAccessDate->MonthNames = $Translation['month names'];
	$combo_LastAccessDate->NamePrefix = 'LastAccessDate';

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='tblContributors' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='tblContributors' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID){
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID){
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("select * from `tblContributors` where `ID`='".makeSafe($selected_id)."'", $eo);
		$row = db_fetch_array($res);
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
		$combo_ParentID->SelectedData = $row['ParentID'];
		$combo_CreationDate->DefaultDate = $row['CreationDate'];
		$combo_ExpirationDate->DefaultDate = $row['ExpirationDate'];
		$combo_LastAccessDate->DefaultDate = $row['LastAccessDate'];
	}else{
		$combo_ParentID->SelectedData = $filterer_ParentID;
	}
	$combo_ParentID->HTML = '<span id="ParentID-container' . $rnd1 . '"></span><input type="hidden" name="ParentID" id="ParentID' . $rnd1 . '">';
	$combo_ParentID->MatchText = '<span id="ParentID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="ParentID" id="ParentID' . $rnd1 . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		var current_ParentID__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['ParentID'] : $filterer_ParentID); ?>"};
		
		jQuery(function() {
			ParentID_reload__RAND__();
		});
		function ParentID_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			jQuery("#ParentID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					jQuery.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: current_ParentID__RAND__.value, t: 'tblContributors', f: 'ParentID' }
					}).done(function(resp){
						c({
							id: resp.results[0].id,
							text: resp.results[0].text
						});
						jQuery('[name="ParentID"]').val(resp.results[0].id);
						jQuery('[id=ParentID-container-readonly__RAND__]').html('<span id="ParentID-match-text">' + resp.results[0].text + '</span>');


						if(typeof(ParentID_update_autofills__RAND__) == 'function') ParentID_update_autofills__RAND__();
					});
				},
				width: '100%',
				formatNoMatches: function(term){ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 10,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page){ return { s: term, p: page, t: 'tblContributors', f: 'ParentID' }; },
					results: function(resp, page){ return resp; }
				}
			}).on('change', function(e){
				current_ParentID__RAND__.value = e.added.id;
				current_ParentID__RAND__.text = e.added.text;
				jQuery('[name="ParentID"]').val(e.added.id);


				if(typeof(ParentID_update_autofills__RAND__) == 'function') ParentID_update_autofills__RAND__();
			});
		<?php }else{ ?>

			jQuery.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: current_ParentID__RAND__.value, t: 'tblContributors', f: 'ParentID' }
			}).done(function(resp){
				jQuery('[id=ParentID-container__RAND__], [id=ParentID-container-readonly__RAND__]').html('<span id="ParentID-match-text">' + resp.results[0].text + '</span>');

				if(typeof(ParentID_update_autofills__RAND__) == 'function') ParentID_update_autofills__RAND__();
			});
		<?php } ?>

		}
	</script>
	<?php
	
	$lookups = str_replace('__RAND__', $rnd1, ob_get_contents());
	ob_end_clean();


	// code for template based detail view forms

	// open the detail view template
	if($dvprint){
		$templateCode = @file_get_contents('./templates/tblContributors_templateDVP.html');
	}else{
		$templateCode = @file_get_contents('./templates/tblContributors_templateDV.html');
	}

	// process form title
	$templateCode=str_replace('<%%DETAIL_VIEW_TITLE%%>', 'tblContributor details', $templateCode);
	$templateCode=str_replace('<%%RND1%%>', $rnd1, $templateCode);
	// process buttons
	if($AllowInsert){
		if(!$selected_id) $templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return tblContributors_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return tblContributors_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']){
		$backAction = 'window.parent.jQuery(\'.modal\').modal(\'hide\'); return false;';
	}else{
		$backAction = '$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id){
		if(!$_REQUEST['Embedded']) $templateCode=str_replace('<%%DVPRINT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate){
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return tblContributors_validateData();"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button tabindex="2" type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)){
		$jsReadOnly .= "\tjQuery('#FName').replaceWith('<p class=\"form-control-static\" id=\"FName\">' + (jQuery('#FName').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#LName').replaceWith('<p class=\"form-control-static\" id=\"LName\">' + (jQuery('#LName').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#Email').replaceWith('<p class=\"form-control-static\" id=\"Email\">' + (jQuery('#Email').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#Email, #Email-edit-link').hide();\n";
		$jsReadOnly .= "\tjQuery('#PW').replaceWith('<p class=\"form-control-static\" id=\"PW\">' + (jQuery('#PW').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#Organization').replaceWith('<p class=\"form-control-static\" id=\"Organization\">' + (jQuery('#Organization').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#ScopeID').replaceWith('<p class=\"form-control-static\" id=\"ScopeID\">' + (jQuery('#ScopeID').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#ParentID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#ParentID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#AdminLevel').replaceWith('<p class=\"form-control-static\" id=\"AdminLevel\">' + (jQuery('#AdminLevel').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#CreationDate').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#CreationDateDay, #CreationDateMonth, #CreationDateYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#ExpirationDate').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#ExpirationDateDay, #ExpirationDateMonth, #ExpirationDateYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#LastAccessDate').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#LastAccessDateDay, #LastAccessDateMonth, #LastAccessDateYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#Online').prop('disabled', true);\n";

		$noUploads = true;
	}elseif($AllowInsert){
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode=str_replace('<%%COMBO(ParentID)%%>', $combo_ParentID->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(ParentID)%%>', $combo_ParentID->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(ParentID)%%>', urlencode($combo_ParentID->MatchText), $templateCode);
	$templateCode=str_replace('<%%COMBO(CreationDate)%%>', ($selected_id && !$arrPerm[3] ? '<p class="form-control-static">' . $combo_CreationDate->GetHTML(true) . '</p>' : $combo_CreationDate->GetHTML()), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(CreationDate)%%>', $combo_CreationDate->GetHTML(true), $templateCode);
	$templateCode=str_replace('<%%COMBO(ExpirationDate)%%>', ($selected_id && !$arrPerm[3] ? '<p class="form-control-static">' . $combo_ExpirationDate->GetHTML(true) . '</p>' : $combo_ExpirationDate->GetHTML()), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(ExpirationDate)%%>', $combo_ExpirationDate->GetHTML(true), $templateCode);
	$templateCode=str_replace('<%%COMBO(LastAccessDate)%%>', ($selected_id && !$arrPerm[3] ? '<p class="form-control-static">' . $combo_LastAccessDate->GetHTML(true) . '</p>' : $combo_LastAccessDate->GetHTML()), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(LastAccessDate)%%>', $combo_LastAccessDate->GetHTML(true), $templateCode);

	// process foreign key links
	if($selected_id){
		$templateCode=str_replace('<%%PLINK(ParentID)%%>', ($combo_ParentID->SelectedData ? "<span id=\"tblContributors_plink1\" class=\"hidden\"><a class=\"btn btn-default\" href=\"tblContributors_view.php?SelectedID=" . urlencode($combo_ParentID->SelectedData) . "\"><i class=\"glyphicon glyphicon-search\"></i></a></span>" : ''), $templateCode);
	}

	// process images
	$templateCode=str_replace('<%%UPLOADFILE(ID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(FName)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(LName)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Email)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(PW)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Organization)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(ScopeID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(ParentID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(AdminLevel)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(CreationDate)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(ExpirationDate)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(LastAccessDate)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Online)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(ID)%%>', htmlspecialchars($row['ID'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ID)%%>', urlencode($urow['ID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(FName)%%>', htmlspecialchars($row['FName'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(FName)%%>', urlencode($urow['FName']), $templateCode);
		$templateCode=str_replace('<%%VALUE(LName)%%>', htmlspecialchars($row['LName'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(LName)%%>', urlencode($urow['LName']), $templateCode);
		$templateCode=str_replace('<%%VALUE(Email)%%>', htmlspecialchars($row['Email'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Email)%%>', urlencode($urow['Email']), $templateCode);
		$templateCode=str_replace('<%%VALUE(PW)%%>', htmlspecialchars($row['PW'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(PW)%%>', urlencode($urow['PW']), $templateCode);
		$templateCode=str_replace('<%%VALUE(Organization)%%>', htmlspecialchars($row['Organization'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Organization)%%>', urlencode($urow['Organization']), $templateCode);
		$templateCode=str_replace('<%%VALUE(ScopeID)%%>', htmlspecialchars($row['ScopeID'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ScopeID)%%>', urlencode($urow['ScopeID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(ParentID)%%>', htmlspecialchars($row['ParentID'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ParentID)%%>', urlencode($urow['ParentID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(AdminLevel)%%>', htmlspecialchars($row['AdminLevel'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(AdminLevel)%%>', urlencode($urow['AdminLevel']), $templateCode);
		$templateCode=str_replace('<%%VALUE(CreationDate)%%>', @date('m/d/Y', @strtotime(htmlspecialchars($row['CreationDate'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(CreationDate)%%>', urlencode(@date('m/d/Y', @strtotime(htmlspecialchars($urow['CreationDate'], ENT_QUOTES)))), $templateCode);
		$templateCode=str_replace('<%%VALUE(ExpirationDate)%%>', @date('m/d/Y', @strtotime(htmlspecialchars($row['ExpirationDate'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ExpirationDate)%%>', urlencode(@date('m/d/Y', @strtotime(htmlspecialchars($urow['ExpirationDate'], ENT_QUOTES)))), $templateCode);
		$templateCode=str_replace('<%%VALUE(LastAccessDate)%%>', @date('m/d/Y', @strtotime(htmlspecialchars($row['LastAccessDate'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(LastAccessDate)%%>', urlencode(@date('m/d/Y', @strtotime(htmlspecialchars($urow['LastAccessDate'], ENT_QUOTES)))), $templateCode);
		$templateCode=str_replace('<%%CHECKED(Online)%%>', ($row['Online'] ? "checked" : ""), $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(ID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(FName)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(FName)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(LName)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(LName)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(Email)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Email)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(PW)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(PW)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(Organization)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Organization)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(ScopeID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ScopeID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(ParentID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ParentID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(AdminLevel)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(AdminLevel)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(CreationDate)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(CreationDate)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(ExpirationDate)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ExpirationDate)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(LastAccessDate)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(LastAccessDate)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%CHECKED(Online)%%>', '', $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans){
		$templateCode=str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode=str_replace('<%%', '<!-- ', $templateCode);
	$templateCode=str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if($_POST['dvprint_x']==''){
		$templateCode.="\n\n<script>jQuery(function(){\n";
		$arrTables=getTableList();
		foreach($arrTables as $name => $caption){
			$templateCode .= "\tjQuery('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\tjQuery('#xs_{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\tjQuery('[id^=\"{$name}_plink\"]').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id){
			$templateCode.="\n\tif(document.getElementById('EmailEdit')){ document.getElementById('EmailEdit').style.display='inline'; }";
			$templateCode.="\n\tif(document.getElementById('EmailEditLink')){ document.getElementById('EmailEditLink').style.display='none'; }";
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode.="<script>";
	$templateCode.="document.observe('dom:loaded', function() {";


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode=preg_replace('/blank.gif" rel="lightbox\[.*?\]"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	// hook: tblContributors_dv
	if(function_exists('tblContributors_dv')){
		$args=array();
		tblContributors_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>