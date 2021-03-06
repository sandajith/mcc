<?php

class Mycloset_Membership_PopupController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sliderAction() {
        $categoryIds = Mage::app()->getRequest()->getParam('categoryId');
//        $customerId = Mage::app()->getRequest()->getParam('productowner');
        $userid = Mage::getSingleton('customer/session')->getId();
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('category_id', array('in' => $categoryIds));
        $i = 0;
        ?>   
        <div class="flexsliderPopup carousel">
            <ul class="slides">
                <?php
                foreach ($collection as $_product):
                    $productowner = Mage::getModel('catalog/product')->load($_product->getId())->getCustomerName();                    
                    Mage::getSingleton('core/session')->setProductOwner($productowner); 
                    $customerid = Mage::getSingleton('core/session')->getProductOwner();
                    if ($userid == $customerid) {
                        $i++;
                        ?>
                        <li>
                            <a userid="<?php echo $userid; ?>" attr="<?php echo $customerId; ?>"  class="getproductid" rel="<?php echo $_product->getId(); ?>" >
                                <img src="<?php echo Mage::helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" alt=""/></a>
                        </li>  
                        <?php
                    }  else {?>
                            <li>
                            <a attr="<?php echo $customerId; ?>"  class="getproductid" rel="<?php echo $_product->getId(); ?>" >
                                <img src="<?php echo Mage::helper('catalog/image')->init($_product, 'small_image')->resize(135); ?>" alt=""/></a>
                        </li> 
                   <?php }
                endforeach;
                ?>
            </ul>
        </div>

        <script>
            var jqCustom = jQuery.noConflict();
            jqCustom('.getproductid').click(function () {
                var productid = jqCustom(this).attr('rel');
                jqCustom.ajax({
                    url: "<?php echo Mage::getBaseUrl(); ?>" + "mycloset/popup/product?productid=" + productid,
                    type: 'get',
                    dataType: "json",
                    success: function (data) {

                        jqCustom('.sku').html('<div style="float: right margin-top: 15px;" class="sku">' + data['sku'] + '</div>');
                        jqCustom('.image').html('<img alt="" src="' + data['ImageUrl'] + '"/>');
                        jqCustom('.signature').html(data['designer']);
                        jqCustom('.colorBox').html('<a style=" background:' + data['color'] + '"></a>');
                        jqCustom('.description').html(data['Description']);
                        jqCustom('.status').html('<b>Status</b> : ' + data['product_status']);
                        jqCustom('.size').html('<i>Size' + data['size'] + '</i>');
                        jqCustom('.season').html('<i>' + data['season'] + '</i>');

                    }
                });
            });
        </script>
        <?php
    }

    public function productAction() {
        $product_id = Mage::app()->getRequest()->getParam('productid');
        $product = array();
        $model = Mage::getModel('catalog/product'); //getting product model
        $product['id'] = $model->load($product_id); //getting product object for particular product id               
        $product['ShortDescription'] = $model->load($product_id)->getShortDescription(); //product's short description
        $product['Description'] = $model->load($product_id)->getDescription(); // product's long description
        $product['Name'] = $model->load($product_id)->getName(); //product name
        $product['Price'] = $model->load($product_id)->getPrice(); //product's regular Price
        $product['SpecialPrice'] = $model->load($product_id)->getSpecialPrice(); //product's special Price
        $product['ProductUrl'] = $model->load($product_id)->getProductUrl(); //product url
        $product['ImageUrl'] = $model->load($product_id)->getImageUrl(); //product's image url
        $product['color'] = $model->load($product_id)->getAttributeText('color'); //product's thumbnail image url
        $product['sku'] = $model->load($product_id)->getSku(); //product's thumbnail image url      
        $product['size'] = $model->load($product_id)->getSize(); //product's thumbnail image url      
        $product['designer'] = $model->load($product_id)->getAttributeText('designer'); //product's thumbnail image url
        $product['product_status'] = $model->load($product_id)->getAttributeText('product_status'); //product's thumbnail image url
        $product['season'] = $model->load($product_id)->getAttributeText('season'); //product's thumbnail image url
        $product['shipped_to'] = $model->load($product_id)->getAttributeText('shipped_to'); //product's thumbnail image url      
        echo json_encode($product);
    }

}
?>

