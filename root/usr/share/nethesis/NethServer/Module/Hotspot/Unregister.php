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


namespace NethServer\Module\Hotspot;

class Unregister extends \Nethgui\Controller\AbstractController
{
    public function prepareView(\Nethgui\View\ViewInterface $view) {
        parent::prepareView($view);
        if($this->getRequest()->isMutation()) {
            $this->getPlatform()->setDetachedProcessCondition('success', array(
                'location' => array(
                'url' => $view->getModuleUrl('/Hotspot/Wizard/Auth'),
                    'freeze' => TRUE,
                )));
        } else {
                $view->getCommandList()->show();
        }
    }
    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()->signalEvent('nethserver-dedalo-unregister &');
        }
    }
}
