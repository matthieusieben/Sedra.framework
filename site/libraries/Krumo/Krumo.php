<?php

# Increase string width
define('KRUMO_TRUNCATE_LENGTH', 100);

# Include the lib
require_once 'libraries/krumo/krumo/class.krumo.php';

# Unregister any other dumper
hook(HOOK_DUMP, NULL, FALSE);

# Register krumo as dumper
hook(HOOK_DUMP, NULL, 'krumo');
