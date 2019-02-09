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
 * Description of PackageInstallManifest
 *
 * @author Lakestone Labs <androidsync@lakestonelabs.com>
 */

require_once __DIR__."/AndroidDevice.php";

class PackageInstallManifest
{
    private $source_device = null,
            $dest_device = null,
            $install_array = [];
        
    public function __construct(AndroidDevice $source_device, AndroidDevice $dest_device)
    {
        $this->source_device = $source_device;
        $this->dest_device = $dest_device;
        
        $this->install_array = ["install" => [], "upgrade" => []];
    }
    
    public function appendAppToInstall($app_name)
    {
        $this->install_array["install"][$app_name] = true;
    }
    
    public function appendAppToUpgrade($app_name, $old_version, $new_version)
    {
        if($this->dest_device->isAppInstalled($app_name))
        {
            $this->install_array["upgrade"][$app_name] = ["old_version" => $old_version, "new_version" => $new_version];
        }
        else
        {
            throw new Exception("App ".$app_name." is not installed on this device.  Can't upgrade it.");
        }
    }
    
    public function getManifest()
    {
        return $this->install_array;
    }
    
    public function getSourceDevice()
    {
        return $this->source_device;
    }
    
    public function getDestDevice()
    {
        return $this->dest_device;
    }
}
