<?php

define('FIREBASE_URL', "https://historymiami.firebaseio.com/images.json");
define('CACHE_FILE', "images_db_cache");

// when script is too old (60 minutes)
define('UPDATE_PERIOD', 60 * 60);
// minimum characters to perform a search
define('MINIMUM_STR_LEN', 3);


/**
 * Queries Firebase server and saves json file into cache
 *
 * @return bool
 */
function update_cache()
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, FIREBASE_URL);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($curl);

    curl_close($curl);

    return file_put_contents(CACHE_FILE, $server_output);
}


/**
 * Generates JSON response and terminates the script
 *
 * @param $output
 */
function display_output($output)
{
    $output = json_encode($output);

    header('Content-Type: application/json');
    echo $output;

    die();
}


if (
    !is_file($filename) ||
    (is_file($filename) && (filemtime($filename) + $update_period) < time())
) {
    update_cache();
}

if (isset($_POST['s']) && strlen($_POST['s'] >= $min_strlen)) {
    $search_str = $_POST['s'];
} else {
    display_output(['error', 'No search parameters in request']);
}


$json = json_decode(file_get_contents($filename), true);

$found_keys = array();

foreach ((array)$json as $key => $record) {
    foreach ((array)$record as $value) {
        if (strpos($value, $search_str) !== false) {
            $found_keys[$key] = $key;
        }
    }
}

display_output($found_keys);

die();