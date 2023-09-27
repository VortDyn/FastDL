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

global $theme, $userbank;

$sid = (int)$_GET['id'];

// Access on that server?
$servers = $GLOBALS['db']->GetAll("SELECT `server_id`, `srv_group_id` FROM ".DB_PREFIX."_admins_servers_groups WHERE admin_id = ". $userbank->GetAid());
$access = false;
foreach($servers as $server)
{
    if($server['server_id'] == $sid)
    {
        $access = true;
        break;
    }
    if($server['srv_group_id'] > 0)
    {
        $servers_in_group = $GLOBALS['db']->GetAll("SELECT `server_id` FROM ".DB_PREFIX."_servers_groups WHERE group_id = ". (int)$server['srv_group_id']);
        foreach($servers_in_group as $servig)
        {
            if($servig['server_id'] == $sid)
            {
                $access = true;
                break 2;
            }
        }
    }
}

$theme->assign('id', $sid);
$theme->assign('permission_rcon', ($access && $userbank->HasAccess(SM_RCON . SM_ROOT)));
$theme->left_delimiter = '-{';
$theme->right_delimiter = '}-';

$theme->display('page_admin_servers_rcon.tpl');

$theme->left_delimiter = '{';
$theme->right_delimiter = '}';
?>

