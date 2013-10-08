<?php

# Provides
global $routes;

hook_register('compile_routes', function(&$routes) {
	foreach($routes as $routeName => &$routeInfo) {
		if(!isset($routeInfo['regex'])) {
			$regex = preg_quote($routeInfo['url'], '@');
			$regex = preg_replace('/(\\\:(\w+))/', '(?<${2}>\w+)', $regex, -1, $count);
			$routeInfo['regex'] = '@^'.$regex.'$@i';
			$routeInfo['needs_regex'] = $count > 0;
		} else {
			$routeInfo['needs_regex'] = TRUE;
		}
		if(!isset($routeInfo['path_generator'])) {
			$url = $routeInfo['url'];
			$routeInfo['path_generator'] = '\''.preg_replace('/:(\w+)/', '\' . \$args[\'${1}\'] . \'', $url).'\'';
		}
		if(!empty($routeInfo['args']) && !isset($routeInfo['args_generator'])) {
			$args_g = var_export($routeInfo['args'], true);
			$routeInfo['args_generator'] = preg_replace("/':(\w+)'/",'\$matches[\'${1}\']', $args_g);
		}
	}
});

hook_register('bootstrap', function() {
	global $routes;

	$cache_id = 'router/routes';
	if($cached_routes = cache_get($cache_id)) {
		$routes = $cached_routes;
	} else {
		foreach((array) explode(PATH_SEPARATOR, get_include_path()) as $module) {
			if(is_dir($module.'routes')) {
				foreach((array) scandir($module.'routes') as $file) {
					if($file && $file[0] !== '.') {
						require "{$module}/routes/{$file}";
					}
				}
			}
		}
		hook_invoke('compile_routes', $routes);
		cache_set($cache_id, $routes);
	}
});

function router_match($url, $method = 'GET') {
	global $routes;

	foreach($routes as $routeName => $routeInfo) {

		if (!empty($routeInfo['methods']) && !in_array($method, $routeInfo['methods'])) continue;

		if ($routeInfo['needs_regex'] && !preg_match($routeInfo['regex'], $url, $matches)) continue;

		if (!$routeInfo['needs_regex'] && $routeInfo['url'] != $url) continue;

		if(isset($routeInfo['args_generator']))
			$args = eval('return '.$routeInfo['args_generator'].';');
		else
			$args = (array) @$routeInfo['args'];

		return array(
			'route' => $routeName,
			'controller' => $routeInfo['controller'],
			'method' => $method,
			'args' => $args,
		);
	}
}

function router_match_current() {
	global $request_path;
	global $request_method;
	if(empty($request_path))
		$request_path = 'index';

	return router_match($request_path, $request_method);
}

function router_load_controller() {
	$route = router_match_current();
	if(!$route) {
		if(config('devel')) {
			throw new FrameworkException(t('No route found.'), 404);
		} else {
			show_404();
		}
	}
	return load_controller($route['controller'], $route['args']);
}

function router_path($routeName, array $args = array()) {
	global $routes;

	$routeInfo = @$routes[$routeName];

	if(empty($routeInfo['path_generator']))
		throw new FrameworkException(t('The route named "@name" could not be found.', array('@name' => $routeName)));

	return eval('return '.$routeInfo['path_generator'].';');
}

function router_url($routeName, array $args = array()) {
	return url(router_path($routeName, $args));
}