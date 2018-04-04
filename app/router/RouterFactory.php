<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList();
    
    $router[] = new Route('admin/<presenter>/<action>[/<id>]', [
      'module' => 'Back',
      'presenter' => 'Homepage',
      'action'  => 'default'
    ]);
    
    $router[] = new Route('<presenter>/<action>[/<id>]', [
      'module' => 'Front',
      'presenter' => 'Homepage',
      'action'  => 'default'
    ]);
    
		return $router;
	}
	
}
