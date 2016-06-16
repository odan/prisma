<?php

namespace App\Controller;

use App\Middleware\AppMiddleware;
use Zend\Diactoros\ServerRequest as Request;

/**
 * AppController
 */
class AppController
{

    /**
     * Get app container
     *
     * @param Request $request
     * @return \App\Container\AppContainer
     */
    public function app(Request $request)
    {
        return $request->getAttribute(AppMiddleware::ATTRIBUTE);
    }

    /**
     * Returns global assets (js, css).
     *
     * @return string
     */
    protected function getAssets()
    {
        // Global assets
        $assets = [];
        $assets[] = 'assets::css/d.css';
        // Customized notifIt with bootstrap colors
        $assets[] = 'assets::css/notifIt-app.css';
        $assets[] = 'view::Index/css/layout.css';
        //$files[] = 'Index/css/print.css';
        $assets[] = 'assets::js/d.js';
        $assets[] = 'assets::js/notifIt.js';
        $assets[] = 'view::Index/js/app.js';

        return $assets;
    }

    /**
     * Returns translated text.
     *
     * @return array
     */
    protected function getTextAssets()
    {
        $result = [];
        $result['Ok'] = __('Ok');
        $result['Cancel'] = __('Cancel');
        $result['Yes'] = __('Yes');
        $result['No'] = __('No');
        return $result;
    }

    /**
     * Set text to global layout
     *
     * @return string
     */
    protected function getJsText($text)
    {
        if (!empty($text)) {
            $js = json_encode($text);
            $js = sprintf("<script>\$d.addText(%s);</script>", $js);
            return $js;
        }
        return '';
    }
}
