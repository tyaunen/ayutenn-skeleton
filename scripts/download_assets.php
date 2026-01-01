<?php

// CDNからのアセットダウンロード
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

// ayutenn-cssからのファイルコピー
$vendorPath = 'vendor/tyaunen/ayutenn-css';

if (is_dir($vendorPath)) {
    echo "\nCopying ayutenn-css files...\n";

    // CSSファイルのコピー
    $cssSrc = $vendorPath . '/assets/css/ayutenn';
    $cssDest = 'app/public/assets/css/@lib/ayutenn';

    if (is_dir($cssSrc)) {
        copyDirectory($cssSrc, $cssDest);
        echo "CSS files copied to $cssDest\n";
    } else {
        echo "Warning: CSS source directory not found: $cssSrc\n";
    }

    // JSファイルのコピー
    $jsSrc = $vendorPath . '/assets/js/ayutenn';
    $jsDest = 'app/public/assets/js/@lib/ayutenn';

    if (is_dir($jsSrc)) {
        copyDirectory($jsSrc, $jsDest);
        echo "JS files copied to $jsDest\n";
    } else {
        echo "Warning: JS source directory not found: $jsSrc\n";
    }

    // ワークフローファイルのコピー
    $workflowSrc = $vendorPath . '/.agent/workflows';
    $workflowDest = '.agent/workflows/css';

    if (is_dir($workflowSrc)) {
        copyDirectory($workflowSrc, $workflowDest);
        echo "Workflow files copied to $workflowDest\n";
    } else {
        echo "Warning: Workflow source directory not found: $workflowSrc\n";
    }

    echo "ayutenn-css files copied successfully.\n";
} else {
    echo "Warning: ayutenn-css vendor directory not found. Skipping file copy.\n";
}

// ayutenn-coreからのワークフローファイルのコピー
$coreVendorPath = 'vendor/tyaunen/ayutenn-core';

if (is_dir($coreVendorPath)) {
    echo "\nCopying ayutenn-core workflow files...\n";

    $coreWorkflowSrc = $coreVendorPath . '/workflows';
    $coreWorkflowDest = '.agent/workflows/core';

    if (is_dir($coreWorkflowSrc)) {
        copyDirectory($coreWorkflowSrc, $coreWorkflowDest);
        echo "Core workflow files copied to $coreWorkflowDest\n";
    } else {
        echo "Warning: Core workflow source directory not found: $coreWorkflowSrc\n";
    }

    echo "ayutenn-core workflow files copied successfully.\n";
} else {
    echo "Warning: ayutenn-core vendor directory not found. Skipping workflow copy.\n";
}


/**
 * ディレクトリを再帰的にコピーする
 *
 * @param string $src コピー元ディレクトリ
 * @param string $dest コピー先ディレクトリ
 */
function copyDirectory($src, $dest) {
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $destPath = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }
        } else {
            copy($item, $destPath);
        }
    }
}
