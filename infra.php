<?php
namespace infrajs\layer\env;
use infrajs\path\Path;
use infrajs\event\Event;
use infrajs\controller\Layer;
use infrajs\config\Config;

Config::get('controller');
Event::handler('Infrajs.oninit', function () {
	Env::init();
	Layer::parsedAdd('envval');
});

Event::handler('layer.oncheck', function (&$layer) {
	Env::checkinit($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	Env::envtochild($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	Env::envframe($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	Env::envframe2($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	Env::envmytochild($layer);
}, 'env:config,external');

Event::handler('layer.isshow', function (&$layer) {
	return Env::check($layer);
}, 'env:counter,tpl,div,is');