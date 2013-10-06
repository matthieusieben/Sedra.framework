<?php

# Provides
global $routes;

hook_register('bootstrap', function() {
	global $routes;

	$cache_id = "router/routes";
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
		hook_invoke('routes', $routes);
		cache_set($cache_id, $routes);
	}
});

function router_match($url, $method = 'GET') {
	global $routes;

	foreach($routes as $routeName => $routeInfo) {

		if (!empty($routeInfo['methods']) && !in_array($method, $routeInfo['methods'])) continue;

		if (!preg_match('@^'.$routeInfo['url'].'$@i', $url, $matches)) continue;

		$args = (array) @$routeInfo['args'];
		foreach($args as &$arg) {
			if($arg && $arg[0] === '$') {
				$arg = @$matches[substr($arg, 1)];
			}
		}

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
	if(!$route) show_404();
	return load_controller($route['controller'], @$route['args']);
}

function _router_create_path($matches) {
	global $_router_args;
	return @$_router_args[$matches[1]];
}

function router_create_path($route_name, array $args = array()) {
	global $routes;
	$route = @$routes[$route_name];
	if(!@$route['url']) return null;
	global $_router_args;
	$_router_args = $args;
	return preg_replace_callback('@\(\?\<(\w+)\>[^\)]+\)@i', '_router_create_path', $route['url']);
}