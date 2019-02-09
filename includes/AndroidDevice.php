<?php

/*
 * Copyright (C) 2019 Lakestone Labs <androidsync at lakestonelabs dot com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of AndroidDevice
 *
 * @author Lakestone Labs <androidsync@lakestonelabs.com>
 */

require_once __DIR__."/Misc.php";
require_once __DIR__."/Adb.php";

class AndroidDevice
{    
    protected   $name,
                $device_id,
                $adb_obj = null,
                $adb_path = null;
    
    private     $properties_array = [],
                $installed_apps_array = [];
    
    public function __construct(\Adb $adb_obj, $device_id)
    {
        $this->adb_obj = $adb_obj;
        $this->adb_path = $this->adb_obj->getPath();
        $this->device_id = $device_id;
        
        $this->getProperties();
        $this->getInstalledApps();
    }
    
    public function getModelNumber() : string
    {
        return $this->properties_array["ro.product.model"];
    }
    
    public function getDeviceName()
    {
        return $this->properties_array["ro.product.device"];
    }
    
    public function getProperty($property_name)
    {
        if(isset($this->properties_array[$property_name]))
        {
            return $this->properties_array[$property_name];
        }
        else
        {
            return false;
        }
    }
    
    public function getDeviceId() : string
    {
        return $this->device_id;
    }
    
    public function isAppInstalled(string $app_name) : bool
    {
        if(isset($this->installed_apps_array[$app_name]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function getAppVersion(string $app_name)
    {
        if($this->isAppInstalled($app_name))
        {
            return $this->installed_apps_array[$app_name]["version"];
        }
        else
        {
            return false;
        }
    }
    
    public function dumpAppInfo()
    {
        return $this->installed_apps_array;
    }
    
    public function dumpDeviceProperties()
    {
        return $this->properties_array;
    }
    
    private function getProperties()
    {
        // Get the device properties and save them for later.
        $return_command = Misc::runLocalCommand($this->adb_path." -s ".$this->device_id." shell getprop", true);
        if($return_command["return_value"] === 0)
        {
            $device_props_array = $return_command["output"];
            foreach($device_props_array as $this_property)
            {
                $key_value_array = preg_split("/]:/", $this_property);
                $this->properties_array[trim($key_value_array[0],"[")] = trim($key_value_array[1], " ][");
            }
        }
        else
        {
            throw new Exception($return_command["output"]);
        }
    }
    
    private function getInstalledApps()
    {
        echo "Retrieving installed apps for device: ".$this->getModelNumber()."...";
        // Get the device apps and save them for later.  -3 param only lists 3rd party packages and not system packages.
        $return = Misc::runLocalCommand($this->adb_path. " -s ".$this->device_id." shell pm list packages -3 | tr -d '\r' | grep -v \"com.google\" | cut -d \":\" -f 2", true);
        if ($return["return_value"] === 0)
        {
            foreach($return["output"] as $this_app)
            {
                $app_info_array = [];
                
                $app_version_array = Misc::runLocalCommand($this->adb_path." -s ".$this->device_id." shell dumpsys package ".$this_app." | grep versionName", true);
                if($app_version_array["return_value"] === 0)
                {
                    //var_dump($app_version_array);
                    if(is_string($app_version_array["output"]))
                    {
                        $version_array = preg_split("/=/", $app_version_array["output"]);
                    }
                    else if (is_array($app_version_array["output"]) && sizeof($app_version_array["output"]) > 1)
                    {
                        echo "Multiple versions for package: ".$this_app.".  Skipping\n";
                    }
                    else
                    {
                        echo "Unexpected version data for package: ".$this_app.".\n";
                    }

                    $app_info_array["version"] = trim($version_array[1]);                    
                }
                else
                {
                    echo "Unable to get package version for: ".$this_app."\n";
                }
                // Use the package name as the index for faster comparison later on.
                $this->installed_apps_array[$this_app] = $app_info_array;
            }
            echo "Done.\n";
        }
        else
        {
            throw new Exception("Unable to retrieve installed app list from device: ".$this->device_id.".");
        }
    }
}
