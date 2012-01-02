<?php

# boot timer.
$timers['boot']['start'] = START_TIME;

/**
 * Start the timer with the specified name. If you start and stop
 * the same timer multiple times, the measured intervals will be
 * accumulated.
 *
 * @param $name	The name of the timer.
 */
function timer_start($name) {
	global $timers;

	$timers[$name]['start'] = microtime(TRUE);
	$timers[$name]['count'] = isset($timers[$name]['count']) ? ++$timers[$name]['count'] : 1;
}

/**
 * Read the current timer value without stopping the timer.
 *
 * @param $name	The name of the timer.
 * @return	The current timer value in ms.
 */
function timer_read($name) {
	global $timers;

	if (isset($timers[$name]['start'])) {
		$stop = microtime(TRUE);
		$diff = round(($stop - $timers[$name]['start']) * 1000, 2);

		if (isset($timers[$name]['time'])) {
			$diff += $timers[$name]['time'];
		}
		return $diff;
	}
	else if(isset($timers[$name]['time'])) {
		return $timers[$name]['time'];
	}
}

/**
 * Stop the timer with the specified name.
 *
 * @param $name	The name of the timer.
 * @return	A timer array. The array contains the number of times the
 * timer has been started and stopped (count) and the accumulated
 * timer value in ms (time).
 */
function timer_stop($name) {
	global $timers;

	if(isset($timers[$name]['start']))
	{
		$timers[$name]['time'] = timer_read($name);
		unset($timers[$name]['start']);
	}
	else if( !isset($timers[$name]['time']) )
	{
		$timers[$name]['time'] = 0;
	}

	return $timers[$name];
}

function timer_read_all() {
	global $timers;
	$values = array();
	foreach($timers as $name => $_) {
		$values[$name] = timer_read($name);
	}
	return $values;
}
