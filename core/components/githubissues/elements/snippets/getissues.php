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

return implode("\n", $output);
