<?php

echo $view->panel()
        ->insert($view->textInput('RadiusServer1'))
        ->insert($view->textInput('Secret'))
        ->insert($view->textInput('AuthPort'))
        ->insert($view->textInput('AcctPort'))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
