<?php
//prova nickkiyotfututyruftftu
$app->get('/hello/', 'PracticaFinal\\Controller\\HelloController::indexAction');
$app->get('add/{num1}/{num2}','SilexApp\\Controller\\HelloController::addAction');

?>