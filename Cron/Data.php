<?php
namespace Ponderosa\WordpressIntegration\Cron;

class Data
{
    private $resources = ['posts', 'categories'];

    public function __construct(
        \Ponderosa\WordpressIntegration\Helper\Api $apiHelper
    )
    {
        $this->apiHelper = $apiHelper;
    }

    public function execute()
    {
        foreach($this->resources as $resource){
            $this->apiHelper->getResourcesFromApi( $resource );
        }
    }
}
