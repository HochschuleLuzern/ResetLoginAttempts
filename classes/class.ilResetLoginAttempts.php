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

require_once './Customizing/global/plugins/Services/Cron/CronHook/ResetLoginAttempts/classes/class.ilResetLoginAttemptsResult.php';

/**
 * Class ilResetLoginAttempts
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */

class ilResetLoginAttempts extends ilCronJob {
	
	const ID = "crreset_rs";
	
	private $cp;
	private $settings;

	public function __construct() {
	    $this->cp = new ilResetLoginAttemptsPlugin();
	    $this->settings = new ilSetting("crreset");
	}
	
	public function getId() {
		return self::ID;
	}
	
	/**
	 * @return bool
	 */
	public function hasAutoActivation() {
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasFlexibleSchedule() {
		return true;
	}
	
	/**
	 * @return int
	 */
	public function getDefaultScheduleType() {
		return self::SCHEDULE_TYPE_IN_MINUTES;
	}
	
	/**
	 * @return int
	 */
	public function getDefaultScheduleValue() {
		return 5;
	}
	
	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->cp->txt("title");
	}
	
	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->cp->txt("description");
	}
	
	/**
	 * Defines whether or not a cron job can be started manually
	 * @return bool
	 */
	public function isManuallyExecutable()
	{
		return false;
	}
	
	public function hasCustomSettings()
	{
		return true;
	}
	
	public function run() {		
		try {
			global $DIC;
			$db = $DIC->database();
			$security = ilSecuritySettings::_getInstance();
			
			$schedule = $this->getScheduleValue();
			$lockout_duration = $this->settings->get('crreset_lockout_duration', '30');
			
			/**
			 * To ensure that nobody is locked out longer than the defined lockout time, we reset the
			 * settings for lockouts longer than lockout_duration - cron_schedule_value
			 */
			$min_lockout = $lockout_duration - $schedule;
						
			$db->manipulate('UPDATE usr_data SET login_attempts = 0, active = 1 WHERE login_attempts >= '
			    .$db->quote($security->getLoginMaxAttempts(), 'integer').' AND inactivation_date <= '
			    .$db->quote(date('Y-m-d H:i:s', strtotime('-'.$min_lockout.' minutes')), 'datetime'));
		    
			return new ilResetLoginAttemptsResult(ilResetLoginAttemptsResult::STATUS_OK, 'Cron job terminated successfully.');
		} catch (Exception $e) {
			return new ilResetLoginAttemptsResult(ilResetLoginAttemptsResult::STATUS_CRASHED, 'Cron job crashed: ' . $e->getMessage());
		}
	}
	
	/**
	 * Defines the custom settings form and returns it to plugin slot
	 *
	 * @param ilPropertyFormGUI $a_form
	 */
	public function addCustomSettingsToForm(ilPropertyFormGUI $a_form)
	{
		include_once 'Services/Form/classes/class.ilNumberInputGUI.php';
		$ws_item = new ilNumberInputGUI(
				$this->cp->txt('lockout_duration'),
				'crreset_lockout_duration'
				);
		$ws_item->setInfo($this->cp->txt('lockout_duration_desc'));
		$ws_item->allowDecimals(false);
		$ws_item->setMinValue(0);
		$ws_item->setRequired(true);
		$ws_item->setValue($this->settings->get('crreset_lockout_duration', '30'));
		$a_form->addItem($ws_item);
	}
	
	/**
	 * Saves the custom settings values
	 *
	 * @param ilPropertyFormGUI $a_form
	 * @return boolean
	 */
	public function saveCustomSettings(ilPropertyFormGUI $a_form)
	{
		if ($_POST['crreset_lockout_duration'] != null) {
			$this->settings->set('crreset_lockout_duration', $_POST['crreset_lockout_duration']);
		}
		
		return true;
	}
}