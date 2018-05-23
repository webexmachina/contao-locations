<?php

/**
 * Module Locations for Contao Open Source CMS
 *
 * Copyright (c) 2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\Location\Controller;

use Contao\Combiner;
use Contao\Controller;

/**
 * Provide utilities function to Locations Extension
 */
class ClassLoader extends Controller
{
	/**
	 * Load the Map Provider Libraries
	 * @param  [String]  $strProvider [Provider wanted]
	 * @param  [Integer] $strVersion  [File Versions]
	 */
	public static function loadLibraries($strProvider, $strVersion = 1){
		switch($strProvider){
			case 'jvector':
				$objCombiner = new Combiner();
				$objCombiner->addMultiple([
					"system/modules/wem-contao-locations/assets/vendor/jquery-jvectormap/jquery-jvectormap-2.0.3.css"
					,"system/modules/wem-contao-locations/assets/css/jvector.css"
				], $strVersion);
				$GLOBALS["TL_HEAD"][] = sprintf('<link rel="stylesheet" href="%s">', $objCombiner->getCombinedFile());

				$objCombiner = new Combiner();
				$objCombiner->addMultiple([
					"system/modules/wem-contao-locations/assets/vendor/jquery-jvectormap/jquery-jvectormap-2.0.3.min.js"
					,"system/modules/wem-contao-locations/assets/vendor/jquery-jvectormap/maps/jquery-jvectormap-world-mill.js"
					,"system/modules/wem-contao-locations/assets/js/jvector.js"
				], $strVersion);
				$GLOBALS['TL_JAVASCRIPT'][] = $objCombiner->getCombinedFile();
			break;
			default:
				throw new \Exception("This provider is unknown");
		}
	}
}