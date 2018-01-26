<?php

echo $view->panel()
        ->insert($view->selector('status'))
        ->insert($view->selector('Device', $view::SELECTOR_DROPDOWN))
        ->insert($view->textInput('DynamicNet'))
;

echo $view->fieldset()->setAttribute('template', $T('HotspotProxy_label'))
        ->insert($view->fieldsetSwitch('Proxy', 'enabled', $view::FIELDSET_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
            ->setAttribute('uncheckedValue', 'disabled')
            ->insert($view->checkBox('ContentFilter', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
            ->insert($view->checkBox('LogTraffic', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
        );



echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

$moduleUrl = json_encode($view->getModuleUrl("/Hotspot?tsonly"));
