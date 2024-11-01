<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\Http\Response;
use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Notice;
use Tussendoor\GmbReviews\Helpers\Setting;
use Tussendoor\GmbReviews\Helpers\Request;

class Controller
{    
    /**
     * Method for displaying an admin notice - returns true on succes
     *
     * @param  string   $message    The message for in the notice
     * @param  bool     $state      The state of the notice 1 = succes; 2 = failure; 3 = warning; 4 = info
     * @param  bool     $global     If we want to show to notice globally 
     * @return bool
     */
    protected function adminNotice(string $message, string $errorcode = '', int $state = 1, bool $global = false) : bool
    {   
        $notice = new Notice();
        $notice->setState($state)->setMessage($message);

        if (!empty($errorcode)) {
            $notice->errorCode($errorcode);
        }

        if (($global === false) && ($this->isAdminPage() === false)) return false;
        
        return $notice->create();
    }
    
    /**
     * Simple check to see if the current page is our admin page
     *
     * @return bool
     */
    public function isAdminPage() : bool
    {
        $request = Request::fromGlobal();

        if (!$request->has('page')) return false;

        return $request->getString('page') === Plugin::config('plugin.tag');
    }
    
    /**
     * Remove all files in our cache folder
     * Delete transient to enforce requesting new authentication information after saving client credentials
     * 
     * @param bool $deleteTransients
     */
    protected function emptyCache($deleteTransients = true)
    {
        if ($deleteTransients) {
            delete_transient(Plugin::config('plugin.tag') . '_get_new_authentication_info');
            delete_transient(Plugin::config('plugin.tag') . '_notices');
        }
    
        $cachePath = Plugin::config('plugin.cachepath');
        $caches = glob($cachePath . '*');
    
        foreach ($caches as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Simple method for including a html modal file on the required place
     *
     * @param  string $folder Folder name
     * @param  string $modal  Name of the file
     * @return string (html)
     */
    public function printModal(string $folder, string $modal)
    {
        $file = Plugin::config('plugin.viewpath') . $folder . '/modals/' . $modal . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }

    
    /**
     * HTTP Package is missing the Delete method
     * This simple method gives us the possibility to do a delete request
     *
     * @param  string $uri
     * @param  array $headers
     * @return Response
     */
    protected function deleteRequest(string $uri, array $headers)
    {
        $response = wp_remote_request($uri,[
            'method'    => 'DELETE',
            'headers'   => $headers
        ]);

        return new Response($response);
    }
    
    /**
     * Private method for deleting the authorization 
     * because we do not want child controllers to delete the authorization directly
     */
    public function authorizationExpired()
    {
        Setting::deleteAuthorization();
        Setting::savePluginSetting(['authorization_expired' => true]);
    }


        
    /**
     * Helper method for returning the desired template
     *
     * @param  string $path
     * @param  array $variables
     * @param  string $extension
     * @return void
     */
    public function template(string $path, array $variables = [], string $extension = 'php')
    {
        $file = Plugin::config('plugin.viewpath') . $path . '.' . $extension;
        
        if (! file_exists($file)) return '';
    
        extract($variables);
    
        ob_start();
        require $file;
        echo ob_get_clean();
    }
}