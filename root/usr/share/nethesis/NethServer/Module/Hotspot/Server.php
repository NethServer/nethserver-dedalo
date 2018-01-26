<?php
namespace NethServer\Module\Hotspot;

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

use Nethgui\System\PlatformInterface as Validate;

/**
 * Implement gui module for hotspot server settings.
 */
class Server extends \Nethgui\Controller\AbstractController
{
    public function initialize()
    {
        parent::initialize();

        $this->declareParameter('RadiusServer1', Validate::HOSTADDRESS, array('configuration', 'hotspot', 'RadiusServer1'));
        $this->declareParameter('Secret', Validate::NOTEMPTY, array('configuration', 'hotspot', 'Secret'));
        $this->declareParameter('AuthPort', Validate::PORTNUMBER, array('configuration', 'hotspot', 'AuthPort'));
        $this->declareParameter('AcctPort', Validate::PORTNUMBER, array('configuration', 'hotspot', 'AcctPort'));
    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-dedalo-save');
    }
}

