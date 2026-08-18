<?php

/**
 * Public entry bootstrap — UI pages load this, which loads backend logic.
 *
 * Hosting: this folder is public_html; backend stays outside it at
 * ../apps/pentagon_quest_logic/ (or PQ_APP_PATH).
 */
require_once dirname(__DIR__) . '/paths.php';
require_once PENTAGON_LOGIC_DIR . '/bootstrap.php';
require_once __DIR__ . '/view.php';
