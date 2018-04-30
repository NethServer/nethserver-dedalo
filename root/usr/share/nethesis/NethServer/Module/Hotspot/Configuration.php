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
use Nethgui\System\PlatformInterface as Validate;

/**
 * Implement UI for hotspot general settings
 */
class Configuration extends \Nethgui\Controller\AbstractController implements \Nethgui\Component\DependencyConsumer
{
    use \NethServer\Module\Hotspot\Lib\DeviceTrait;

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('Device', Validate::NOTEMPTY, array());
        $this->declareParameter('Network', Validate::CIDR_BLOCK, array('configuration', 'dedalo', 'Network'));
        $this->declareParameter('Proxy', Validate::SERVICESTATUS, array('configuration', 'dedalo', 'Proxy'));
        $this->declareParameter('LogTraffic', Validate::SERVICESTATUS, array('configuration', 'dedalo', 'LogTraffic'));
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['DeviceDatasource'] = \Nethgui\Renderer\AbstractRenderer::hashToDatasource($this->initNetworkDevicesList($view));
        $view['UnitName'] = $this->getPlatform()->getDatabase('configuration')->getProp('dedalo', 'UnitName') . " (" .$this->getPlatform()->getDatabase('configuration')->getProp('dedalo', 'Uuid'). ")";
        $name = $this->getPlatform()->getDatabase('configuration')->getProp('dedalo', 'Name');
        if ($name == '') {
            $name = $this->getPlatform()->getDatabase('configuration')->getProp('dedalo', 'Id');
        }
        $view['Id'] = $name;
        $view['Unregister'] = $view->getModuleUrl('../Unregister');
        $view['ProxyEnabled'] = $this->getPlatform()->getDatabase('configuration')->getProp('squid', 'status') == "enabled";
        if($this->getRequest()->hasParameter('installSuccess')) {
            $this->notifications->message($view->translate('hotspotRegistrationSuccess_notification'));
            $view->getCommandList()->show();
        }
    }

    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-dedalo-save &');
    }

    public function setUserNotifications(\Nethgui\Model\UserNotifications $n)
    {
        $this->notifications = $n;
        return $this;
    }

    public function setSystemTasks(\Nethgui\Model\SystemTasks $t)
    {
        $this->systemTasks = $t;
        return $this;
    }

    public function getDependencySetters()
    {
        return array(
            'UserNotifications' => array($this, 'setUserNotifications'),
            'SystemTasks' => array($this, 'setSystemTasks'),
        );
    }
}
