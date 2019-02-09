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
 * Description of AndroidDeviceNetwork
 *
 * @author Lakestone Labs <androidsync@lakestonelabs.com>
 */

require_once __DIR__."/AndroidDevice.php";

class AndroidDeviceNetwork extends AndroidDevice
{
    private $ip_address,
            $network_port = 5555;
    
    public function __construct(\Adb $adb_obj, $ip_address)
    {
        if(filter_var($ip_address, FILTER_VALIDATE_IP))
        {
            $this->ip_address = $ip_address;
            $device_id = $this->ip_address.":".$this->network_port;
        }
        else
        {
            throw new Exception("Invalid IP address: ".$ip_address);
        }

        // Connect to our devices over the network.
        $connect_return = Misc::runLocalCommand($adb_obj->getPath()." connect ".$device_id, true);
        if($connect_return["return_value"] === 0)
        {
            // Need to let the connect command bake for a bit before we interogate the device.
            sleep(1);
            
            // Call our parent.
            try
            {
                parent::__construct($adb_obj, $device_id);
            }
            catch(Exception $e)
            {
                throw $e;
            }
        }
        else
        {
            throw new Exception("Failed to connect to device on IP:".$this->ip_address.":".$this->network_port.".");
        }        
    }
}
