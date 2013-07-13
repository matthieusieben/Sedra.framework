</head>
<?php global $request_path, $controller ?>
<?php $_attr['class'][] = 'controller-'.$controller ?>
<?php $_attr['class'][] = 'path-'.str_replace('/','-',$request_path) ?>
<body<?php echo attributes($_attr) ?>>