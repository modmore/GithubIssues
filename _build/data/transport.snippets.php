<?php
/**
 * @var modX $modx
 * @package githubissues
 * @subpackage build
 */
$snippets = array();
$i = 1;

/**
 * @var modSnippet $snippet
 */
$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'id' => $i,
    'name' => 'getIssues',
    'description' => 'Displays a list of issues for the given user|organisation/repository.',
    'snippet' => Helper::getPHPContent($sources['elements'] . 'snippets/getissues.php'),
), '', true, true);
//$properties = include $sources['data'] . 'properties/githubissues.php';
//$snippet->setProperties($properties);
//unset($properties);
$snippets[$i] = $snippet;

$i++;
$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'id' => $i,
    'name' => 'Auth',
    'description' => 'Temporary snippet to help you generate a token for Github.',
    'snippet' => Helper::getPHPContent($sources['elements'] . 'snippets/auth.php'),
), '', true, true);
//$properties = include $sources['data'] . 'properties/auth.php';
//$snippet->setProperties($properties);
//unset($properties);

$snippets[$i] = $snippet;

return $snippets;
