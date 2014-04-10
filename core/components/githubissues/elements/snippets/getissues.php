<?php
/**
 * @var modX $modx
 * @var GithubIssues $githubissues
 * @var array $scriptProperties
 * @package githubissues
 */

// Snippet parameters
$tpl = $modx->getOption('tpl', $scriptProperties, 'issue');
$emptyMsg = $modx->getOption('emptyMsg', $scriptProperties, '<li>No issue</li>');
$token = $modx->getOption('token', $scriptProperties, $modx->getOption('githubissues.token'));
$label = $modx->getOption('label', $scriptProperties);
$state = $modx->getOption('state', $scriptProperties);
$fromUser = $modx->getOption('fromUser', $scriptProperties, 'modmore');
$forRepo = $modx->getOption('forRepo', $scriptProperties, 'GithubIssues');
$sortBy = $modx->getOption('sortBy', $scriptProperties, 'created_at');
$sortDir = $modx->getOption('sortDir', $scriptProperties, 'SORT_DESC');

if (!$token) {
    return 'You MUST provide an API token';
}
if (!$fromUser) {
    return 'Which user/organisation would you like to check against ?';
}
if (!$forRepo) {
    return 'Which repository would you like to check against ?';
}
// Generate a unique cache key
$cacheSegments = array('gh', $fromUser, $forRepo, $tpl, $state, $label);
$cacheKey = str_replace(' ', '_', implode('_', $cacheSegments));
$cacheTime = $modx->getOption('cacheTime', $scriptProperties, false);
if ($cacheTime !== false) {
    // If cache is enabled, try to grab it first
    $modx->getCacheManager();
    $output = $modx->cacheManager->get($cacheKey);
    if (!empty($output)) {
        return implode("\n", $output);
    }
}

$githubissues = $modx->getService('githubissues', 'GithubIssues', $modx->getOption('githubissues.core_path', null, $modx->getOption('core_path') . 'components/githubissues/') . 'model/githubissues/');
if (!($githubissues instanceof GithubIssues)) return '';

$client = $githubissues->getClient($token);

$params = array();
if ($label) $params['labels'] = $label;
$validStates = array('open', 'closed');
if ($state && in_array($state, $validStates)) $params['state'] = $state;

$issues = $client->api('issue')
    ->all($fromUser, $forRepo, $params);

/**
 * @param array $array The array to sort
 * @param string $column The array key to sort by
 * @param string $sortDir The sorting option. See http://php.net/manual/en/function.array-multisort.php for valid constant names
 */
if (!function_exists('sortMe')) { 
    function sortMe(&$array, $column, $sortDir) {
        $sortDir = strtoupper($sortDir);
        // Grab a sorted array
        $sorted = array();
        foreach ($array as $key => $row) {
            $sorted[$key] = $row[$column];
        }
        // Sort the original array accordingly
        array_multisort($sorted, constant($sortDir), $array);
    }
}

sortMe($issues, $sortBy, $sortDir);

//$modx->log(modX::LOG_LEVEL_INFO, 'params : ' . print_r($params, true));
//$modx->log(modX::LOG_LEVEL_INFO, print_r($issues, true));

$output = array();
foreach ($issues as $issue) {
    $output[] = $githubissues->getChunk($tpl, $issue);
}
if (empty($output)) {
    $output[] = $emptyMsg;
}

if ($cacheTime !== false) {
    // Set to the default cache partition
    $modx->cacheManager->set($cacheKey, $output, $cacheTime);
}

//$modx->log(modX::LOG_LEVEL_INFO, print_r($output, true));

return implode("\n", $output);
