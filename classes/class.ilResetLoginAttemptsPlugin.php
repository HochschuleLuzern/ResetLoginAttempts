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

include_once "./Services/Cron/classes/class.ilCronHookPlugin.php";
require_once './Customizing/global/plugins/Services/Cron/CronHook/ResetLoginAttempts/classes/class.ilResetLoginAttempts.php';


/**
 * Class ilNotifyOnCronFailurePlugin
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */

class ilResetLoginAttemptsPlugin extends ilCronHookPlugin
{
    const PLUGIN_NAME = "ResetLoginAttempts";
    
    /**
     * @var ilResetLoginAttemptsPlugin
     */
    protected static $instance;
    
    /**
     * @return ilResetLoginAttemptsPlugin
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            global $DIC;
            self::$instance = $DIC["component.factory"]->getPlugin("crreset");
        }
        
        return self::$instance;
    }
    
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }
    
    /**
     * @var  ilNotifyOnCronFailureNotify
     */
    protected static $cron_job_instances;
    
    /**
     * @return  ilResetLoginAttemptsPluginInstances[]
     */
    public function getCronJobInstances(): array
    {
        $this->loadCronJobInstance();
        
        return array_values(self::$cron_job_instances);
    }
    
    /**
     * @return  ilResetLoginAttemptsPluginInstance or false on failure
     */
    public function getCronJobInstance($a_job_id): ilCronJob
    {
        $this->loadCronJobInstance();
        if (isset(self::$cron_job_instances[$a_job_id])) {
            return self::$cron_job_instances[$a_job_id];
        } else {
            return false;
        }
    }
    
    protected function loadCronJobInstance()
    {
        if (!isset(self::$cron_job_instances)) {
            self::$cron_job_instances[ilResetLoginAttempts::ID] = new ilResetLoginAttempts();
        }
    }
}
