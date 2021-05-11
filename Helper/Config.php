<?php

namespace Ponderosa\WordpressIntegration\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;


class Config extends AbstractHelper
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
        $resourcePath = 'ponderosa/'.$resources;
        $page = 1;
        $querySuccessful = true;
        $timestamp = time();

        if(!$apiUrl){
            return false;
        }

        while($querySuccessful) {

            $url = $apiUrl.'?page='.$page;
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
        $postsPerPage = $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_max_posts_per_page", $this->storeScope);

        if(is_dir($pathToTimestampedDirs)){

            $dirs = scandir($pathToTimestampedDirs, SCANDIR_SORT_DESCENDING);
            $latestTimestampedDir = $dirs[0];
            $targetFile = $pathToTimestampedDirs . '/' . $latestTimestampedDir . '/page_' . $page . '.json';

            if( !file_exists($targetFile) ){
                return false;
            }

            $json = file_get_contents($pathToTimestampedDirs . '/' . $latestTimestampedDir . '/page_' . $page . '.json');
        }

        // if($page){

        //     $data = json_decode($json);
        //     $start = ( $page - 1 ) * $postsPerPage;
        //     $pagedData = array_slice($data, $start, $postsPerPage);
        //     $json = json_encode($pagedData);
        // }

        return $json;
    }

    public function getMaxPosts()
    {
        return $this->scopeConfig->getValue("ponderosa_wordpress_integration/ponderosa_wp_integration_settings/wp_latest_posts_display_num", $this->storeScope);
    }
}
