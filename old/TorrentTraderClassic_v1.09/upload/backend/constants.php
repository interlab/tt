<?php

const UC_USER = 0;
const UC_UPLOADER = 1;
const UC_VIP = 2;
const UC_JMODERATOR = 3;
const UC_MODERATOR = 4;
const UC_ADMINISTRATOR = 5;

define('TT_ROOT_DIR', dirname(__DIR__));
define('TT_DIR', dirname(__DIR__));
define('TT_URL', $GLOBALS['SITEURL']);

const TT_LIBS_DIR = TT_ROOT_DIR.'/libs';
const TT_BACKEND_DIR = TT_ROOT_DIR.'/backend';
const TT_CONFIG_DIR = TT_ROOT_DIR.'/backend/config';
const TT_HELP_DIR = TT_ROOT_DIR.'/helpers';

const TT_CACHE_DIR = TT_ROOT_DIR.'/storage/cache';
const TT_CACHE_TIME = 60*60*2; // 2 hour

const TT_PUB_DIR = TT_ROOT_DIR.'/pub';
const TT_IMG_DIR = TT_PUB_DIR.'/images';
const TT_AVATARS_DIR = TT_PUB_DIR.'/avatars';
const TT_THEMES_DIR = TT_ROOT_DIR.'/themes';
const TT_COLUMNS_DIR = TT_ROOT_DIR . '/columns';
const TT_JS_DIR = TT_PUB_DIR.'/js';
const TT_PUB_URL = TT_URL.'';
const TT_THEMES_URL = TT_URL.'/themes';

const TT_IMG_URL = TT_PUB_URL.'/images';
const TT_AVATARS_URL = TT_PUB_URL.'/avatars';

const TT_CONTR_DIR = TT_ROOT_DIR . '/controllers';
const TT_JS_URL = TT_PUB_URL . '/js';

