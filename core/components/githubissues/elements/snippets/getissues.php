<?php
/**
 * @var modX $modx
 * @var GithubIssues $githubissues
 * @var array $scriptProperties
 * @package githubissues
 */
$githubissues = $modx->getService('githubissues', 'GithubIssues', $modx->getOption('githubissues.core_path', null, $modx->getOption('core_path') . 'components/githubissues/') . 'model/githubissues/');
if (!($githubissues instanceof GithubIssues)) return '';

// setup default properties
$tpl = $modx->getOption('tpl', $scriptProperties, 'issue');

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
$client = new Github\Client();
$client->authenticate($token, null, Github\Client::AUTH_URL_TOKEN);

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

return implode("\n", $output);
