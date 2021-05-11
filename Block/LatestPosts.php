<?php
namespace Ponderosa\WordpressIntegration\Block;

class LatestPosts extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ponderosa\WordpressIntegration\Helper\Api $apiHelper
    ) {
        parent::__construct($context, []);
        $this->apiHelper = $apiHelper;
    }

    public function getApiUrl()
    {
        return $this->apiHelper->getResourcesApiUrl('posts');
    }

    public function getLatestPosts()
    {
        return $this->apiHelper->getLatestResources('posts');
    }

    public function scrapeImageFromContent( $content )
    {
        return $this->apiHelper->scrapeImageFromContent($content);
    }

    public function getMaxPosts()
    {
        return $this->apiHelper->getMaxPosts();
    }
}
