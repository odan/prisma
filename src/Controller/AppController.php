<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * AppController
 */
class AppController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function getRequest()
    {
        return $this->request;
    }

    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns global assets (js, css).
     *
     * @param array $assets Assets
     * @return array
     */
    protected function getAssets(array $assets = array())
    {
        // Global assets
        $result = [];
        $result[] = 'assets::css/d.css';
        // Customized notifIt with bootstrap colors
        $result[] = 'assets::css/notifIt-app.css';
        $result[] = 'view::Index/css/layout.css';
        //$files[] = 'Index/css/print.css';
        $result[] = 'assets::js/d.js';
        $result[] = 'assets::js/notifIt.js';
        $result[] = 'view::Index/js/app.js';

        if (!empty($assets)) {
            $result = array_merge($result, $assets);
        }
        return $result;
    }

    /**
     * Returns translated text.
     *
     * @param array $text Text
     * @return array
     */
    protected function getText(array $text = array())
    {
        // Default text
        $result = [];
        $result['Ok'] = __('Ok');
        $result['Cancel'] = __('Cancel');
        $result['Yes'] = __('Yes');
        $result['No'] = __('No');

        if (!empty($text)) {
            $result = array_replace_recursive($result, $text);
        }
        return $result;
    }

    public function getData(Request $request, array $data = null)
    {
        $result = [
            'baseurl' => $request->getAttribute('base_url'),
        ];
        if (!empty($data)) {
            $result = array_replace_recursive($result, $data);
        }
        return $result;
    }
}
