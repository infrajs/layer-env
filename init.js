import { Controller } from '/vendor/infrajs/controller/src/Controller.js'
import { Event } from '/vendor/infrajs/event/Event.js'
import { External } from '/vendor/infrajs/controller/src/External.js'
import { Parsed } from '/vendor/infrajs/controller/src/Parsed.js'
import { Template } from '/vendor/infrajs/template/Template.js'

External.add('myenv', 'config');//Обрабатывается также как config
External.add('env', '');//Никак не обрабатывается.. будет установлено только если нечего небыло
External.add('envs', 'childs');//Объединяется так же как childs
Controller.runAddKeys('envs');//Теперь бегаем и по envs свойству


let envCheck = function (layer) {
	if (!layer.env) return;
	var store = Controller.store();
	if (!store.ismainrun) {
		return !!layer['envval'];
	}
	//Слои myenv надо показывать тогдаже когда и показывается сам слой
	var myenv, ll;
	Controller.run(Controller.getWorkLayers(), function (l) {//Есть окружение и мы не нашли ни одного true для него
		if (!l.myenv) return;
		if (!Event.fire('Layer.ischeck', l)) return;//В back режиме выйти нельзя.. смотрятся все слои
		if (l === layer) return;//Значение по умолчанию смотрится отдельно 
		if (l.myenv[layer.env] === undefined) return;
		if (Event.fire('Layer.isshow', l)) {//Ищим последнюю установку на счёт env
			myenv = l.myenv[layer.env];
			ll = l;
		}
	});
	var r;
	if (typeof (myenv) !== 'undefined') {//Если слой скрываем слоем окружения который у него в родителях числиться он после этого сам всё равно должен показаться
		if (myenv) {//Значение по умолчанию смотрим только если myenv undefined
			r = true;
		} else {
			r = false;
			layer.is_save_branch = !!Controller.isParent(ll, layer);
			//Controller.isSaveBranch(layer,false);
		}
	}
	if (typeof (r) == 'undefined' && layer.myenv) {//Значение по умолчанию
		var myenv = layer.myenv[layer.env];
		if (myenv !== undefined) {//Оо есть значение по умолчанию для самого себя
			if (myenv) {
				r = true;
			} else {//Если слой по умолчанию скрыт его детей не показываем
				r = false;
				layer.is_save_branch = false;
			}
		}
	}
	layer.envval = myenv;
	if (r) return !!myenv;
	return false;
};


//myenv:(object),//Перечислены env которые нужно показать и значения которые им нужно передать в envval
//env:(string),//Имя окружения которое нужно укзать чтобы слой с этим свойством показался
//envval:(mix),//Значение, которое было установленое в myenv. envval устанавливается автоматически, в ручную устанавливать его нельзя



//Обработка envs, envtochild, myenvtochild


let envcheckinit = function (layer) {
	if (!layer.envs) return;
	infra.forx(layer.envs, function (l, env) {
		Controller.run(l, function (la) {
			if (!la.env) la.env = env;
			la.envtochild = true;
		});
	});
}

let envtochild = function (layer) {
	var par = layer;
	while (par.parent && par.parent.env) {
		par = par.parent;
		if (par['envtochild']) {
			layer['env'] = par['env'];
			return;
		}
	}
}

let envframe = function (layer) {
	if (!layer['envframe']) return;
	if (layer['env']) return;

	var stor = infra.stor();
	if (!stor['envcouter']) stor['envcouter'] = 0;
	stor['envcouter']++;
	layer['env'] = 'envframe' + stor['envcouter'];
}
let envframe2 = function (layer) {
	var par = layer['parent'];
	if (!par) return;
	if (!par['envframe']) return;
	if (!layer['myenv']) layer['myenv'] = {};
	layer['myenv'][par['env']] = true;
	layer['myenvtochild'] = true;
}

let envmytochild = function (layer) {
	var par = layer;
	while (par.parent && par.parent.myenv) {
		par = par.parent;
		if (par['myenvtochild']) {
			if (!layer['myenv']) layer['myenv'] = {};
			for (var i in par['myenv']) {
				layer['myenv'][i] = par['myenv'][i];
			}
			return;
		}
	}
}

Parsed.add(function(layer, r){
    //Рекурсивно собираем все значения в строку
    if (!layer.envval) return '';
    return JSON.stringify(layer.envval);
});

Event.handler('Layer.oncheck', function (layer){
	envcheckinit(layer);
	envtochild(layer)
	envframe(layer);
	envframe2(layer);
	envmytochild(layer);
	
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
	return envCheck(layer);
}, 'env:counter,tpl,div,is');