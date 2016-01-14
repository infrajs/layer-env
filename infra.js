Event.one('Infrajs.oninit', function () {
	infrajs.parsedAdd('envval');
}, 'env');

Event.handler('layer.oncheck', function (layer){
	//envs
	infrajs.envEnvs(layer);
}, 'env:div');
Event.handler('layer.oncheck', function (layer){
	//envframe
	infrajs.envframe(layer);
}, 'env:div');
Event.handler('layer.oncheck', function (layer){
	//envframe
	infrajs.envframe2(layer);
}, 'env:div');
Event.handler('layer.oncheck', function (layer){//external то ещё не применился нельзя
	//env myenvtochild
	infrajs.envmytochild(layer);
}, 'env:div');
Event.handler('layer.oncheck', function (layer){//external то ещё не применился нельзя
	//envtochild
	infrajs.envtochild(layer)
}, 'env:div');


Event.handler('layer.isshow', function (layer){
	//env, counter
	return infrajs.envCheck(layer);
}, 'env:div');