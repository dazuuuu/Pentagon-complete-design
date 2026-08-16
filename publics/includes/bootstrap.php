<?php

/**
 * Public entry bootstrap — UI pages load this, which loads backend logic.
 * Hosting: publics/ is public_html; backend stays at ../apps/pentagon_quest_logic/
 */
require_once dirname(__DIR__, 2) . '/apps/pentagon_quest_logic/bootstrap.php';
require_once __DIR__ . '/view.php';
