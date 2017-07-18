<?php
// Routes

//Account # START
$app->get('/api/account/create', 'App\Billing\Controller\AccountController:create');
$app->get('/api/account/update', 'App\Billing\Controller\AccountController:update');
$app->get('/api/account/list', 'App\Billing\Controller\AccountController:fetchAll');
//Account # END

//CONTRACT # START
$app->get('/api/contract/create', 'App\Billing\Controller\ContractController:create');
$app->get('/api/contract/update', 'App\Billing\Controller\ContractController:update');
$app->get('/api/contract/list', 'App\Billing\Controller\ContractController:fetchAll');
//CONTRACT # END
