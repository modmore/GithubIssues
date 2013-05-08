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
$tpl = $modx->getOption('tpl', $scriptProperties, 'rowTpl');
$sort = $modx->getOption('sort', $scriptProperties, 'name');
$dir = $modx->getOption('dir', $scriptProperties, 'ASC');

$token = $modx->getOption('token', $scriptProperties);
$label = $modx->getOption('label', $scriptProperties);
$state = $modx->getOption('state', $scriptProperties);
if (!$token) {
    return 'You MUST provide an API token';
}
$client = new Github\Client();
$client->authenticate($token, null, Github\Client::AUTH_URL_TOKEN);

$params = array();
if ($label) $params['labels'] = $label;
$validStates = array('open', 'closed');
if ($state && in_array($state, $validStates)) $params['state'] = $state;

$issues = $client->api('issue')->all('modmore', 'GithubIssues', $params);

return '<pre>' . print_r($issues, true) . '</pre>';
