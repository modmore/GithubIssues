<?php
/**
 * GithubIssues build script
 *
 * @package githubissues
 * @subpackage build
 */
$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

// define package names
define('PKG_NAME', 'GithubIssues');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '0.1.0');
define('PKG_RELEASE', 'beta');

// Define build paths
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root . 'core/components/'. PKG_NAME_LOWER .'/chunks/',
    'lexicon' => $root . 'core/components/'. PKG_NAME_LOWER .'/lexicon/',
    'docs' => $root . 'core/components/'. PKG_NAME_LOWER .'/docs/',
    'elements' => $root . 'core/components/'. PKG_NAME_LOWER .'/elements/',
    'source_assets' => $root . 'manager/components/'. PKG_NAME_LOWER,
    'source_core' => $root . 'core/components/'. PKG_NAME_LOWER,
);
unset($root);

// Override with your own defines here (see build.config.sample.php)
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/helper.php';

// Instantiate modX
$modx = new modX();
$modx->initialize('mgr');
// used for nice formatting of log messages
if (!XPDO_CLI_MODE) echo '<pre>';
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/'. PKG_NAME_LOWER .'/');

// Create category
/** @var $category modCategory */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);

// Add snippets
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in snippets...');
$snippets = include $sources['data'] . 'transport.snippets.php';
if (empty($snippets)) $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in snippets.');
$category->addMany($snippets);

// Create category vehicle
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category, $attr);

$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to category...');
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);

// Now pack in the license file, readme and setup options
$modx->log(modX::LOG_LEVEL_INFO, 'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    /*'setup-options' => array(
        'source' => $sources['build'] . 'setup.options.php',
    ),*/
));

// zip up package
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
$builder->pack();

$tend = explode(" ", microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO, "\n\nPackage Built. \nExecution time: {$totalTime}\n");
if (!XPDO_CLI_MODE) echo '</pre>';
exit ();
