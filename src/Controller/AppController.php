<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;
use League\Route\Http\Exception\UnauthorizedException;

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

    /**
     * Constructor.
     *
     * @param Request|null $request
     * @param Response|null $response
     * @throws UnauthorizedException
     */
    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request = $request;
        $this->response = $response;

        // Authentication check
        $attributes = $request->getAttributes();
        $auth = isset($attributes['_auth']) ? $attributes['_auth'] : true;
        
        if ($auth === true && !user()->isValid()) {
            throw new UnauthorizedException();
        }
    }

    /**
     * Request.
     *
     * @return null|Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Response.
     *
     * @return null|Response
     */
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

    /**
     * Get view data.
     *
     * @param array $viewData
     * @return array
     */
    public function getViewData(array $viewData = [])
    {
        $result = [
            'baseurl' => baseurl('/'),
        ];
        if (!empty($viewData)) {
            $result = array_replace_recursive($result, $viewData);
        }
        return $result;
    }

    /**
     * Render template.
     *
     * @param string $name
     * @param array $viewData
     * @return Response
     */
    protected function render($name, array $viewData = array())
    {
        $content = view()->render($name, $viewData);
        $this->response->getBody()->write($content);
        return $this->response;
    }

    /**
     * Helper to return JSON from a controller.
     *
     * @param mixed $result
     * @return JsonResponse
     */
    protected function json($result, $status = 200, $headers = [])
    {
        return new JsonResponse($result, $status, $headers);
    }
}
