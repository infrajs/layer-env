<?php
namespace infrajs\layer\env;

use infrajs\event\Event;
use infrajs\controller\Layer;
use infrajs\config\Config;
use infrajs\template\Template;

Config::get('controller');
Event::handler('Controller.oninit', function () {
	Env::init();
	Layer::parsedAdd( function ($layer) {
		//Рекурсивно собираем все значения в строку
		if (empty($layer['envval'])) return '';
		return json_encode($layer['envval']);
	});
},'env');

Event::handler('Layer.oncheck', function (&$layer) {
	
	Env::checkinit($layer);
	Env::envtochild($layer);
	
	Env::envframe($layer);
	Env::envframe2($layer);

	Env::envmytochild($layer);

	$name = 'myenv';//stencil//
	$nametpl = $name.'tpl';
	if (isset($layer[$nametpl])) {
		if (!isset($layer[$name])) $layer[$name] = array();
		foreach ($layer[$nametpl] as $i => $v) {
			$layer[$name][$i] = Template::parse(array($layer[$nametpl][$i]), $layer);
		}
	}
}, 'env:div,config,external');

Event::handler('Layer.isshow', function (&$layer) {
	return Env::check($layer);
}, 'env:counter,tpl,div,is');