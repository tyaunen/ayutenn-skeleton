<?php

$assets = [
    'https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js' => 'app/public/assets/js/@lib/axios.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js' => 'app/public/assets/js/@lib/Sortable.min.js',
];

foreach ($assets as $url => $path) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    echo "Downloading $url to $path ...\n";

    $content = file_get_contents($url);
    if ($content === false) {
        echo "Failed to download $url\n";
        exit(1);
    }

    if (file_put_contents($path, $content) === false) {
        echo "Failed to save to $path\n";
        exit(1);
    }

    echo "Done.\n";
}
