<?php
$resource_dir = URL_ROOT;
$asset_path = PROJECT_ROOT . "/app/public/assets";
$path_head_length = strlen($asset_path);

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($asset_path)
);

// css・js一括読み込み
$css_link_tag = '';
$js_script_tag = '';
foreach ($iterator as $file) {
    if ($file->isFile()) {
        // ファイル名が _ から始まる場合はスキップ
        if (str_starts_with($file->getFilename(), '_')) {
            continue;
        }

        if ($file->getExtension() === 'css') {
            $css_file_path = str_replace('\\', '/', substr($file->getPathname(), $path_head_length));
            $css_link_tag .= "<link href='assets{$css_file_path}' rel='stylesheet'>\r\n";
        }
        if ($file->getExtension() === 'js') {
            $js_file_path = str_replace('\\', '/', substr($file->getPathname(), $path_head_length));
            $js_script_tag .= "<script src='assets{$js_file_path}'></script>\r\n";
        }
    }
}

?>
<base href="<?= URL_ROOT ?>/">

<meta name='viewport' content='width=device-width, initial-scale=1'>
<meta charset='utf-8'>

<!-- favicon -->
<link rel='icon' href='assets/img/common/icon.png' id='favicon'>

<!-- CSS -->
<?= $css_link_tag ?>

<!-- js -->
<?= $js_script_tag ?>
