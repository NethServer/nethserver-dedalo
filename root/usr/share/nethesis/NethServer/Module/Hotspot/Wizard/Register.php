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


namespace NethServer\Module\Hotspot\Wizard;
use Nethgui\System\PlatformInterface as Validate;

class Register extends \Nethgui\Controller\AbstractController
{
    use \NethServer\Module\Hotspot\Lib\DeviceTrait;

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('UnitName', Validate::HOSTNAME, array('configuration', 'dedalo', 'UnitName'));
        $this->declareParameter('Description', Validate::ANYTHING, array('configuration', 'dedalo', 'Description'));
        $this->declareParameter('Id', Validate::NOTEMPTY, array('configuration', 'dedalo', 'Id'));
        $this->declareParameter('Device', Validate::NOTEMPTY, array());
        $this->declareParameter('NameMap', Validate::NOTEMPTY);
    }

    private function listHotspots()
    {
        $ret = array();
        $icaroSessionToken = $this->getPlatform()->getDatabase('SESSION')->getType('IcaroSession');
        $stash = new \NethServer\Tool\PasswordStash();
        $stash->store($icaroSessionToken);
        $host = $this->getPlatform()->getDatabase('configuration')->getProp('dedalo','IcaroHost');
        $process = $this->getPlatform()->exec('/usr/libexec/nethserver/dedalo-hotspot-list ${@}', array($host, $stash->getFilePath()));
        $hotspots = json_decode($process->getOutput(),TRUE);
        if (isset($hotspots['message'])) {
            $ret[] = array('',$hotspots['message']);
            return $ret;
        }
        foreach ($hotspots['data'] as $hotspot) {
            $ret[] = array($hotspot['id'], $hotspot['name']." (".$hotspot['description'].")");
            $nameMap[$hotspot['id']] = $hotspot['name'];
        }
        return array($ret,$nameMap);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) {
        parent::prepareView($view);
        $view['configuration'] = $view->getModuleUrl('../../Configuration');
        $view['Back'] = $view->getModuleUrl('../Auth');

        if($this->getRequest()->isValidated()) {
            $view['IcaroHost'] = $this->getPlatform()->getDatabase('configuration')->getProp('dedalo', 'IcaroHost');
            $view['DeviceDatasource'] = \Nethgui\Renderer\AbstractRenderer::hashToDatasource($this->initNetworkDevicesList($view));
            $list = $this->listHotspots();
            $view['IdDatasource'] = $list[0];
            $view['NameMap'] = json_encode($list[1]);
            if( ! $view['UnitName']) {
                $view['UnitName'] = \gethostname();
            }
            if($this->getRequest()->isMutation()) {
                $this->getPlatform()->setDetachedProcessCondition('success', array(
                    'location' => array(
                        'url' => $view->getModuleUrl('/Hotspot/Configuration?installSuccess'),
                        'freeze' => TRUE,
                )));
            } else {
                $view->getCommandList()->show();
            }
        }
    }

    protected function onParametersSaved($changes)
    {
        $sessDb = $this->getPlatform()->getDatabase('SESSION');

        $arr = json_decode($this->parameters['NameMap'], 1);
        $name = $arr[$this->parameters['Id']];
        $this->getPlatform()->getDatabase('configuration')->setProp('dedalo',array('Name' => $name));

        $icaroSessionToken = $sessDb->getType('IcaroSession');
        $stash = new \NethServer\Tool\PasswordStash();
        $stash->store($icaroSessionToken);
        $this->getPlatform()->signalEvent('nethserver-dedalo-register &', array($stash->getFilePath()));
    }

}
