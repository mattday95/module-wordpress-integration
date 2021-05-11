<?php
namespace Ponderosa\WordpressIntegration\Block;

class Post extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Element\Template\Context $context,
        \Ponderosa\WordpressIntegration\Helper\Api $apiHelper
    ) {
        parent::__construct($context, []);
        $this->apiHelper = $apiHelper;
        $this->request = $request;
    }

    public function getPostContent()
    {
        $slug = basename(parse_url($this->getRequest()->getUriString(), PHP_URL_PATH));
        return $this->apiHelper->getPostContentBySlug( $slug );
    }

    public function getCategoryNicename ( int $id ){

        $category = $this->apiHelper->getResourceById('categories', $id);
        return $category->name;
    }

    public function scrapeImageFromContent( $content )
    {
        return $this->apiHelper->scrapeImageFromContent($content);
    }

}
