<?php
namespace Ponderosa\WordpressIntegration\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;


class Api extends AbstractHelper
{

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->logger = $logger;
        $this->fileSystem = $fileSystem;
        $this->driverFile = $driverFile;
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
        $this->resourcePath = $this->directoryList::VAR_DIR;
        $this->resourceBasePath = $this->directoryList::MEDIA;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    public function getResourcesFromApi( string $resources ){

        $this->logger->info('Getting latest Wordpress '.$resources.'...');

        $apiUrl = $this->getResourcesApiUrl( $resources );
        $postsPerPage = $this->getPostsPerPage();
        $resourcePath = 'ponderosa/'.$resources;
        $page = 1;
        $querySuccessful = true;
        $timestamp = time();

        if(!$apiUrl){
            return false;
        }

        while($querySuccessful) {

            $url = $apiUrl.'?page='.$page.'&per_page='.$postsPerPage;
            $json = @file_get_contents($url);

            $filename = 'page_'.$page. '.json';

            $querySuccessful = $json && !empty(json_decode($json));

            if($querySuccessful){

                try {
                    $relativeFileLocation = $resourcePath . '/' . $timestamp . '/'. $filename;
                    $target = $this->fileSystem->getDirectoryWrite($this->resourceBasePath);
                    $target->writeFile($relativeFileLocation, $json);
                    $this->logger->info('WP '.$resources.' successfully written to ' . $this->resourceBasePath . '/' . $relativeFileLocation);

                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }

            $page++;
        }


    }

    public function getResourcesApiUrl( string $resources ){

        return $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_rest_api_".$resources."_url", $this->storeScope);

    }

    public function getLatestResources( string $resources, $page = null )
    {
        $json = false;
        $page = $page ? $page : 1;
        $resourcePath = 'ponderosa/'.$resources;
        $pathToTimestampedDirs = $this->directoryList->getPath($this->resourceBasePath).'/'.$resourcePath;

        if(is_dir($pathToTimestampedDirs)){

            $dirs = scandir($pathToTimestampedDirs, SCANDIR_SORT_DESCENDING);
            $latestTimestampedDir = $dirs[0];
            $targetFile = $pathToTimestampedDirs . '/' . $latestTimestampedDir . '/page_' . $page . '.json';

            if( !file_exists($targetFile) ){
                return false;
            }

            $json = file_get_contents($pathToTimestampedDirs . '/' . $latestTimestampedDir . '/page_' . $page . '.json');
        }

        return $json;
    }

    public function getResourceById( string $resources , int $id ){

        $categoryExists = false;
        $resource = null;
        $page = 1;

        while(!$categoryExists){

            $pageData = json_decode($this->getLatestResources( $resources , $page ));

            if(!$pageData){
                break;
            }

            foreach ($pageData as $key => $entry) {

                if($entry->id == $id){

                    $resource = $entry;
                    $categoryExists = true;

                    break;
                }
                # code...
            }

            $page++;

        }

        return $resource;

    }

    public function getPostContentBySlug( string $slug ){

        $postExists = false;
        $resource = null;
        $page = 1;

        while(!$postExists){

            $pageData = json_decode($this->getLatestResources( 'posts' , $page ));

            if(!$pageData){
                break;
            }

            foreach ($pageData as $key => $entry) {

                if($entry->slug == $slug){

                    $resource = $entry;
                    $postExists = true;

                    break;
                }
                # code...
            }

            $page++;

        }

        return $resource;

    }

    public function getNiceDate( string $date )
    {
        $date = strtotime($date);
        $format = $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_post_date_format", $this->storeScope);
        return date($format,$date);
    }

    public function getNumPages($resources)
    {
        $resourcePath = 'ponderosa/'.$resources;
        $pathToTimestampedDirs = $this->directoryList->getPath($this->resourceBasePath).'/'.$resourcePath;

        if(!is_dir($pathToTimestampedDirs)){
            return $false;
        }

        $dirs = scandir($pathToTimestampedDirs, SCANDIR_SORT_DESCENDING);
        $latestTimestampedDir = $dirs[0];

        return count(glob($pathToTimestampedDirs . '/' . $latestTimestampedDir . '/' . "*"));
    }

    public function scrapeImageFromContent( $content )
    {
        preg_match_all('/<img[^>]+>/i', $content, $result);
        return $result[0][0];
    }

    public function getDefaultAuthor()
    {
        return $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_default_author", $this->storeScope);
    }

    public function getBlogSlug()
    {
        return stripslashes($this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_blog_path", $this->storeScope));
    }

    public function getMaxPosts()
    {
        return $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_latest_posts_display_num", $this->storeScope);
    }

    public function getPostsPerPage()
    {
        return $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_max_posts_per_page", $this->storeScope);
    }

    public function getNumPaginationLinks()
    {
        return $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_max_pagination_links", $this->storeScope);
    }
}
