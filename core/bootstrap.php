<?php

define('ROOT_PATH', dirname(__DIR__));

// CONFIG (session + DB)
require_once ROOT_PATH . '/config/config.php';

// ✅ HELPERS FIRST (this defines fetchAll)
require_once ROOT_PATH . '/helpers/auth.php';

// SERVICES
require_once ROOT_PATH . '/services/MonsterHuntService.php';
require_once ROOT_PATH . '/services/AttackEngine.php';

// CONTROLLERS (these depend on helpers + services)
require_once ROOT_PATH . '/controllers/SquadController.php';