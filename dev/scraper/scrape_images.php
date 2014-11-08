<?php

include('simple_html_dom.php');

define('START', 0);
define('LIMIT', 3);

define('DUMP_FILENAME', "dump.json");

define('SOURCE_URL', "http://historymiamiarchives.org/guides/?p=digitallibrary/digitalcontent&id=");
define('FIREBASE_URL', "https://historymiami.firebaseio.com/images.json");

$index = 0;
$array_data = array();

while ($index <= LIMIT) {
    $url = SOURCE_URL . strval($index);

    $html = file_get_html($url);

    // Find the image
    $image_dom_obj = $html->find("img[class='digcontentfile']");

    if ($image_dom_obj) {
        $image_url = SOURCE_URL . "/" . basename($image_dom_obj[0]->src);
        $image_url = str_replace('&amp;', '&', $image_url);

        $label_obj = $html->find("div[class='digcontentlabel']");
        $value_obj = $html->find("div[class='digcontentdata']");

        $attributes = array();

        foreach ((array)$label_obj as $k => $label) {
            $attributes[rtrim($label->plaintext, ':')] = trim($value_obj[$k]->plaintext);
        }

        $attributes['archive_id'] = $attributes['ID'];

        unset($attributes['ID']);

        $attributes['url'] = $image_url;

        $array_data[$index] = $attributes;
        $json_to_save = json_encode([$index => $attributes]);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, FIREBASE_URL);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_to_save);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($curl);
        curl_close($curl);

        echo "Processed index $index \n";
    } else {
        echo "Index $index is empty \n";
    }

    sleep(0.2);
    $index++;
}

echo "Finished. Dumping results into file " . DUMP_FILENAME . "...\n";
file_put_contents(DUMP_FILENAME, json_encode($array_data, JSON_PRETTY_PRINT));

echo "Done!\n";

die();
