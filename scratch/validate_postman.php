<?php

$file = __DIR__.'/../VidyaMission.postman_collection.json';
$data = json_decode(file_get_contents($file), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'JSON Error: '.json_last_error_msg()."\n";
    exit(1);
}

$count = 0;
function countItems($items, &$count)
{
    foreach ($items as $item) {
        if (isset($item['item'])) {
            countItems($item['item'], $count);
        } else {
            $count++;
        }
    }
}
countItems($data['item'], $count);

echo 'Postman Collection Name: '.$data['info']['name']."\n";
echo 'Postman Version: '.$data['info']['version']."\n";
echo 'Total Request Items: '.$count."\n";
echo 'Collection File Size: '.round(filesize($file) / 1024, 2)." KB\n";
echo "\nPOSTMAN COLLECTION VALIDATED SUCCESSFULLY!\n";
