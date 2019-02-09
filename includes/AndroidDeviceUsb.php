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
 * Description of AndroidDeviceUsb
 *
 * @author Lakestone Labs <androidsync@lakestonelabs.com>
 */

require_once __DIR__."/AndroidDevice.php";

class AndroidDeviceUsb extends AndroidDevice
{
   public function __construct(\Adb $adb_obj, $device_id)
   {
        $connected_devices_array = $this->adb_obj->scanForDevices();
        if(in_array($device_id, $connected_devices_array))
        {
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
            throw new Exception("Device with ID: ".$device_id." is not connected.");
        }
   }
}
