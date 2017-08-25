Event.one('Controller.oninit', function () {
	Controller.parsedAdd(function(layer, r){
		//Рекурсивно собираем все значения в строку
		if (!layer.envval) return '';
		return Hash.exec(layer.envval);
	});
}, 'env');

Event.handler('Layer.oncheck', function (layer){
	
	
	Controller.envcheckinit(layer);
	Controller.envtochild(layer)

	Controller.envframe(layer);
	Controller.envframe2(layer);

	Controller.envmytochild(layer);
	
	
	var name = 'myenv';//stencil//
	var nametpl = name + 'tpl';
	if (layer[nametpl]) {
		if (!layer[name]) layer[name] = { };
		for (var i in layer[nametpl]) {
			layer[name][i] = Template.parse([layer[nametpl][i]], layer);
		}
	}

}, 'env:div,config,external');


Event.handler('Layer.isshow', function (layer){
	//env, counter
	return Controller.envCheck(layer);
}, 'env:counter,tpl,div,is');