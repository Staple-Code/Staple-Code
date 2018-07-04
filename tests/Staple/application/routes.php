<?php
use Staple\Route;

/**
 * Do this action before the any routes are executed.
 */
Route::before(function() {

});

/** Static Routes */
Route::add('/text', function() {
	return 'STAPLE Framework';
});

/**
 * Actions to perform after routes are executed.
 */
Route::after(function () {

});