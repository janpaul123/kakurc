<?php
/*
 * kakurc, room control software
 * Copyright (C) 2010
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();
require_once("init.php"); 

// find the command using the type id
$commands = $settings['menu.types'][$_GET['type']]['commands'];

if (!is_array($commands)) $commands = array($commands);

foreach($commands as $command)
{
	// replace placeholders in the command by using all the data in the options array
	// ie. {id} will be replaced by 1 if 'id' => 1 is in the options array
	foreach($settings['menu.options'][$_GET['option']] as $key => $value) 
	{
		$command = str_replace('{' . $key . '}', $value, $command);
	}
	
	// execute the command
	if (!$settings['general.demo']) exec($command);
}