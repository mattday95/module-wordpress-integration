<?php
namespace Ponderosa\WordpressIntegration\Block;

class Pagination extends \Magento\Framework\View\Element\Template
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

    public function getCurrentPage()
    {
        $page = is_numeric($this->request->getParam('page')) && intval($this->request->getParam('page')) > 0 ? $this->request->getParam('page') : 1;

        return $page;
    }

    public function getPaginationLinks()
    {
        $numLinks = $this->apiHelper->getNumPaginationLinks();
        $currentPage = $this->getCurrentPage();
        $numPages = $this->apiHelper->getNumPages('posts');
        $prevNavigation = $currentPage > 1 ? '<div class="ponderosa-pagination-links__navigation ponderosa-pagination-links__navigation--prev"><a href="?page='.($currentPage - 1).'">Prev</a></div>' : '';
        $nextNavigation = $currentPage < $numPages ? '<div class="ponderosa-pagination-links__navigation ponderosa-pagination-links__navigation--next"><a href="?page='.($currentPage + 1).'">Next</a></div>' : '';

        $pagination = $prevNavigation;

        if($currentPage <= $numLinks){
            $currentLink = 1;
        }
        else {
            $currentLink = $currentPage % $numLinks > 0 ? (floor($currentPage / $numLinks) * $numLinks) + 1 : (($currentPage / $numLinks - 1) * $numLinks) + 1;
        }

        for ($i=0; $i < $numLinks ; $i++) {

            if($currentLink > $numPages){
                break;
            }

            $class = $currentLink == $currentPage ? 'ponderosa-pagination-links__link ponderosa-pagination-links__link--active' : 'ponderosa-pagination-links__link';
            $pagination.= '<div class="'.$class.'"><a href="?page='.$currentLink.'">'.$currentLink.'</a></div>';

            $currentLink++;
        }

        $pagination.=$nextNavigation;

        return $pagination;
    }

}
