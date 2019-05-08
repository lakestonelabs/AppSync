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
 * Description of Adb
 *
 * @author Lakestone Labs <androidsync@lakestonelabs.com>
 */

require_once __DIR__."/Misc.php";
require_once __DIR__."/AndroidDevice.php";

class Adb
{
    private     $path = null,
                $supported_modes = ["usb", "network"],
                $mode = null,
                $network_port = 5555,
                $export_dir = "tmp";
    
    public function __construct(string $path, string $mode)
    {
        if(in_array($mode, $this->supported_modes))
        {
            $this->mode = $mode;
        }
        else
        {
            throw new Exception("Unsupported connection type of: ".$mode);
        }
        
        if(strlen($path) > 0)
        {
            if(is_file($path))
            {
                $this->path = $path;
            }
            else
            {
                throw new Exception("Adb executable, no such file: ".$path.".");
            }
        }
        else
        {
            throw new Exception("Adb executable path cannot be blank.");
        }
        
        if($this->mode == "network")
        {
            // Enter ADB into network mode.
            Misc::runLocalCommand($this->path." tcpip ".$this->network_port);
        }
        
        $this->export_dir = __DIR__."/../".$this->export_dir;
        if(!is_dir($this->export_dir))
        {
            if(!mkdir($this->export_dir))
            {
                throw new Exception("Failed to create export directory: ".$this->export_dir.".");
            }
        }
    }
    
    public function scanForDevices() : array
    {
        if($this->mode == "network")
        {
            throw new Exception("Can't scan for devices in network mode.");
        }
    
        $devices_array = [];
        $devices_return = Misc::runLocalCommand($this->path . " devices", true);
        if($devices_return["return_value"] === 0)
        {
            $devices_return_array = preg_grep("/^\S+\s+device$/", $devices_return["output"]);
            if(sizeof($devices_return_array) > 0)
            {
                foreach($devices_return_array as $this_device)
                {
                    $this_device_exploded = preg_split("/\s+/", $this_device);
                    $adb_id = $this_device_exploded[0];
                    $devices_array[] = $adb_id;
                }
                return $devices_array;
            }
            else
            {
                throw new Exception("Successful scan of devices but no devices returned.");
            }
        }
        else
        {
            throw new Exception("Failed to scan for devices: ". $devices_return["output"]);
        }
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getMode()
    {
        return $this->getMode();
    }
    
    public static function getUpgradableAppsList(AndroidDevice $source_device, AndroidDevice $dest_device)
    {
        $upgrade_array = [];
        foreach($source_device->dumpAppInfo() as $this_app_name => $this_app_info_array)
        {
            $this_app_version = $this_app_info_array["version"];
            if($dest_device->isAppInstalled($this_app_name))
            {
                if($dest_device->getAppVersion($this_app_name) != $this_app_version)
                {
                    $upgrade_array[$this_app_name] = ["old_version" => $dest_device->getAppVersion($this_app_name), "new_version" => $this_app_version];
                }
            }
        }
        return $upgrade_array;
    }
    
    public static function getNewAppsList(AndroidDevice $source_device, AndroidDevice $dest_device)
    {
        $new_apps_array = [];
        foreach($source_device->dumpAppInfo() as $this_app_name => $this_app_info_array)
        {
            if(!$dest_device->isAppInstalled($this_app_name))
            {
                $new_apps_array[] = $this_app_name;
            }
        }
        return $new_apps_array;
    }
    
    public function installPackages(PackageInstallManifest $pack_man_obj)
    {
        $source_device_obj = $pack_man_obj->getSourceDevice();
        $dest_device_obj = $pack_man_obj->getDestDevice();
        
        $manifest_array = $pack_man_obj->getManifest();
        foreach($manifest_array as $manifest_type => $this_manifest_type_array)
        {
            $install_options = null;
            if($manifest_type == "upgrade")
            {
                $install_options = "-r";
            }
            
            foreach($this_manifest_type_array as $this_app_name => $this_app_info)
            {
                echo ucfirst($manifest_type)." package ".$this_app_name."...";
                
                $packages_paths = Misc::runLocalCommand($this->path. " -s ".$source_device_obj->getDeviceId()." shell pm path $this_app_name | tr -d '\r' | cut -d \":\" -f 2");
                /*
                 * For apps that have multiple packages, we need to pull all of them
                 * first before we attempt to install because we need to use the
                 * 'install-multiple' command instead of just 'install'.  You 
                 * can't install packages separately for an app, they must be 
                 * installed in one atomic operation.
                 */
                $packages_array = [];
                $install_cmd = null;
                $pkg_install_str = null;
                
                if(is_array($packages_paths))
                {
                    $packages_array = $packages_paths;
                    $install_cmd = "install-multiple";
                }
                else
                {
                    $packages_array[] = $packages_paths;
                    $install_cmd = "install";
                    
                }
                
                foreach($packages_array as $package_path)
                {
                    $return = Misc::runLocalCommand($this->path." -s ".$source_device_obj->getDeviceId()." pull ".$package_path." ".$this->export_dir."/", true);
                    if($return["return_value"] == 0)
                    {
                        $pkg_install_str .= $this->export_dir."/".basename($package_path)." ";
                    }
                    else
                    {
                        echo "Could not pull package:".$package_path." for app:".$this_app_name.".  Error was:". $return["output"] .".  Skipping app install.\n";
                        unlink($this->export_dir."/".basename($package_path));
                        // Try the next app as this one completely failed.
                        continue 2; 
                    }
                }
                $install_return = Misc::runLocalCommand($this->path." -s ".$dest_device_obj->getDeviceId()." ".$install_cmd." ".$install_options." ".$pkg_install_str, true);
                if($install_return["return_value"] === 0)
                {
                    echo "Done.\n";
                }
                else
                {
                    echo "Failed.  Reason: ".$install_return["output"]."\n";
                }                
            }
        }
    }
}
