<?php 
// *************************************************************************
//  This file is part of SourceBans++.
//
//  Copyright (C) 2014-2016 Sarabveer Singh <me@sarabveer.me>
//
//  SourceBans++ is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, per version 3 of the License.
//
//  SourceBans++ is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with SourceBans++. If not, see <http://www.gnu.org/licenses/>.
//
//  This file is based off work covered by the following copyright(s):  
//
//   SourceBans 1.4.11
//   Copyright (C) 2007-2015 SourceBans Team - Part of GameConnect
//   Licensed under GNU GPL version 3, or later.
//   Page: <http://www.sourcebans.net/> - <https://github.com/GameConnect/sourcebansv1>
//
// *************************************************************************

if(!defined("IN_SB")){echo "Ошибка доступа!";die();} 
global $userbank, $theme;

echo '<div id="admin-page-content">';

// web groups
$web_group_list = $GLOBALS['db']->GetAll("SELECT * FROM `" . DB_PREFIX . "_groups` WHERE type != '3'");
for($i=0;$i<count($web_group_list);$i++)
{
	$web_group_list[$i]['permissions'] = BitToString($web_group_list[$i]['flags'], $web_group_list[$i]['type']);
	$query = $GLOBALS['db']->GetRow("SELECT COUNT(gid) AS cnt FROM `" . DB_PREFIX . "_admins` WHERE gid = '" . $web_group_list[$i]['gid'] . "'");
	$web_group_count[$i] = $query['cnt'];
	$web_group_admins[$i] = $GLOBALS['db']->GetAll("SELECT aid, user, authid FROM `" . DB_PREFIX . "_admins` WHERE gid = '" . $web_group_list[$i]['gid'] . "'");
}

// Server admin groups
$server_admin_group_list = $GLOBALS['db']->GetAll("SELECT * FROM `" . DB_PREFIX . "_srvgroups`") ;
for($i=0;$i<count($server_admin_group_list);$i++)
{
	$server_admin_group_list[$i]['permissions'] = SmFlagsToSb($server_admin_group_list[$i]['flags']);
	$srvGroup = $GLOBALS['db']->qstr($server_admin_group_list[$i]['name']);
	$query = $GLOBALS['db']->GetRow("SELECT COUNT(aid) AS cnt FROM `" . DB_PREFIX . "_admins` WHERE srv_group = $srvGroup;");
	$server_admin_group_count[$i] = $query['cnt'];
	$server_admin_group_admins[$i] = $GLOBALS['db']->GetAll("SELECT aid, user, authid FROM `" . DB_PREFIX . "_admins` WHERE srv_group = $srvGroup;");
	$server_admin_group_overrides[$i] = $GLOBALS['db']->GetAll("SELECT type, name, access FROM `" . DB_PREFIX . "_srvgroups_overrides` WHERE group_id = ?", array($server_admin_group_list[$i]['id']));
}


// server groups
$server_group_list = $GLOBALS['db']->GetAll("SELECT * FROM `" . DB_PREFIX . "_groups` WHERE type = '3'") ;
for($i=0;$i<count($server_group_list);$i++)
{
	$query = $GLOBALS['db']->GetRow("SELECT COUNT(server_id) AS cnt FROM `" . DB_PREFIX . "_servers_groups` WHERE `group_id` = ".  $server_group_list[$i]['gid'] ) ;
	$server_group_count[$i] = $query['cnt'];
	$server_group_list[$i]['servers'] = $GLOBALS['db']->GetAll("SELECT server_id FROM `" . DB_PREFIX . "_servers_groups` WHERE group_id = " . $server_group_list[$i]['gid']);
}



// List Group
echo '<div id="0" style="display:none;">';

	$theme->assign('permission_listgroups', 	$userbank->HasAccess(ADMIN_OWNER|ADMIN_LIST_GROUPS));
	$theme->assign('permission_editgroup',		$userbank->HasAccess(ADMIN_OWNER|ADMIN_EDIT_GROUPS));
	$theme->assign('permission_deletegroup',	$userbank->HasAccess(ADMIN_OWNER|ADMIN_DELETE_GROUPS));
	$theme->assign('permission_editadmin',		$userbank->HasAccess(ADMIN_OWNER|ADMIN_EDIT_ADMINS));
	$theme->assign('web_group_count',			count($web_group_list));
	$theme->assign('web_admins', 				(isset($web_group_count)?$web_group_count:'0'));
	$theme->assign('web_admins_list',			$web_group_admins);
	$theme->assign('web_group_list', 			$web_group_list);
	$theme->assign('server_admin_group_count',	count($server_admin_group_list));
	$theme->assign('server_admins', 			(isset($server_admin_group_count)?$server_admin_group_count:'0'));
	$theme->assign('server_admins_list',		$server_admin_group_admins);
	$theme->assign('server_overrides_list',		$server_admin_group_overrides);
	$theme->assign('server_group_list', 		$server_admin_group_list);
	$theme->assign('server_group_count',		count($server_group_list));
	$theme->assign('server_list', 				$server_group_list);
	$theme->display('page_admin_groups_list.tpl');

echo '</div>';



// Add Groups
echo '<div id="1" style="display:none;">';
	$theme->assign('permission_addgroup', 		$userbank->HasAccess(ADMIN_OWNER|ADMIN_ADD_GROUP));
	$theme->display('page_admin_groups_add.tpl');
echo '</div>';

?>

</div>
