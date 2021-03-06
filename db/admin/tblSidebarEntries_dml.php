<?php

// Data functions for table tblSidebarEntries

// This script and data application were generated by AppGini 5.30
// Download AppGini for free from http://bigprof.com/appgini/download/

function tblSidebarEntries_insert(){
	global $Translation;

	if($_GET['insert_x']!=''){$_POST=$_GET;}

	// mm: can member insert record?
	$arrPerm=getTablePermissions('tblSidebarEntries');
	if(!$arrPerm[1]){
		return false;
	}

	$data['Title'] = makeSafe($_POST['Title']);
		if($data['Title'] == empty_lookup_value){ $data['Title'] = ''; }
	$data['Caption'] = makeSafe($_POST['Caption']);
		if($data['Caption'] == empty_lookup_value){ $data['Caption'] = ''; }
	$data['AccessionID'] = makeSafe($_POST['AccessionID']);
		if($data['AccessionID'] == empty_lookup_value){ $data['AccessionID'] = ''; }
	$data['LinkURL'] = makeSafe($_POST['LinkURL']);
		if($data['LinkURL'] == empty_lookup_value){ $data['LinkURL'] = ''; }
	$data['ThumbnailID'] = makeSafe($_POST['ThumbnailID']);
		if($data['ThumbnailID'] == empty_lookup_value){ $data['ThumbnailID'] = ''; }
	$data['NewDateExpiration'] = intval($_POST['NewDateExpirationYear']) . '-' . intval($_POST['NewDateExpirationMonth']) . '-' . intval($_POST['NewDateExpirationDay']);
	$data['NewDateExpiration'] = parseMySQLDate($data['NewDateExpiration'], '');
	$data['Group'] = makeSafe($_POST['Group']);
		if($data['Group'] == empty_lookup_value){ $data['Group'] = ''; }
	$data['SortWithinGroup'] = makeSafe($_POST['SortWithinGroup']);
		if($data['SortWithinGroup'] == empty_lookup_value){ $data['SortWithinGroup'] = ''; }

	// hook: tblSidebarEntries_before_insert
	if(function_exists('tblSidebarEntries_before_insert')){
		$args=array();
		if(!tblSidebarEntries_before_insert($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('insert into `tblSidebarEntries` set       `Title`=' . (($data['Title'] !== '' && $data['Title'] !== NULL) ? "'{$data['Title']}'" : 'NULL') . ', `Caption`=' . (($data['Caption'] !== '' && $data['Caption'] !== NULL) ? "'{$data['Caption']}'" : 'NULL') . ', `AccessionID`=' . (($data['AccessionID'] !== '' && $data['AccessionID'] !== NULL) ? "'{$data['AccessionID']}'" : 'NULL') . ', `LinkURL`=' . (($data['LinkURL'] !== '' && $data['LinkURL'] !== NULL) ? "'{$data['LinkURL']}'" : 'NULL') . ', `ThumbnailID`=' . (($data['ThumbnailID'] !== '' && $data['ThumbnailID'] !== NULL) ? "'{$data['ThumbnailID']}'" : 'NULL') . ', `NewDateExpiration`=' . (($data['NewDateExpiration'] !== '' && $data['NewDateExpiration'] !== NULL) ? "'{$data['NewDateExpiration']}'" : 'NULL') . ', `Group`=' . (($data['Group'] !== '' && $data['Group'] !== NULL) ? "'{$data['Group']}'" : 'NULL') . ', `SortWithinGroup`=' . (($data['SortWithinGroup'] !== '' && $data['SortWithinGroup'] !== NULL) ? "'{$data['SortWithinGroup']}'" : 'NULL'), $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"tblSidebarEntries_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID=db_insert_id(db_link());

	// hook: tblSidebarEntries_after_insert
	if(function_exists('tblSidebarEntries_after_insert')){
		$res = sql("select * from `tblSidebarEntries` where `ID`='" . makeSafe($recID) . "' limit 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID);
		$args=array();
		if(!tblSidebarEntries_after_insert($data, getMemberInfo(), $args)){ return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID); }
	}

	// mm: save ownership data
	sql("insert into membership_userrecords set tableName='tblSidebarEntries', pkValue='$recID', memberID='".getLoggedMemberID()."', dateAdded='".time()."', dateUpdated='".time()."', groupID='".getLoggedGroupID()."'", $eo);

	return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID);
}

function tblSidebarEntries_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('tblSidebarEntries');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='tblSidebarEntries' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='tblSidebarEntries' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: tblSidebarEntries_before_delete
	if(function_exists('tblSidebarEntries_before_delete')){
		$args=array();
		if(!tblSidebarEntries_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	sql("delete from `tblSidebarEntries` where `ID`='$selected_id'", $eo);

	// hook: tblSidebarEntries_after_delete
	if(function_exists('tblSidebarEntries_after_delete')){
		$args=array();
		tblSidebarEntries_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='tblSidebarEntries' and pkValue='$selected_id'", $eo);
}

function tblSidebarEntries_update($selected_id){
	global $Translation;

	if($_GET['update_x']!=''){$_POST=$_GET;}

	// mm: can member edit record?
	$arrPerm=getTablePermissions('tblSidebarEntries');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='tblSidebarEntries' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='tblSidebarEntries' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['Title'] = makeSafe($_POST['Title']);
		if($data['Title'] == empty_lookup_value){ $data['Title'] = ''; }
	$data['Caption'] = makeSafe($_POST['Caption']);
		if($data['Caption'] == empty_lookup_value){ $data['Caption'] = ''; }
	$data['AccessionID'] = makeSafe($_POST['AccessionID']);
		if($data['AccessionID'] == empty_lookup_value){ $data['AccessionID'] = ''; }
	$data['LinkURL'] = makeSafe($_POST['LinkURL']);
		if($data['LinkURL'] == empty_lookup_value){ $data['LinkURL'] = ''; }
	$data['ThumbnailID'] = makeSafe($_POST['ThumbnailID']);
		if($data['ThumbnailID'] == empty_lookup_value){ $data['ThumbnailID'] = ''; }
	$data['NewDateExpiration'] = intval($_POST['NewDateExpirationYear']) . '-' . intval($_POST['NewDateExpirationMonth']) . '-' . intval($_POST['NewDateExpirationDay']);
	$data['NewDateExpiration'] = parseMySQLDate($data['NewDateExpiration'], '');
	$data['Group'] = makeSafe($_POST['Group']);
		if($data['Group'] == empty_lookup_value){ $data['Group'] = ''; }
	$data['SortWithinGroup'] = makeSafe($_POST['SortWithinGroup']);
		if($data['SortWithinGroup'] == empty_lookup_value){ $data['SortWithinGroup'] = ''; }
	$data['selectedID']=makeSafe($selected_id);

	// hook: tblSidebarEntries_before_update
	if(function_exists('tblSidebarEntries_before_update')){
		$args=array();
		if(!tblSidebarEntries_before_update($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('update `tblSidebarEntries` set       `Title`=' . (($data['Title'] !== '' && $data['Title'] !== NULL) ? "'{$data['Title']}'" : 'NULL') . ', `Caption`=' . (($data['Caption'] !== '' && $data['Caption'] !== NULL) ? "'{$data['Caption']}'" : 'NULL') . ', `AccessionID`=' . (($data['AccessionID'] !== '' && $data['AccessionID'] !== NULL) ? "'{$data['AccessionID']}'" : 'NULL') . ', `LinkURL`=' . (($data['LinkURL'] !== '' && $data['LinkURL'] !== NULL) ? "'{$data['LinkURL']}'" : 'NULL') . ', `ThumbnailID`=' . (($data['ThumbnailID'] !== '' && $data['ThumbnailID'] !== NULL) ? "'{$data['ThumbnailID']}'" : 'NULL') . ', `NewDateExpiration`=' . (($data['NewDateExpiration'] !== '' && $data['NewDateExpiration'] !== NULL) ? "'{$data['NewDateExpiration']}'" : 'NULL') . ', `Group`=' . (($data['Group'] !== '' && $data['Group'] !== NULL) ? "'{$data['Group']}'" : 'NULL') . ', `SortWithinGroup`=' . (($data['SortWithinGroup'] !== '' && $data['SortWithinGroup'] !== NULL) ? "'{$data['SortWithinGroup']}'" : 'NULL') . " where `ID`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="tblSidebarEntries_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: tblSidebarEntries_after_update
	if(function_exists('tblSidebarEntries_after_update')){
		$res = sql("SELECT * FROM `tblSidebarEntries` WHERE `ID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['ID'];
		$args = array();
		if(!tblSidebarEntries_after_update($data, getMemberInfo(), $args)){ return; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='tblSidebarEntries' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function tblSidebarEntries_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('tblSidebarEntries');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	$AllowInsert = ($arrPerm[1] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}

	$filterer_AccessionID = thisOr(undo_magic_quotes($_REQUEST['filterer_AccessionID']), '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: AccessionID
	$combo_AccessionID = new DataCombo;
	// combobox: NewDateExpiration
	$combo_NewDateExpiration = new DateCombo;
	$combo_NewDateExpiration->DateFormat = "mdy";
	$combo_NewDateExpiration->MinYear = 1900;
	$combo_NewDateExpiration->MaxYear = 2100;
	$combo_NewDateExpiration->DefaultDate = parseMySQLDate('', '');
	$combo_NewDateExpiration->MonthNames = $Translation['month names'];
	$combo_NewDateExpiration->NamePrefix = 'NewDateExpiration';

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='tblSidebarEntries' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='tblSidebarEntries' and pkValue='".makeSafe($selected_id)."'");
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

		$res = sql("select * from `tblSidebarEntries` where `ID`='".makeSafe($selected_id)."'", $eo);
		$row = db_fetch_array($res);
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
		$combo_AccessionID->SelectedData = $row['AccessionID'];
		$combo_NewDateExpiration->DefaultDate = $row['NewDateExpiration'];
	}else{
		$combo_AccessionID->SelectedData = $filterer_AccessionID;
	}
	$combo_AccessionID->HTML = '<span id="AccessionID-container' . $rnd1 . '"></span><input type="hidden" name="AccessionID" id="AccessionID' . $rnd1 . '">';
	$combo_AccessionID->MatchText = '<span id="AccessionID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="AccessionID" id="AccessionID' . $rnd1 . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		var current_AccessionID__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['AccessionID'] : $filterer_AccessionID); ?>"};
		
		jQuery(function() {
			AccessionID_reload__RAND__();
		});
		function AccessionID_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			jQuery("#AccessionID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					jQuery.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: current_AccessionID__RAND__.value, t: 'tblSidebarEntries', f: 'AccessionID' }
					}).done(function(resp){
						c({
							id: resp.results[0].id,
							text: resp.results[0].text
						});
						jQuery('[name="AccessionID"]').val(resp.results[0].id);
						jQuery('[id=AccessionID-container-readonly__RAND__]').html('<span id="AccessionID-match-text">' + resp.results[0].text + '</span>');


						if(typeof(AccessionID_update_autofills__RAND__) == 'function') AccessionID_update_autofills__RAND__();
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
					data: function(term, page){ return { s: term, p: page, t: 'tblSidebarEntries', f: 'AccessionID' }; },
					results: function(resp, page){ return resp; }
				}
			}).on('change', function(e){
				current_AccessionID__RAND__.value = e.added.id;
				current_AccessionID__RAND__.text = e.added.text;
				jQuery('[name="AccessionID"]').val(e.added.id);


				if(typeof(AccessionID_update_autofills__RAND__) == 'function') AccessionID_update_autofills__RAND__();
			});
		<?php }else{ ?>

			jQuery.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: current_AccessionID__RAND__.value, t: 'tblSidebarEntries', f: 'AccessionID' }
			}).done(function(resp){
				jQuery('[id=AccessionID-container__RAND__], [id=AccessionID-container-readonly__RAND__]').html('<span id="AccessionID-match-text">' + resp.results[0].text + '</span>');

				if(typeof(AccessionID_update_autofills__RAND__) == 'function') AccessionID_update_autofills__RAND__();
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
		$templateCode = @file_get_contents('./templates/tblSidebarEntries_templateDVP.html');
	}else{
		$templateCode = @file_get_contents('./templates/tblSidebarEntries_templateDV.html');
	}

	// process form title
	$templateCode=str_replace('<%%DETAIL_VIEW_TITLE%%>', 'TblSidebarEntrie details', $templateCode);
	$templateCode=str_replace('<%%RND1%%>', $rnd1, $templateCode);
	// process buttons
	if($AllowInsert){
		if(!$selected_id) $templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return tblSidebarEntries_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return tblSidebarEntries_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
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
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<button tabindex="2" type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return tblSidebarEntries_validateData();"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
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
		$jsReadOnly .= "\tjQuery('#Title').replaceWith('<p class=\"form-control-static\" id=\"Title\">' + (jQuery('#Title').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#Caption').replaceWith('<p class=\"form-control-static\" id=\"Caption\">' + (jQuery('#Caption').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#AccessionID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#AccessionID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#LinkURL').replaceWith('<p class=\"form-control-static\" id=\"LinkURL\">' + (jQuery('#LinkURL').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#ThumbnailID').replaceWith('<p class=\"form-control-static\" id=\"ThumbnailID\">' + (jQuery('#ThumbnailID').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#NewDateExpiration').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#NewDateExpirationDay, #NewDateExpirationMonth, #NewDateExpirationYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#Group').replaceWith('<p class=\"form-control-static\" id=\"Group\">' + (jQuery('#Group').val() || '') + '</p>');\n";
		$jsReadOnly .= "\tjQuery('#SortWithinGroup').replaceWith('<p class=\"form-control-static\" id=\"SortWithinGroup\">' + (jQuery('#SortWithinGroup').val() || '') + '</p>');\n";

		$noUploads = true;
	}elseif($AllowInsert){
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode=str_replace('<%%COMBO(AccessionID)%%>', $combo_AccessionID->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(AccessionID)%%>', $combo_AccessionID->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(AccessionID)%%>', urlencode($combo_AccessionID->MatchText), $templateCode);
	$templateCode=str_replace('<%%COMBO(NewDateExpiration)%%>', ($selected_id && !$arrPerm[3] ? '<p class="form-control-static">' . $combo_NewDateExpiration->GetHTML(true) . '</p>' : $combo_NewDateExpiration->GetHTML()), $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(NewDateExpiration)%%>', $combo_NewDateExpiration->GetHTML(true), $templateCode);

	// process foreign key links
	if($selected_id){
		$templateCode=str_replace('<%%PLINK(AccessionID)%%>', ($combo_AccessionID->SelectedData ? "<span id=\"tblAccessions_plink1\" class=\"hidden\"><a class=\"btn btn-default\" href=\"tblAccessions_view.php?SelectedID=" . urlencode($combo_AccessionID->SelectedData) . "\"><i class=\"glyphicon glyphicon-search\"></i></a></span>" : ''), $templateCode);
	}

	// process images
	$templateCode=str_replace('<%%UPLOADFILE(ID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Title)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Caption)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(AccessionID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(LinkURL)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(ThumbnailID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(NewDateExpiration)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Group)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(SortWithinGroup)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(ID)%%>', htmlspecialchars($row['ID'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ID)%%>', urlencode($urow['ID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(Title)%%>', htmlspecialchars($row['Title'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Title)%%>', urlencode($urow['Title']), $templateCode);
		$templateCode=str_replace('<%%VALUE(Caption)%%>', htmlspecialchars($row['Caption'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Caption)%%>', urlencode($urow['Caption']), $templateCode);
		$templateCode=str_replace('<%%VALUE(AccessionID)%%>', htmlspecialchars($row['AccessionID'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(AccessionID)%%>', urlencode($urow['AccessionID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(LinkURL)%%>', htmlspecialchars($row['LinkURL'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(LinkURL)%%>', urlencode($urow['LinkURL']), $templateCode);
		$templateCode=str_replace('<%%VALUE(ThumbnailID)%%>', htmlspecialchars($row['ThumbnailID'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ThumbnailID)%%>', urlencode($urow['ThumbnailID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(NewDateExpiration)%%>', @date('m/d/Y', @strtotime(htmlspecialchars($row['NewDateExpiration'], ENT_QUOTES))), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(NewDateExpiration)%%>', urlencode(@date('m/d/Y', @strtotime(htmlspecialchars($urow['NewDateExpiration'], ENT_QUOTES)))), $templateCode);
		$templateCode=str_replace('<%%VALUE(Group)%%>', htmlspecialchars($row['Group'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Group)%%>', urlencode($urow['Group']), $templateCode);
		$templateCode=str_replace('<%%VALUE(SortWithinGroup)%%>', htmlspecialchars($row['SortWithinGroup'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(SortWithinGroup)%%>', urlencode($urow['SortWithinGroup']), $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(ID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(Title)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Title)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(Caption)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Caption)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(AccessionID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(AccessionID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(LinkURL)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(LinkURL)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(ThumbnailID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ThumbnailID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(NewDateExpiration)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(NewDateExpiration)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(Group)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Group)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(SortWithinGroup)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(SortWithinGroup)%%>', urlencode(''), $templateCode);
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

	// hook: tblSidebarEntries_dv
	if(function_exists('tblSidebarEntries_dv')){
		$args=array();
		tblSidebarEntries_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>