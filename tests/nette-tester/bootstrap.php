<?php

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/AbstractTestCase.php';
require_once __DIR__ . '/AbstractInstallEventTestCase.php';
require_once __DIR__ . '/AbstractUninstallEventTestCase.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');
