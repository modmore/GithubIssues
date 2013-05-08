<?php
/**
 * @var modX $modx
 * @var array $scriptProperties
 */
function requestOauth($data = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_URL, 'https://github.com/login/oauth/access_token');

    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}
$client = $modx->getOption('client_id', $scriptProperties);
$secret = $modx->getOption('client_secret', $scriptProperties);
if (!$client || !$secret) {
    return 'Your client_id & client_secret keys are REQUIRED!';
}

$code = $modx->getOption('code', $_GET);
$data = array(
    'client_id' => $client,
    'client_secret' => $secret,
    'code' => $code,
);

if ($code) {
    $response = requestOauth($data);

    $final = array();
    $dirty = explode('&', $response);
    foreach ($dirty as $segment) {
        $tmp = explode('=', $segment);
        $final[$tmp[0]] = $tmp[1];
    }

    if (!array_key_exists('access_token', $final)) {
        return 'Uhoh, something went wrong! (response : '. $response .')';
    }

    return 'Your token key is <pre>' . $final['access_token'] . '</pre>';
}

return 'Nuttin to see here, sorry';
