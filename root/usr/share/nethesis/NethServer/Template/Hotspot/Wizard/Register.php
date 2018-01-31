<?php

/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->header('IcaroHost')->setAttribute('template', $T('Register_header'));

echo $view->selector('Id', $view::SELECTOR_DROPDOWN);
echo $view->textInput('UnitName');
echo $view->selector('Device', $view::SELECTOR_DROPDOWN);


echo $view->buttonList($view::BUTTON_HELP)
    ->insert($view->button('Register', $view::BUTTON_SUBMIT))
    ->insert($view->button('Back', $view::BUTTON_LINK))
;
