<?php

/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->header()->setAttribute('template', $T('Auth_header'));

echo $view->textInput('IcaroHost', $view['IcaroHost']);
echo $view->textInput('Username');
echo $view->textInput('Password', $view::TEXTINPUT_PASSWORD);

echo $view->buttonList($view::BUTTON_HELP)
    ->insert($view->button('Authenticate', $view::BUTTON_SUBMIT))
;
