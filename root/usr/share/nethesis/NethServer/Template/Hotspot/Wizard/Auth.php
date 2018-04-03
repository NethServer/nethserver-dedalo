<?php

/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->header()->setAttribute('template', $T('Auth_header'));

echo "<div id='icaroInstance'>";
echo "<p>". htmlspecialchars($T("info_label")) . " <a href='https://nethesis.github.io/icaro/'>Icaro.</a></p>";
if ($view['IcaroHost']) {
    echo "<p class='ready'>".$T('ready_label')."<a href='https://{$view['IcaroHost']}'>https://".$view['IcaroHost']."</a></p>";
}
echo '</div>';

echo $view->textInput('IcaroHost', $view['IcaroHost']);
echo $view->textInput('Username');
echo $view->textInput('Password', $view::TEXTINPUT_PASSWORD);

echo $view->buttonList($view::BUTTON_HELP)
    ->insert($view->button('Authenticate', $view::BUTTON_SUBMIT))
;

$view->includeCss("
    #icaroInstance p {
        margin: 5px;
        font-size: 120%;
        line-height: 1.5;
        text-align: justify;
        text-justify: inter-word;
    }

    #icaroInstance {
        max-width: 716px;
        margin-bottom: 2em;
        padding: 8px;
        background: linear-gradient(to right, #eee, white);
        border-left: 8px solid #00719a;
    }

    #icaroInstance .ready {
        font-size: 130%;
        padding: 5px;
    }

");
