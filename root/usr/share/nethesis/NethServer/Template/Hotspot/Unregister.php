<?php

/* @var $view \Nethgui\Renderer\Xhtml */
$view->requireFlag($view::INSET_DIALOG);

echo $view->header()->setAttribute('template', $T('Unregister_header'));

echo '<p>' . htmlspecialchars($T('Unregister_message')) . '</p>';

echo $view->buttonList($view::BUTTON_CANCEL)
    ->insert($view->button('Unregister', $view::BUTTON_SUBMIT))
;
