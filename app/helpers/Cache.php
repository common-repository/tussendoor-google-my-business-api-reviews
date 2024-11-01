<?php namespace Tussendoor\GmbReviews\Helpers;

use Tussendoor\GmbReviews\Plugin;

class Cache
{
    /**
     * Save array values as json content to a file in our cache folder
     *
     * @param  string   $filename   Name of the file
     * @param  array    $content    The content to save as json file
     * @return void
     */
    public static function setJsonCache(string $filename, array $content)
    {
        $json = json_encode($content);
        file_put_contents(Plugin::config('plugin.cachepath') . $filename .'.json', $json);
    }
    
    /**
     * Get saved json content and return in string type
     *
     * @param  string $filename Name of the file
     * @return string
     */
    public static function getJsonCache(string $filename) : string
    {
        $file = Plugin::config('plugin.cachepath') . $filename .'.json';
    
        // check if file exists, file is empty or file is older then two hours
        if (!file_exists($file)
            || filesize($file) == 0
            || (time() - filemtime($file)) > (2 * 3600)
        ){
            return '';
        }
    
        return (!file_get_contents($file) === false ? file_get_contents($file) : '');
    }
    
     /**
      * Save array values as json content to a file in our cache folder
      *
      * @param  string   $filename   Name of the file
      * @return void
      */
     public static function deleteJsonCache(string $filename)
     {
        $file = Plugin::config('plugin.cachepath') . $filename . '.json';
        if (file_exists($file)) {
            unlink($file);
        }
     }
    
    /**
     * Simple method for returning the filename corresponding to the selected location
     *
     * @param  string $selectedLocation
     * @return string
     */
    public static function getFileName(string $selectedLocation) : string
    {
        return str_replace(['/', 'accounts', 'locations'], '', $selectedLocation);
    }
}