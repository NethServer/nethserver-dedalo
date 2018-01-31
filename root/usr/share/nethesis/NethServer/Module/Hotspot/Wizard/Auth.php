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

class Auth extends \Nethgui\Controller\AbstractController
{
    
    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('IcaroHost', Validate::HOSTNAME, array('configuration', 'dedalo', 'IcaroHost'));
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if( ! $this->getRequest()->isMutation() ) {
            // clear the user session DB
            $sessDb = $this->getPlatform()->getDatabase('SESSION');
            $sessDb->deleteKey('IcaroSession');
            $sessDb->setType('IcaroSession', '');
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if( ! $this->getRequest()->isMutation()) {
            return;
        }

        $stash = new \NethServer\Tool\PasswordStash();
        $host = $this->getRequest()->getParameter('IcaroHost'); 
        $username = $this->getRequest()->getParameter('Username');
        $password = $this->getRequest()->getParameter('Password');
        $stash->store(json_encode(array( "username" => $username, "password" => $password)));
        $process = $this->getPlatform()->exec('/usr/libexec/nethserver/dedalo-authentication ${@}', array($host, $stash->getFilePath()));
        $result = json_decode($process->getOutput(),TRUE);
        $loginSuccessful = isset($result['token']);
        if($loginSuccessful) {
            // save the authentication token in the user session DB
            $sessDb = $this->getPlatform()->getDatabase('SESSION');
            $sessDb->setType('IcaroSession', $result['token']);
        } else {
            $report->addValidationErrorMessage($this, 'authentication_validator', 'icaro_auth_failed');
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) {
        parent::prepareView($view);
        $view['selection'] = $view->getModuleUrl('../Selection');
        if($this->getRequest()->isValidated()) {
            if($this->getRequest()->isMutation()) {
                $view->getCommandList()->sendQuery($view->getModuleUrl('../Register'));
            } else {
                $view->getCommandList()->show();
            }
        }
    }
}
