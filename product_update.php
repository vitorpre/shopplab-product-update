<?php
/**
 * Plugin Name: Shopplab Product Update
 * Plugin URI: https://shoplab.ecobp.com.br/
 * Description: Atualizador de produtos da shopplab no woo-commerce.
 * Version: 1.0
 * Author: Presutti
 **/

require_once ("options_page.php");

require_once ("WooCommerceClient.php");
require_once ("WooCommerceAttributes.php");
require_once ("WooCommerceCategories.php");
require_once ("ERPClient.php");
require_once ("Product.php");

function debug($var) {
    print "<pre>";
    var_dump($var);
    print "</pre>";
}

function updateWooCommerceProducts() {

    debug(11);exit;

    set_time_limit (500);

    $apiUser = get_option('api_login');
    $apiPassword = get_option('api_password');

    if(!$apiUser || !$apiPassword) {
        return;
    }


    WooCommerceClient::setAuthentication($apiUser, $apiPassword);
    WooCommerceAttributes::loadAttributes();
    WooCommerceCategories::loadCategories();

    if(!empty($apiUser) && !empty($apiPassword)) {
        $wooCommerceClient = new WooCommerceClient();
        $erpClient = new ERPClient();

        $products = $wooCommerceClient->getAllProducts();
        unset($products[""]);

        $productsErp = $erpClient->getActiveProducts();

        WooCommerceCategories::saveNewCategories($productsErp->grupos);

        processUpdate($products, $productsErp->produtos);
    }



}

function processUpdate($products, $productsErp) {

    $productsToAdd = array();
    $productsToUpdate = array();
    $productsToRemove = array();

    $productsToProcess = $products;

    foreach ($productsErp as $index => $productErp) {

        if( isset($productsToProcess[$productErp->codProduto]) ) {

            $productsToUpdate[$productsToProcess[$productErp->codProduto]->id] = $productErp;
        } else {
            $productsToAdd[] = $productErp;
        }

        unset($productsToProcess[$productErp->codProduto]);
    }

    foreach ($productsToProcess as $product) {
        $productsToRemove[$product->id] = $product;
    }

    $data = array();
    $data['create'] = array();
    $data['update'] = array();
    $data['delete'] = array();


    foreach ($productsToAdd as $productToAdd) {
        $product = Product::createByErpProduct($productToAdd);
        $data['create'][] = $product->export();
    }

    foreach ($productsToUpdate as $id => $productToUpdate) {
        $wooComerceProduct = $products[$productToUpdate->codProduto];
        $product = Product::createByErpProduct($productToUpdate, $wooComerceProduct);
        $product->setId($id);
        $data['update'][] = $product->export();
    }

    foreach ($productsToRemove as $id => $productToRemove) {
        $product = new Product();
        $product->setId($id);
        $product->setStatus('private');
        $data['update'][] = $product->export();
    }

    $wooCommerce = new WooCommerceClient();
    $response = $wooCommerce->updateBatchProducts($data);

    file_put_contents("C:/teste/log.log", $response, FILE_APPEND);

}

register_activation_hook(__FILE__, 'activate_product_update');

function activate_product_update() {
    if (! wp_next_scheduled ( 'update_product_daily_event' )) {
        wp_schedule_event(time(), 'daily', 'update_product_daily_event');
    }

    WooCommerceAttributes::registerAttributes();
}

add_action('update_product_daily_event', 'updateWooCommerceProducts');