<?php
namespace Ponderosa\WordpressIntegration\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    private $blockFactory;

    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5', '<')) {

            $latestPostsBlock = [
                'title' => 'Ponderosa WP Latest Posts',
                'identifier' => 'ponderosa-latest-posts',
                'content' => '{{block class="Ponderosa\\WordpressIntegration\\Block\\LatestPosts" template="Ponderosa_WordpressIntegration::latest_posts.phtml" block_id="Ponderosa_WordpressIntegration_LatestPosts"}}',
                'stores' => [0],
                'is_active' => 1,
            ];

            $this->blockFactory->create()->setData($latestPostsBlock)->save();
        }

        $setup->endSetup();
    }

}
