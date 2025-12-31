<?php
use ayutenn\core\config\Config;

$resource_dir = URL_ROOT;
$asset_path = APP_ROOT . Config::get('APP_DIR') . "/public/assets";
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
            $css_file_path = substr($file->getPathname(), $path_head_length);
            $css_link_tag .= "<link href='./assets/{$css_file_path}' rel='stylesheet'>\r\n";
        }
        if ($file->getExtension() === 'js') {
            $js_file_path = substr($file->getPathname(), $path_head_length);
            $js_script_tag .= "<script src='./assets/{$js_file_path}'></script>\r\n";
        }
    }
}

?>
<base href="<?= URL_ROOT ?>/">

<meta name='viewport' content='width=device-width, initial-scale=1'>
<meta charset='utf-8'>

<!-- favicon -->
<link rel='icon' href='./app/public/img/favicon/favicon.png' id='favicon'>

<!-- fonts -->
<link rel='preconnect' href='https://fonts.googleapis.com'>
<link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
<link href='https://fonts.googleapis.com/css2?family=Shippori+Antique&display=swap' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css2?family=Squada+One&display=swap' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css2?family=Germania+One&display=swap' rel='stylesheet'>

<!-- CSS -->
<?= $css_link_tag ?>

<!-- js -->
<?= $js_script_tag ?>

<script type='text/javascript'>
    function check(){
        if(window.confirm('本当によいですか？')){
            return true;
        }else{
            return false;
        }
    }
</script>