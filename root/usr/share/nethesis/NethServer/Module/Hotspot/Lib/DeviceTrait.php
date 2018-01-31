<?php
/*
 * Copyright (C) 2018 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */


namespace NethServer\Module\Hotspot\Lib;

/**
 * Convenience methods to read/write the networks DB records
 */
trait DeviceTrait
{
    public function readDevice()
    {
        $ret = array();
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $interface => $props) {
            if (preg_match('/ethernet|bridge|bond|alias|ipsec|vlan/',$props['type'])) {
                if ($props['role'] == 'hotspot') {
                    return array($interface);
                }
            }
        }
        return '';
    }

    public function writeDevice($v)
    {
       $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
       foreach ($interfaces as $interface => $props) {
           if ($props['role'] == 'hotspot') {
               $this->getPlatform()->getDatabase('networks')->setProp($interface, array('role' => ''));
           }
       }
       $this->getPlatform()->getDatabase('networks')->setProp($v, array('role' => 'hotspot'));
       return TRUE;
    }

    public function initNetworkDevicesList($view)
    {
        // retrieve network configured interfaces
        $db = $this->getPlatform()->getDatabase('networks');
        $devices = $db->getAll();

        $networks = array();

        foreach ($devices as $dev => $val) {
            if (preg_match('/ethernet|bridge|bond|alias|ipsec|vlan/',$val['type'])) {
                if ($val['role'] == 'hotspot') {
                    $networks[$dev] =  $dev. ' - ' . $view->translate('hotspot_assigned_label');
                }
            }
        }
        foreach ($devices as $dev => $val) {
            if (preg_match('/ethernet|bridge|bond|alias|ipsec|vlan/',$val['type'])) {
                if (!isset($val['role']) || ! $val['role']) {
                    $networks[$dev] =  $dev;
                }
            }
        }

        return $networks;
    }

}
