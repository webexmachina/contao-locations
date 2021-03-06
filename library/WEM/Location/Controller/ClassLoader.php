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
use Contao\Encryption;

/**
 * Provide utilities function to Locations Extension
 */
class ClassLoader extends Controller
{
    /**
     * Correctly load a generic Provider
     * Not used for now, but keep it for later !
     * @param  [String] $strProvider [Provider classname]
     * @return [Object]              [Provider class]
     */
    public static function loadProviderClass($strProvider)
    {
        try {
            // Parse the classname
            $strClass = sprintf("WEM\Location\Controller\Provider\%s", ucfirst($strProvider));

            // Throw error if class doesn't exists
            if (!class_exists($strClass)) {
                throw new Exception(sprintf("Unknown class %s", $strClass));
            }

            // Create the object
            $objProvider = new $strClass;

            // And return
            return $objProvider;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Load the Map Provider Libraries
     * @param  [Object]  $objMap      [Map model]
     * @param  [Integer] $strVersion  [File Versions]
     */
    public static function loadLibraries($objMap, $strVersion = 1)
    {
        // Generate the combiners
        $objCssCombiner = new Combiner();
        $objJsCombiner = new Combiner();
        
        // Load generic files
        $objCssCombiner->add("system/modules/wem-contao-locations/assets/css/default.css", $strVersion);
        $objJsCombiner->add("system/modules/wem-contao-locations/assets/js/default.js", $strVersion);

        // Depending on the provider, we will need more stuff
        switch ($objMap->mapProvider) {
            case 'jvector':
                $objCssCombiner->addMultiple([
                    "system/modules/wem-contao-locations/assets/vendor/jquery-jvectormap/jquery-jvectormap-2.0.3.css"
                    ,"system/modules/wem-contao-locations/assets/css/jvector.css"
                ], $strVersion);
                $objJsCombiner->addMultiple([
                    "system/modules/wem-contao-locations/assets/vendor/jquery-jvectormap/jquery-jvectormap-2.0.3.min.js"
                    ,"system/modules/wem-contao-locations/assets/vendor/jquery-jvectormap/maps/jquery-jvectormap-".$objMap->mapFile."-mill.js"
                    ,"system/modules/wem-contao-locations/assets/js/jvector.js"
                ], $strVersion);
                break;
            case 'gmaps':
                if (!$objMap->mapProviderGmapKey) {
                    throw new \Exception("Google Maps needs an API Key !");
                }

                $objCssCombiner->add("system/modules/wem-contao-locations/assets/css/gmaps.css", $strVersion);
                $objJsCombiner->add("system/modules/wem-contao-locations/assets/js/gmaps.js", $strVersion);
                $GLOBALS["TL_JQUERY"][] = sprintf('<script src="https://maps.googleapis.com/maps/api/js?key=%s"></script>', $objMap->mapProviderGmapKey);
                break;
            case 'leaflet':
                $objCssCombiner->addMultiple([
                    "system/modules/wem-contao-locations/assets/vendor/leaflet/leaflet.css"
                    ,"system/modules/wem-contao-locations/assets/css/leaflet.css"
                ], $strVersion);
                $objJsCombiner->addMultiple([
                    "system/modules/wem-contao-locations/assets/vendor/leaflet/leaflet.js"
                    ,"system/modules/wem-contao-locations/assets/js/leaflet.js"
                ], $strVersion);
                break;
            default:
                throw new \Exception("This provider is unknown");
        }

        // And add them to pages
        $GLOBALS["TL_HEAD"][] = sprintf('<link rel="stylesheet" href="%s">', $objCssCombiner->getCombinedFile());
        $GLOBALS["TL_JQUERY"][] = sprintf('<script src="%s"></script>', $objJsCombiner->getCombinedFile());
    }
}
