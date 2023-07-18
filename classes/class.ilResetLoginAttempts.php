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

require_once './Services/Cron/classes/class.ilCronJob.php';
require_once './Customizing/global/plugins/Services/Cron/CronHook/ResetLoginAttempts/classes/class.ilResetLoginAttemptsResult.php';
include_once './Services/PrivacySecurity/classes/class.ilSecuritySettings.php';

/**
 * Class ilResetLoginAttempts
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */

class ilResetLoginAttempts extends ilCronJob
{
    const ID = "crreset_rs";
    
    private $cp;

    public function __construct()
    {
        $this->cp = \ilResetLoginAttemptsPlugin::getInstance();
    }
    
    public function getId(): string
    {
        return self::ID;
    }
    
    /**
     * @return bool
     */
    public function hasAutoActivation(): bool
    {
        return false;
    }
    
    /**
     * @return bool
     */
    public function hasFlexibleSchedule(): bool
    {
        return true;
    }
    
    /**
     * @return int
     */
    public function getDefaultScheduleType(): int
    {
        return self::SCHEDULE_TYPE_IN_MINUTES;
    }
    
    /**
     * @return int
     */
    public function getDefaultScheduleValue(): int
    {
        return 5;
    }
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->cp->txt("title");
    }
    
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->cp->txt("description");
    }
    
    /**
     * Defines whether or not a cron job can be started manually
     * @return bool
     */
    public function isManuallyExecutable(): bool
    {
        return false;
    }
    
    public function hasCustomSettings(): bool
    {
        return false;
    }
    
    public function run(): ilCronJobResult
    {
        include_once "Services/Cron/classes/class.ilCronJobResult.php";
        
        try {
            global $DIC;
            $db = $DIC->database();
            $security = ilSecuritySettings::_getInstance();
            
            $dur = $this->getScheduleValue();
                        
            $db->manipulate('UPDATE usr_data SET login_attempts = 0, active = 1 WHERE login_attempts >= '
                . $db->quote($security->getLoginMaxAttempts(), 'integer') . ' AND inactivation_date <= '
                . $db->quote(date('Y-m-d H:i:s', strtotime('-25 minutes')), 'datetime')
                . ' AND inactivation_date >= ' . $db->quote(date('Y-m-d H:i:s', strtotime('-35 minutes')), 'datetime'));
            
            return new ilResetLoginAttemptsResult(ilNotifyOnCronFailureResult::STATUS_OK, 'Cron job terminated successfully.');
        } catch (Exception $e) {
            return new ilResetLoginAttemptsResult(ilNotifyOnCronFailureResult::STATUS_CRASHED, 'Cron job crashed: ' . $e->getMessage());
        }
    }
}
