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


namespace NethServer\Module;

/**
 * Implement gui module for hotspot general settings.
 */
class Hotspot extends \Nethgui\Controller\CompositeController
{
    protected $firstModuleIdentifier;
    
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($base, array(
            'languageCatalog' => array('NethServer_Module_Hotspot'),
            'category' => 'Gateway')
        );
    }

    public function initialize()
    {
        parent::initialize();
        $this->loadChildrenDirectory();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $hotspotId = $this->getPlatform()->getDatabase('configuration')->getProp('dedalo', 'Id');

        $firstModuleIdentifier = 'Wizard';
        if($hotspotId) {
            $firstModuleIdentifier = 'Configuration';
        }
        $this->firstModuleIdentifier = $firstModuleIdentifier;

        // Sort children so that if the Provider prop is "none", it starts the Wizard:
        $this->sortChildren(function ($a, $b) use ($firstModuleIdentifier) {
            if($a->getIdentifier() === $firstModuleIdentifier) {
                $c = -1;
            } elseif($b->getIdentifier() === $firstModuleIdentifier) {
                $c = 1;
            } else {
                $c = 0;
            }
            return $c;
        });

        parent::bind($request);
        if (is_null($this->currentAction)) {
            $action = $this->getAction($firstModuleIdentifier);
            $action->bind($request->spawnRequest($firstModuleIdentifier));
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if (is_null($this->currentAction)) {
            $action = $this->getAction($this->firstModuleIdentifier);
            if ($action instanceof \Nethgui\Controller\RequestHandlerInterface) {
                $action->validate($report);
            }
        }
        parent::validate($report);
    }
}
