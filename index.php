<?php

# Load the framework
require_once 'framework/bootstrap.php';

# Get the request parts
global $request_segments;

# Load and print the controller
echo load_controller($request_segments);
