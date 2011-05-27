<?php

$file_path = BASE_DIR . Url::$query_string;
$less_string = file_get_contents($file_path);
# TODO: less css processor
$css_string = $less_string;
# TODO: headers & cache (if not DEVEL)
echo $css_string;