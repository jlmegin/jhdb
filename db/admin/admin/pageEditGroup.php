<?php
	$currDir=dirname(__FILE__);
	require("$currDir/incCommon.php");

	// get groupID of anonymous group
	$anonGroupID=sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'");

	// request to save changes?
	if($_POST['saveChanges']!=''){
		// validate data
		$name=makeSafe($_POST['name']);
		$description=makeSafe($_POST['description']);
		switch($_POST['visitorSignup']){
			case 0:
				$allowSignup=0;
				$needsApproval=1;
				break;
			case 2:
				$allowSignup=1;
				$needsApproval=0;
				break;
			default:
				$allowSignup=1;
				$needsApproval=1;
		}
		###############################
		$tblContributors_insert=checkPermissionVal('tblContributors_insert');
		$tblContributors_view=checkPermissionVal('tblContributors_view');
		$tblContributors_edit=checkPermissionVal('tblContributors_edit');
		$tblContributors_delete=checkPermissionVal('tblContributors_delete');
		###############################
		$tblEntities_insert=checkPermissionVal('tblEntities_insert');
		$tblEntities_view=checkPermissionVal('tblEntities_view');
		$tblEntities_edit=checkPermissionVal('tblEntities_edit');
		$tblEntities_delete=checkPermissionVal('tblEntities_delete');
		###############################
		$tblGalleries_insert=checkPermissionVal('tblGalleries_insert');
		$tblGalleries_view=checkPermissionVal('tblGalleries_view');
		$tblGalleries_edit=checkPermissionVal('tblGalleries_edit');
		$tblGalleries_delete=checkPermissionVal('tblGalleries_delete');
		###############################
		$tblAccessions_insert=checkPermissionVal('tblAccessions_insert');
		$tblAccessions_view=checkPermissionVal('tblAccessions_view');
		$tblAccessions_edit=checkPermissionVal('tblAccessions_edit');
		$tblAccessions_delete=checkPermissionVal('tblAccessions_delete');
		###############################
		$tblGalleryItems_insert=checkPermissionVal('tblGalleryItems_insert');
		$tblGalleryItems_view=checkPermissionVal('tblGalleryItems_view');
		$tblGalleryItems_edit=checkPermissionVal('tblGalleryItems_edit');
		$tblGalleryItems_delete=checkPermissionVal('tblGalleryItems_delete');
		###############################
		$tblContentTypes_insert=checkPermissionVal('tblContentTypes_insert');
		$tblContentTypes_view=checkPermissionVal('tblContentTypes_view');
		$tblContentTypes_edit=checkPermissionVal('tblContentTypes_edit');
		$tblContentTypes_delete=checkPermissionVal('tblContentTypes_delete');
		###############################
		$tblSubGalleryTypes_insert=checkPermissionVal('tblSubGalleryTypes_insert');
		$tblSubGalleryTypes_view=checkPermissionVal('tblSubGalleryTypes_view');
		$tblSubGalleryTypes_edit=checkPermissionVal('tblSubGalleryTypes_edit');
		$tblSubGalleryTypes_delete=checkPermissionVal('tblSubGalleryTypes_delete');
		###############################
		$tblSources_insert=checkPermissionVal('tblSources_insert');
		$tblSources_view=checkPermissionVal('tblSources_view');
		$tblSources_edit=checkPermissionVal('tblSources_edit');
		$tblSources_delete=checkPermissionVal('tblSources_delete');
		###############################
		$tblTemplates_insert=checkPermissionVal('tblTemplates_insert');
		$tblTemplates_view=checkPermissionVal('tblTemplates_view');
		$tblTemplates_edit=checkPermissionVal('tblTemplates_edit');
		$tblTemplates_delete=checkPermissionVal('tblTemplates_delete');
		###############################
		$tblNavMenu_insert=checkPermissionVal('tblNavMenu_insert');
		$tblNavMenu_view=checkPermissionVal('tblNavMenu_view');
		$tblNavMenu_edit=checkPermissionVal('tblNavMenu_edit');
		$tblNavMenu_delete=checkPermissionVal('tblNavMenu_delete');
		###############################
		$tblUploadLogs_insert=checkPermissionVal('tblUploadLogs_insert');
		$tblUploadLogs_view=checkPermissionVal('tblUploadLogs_view');
		$tblUploadLogs_edit=checkPermissionVal('tblUploadLogs_edit');
		$tblUploadLogs_delete=checkPermissionVal('tblUploadLogs_delete');
		###############################
		$tblSidebarEntries_insert=checkPermissionVal('tblSidebarEntries_insert');
		$tblSidebarEntries_view=checkPermissionVal('tblSidebarEntries_view');
		$tblSidebarEntries_edit=checkPermissionVal('tblSidebarEntries_edit');
		$tblSidebarEntries_delete=checkPermissionVal('tblSidebarEntries_delete');
		###############################
		$tblKeywords_insert=checkPermissionVal('tblKeywords_insert');
		$tblKeywords_view=checkPermissionVal('tblKeywords_view');
		$tblKeywords_edit=checkPermissionVal('tblKeywords_edit');
		$tblKeywords_delete=checkPermissionVal('tblKeywords_delete');
		###############################

		// new group or old?
		if($_POST['groupID']==''){ // new group
			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name'")){
				echo "<div class=\"alert alert-danger\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// add group
			sql("insert into membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval'", $eo);

			// get new groupID
			$groupID=db_insert_id(db_link());

		}else{ // old group
			// validate groupID
			$groupID=intval($_POST['groupID']);

			if($groupID==$anonGroupID){
				$name=$adminConfig['anonymousGroup'];
				$allowSignup=0;
				$needsApproval=0;
			}

			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name' and groupID!='$groupID'")){
				echo "<div class=\"alert alert-danger\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// update group
			sql("update membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval' where groupID='$groupID'", $eo);

			// reset then add group permissions
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblContributors'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblEntities'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblGalleries'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblAccessions'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblGalleryItems'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblContentTypes'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblSubGalleryTypes'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblSources'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblTemplates'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblNavMenu'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblUploadLogs'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblSidebarEntries'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='tblKeywords'", $eo);
		}

		// add group permissions
		if($groupID){
			// table 'tblContributors'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblContributors', allowInsert='$tblContributors_insert', allowView='$tblContributors_view', allowEdit='$tblContributors_edit', allowDelete='$tblContributors_delete'", $eo);
			// table 'tblEntities'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblEntities', allowInsert='$tblEntities_insert', allowView='$tblEntities_view', allowEdit='$tblEntities_edit', allowDelete='$tblEntities_delete'", $eo);
			// table 'tblGalleries'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblGalleries', allowInsert='$tblGalleries_insert', allowView='$tblGalleries_view', allowEdit='$tblGalleries_edit', allowDelete='$tblGalleries_delete'", $eo);
			// table 'tblAccessions'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblAccessions', allowInsert='$tblAccessions_insert', allowView='$tblAccessions_view', allowEdit='$tblAccessions_edit', allowDelete='$tblAccessions_delete'", $eo);
			// table 'tblGalleryItems'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblGalleryItems', allowInsert='$tblGalleryItems_insert', allowView='$tblGalleryItems_view', allowEdit='$tblGalleryItems_edit', allowDelete='$tblGalleryItems_delete'", $eo);
			// table 'tblContentTypes'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblContentTypes', allowInsert='$tblContentTypes_insert', allowView='$tblContentTypes_view', allowEdit='$tblContentTypes_edit', allowDelete='$tblContentTypes_delete'", $eo);
			// table 'tblSubGalleryTypes'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblSubGalleryTypes', allowInsert='$tblSubGalleryTypes_insert', allowView='$tblSubGalleryTypes_view', allowEdit='$tblSubGalleryTypes_edit', allowDelete='$tblSubGalleryTypes_delete'", $eo);
			// table 'tblSources'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblSources', allowInsert='$tblSources_insert', allowView='$tblSources_view', allowEdit='$tblSources_edit', allowDelete='$tblSources_delete'", $eo);
			// table 'tblTemplates'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblTemplates', allowInsert='$tblTemplates_insert', allowView='$tblTemplates_view', allowEdit='$tblTemplates_edit', allowDelete='$tblTemplates_delete'", $eo);
			// table 'tblNavMenu'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblNavMenu', allowInsert='$tblNavMenu_insert', allowView='$tblNavMenu_view', allowEdit='$tblNavMenu_edit', allowDelete='$tblNavMenu_delete'", $eo);
			// table 'tblUploadLogs'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblUploadLogs', allowInsert='$tblUploadLogs_insert', allowView='$tblUploadLogs_view', allowEdit='$tblUploadLogs_edit', allowDelete='$tblUploadLogs_delete'", $eo);
			// table 'tblSidebarEntries'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblSidebarEntries', allowInsert='$tblSidebarEntries_insert', allowView='$tblSidebarEntries_view', allowEdit='$tblSidebarEntries_edit', allowDelete='$tblSidebarEntries_delete'", $eo);
			// table 'tblKeywords'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='tblKeywords', allowInsert='$tblKeywords_insert', allowView='$tblKeywords_view', allowEdit='$tblKeywords_edit', allowDelete='$tblKeywords_delete'", $eo);
		}

		// redirect to group editing page
		redirect("admin/pageEditGroup.php?groupID=$groupID");

	}elseif($_GET['groupID']!=''){
		// we have an edit request for a group
		$groupID=intval($_GET['groupID']);
	}

	include("$currDir/incHeader.php");

	if($groupID!=''){
		// fetch group data to fill in the form below
		$res=sql("select * from membership_groups where groupID='$groupID'", $eo);
		if($row=db_fetch_assoc($res)){
			// get group data
			$name=$row['name'];
			$description=$row['description'];
			$visitorSignup=($row['allowSignup']==1 && $row['needsApproval']==1 ? 1 : ($row['allowSignup']==1 ? 2 : 0));

			// get group permissions for each table
			$res=sql("select * from membership_grouppermissions where groupID='$groupID'", $eo);
			while($row=db_fetch_assoc($res)){
				$tableName=$row['tableName'];
				$vIns=$tableName."_insert";
				$vUpd=$tableName."_edit";
				$vDel=$tableName."_delete";
				$vVue=$tableName."_view";
				$$vIns=$row['allowInsert'];
				$$vUpd=$row['allowEdit'];
				$$vDel=$row['allowDelete'];
				$$vVue=$row['allowView'];
			}
		}else{
			// no such group exists
			echo "<div class=\"alert alert-danger\">Error: Group not found!</div>";
			$groupID=0;
		}
	}
