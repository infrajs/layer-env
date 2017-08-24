Event.one('Controller.oninit', function () {
	Controller.parsedAdd(function(layer, r){
		//Рекурсивно собираем все значения в строку
		if (!layer.envval) return '';
		return Hash.exec(layer.envval);
	});
}, 'env');

Event.handler('Layer.oncheck', function (layer){
	//envs
	Controller.envEnvs(layer);
}, 'env:div');
Event.handler('Layer.oncheck', function (layer){
	//envframe
	Controller.envframe(layer);
}, 'env:div');
Event.handler('Layer.oncheck', function (layer){
	//envframe
	Controller.envframe2(layer);
}, 'env:div');
Event.handler('Layer.oncheck', function (layer){//external то ещё не применился нельзя
	//env myenvtochild
	Controller.envmytochild(layer);
}, 'env:div');
Event.handler('Layer.oncheck', function (layer){//external то ещё не применился нельзя
	//envtochild
	Controller.envtochild(layer)
}, 'env:div');


Event.handler('Layer.isshow', function (layer){
	//env, counter
	return Controller.envCheck(layer);
}, 'env:div');