<?php

$controller = 'account/' . url_segment(1, 'index');

return load_controller($controller);