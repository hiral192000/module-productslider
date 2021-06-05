<?php

namespace Itabtech\Productslider\Block;

class Index extends \Magento\Framework\View\Element\Template {

    protected $_productCollectionFactory;
    protected $categoryFactory;
    protected $priceCurrency;
    protected $_storeManager;
    protected $_urlInterface;
    protected $_product;
    protected $httpContext;
    protected $_productRepository;
    protected $listProductBlock;
    protected $_resource;
    protected $_connection;
    protected $scopeConfig;
    const CONFIG_ENABLE_DATA = 'promotion/promoslider/active';
    const CONFIG_TITLE_DATA = 'promotion/promoslider/title';
    const CONFIG_SHOW_PRICE = 'promotion/promoslider/show_price';
    const CONFIG_DESKTOP = 'promotion/promoslider/item_desktop';
    const CONFIG_DESKTOP_SMALL = 'promotion/promoslider/item_desktop_small';
    const CONFIG_TABLET = 'promotion/promoslider/item_tablet';
    const CONFIG_MOBILE_ITEM = 'promotion/promoslider/item_mobile';
    const CONFIG_SLIDER_SPEED  = 'promotion/promoslider/slider_speed';
    const CONFIG_VALUE_SLIDER_SPEED = 'promotion/promoslider/auto_slide';
    const CONFIG_SHOW_NAVIGATION_CONTROL = 'promotion/promoslider/show_navigation_control';
    const CONFIG_ITEM_DEFAULT  = 'promotion/promoslider/item_default';
    const CONFIG_ADD_TO_CART  = 'promotion/promoslider/show_to_cart';
    const CONFIG_DESCRIPTION = 'promotion/promoslider/description';
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\App\Http\Context $httpContext, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Block\Product\ListProduct $listProductBlock, \Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\CategoryFactory $CategoryFactory, \Magento\Catalog\Block\Product\ImageBuilder $_imageBuilder, \Magento\Catalog\Api\ProductRepositoryInterface $productRepo, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\UrlInterface $urlInterface, array $data = []
    ) {

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $CategoryFactory;
        $this->_imageBuilder = $_imageBuilder;
        $this->productRepo = $productRepo;
        $this->priceCurrency = $priceCurrency;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->_product = $product;
        $this->httpContext = $httpContext;
        $this->listProductBlock = $listProductBlock;
        $this->_resource = $resource;
        $this->scopeConfig = $scopeConfig;
        $this->_connection = $resource->getConnection();
        parent::__construct($context, $data);
    }

    public function getProductCollection() {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('promotion', '5431');
        return $collection;
    }

    public function getImage($product, $imageId, $attributes = []) {
        return $this->_imageBuilder->setProduct($product)
                        ->setImageId($imageId)
                        ->setAttributes($attributes)
                        ->create();
    }

    public function getProductImage() {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }

    public function getFormatedPrice($amount) {
        return $this->priceCurrency->convertAndFormat($amount);
    }

    public function getUrlInterfaceData() {
        echo $this->_urlInterface->getBaseUrl();
    }

    public function getProductPrice(\Magento\Catalog\Model\Product $product) {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                    \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE, $product, [
                'include_container' => true,
                'display_minimal_price' => true,
                'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                'list_category_page' => true
                    ]
            );
        }

        return $price;
    }

    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product) {
        $renderer = $this->getDetailsRenderer($product->getTypeId());

        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    public function getDetailsRenderer($type = null) {
        if ($type === null) {
            $type = 'default';
        }

        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }
        return null;
    }

    protected function getDetailsRendererList() {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
                        $this->getDetailsRendererListName()
                ) : $this->getChildBlock(
                        'topselling.toprenderers'
        );
    }

    /**
     * Specifies that price rendering should be done for the list of products
     * i.e. rendering happens in the scope of product list, but not single product
     *
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender() {
        return $this->getLayout()->getBlock('product.price.render.default')
                        ->setData('is_product_list', true);
    }

    public function customerLoggedIn() {
        $isLoggedIn = (bool) $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if ($isLoggedIn) {
            $checkLoggedIn = 1;
        } else {
            $checkLoggedIn = 0;
        }
        return $checkLoggedIn;
    }

    public function getAddToCartPostParams($product) {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

    public function getEnabledata() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_ENABLE_DATA, $storeScope);
     }

     public function getTitle() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_TITLE_DATA, $storeScope);

     }
     public function getShowprice() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_SHOW_PRICE, $storeScope);

     }
     public function getDesktopval() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_DESKTOP, $storeScope);

     }
      public function getDesktopsmall(){
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_DESKTOP_SMALL, $storeScope);

     }

     public function getTabletitem() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_TABLET, $storeScope);

     }
     public function getMobileitem() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_MOBILE_ITEM, $storeScope);

     }
     public function getSliderSpeed() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_SLIDER_SPEED, $storeScope);

     }
      public function getAutoSlideValue() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_VALUE_SLIDER_SPEED, $storeScope);

     }
     public function getShowNavControl() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_SHOW_NAVIGATION_CONTROL, $storeScope);

     }
     public function getItemDefault() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_ITEM_DEFAULT, $storeScope);

     }

     public function getShowAddToCart() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_ADD_TO_CART, $storeScope);

     }
     public function getDescription() {
     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

     return $this->scopeConfig->getValue(self::CONFIG_DESCRIPTION, $storeScope);

     }

}
