<?php

echo $view->header()->setAttribute('template', $T('Hotspot_Title'));

$proxy = $view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('HotspotProxy_label'))
        ->insert($view->fieldsetSwitch('Proxy', 'enabled', $view::FIELDSET_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
            ->setAttribute('uncheckedValue', 'disabled')
            ->insert($view->checkBox('ContentFilter', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
            ->insert($view->checkBox('LogTraffic', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
        );

$required = $view->panel()
    ->insert($view->fieldset()->setAttribute('template', $T('Remote_label'))
        ->insert($view->textInput('SplashPageUrl'))
        ->insert($view->textInput('Id'))
    );

$advanced = $view->panel()
    ->insert($view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('Advanced_label'))
        ->insert($view->fieldset()->setAttribute('template', $T('Advanced_Dedalo_label'))
           ->insert($view->textInput('Uuid'))
           ->insert($view->textInput('UnitName'))
           ->insert($view->textInput('Network'))
           ->insert($view->textInput('AllowOrigins')->setAttribute('placeholder',$view['defaultAllowOrigins']))
        )
        ->insert($view->fieldset()->setAttribute('template', $T('Advanced_Icaro_label'))
           ->insert($view->textInput('AaaUrl')->setAttribute('placeholder',$view['defaultAaaUrl']))
           ->insert($view->textInput('ApiUrl')->setAttribute('placeholder',$view['defaultApiUrl']))
        )
    );

$status = $view->fieldset()->setAttribute('template', $T('HotspotStatus_label'))
        ->insert($view->fieldsetSwitch('status', 'enabled', $view::FIELDSET_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
            ->setAttribute('uncheckedValue', 'disabled')
            ->insert($view->selector('Device', $view::SELECTOR_DROPDOWN))
            ->insert($required)
            ->insert($proxy)
            ->insert($advanced)
        );


echo $view->panel()->insert($status);

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);
