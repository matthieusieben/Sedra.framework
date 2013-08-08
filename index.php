<?php

# Load the framework
require_once 'framework/bootstrap.php';

# Get the requested url parts
global $request_segments;

# Load and print the corresponding controller
echo load_controller($request_segments);
