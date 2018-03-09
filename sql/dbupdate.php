<?php
/**
 * Copyright (c) 2017 Hochschule Luzern
 *
 * This file is part of the ResetLoginAttempts-Plugin for ILIAS.

 * NotifyOnCronFailure-Plugin for ILIAS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * NotifyOnCronFailure-Plugin for ILIAS is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with NotifyOnCronFailure-Plugin for ILIAS.  If not,
 * see <http://www.gnu.org/licenses/>.
 */
?>
	 
<#1>

<?php
/**
 * We need reset all the login counters to make sure we don't reactivate any users by accident

 */
	$ilDB->manipulate('UPDATE usr_data SET login_attempts = 0');
?>