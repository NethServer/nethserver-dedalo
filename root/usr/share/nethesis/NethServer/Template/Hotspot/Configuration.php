<?php

echo $view->header('UnitName')->setAttribute('template', $T('Configuration_header'));

echo $view->textInput('Id', $view::STATE_DISABLED | $view::STATE_READONLY);
echo $view->selector('Device', $view::SELECTOR_DROPDOWN);
echo $view->textInput('Network');


if ($view['ProxyEnabled']) {
    $proxy = $view->fieldsetSwitch('Proxy', 'enabled', $view::FIELDSET_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->checkBox('LogTraffic', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
    ;
    echo $proxy;
}

echo $view->buttonList($view::BUTTON_HELP)
    ->insert($view->button('Save', $view::BUTTON_SUBMIT))
    ->insert($view->button('Unregister', $view::BUTTON_LINK))
;
