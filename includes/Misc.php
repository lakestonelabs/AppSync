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


Class Misc
{    
    public static function runLocalCommand($command, $debug = false, $timeout = null)
    {
        $return_value = null;
        $output = null;
        
        if(empty($timeout))
        {
            exec($command.' 2>&1', $output, $return_value);
        }
        
        if($return_value == 0)
        {
            if(sizeof($output) == 1)
            {
                if($debug)
                {
                    return array("output" => $output[0], "return_value" => $return_value);
                }
                else
                {
                    return $output[0];
                }
            }
            else if(sizeof($output) > 1)
            {
                if($debug)
                {
                    return array("output" => $output, "return_value" => $return_value);
                }
                else
                {
                    return $output;
                }
            }
            else
            {
                if($debug)
                {
                    return array("output" => $output, "return_value" => $return_value);
                }
                else
                {
                    return $output;
                }
            }
        }
        else if($return_value == 255)
        {  
            return array("output" => "ERROR running command: " . $command, "return_value" => $return_value);
        }
        else if ($return_value > 0)
        {
            if(sizeof($output) > 1)
            {
                // Got an error from command.
                if($debug)
                {
                    return array("output" => implode("\n", $output), "return_value" => $return_value);
                }
                else
                {
                    return $return_value;
                }
            }
            else
            {
                // Got an error from command.
                if($debug)
                {
                    if(isset($output[0]))
                    {
                        return array("output" => $output[0], "return_value" => $return_value);
                    }
                    else
                    {
                        return array("output" => null, "return_value" => $return_value);
                    }
                }
                else
                {
                    return $return_value;
                }
            }
        }
    }
    
} // End of Misc. Class



