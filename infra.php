<?php
namespace infrajs\layer\env;
use infrajs\path\Path;
use infrajs\event\Event;
use infrajs\hash\Hash;
use infrajs\controller\Layer;
use infrajs\config\Config;

Config::get('controller');
Event::handler('Controller.oninit', function () {
	Env::init();
	Layer::parsedAdd( function ($layer) {
		//Рекурсивно собираем все значения в строку
		if (empty($layer['envval'])) return '';
		return Hash::make($layer['envval']);
	});
});

Event::handler('Layer.oncheck', function (&$layer) {
	Env::checkinit($layer);
}, 'env:config,external');
Event::handler('Layer.oncheck', function (&$layer) {
	Env::envtochild($layer);
}, 'env:config,external');
Event::handler('Layer.oncheck', function (&$layer) {
	Env::envframe($layer);
}, 'env:config,external');
Event::handler('Layer.oncheck', function (&$layer) {
	Env::envframe2($layer);
}, 'env:config,external');
Event::handler('Layer.oncheck', function (&$layer) {
	Env::envmytochild($layer);
}, 'env:config,external');

Event::handler('Layer.isshow', function (&$layer) {
	return Env::check($layer);
}, 'env:counter,tpl,div,is');