?>
<div class="page-header"><h1><?php echo ($groupID ? "Edit Group '$name'" : "Add New Group"); ?></h1></div>
<?php if($anonGroupID==$groupID){ ?>
	<div class="alert alert-warning">Attention! This is the anonymous group.</div>
<?php } ?>
<input type="checkbox" id="showToolTips" value="1" checked><label for="showToolTips">Show tool tips as mouse moves over options</label>
<form method="post" action="pageEditGroup.php">
	<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
	<div class="table-responsive"><table class="table table-striped">
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption">Group name</div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="name" <?php echo ($anonGroupID==$groupID ? "readonly" : ""); ?> value="<?php echo $name; ?>" size="20" class="formTextBox">
				<br />
				<?php if($anonGroupID==$groupID){ ?>
					The name of the anonymous group is read-only here.
				<?php }else{ ?>
					If you name the group '<?php echo $adminConfig['anonymousGroup']; ?>', it will be considered the anonymous group<br />
					that defines the permissions of guest visitors that do not log into the system.
				<?php } ?>
				</td>
			</tr>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Description</div>
				</td>
			<td align="left" class="tdFormInput">
				<textarea name="description" cols="50" rows="5" class="formTextBox"><?php echo $description; ?></textarea>
				</td>
			</tr>
		<?php if($anonGroupID!=$groupID){ ?>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Allow visitors to sign up?</div>
				</td>
			<td align="left" class="tdFormInput">
				<?php
					echo htmlRadioGroup(
						"visitorSignup",
						array(0, 1, 2),
						array(
							"No. Only the admin can add users.",
							"Yes, and the admin must approve them.",
							"Yes, and automatically approve them."
						),
						($groupID ? $visitorSignup : $adminConfig['defaultSignUp'])
					);
				?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		<tr>
			<td colspan="2" class="tdFormHeader">
				<table class="table table-striped">
					<tr>
						<td class="tdFormHeader" colspan="5"><h2>Table permissions for this group</h2></td>
						</tr>
					<?php
						// permissions arrays common to the radio groups below
						$arrPermVal=array(0, 1, 2, 3);
						$arrPermText=array("No", "Owner", "Group", "All");
					?>
					<tr>
						<td class="tdHeader"><div class="ColCaption">Table</div></td>
						<td class="tdHeader"><div class="ColCaption">Insert</div></td>
						<td class="tdHeader"><div class="ColCaption">View</div></td>
						<td class="tdHeader"><div class="ColCaption">Edit</div></td>
						<td class="tdHeader"><div class="ColCaption">Delete</div></td>
						</tr>
				<!-- tblContributors table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblContributors</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblContributors_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblContributors_insert" value="1" <?php echo ($tblContributors_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblContributors_view", $arrPermVal, $arrPermText, $tblContributors_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblContributors_edit", $arrPermVal, $arrPermText, $tblContributors_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblContributors_delete", $arrPermVal, $arrPermText, $tblContributors_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblEntities table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblEntities</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblEntities_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblEntities_insert" value="1" <?php echo ($tblEntities_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblEntities_view", $arrPermVal, $arrPermText, $tblEntities_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblEntities_edit", $arrPermVal, $arrPermText, $tblEntities_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblEntities_delete", $arrPermVal, $arrPermText, $tblEntities_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblGalleries table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblGalleries</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblGalleries_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblGalleries_insert" value="1" <?php echo ($tblGalleries_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblGalleries_view", $arrPermVal, $arrPermText, $tblGalleries_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblGalleries_edit", $arrPermVal, $arrPermText, $tblGalleries_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblGalleries_delete", $arrPermVal, $arrPermText, $tblGalleries_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblAccessions table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblAccessions</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblAccessions_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblAccessions_insert" value="1" <?php echo ($tblAccessions_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblAccessions_view", $arrPermVal, $arrPermText, $tblAccessions_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblAccessions_edit", $arrPermVal, $arrPermText, $tblAccessions_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblAccessions_delete", $arrPermVal, $arrPermText, $tblAccessions_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblGalleryItems table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblGalleryItems</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblGalleryItems_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblGalleryItems_insert" value="1" <?php echo ($tblGalleryItems_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblGalleryItems_view", $arrPermVal, $arrPermText, $tblGalleryItems_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblGalleryItems_edit", $arrPermVal, $arrPermText, $tblGalleryItems_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblGalleryItems_delete", $arrPermVal, $arrPermText, $tblGalleryItems_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblContentTypes table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblContentTypes</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblContentTypes_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblContentTypes_insert" value="1" <?php echo ($tblContentTypes_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblContentTypes_view", $arrPermVal, $arrPermText, $tblContentTypes_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblContentTypes_edit", $arrPermVal, $arrPermText, $tblContentTypes_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblContentTypes_delete", $arrPermVal, $arrPermText, $tblContentTypes_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblSubGalleryTypes table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblSubGalleryTypes</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblSubGalleryTypes_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblSubGalleryTypes_insert" value="1" <?php echo ($tblSubGalleryTypes_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSubGalleryTypes_view", $arrPermVal, $arrPermText, $tblSubGalleryTypes_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSubGalleryTypes_edit", $arrPermVal, $arrPermText, $tblSubGalleryTypes_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSubGalleryTypes_delete", $arrPermVal, $arrPermText, $tblSubGalleryTypes_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblSources table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblSources</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblSources_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblSources_insert" value="1" <?php echo ($tblSources_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSources_view", $arrPermVal, $arrPermText, $tblSources_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSources_edit", $arrPermVal, $arrPermText, $tblSources_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSources_delete", $arrPermVal, $arrPermText, $tblSources_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblTemplates table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblTemplates</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblTemplates_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblTemplates_insert" value="1" <?php echo ($tblTemplates_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblTemplates_view", $arrPermVal, $arrPermText, $tblTemplates_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblTemplates_edit", $arrPermVal, $arrPermText, $tblTemplates_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblTemplates_delete", $arrPermVal, $arrPermText, $tblTemplates_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblNavMenu table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblNavMenu</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblNavMenu_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblNavMenu_insert" value="1" <?php echo ($tblNavMenu_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblNavMenu_view", $arrPermVal, $arrPermText, $tblNavMenu_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblNavMenu_edit", $arrPermVal, $arrPermText, $tblNavMenu_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblNavMenu_delete", $arrPermVal, $arrPermText, $tblNavMenu_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblUploadLogs table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblUploadLogs</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblUploadLogs_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblUploadLogs_insert" value="1" <?php echo ($tblUploadLogs_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblUploadLogs_view", $arrPermVal, $arrPermText, $tblUploadLogs_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblUploadLogs_edit", $arrPermVal, $arrPermText, $tblUploadLogs_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblUploadLogs_delete", $arrPermVal, $arrPermText, $tblUploadLogs_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblSidebarEntries table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblSidebarEntries</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblSidebarEntries_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblSidebarEntries_insert" value="1" <?php echo ($tblSidebarEntries_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSidebarEntries_view", $arrPermVal, $arrPermText, $tblSidebarEntries_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSidebarEntries_edit", $arrPermVal, $arrPermText, $tblSidebarEntries_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblSidebarEntries_delete", $arrPermVal, $arrPermText, $tblSidebarEntries_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- tblKeywords table -->
					<tr>
						<td class="tdCaptionCell" valign="top">tblKeywords</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(tblKeywords_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="tblKeywords_insert" value="1" <?php echo ($tblKeywords_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblKeywords_view", $arrPermVal, $arrPermText, $tblKeywords_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblKeywords_edit", $arrPermVal, $arrPermText, $tblKeywords_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("tblKeywords_delete", $arrPermVal, $arrPermText, $tblKeywords_delete, "highlight");
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		</table></div>
</form>


<?php
	include("$currDir/incFooter.php");
?>