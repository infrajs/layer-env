<?php

namespace infrajs\layer\env;
use infrajs\event\Event;
use infrajs\each\Each;
use infrajs\controller\Layer;
use infrajs\controller\External;
use infrajs\controller\Run;
use infrajs\controller\Controller;

class env
{
	public static function init()
	{
		global $infra,$infrajs;
		Event::handler('Controller.oninit', function () {
			//Обработка envs, envtochild, myenvtochild, envframe
			External::add('myenv', 'config');//Обрабатывается также как config
			//external::add('env', '');//Никак не обрабатывается.. будет установлено только если нечего небыло
			External::add('envs', 'childs');//Объединяется так же как childs

			Run::runAddKeys('envs');//Теперь бегаем и по envs свойству
		});
	}
	public static function check(&$layer)
	{
		$r = null;
		if (!isset($layer['env'])) return $r;
		//Слои myenv надо показывать тогдаже когда и показывается сам слой
		$myenv = null;
		$ll = null;
		Run::exec(Controller::$layers, function &(&$l) use (&$layer, &$myenv, &$ll) {
			//Есть окружение и мы не нашли ни одного true для него
			$r = null;
			if (!isset($l['myenv'])) return $r;
			if (!Event::fire('Layer.ischeck', $l)) return $r; //В back режиме выйти нельзя.. смотрятся все слои
			
			if (Each::isEqual($l, $layer)) return $r; //Значение по умолчанию смотрится отдельно

			if (!isset($l['myenv'][$layer['env']])) return $r;
			
			if (is_null($l['myenv'][$layer['env']])) return $r;

			if (Event::fire('Layer.isshow', $l)) {
				//Ищим последнюю установку на счёт env
				$myenv = $l['myenv'][$layer['env']];
				$ll = &$l;
			}

			return $r;
		});

		if (!is_null($myenv)) {
			//Если слой скрываем слоем окружения который у него в родителях числиться он после этого сам всё равно должен показаться
			if ($myenv) {
				//Значение по умолчанию смотрим только если myenv undefined
				$r = true;
			} else {
				$r = false;
				$layer['is_save_branch'] = !!Layer::isParent($ll, $layer);
				//infrajs_isSaveBranch($layer,false);
			}
		}
		if (is_null($r) && !empty($layer['myenv'])) {
			//Значение по умолчанию
			$myenv = $layer['myenv'][$layer['env']];
			if (!is_null($myenv)) {
				//Оо есть значение по умолчанию для самого себя
				if ($myenv) {
					$r = true;
				} else {
					//Если слой по умолчанию скрыт его детей не показываем
					$r = false;
					$layer['is_save_branch'] = false;
				}
			}
		}
		$layer['envval'] = $myenv;
		if ($r) {
			return !!$myenv;
		}
		$r = false;
		return $r;
	}

//myenv:(object),//Перечислены env которые нужно показать и значения которые им нужно передать в envval
	//env:(string),//Имя окружения которое нужно укзать чтобы слой с этим свойством показался
	//envval:(mix),//Значение, которое было установленое в myenv. envval устанавливается автоматически, в ручную устанавливать его нельзя


/*
	//когда есть главная страница и структура вложенных слоёв, но вложенные показываются не при всех состояниях и иногда нужно показать главную страницу. Это не правильно. Адреса должны автоматически нормализовываться.
	//Если такого состояния нет нужно сделать редирект на главную и по этому задачи показывать главную во внутренних состояниях отпадает
	//при переходе на клиенте должно быть сообщение страницы нет, а при обновлении постоянный редирект на главную или на страницу поиска
	infra.listen(infra,'Layer.oncheck',function(){
		//myenv Наследуется от родителя только когда совсем ничего не указано. Если хоть что-то указано от родителя наследования не будет.
		var layer=this;
		if(layer.myenv)return;
		if(!layer.parent||!layer.parent.myenv)return;
		layer.myenv={};
		infra.foro(layer.parent.myenv,function(v,k){
			layer.myenv[k]=v;
		});
	});
	*/

	public static function checkinit(&$layer)
	{
		if (empty($layer['envs'])) {
			return;
		}
		Each::forx($layer['envs'], function (&$l, $env) {
			//Из-за забегания вперёд external не применился а в external могут быть вложенные слои
			$l['env'] = $env;
			$l['envtochild'] = true;
		});
	}
	public static function envtochild(&$layer)
	{
		$parent = $layer;
		while (!empty($parent['parent']) && !empty($parent['parent']['env'])) {
			$parent = $parent['parent'];
			if (!empty($parent['envtochild'])) {
				$layer['env'] = $parent['env'];

				return;
			}
		}
	}
	public static function envframe(&$layer)
	{
		if (empty($layer['envframe'])) return;
		if (!empty($layer['env'])) return;

		$stor = infra_stor();
		if (empty($stor['envcouter'])) $stor['envcouter'] = 0;
		++$stor['envcouter'];
		$layer['env'] = 'envframe'.$stor['envcouter'];
	}
	public static function envframe2(&$layer)
	{
		if (empty($layer['parent'])) return;
		$parent = $layer['parent'];
		if (empty($parent['envframe'])) return;
		
		if (empty($layer['myenv'])) $layer['myenv'] = array();
		
		$layer['myenv'][$parent['env']] = true;
		$layer['myenvtochild'] = true;
	}
	public static function envmytochild(&$layer)
	{
		$parent = $layer;
		while (!empty($parent['parent']) && !empty($parent['parent']['myenv'])) {
			$parent = $parent['parent'];
			if (!empty($parent['myenvtochild'])) {
				if (!isset($layer['myenv'])) {
					$layer['myenv'] = array();
				}
				foreach ($parent['myenv'] as $i => $v) {
					$layer['myenv'][$i] = $parent['myenv'][$i];
				}

				return;
			}
		}
	}
}
