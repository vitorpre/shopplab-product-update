<?php
/**
 * Created by PhpStorm.
 * User: AMD
 * Date: 08/06/2019
 * Time: 01:58
 */

class ERPClient
{
    public static $baseUrl = "http://shopplabapi.paicon.com.br/";

    private static function get($url, array $parameters = []) {


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, SELF::$baseUrl . $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $response = curl_exec( $curl );
        curl_close( $curl );

        return json_decode($response);
    }

    public static function getActiveProducts() {

        $products = SELF::get("api/Produto/listaProdutos?nomeReferencia&vitrineWeb=1");

        return $products;
    }

    public static function getProductDetails($id) {

        $product = SELF::get("api/Produto/buscaProdutoDetalhe?codProduto=" . $id);

        return $product;
    }

    public static function getProductPhotosUrl($id) {

        $found = true;
        $codFoto = 1;
        $photos = array();

        do {

            $response = SELF::get('api/Produto/GetProdutoFoto?codProduto=' . $id . '&tipo=Foto&codFoto=' . $codFoto . '&width=300&height=0');

            if(isset($response->message)) {
                $found = false;
            } else {
                $photos[] = SELF::$baseUrl. 'api/Produto/GetProdutoFoto?codProduto=' . $id . '&tipo=Foto&codFoto=' . $codFoto . '&width=300&height=0';
            }

            $codFoto++;

        } while($found);

        return $photos;


    }



}