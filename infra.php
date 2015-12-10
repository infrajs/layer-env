<?php
namespace infrajs\controller;
use infrajs\path\Path;
use infrajs\event\Event;

Event::handler('oninit', function () {
	ext\env::init();
});

Event::handler('layer.oncheck', function (&$layer) {
	ext\env::checkinit($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	ext\env::envtochild($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	ext\env::envframe($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	ext\env::envframe2($layer);
}, 'env:config,external');
Event::handler('layer.oncheck', function (&$layer) {
	ext\env::envmytochild($layer);
}, 'env:config,external');

Event::handler('layer.isshow', function (&$layer) {
	return ext\env::check($layer);
}, 'env:counter,tpl,div,is');