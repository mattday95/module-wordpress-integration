<?php

namespace Ponderosa\WordpressIntegration\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    protected $actionFactory;
    protected $response;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Ponderosa\WordpressIntegration\Helper\Api $apiHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->apiHelper = $apiHelper;
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->response = $response;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        $parts = explode('/', trim($request->getPathInfo(), '/'));
        $blogPath = $this->apiHelper->getBlogSlug();

        if(strpos($identifier, $blogPath) !== false) {

            if(isset($parts[1])){
                $request->setModuleName('ponderosa_blog')->//module name
                setControllerName('post')->//controller name
                setActionName('view');
            }
            else {
                $request->setModuleName('ponderosa_blog')-> //module name
                setControllerName('index')-> //controller name
                setActionName('index');
            }

        } else {
            return false;
        }
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
