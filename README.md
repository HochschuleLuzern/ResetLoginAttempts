# ResetLoginAttempts

ResetOnLoginAttempts is a Cron-Plugin that checks users who have reached the maximum number of failed login attempts and have thus been deactivated and resets them. Thus a very simple soft lockout is implemented. This was developed to avoid lockout from sso services like ldap.
**It needs a patch in ILIAS core to avoid resetting manually deactivated users. See https://github.com/HochschuleLuzern/ILIAS-HSLU/commit/cea03d0fc47018e8fdf860a09be4ecf826170acd for the commit. This Cronjob MUST run at least every 5 minutes!**


**Minimum ILIAS Version:**
5.2.0

**Maximum ILIAS Version:**
5.3.999

**Responsible Developer:**
Stephan Winiker - stephan.winiker@hslu.ch

**Supported Languages:**
German, English

### Quick Installation Guide
1. Copy the content of this folder in <ILIAS_directory>/Customizing/global/plugins/Services/Cron/CronHook/ResetLoginAttempts or clon this Github-Repo to <ILIAS_directory>/Customizing/global/plugins/Services/Cron/CronHook/

2. Access ILIAS, go to the administration menu and select "Plugins".

3. Look for the ResetLoginAttempts plugin in the table, press the "Action" button and select "Update". All existing data on failed Login Attempts will be reseted. This needs to be done to avoid reactivating old login accounts.

4. Press the "Action" button and select "Activate" to activate the plugin.

5. Go to the administration menu, select "General Settings" and then "Cron Jobs".

7. Look for "Resets failed login attempts and reactivates corresponding users" in the table and click "Edit".

8. Choose your schedule.

9. Save and activate the Cron-Job.