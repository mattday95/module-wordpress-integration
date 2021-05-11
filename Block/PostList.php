<?php
namespace Ponderosa\WordpressIntegration\Block;

class PostList extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Ponderosa\WordpressIntegration\Helper\Api $apiHelper
    ) {
        parent::__construct($context, []);
        $this->apiHelper = $apiHelper;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
    }

    public function getApiUrl()
    {
        return $this->apiHelper->getResourcesApiUrl('posts');
    }

    public function getLatestPosts()
    {
        $page = is_numeric($this->request->getParam('page')) && intval($this->request->getParam('page')) > 0 ? $this->request->getParam('page') : 1;
        $postJson = $this->apiHelper->getLatestResources('posts', $page);

        return $postJson;
    }

    public function scrapeImageFromContent( $content )
    {
        return $this->apiHelper->scrapeImageFromContent($content);
    }

    public function getCategoryNicename ( int $id ){

        $category = $this->apiHelper->getResourceById('categories', $id);
        return $category->name;
    }

    public function getMaxPosts()
    {
        return $this->apiHelper->getMaxPosts();
    }
}
