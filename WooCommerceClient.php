<?php
/**
 * Created by PhpStorm.
 * User: AMD
 * Date: 05/06/2019
 * Time: 23:51
 */

class WooCommerceClient
{
    protected static $user;
    protected static $password;


    public static function setAuthentication($apiUser, $apiPassword) {
        SELF::$user = $apiUser;
        SELF::$password = $apiPassword;
    }

    private static function get($url, array $parameters = []) {

        $parametersFormated = http_build_query($parameters);

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode(SELF::$user . ":" . SELF::$password)
        );

        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, "ck_06542b1fcb33bac732691b6a2029e440fdde00b6:cs_ccfbc04898b89bcfdabd10f651b80543f239c6dd");
        curl_setopt($curl, CURLOPT_URL, get_site_url() . $url . "?" . $parametersFormated);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $response = curl_exec( $curl );
        curl_close( $curl );

        return $response;
    }

    private static function post($url, array $fields = []) {

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode(SELF::$user . ":" . SELF::$password)
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_USERPWD, "ck_06542b1fcb33bac732691b6a2029e440fdde00b6:cs_ccfbc04898b89bcfdabd10f651b80543f239c6dd");
        curl_setopt($curl, CURLOPT_URL, get_site_url() . $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_POST, 1);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields, true));
        $response = curl_exec( $curl );

        curl_close( $curl );

        return $response;
    }

    public function getAllProducts() {

        $allProducts = array();

        $parameters = array('per_page' => 100,
            'order' => "asc",
            'orderby' => "id",
            'offset' => 0
        );


        $countProducts = null;

        do {
            $response = SELF::get("/wp-json/wc/v3/products", $parameters);

            $products = json_decode($response);

            $allProducts = array_merge($allProducts, $products);

            $countProducts = count($products);
            $parameters['offset'] += $countProducts;


        } while ($countProducts >= 100);

        return $this->indexProductsBySku($allProducts);

    }

    public static function saveCategory($categorieFormated) {
        $response = SELF::post('/wp-json/wc/v3/products/categories', $categorieFormated);

        return json_decode($response);
    }

    public function updateBatchCategories($categoriesFormated) {

        $response = SELF::post('/wp-json/wc/v3/products/categories/batch', $categoriesFormated);

        return $response;
    }


    private function indexProductsBySku($products) {

        $indexedProductsArray = array();

        foreach ($products as $product) {
            $indexedProductsArray[$product->sku] = $product;
        }

        return $indexedProductsArray;

    }

    public function getAllCategories() {

        $allCategories = array();

        $page = 1;

        $parameters = array(
            'per_page' => 100,
            'order' => "asc",
            'orderby' => "id",
            'page' => $page
        );


        $countProducts = null;

        do {
            $response = SELF::get("/wp-json/wc/v3/products/categories", $parameters);

            $categories = json_decode($response);

            $allCategories = array_merge($allCategories, $categories);

            $countCategories = count($categories);

            $page++;
            $parameters['page'] = $page;


        } while ($countCategories >= 100);

        return $allCategories;

    }

    public function saveProduct($productFormated) {
        $response = SELF::post('/wp-json/wc/v3/products', $productFormated);

        return $response;
    }

    public function updateBatchProducts($productFormated) {

        $response = SELF::post('/wp-json/wc/v3/products/batch', $productFormated);

        return $response;
    }

    public function getAttributes() {

        $parameters = array(
            'context' => 'edit'
        );

        $response = SELF::get("/wp-json/wc/v3/products/attributes", $parameters);

        return json_decode($response);

    }

    public function sendAttribute($attribute) {

        $response = SELF::post('/wp-json/wc/v3/products/attributes', $attribute);

        return $response;

    }





}