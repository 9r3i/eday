<?php
/* ----- PRE-LOADED DEFINES -- REQUIRES ----- */
/* index -- DO NOT CHANGE */
defined('INDEX_ROOT') or define('INDEX_ROOT',str_replace('\\','/',dirname(__DIR__)).'/');
defined('INDEX_SERVER') or define('INDEX_SERVER',INDEX_ROOT.'server/');
/* mserver -- DO NOT CHANGE */
defined('MSERVER_LIBS') or define('MSERVER_LIBS',INDEX_ROOT.'libs/');
defined('MSERVER_ROOT') or define('MSERVER_ROOT',INDEX_ROOT.'root/');
defined('MSERVER_DB_ROOT') or define('MSERVER_DB_ROOT',INDEX_ROOT.'db/');
/* databases */
defined('LDB_CLI_DIR') or define('LDB_CLI_DIR',MSERVER_DB_ROOT);
defined('KDB_CLI_DIR') or define('KDB_CLI_DIR',MSERVER_DB_ROOT);
defined('SDB_CLI_DIR') or define('SDB_CLI_DIR',MSERVER_DB_ROOT);
defined('LDB2X_CLI_DIR') or define('LDB2X_CLI_DIR',MSERVER_DB_ROOT.'ldb2x/');
/* applications data */
defined('ONLINE_TEST_LOGDIR') or define('ONLINE_TEST_LOGDIR',MSERVER_DB_ROOT.'oltest/');
defined('KSITE_CLI_DIR') or define('KSITE_CLI_DIR',MSERVER_LIBS.'ksite/');
defined('KSITE_DATA_DIR') or define('KSITE_DATA_DIR',MSERVER_DB_ROOT.'ksite/');

/* load mserver library
 * NOTE: Please, use COB file in production mode
 */
@require_once(MSERVER_LIBS.'mserver.cob.php');

/* start mserver -- DO NOT CHANGE */
new mserver([
  'root'=>MSERVER_ROOT,
  'libs'=>MSERVER_LIBS,
],__FILE__);
