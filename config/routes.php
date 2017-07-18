<?php
// Routes

//Account # START
$app->get('/api/account/create', 'App\Billing\Controller\AccountController:createAction');
$app->get('/api/account/update', 'App\Billing\Controller\AccountController:updateAction');
$app->get('/api/account/list', 'App\Billing\Controller\AccountController:fetchAllAction');
//Account # END

//CONTRACT # START
$app->get('/api/contract/create', 'App\Billing\Controller\ContractController:createAction');
$app->get('/api/contract/update', 'App\Billing\Controller\ContractController:updateAction');
$app->get('/api/contract/list', 'App\Billing\Controller\ContractController:fetchAllAction');
//CONTRACT # END
