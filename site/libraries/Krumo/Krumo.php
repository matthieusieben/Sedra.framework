<?php

# Increase string width
define('KRUMO_TRUNCATE_LENGTH', 100);

# Include the lib
require_once 'libraries/krumo/krumo/class.krumo.php';

# Unregister any other dumper and setup krumo as one
Hook::register(HOOK_DUMP, 'krumo', TRUE);
