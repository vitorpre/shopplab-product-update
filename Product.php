<?php
/**
 * Created by PhpStorm.
 * User: AMD
 * Date: 03/06/2019
 * Time: 01:55
 */

class Product
{
    protected $id;
    protected $sku;
    protected $name;
    protected $type;
    protected $status;
    protected $regular_price;
    protected $description;
    protected $short_description;
    protected $category;
    protected $images = array();
    protected $synonyms = array();
    protected $documents = array();
    protected $attributes = array();
    protected $dimensions = array();
    protected $tags = array();
    protected $weight;
    protected $isOnSale = array();

    public function setId($id) {
        $this->id = $id;
    }
    public function setStatus($status) {
        $this->status = $status;
    }

    public function export() {

        $productFormated = array();

        if(isset($this->id)) {
            $productFormated['id'] = $this->id;
        }

        if(isset($this->status)) {
            $productFormated['status'] = $this->status;
        }

        if(isset($this->name)) {
            $productFormated['name'] = $this->name;
        }

        if(isset($this->type)) {
            $productFormated['type'] = $this->type;
        }

        if(isset($this->regular_price)) {
            $productFormated['regular_price'] = number_format($this->regular_price, 2, '.', '');
        }

        if(isset($this->short_description)) {
            $productFormated['short_description'] = $this->short_description;
        }

        if(isset($this->sku)) {
            $productFormated['sku'] = (string) $this->sku;
        }

        if(count($this->attributes) > 0) {
            $productFormated['attributes'] = $this->attributes;
        }

        if(count($this->synonyms) > 0) {
            $productFormated['short_description'] .= "\r\n Sinonimos: " . implode(", ", $this->synonyms) ;
        }

        if(count($this->documents) > 0) {
            $productFormated['short_description'] .= "\r\n Documentos: " . implode(", ", $this->documents) ;
        }

        if(count($this->dimensions) > 0) {
            $productFormated['dimensions'] = $this->dimensions;
        }

        if($this->weight != "") {
            $productFormated['weight'] = $this->weight;
        }

        $productFormated['categories'][] = $this->category;

        $productFormated['manage_stock'] = true;

        $productFormated['on_sale'] = $this->isOnSale;

        $productFormated['tags'] = $this->tags;


        if(is_array($this->images)) {
            foreach ($this->images as $imageUrl) {
                $productFormated['images'][] = array('src' => $imageUrl);
            }
        }

        return $productFormated;

    }

    public static function createByErpProduct($erpProduct, $wooComerceProductSku = null) {

        $ERPClient = new ERPClient();
        $product = new Product();

        $erpProductDetails = $ERPClient->getProductDetails($erpProduct->codProduto);
        $erpProductDetails->marca   = $erpProduct->marca;

        $product->sku               = $erpProductDetails->codProduto;
        $product->name              = $erpProductDetails->nome;
        $product->type              = 'simple';
        $product->regular_price     = $erpProductDetails->produtoPreco->valorFinal;
        $product->description       = SELF::escapeJsonString($erpProductDetails->descricao);
        $product->short_description = SELF::escapeJsonString($erpProductDetails->descricao2);
        $product->category          = array('id' => WooCommerceCategories::$categories[$erpProductDetails->codProdutoGrupo]->id);
        if($wooComerceProductSku == null) {
            $product->images            = SELF::uploadProductPhotos($erpProductDetails);
        }
        $product->status            = 'publish';
        $product->attributes        = SELF::populateAttributes($erpProductDetails);
        $product->synonyms          = SELF::populateSynonyms($erpProductDetails);
        $product->documents         = SELF::populateDocuments($erpProductDetails);
        $product->tags              = SELF::populateTags($erpProduct);
//        $product->dimensions        = SELF::getDimensions($product->short_description);
        $product->weight            = $erpProductDetails->pesoLiquido;
        $product->isOnSale          = $erpProduct->vitrineWeb;


        return $product;

    }

    private static function populateTags($erpProduct) {

        $tagsFormated = array();

        $tag = WooCommerceTags::$tags[ str_replace(" ", "-", strtolower($erpProduct->marca)) ];

        $tagsFormated[] = ['id' => $tag->id];

        return $tagsFormated;

    }


    private static function populateAttributes($erpProductDetails) {

        $attributesFormated = array();

        $attributesConfiguration = WooCommerceAttributes::attributesConfigurations();


        foreach ($attributesConfiguration as $slug => $attributeConfiguration) {
            if($erpProductDetails->{$slug} != "") {
                $attributeFormated = $attributeConfiguration;
                $attributeFormated['id'] = WooCommerceAttributes::$attributes["pa_" . strtolower($slug)]->id;
                $attributeFormated['options'] = array($erpProductDetails->{$slug});
                $attributesFormated[] = $attributeFormated;
            }
        }
        return $attributesFormated;

    }

    private static function populateSynonyms($erpProductDetails) {

        $synonyms = array();

        foreach($erpProductDetails->produtoSinonimos as $produtoSinonimo) {
            $synonyms[] = $produtoSinonimo->sinonimo;
        }

        return $synonyms;
    }

    private static function populateDocuments($erpProductDetails) {

        $documents = array();

        foreach($erpProductDetails->produtoArquivos as $produtoArquivo) {

            if($produtoArquivo->tipo == "outros") {
                $documents[] = "<a href='http://shopplabapi.paicon.com.br/api/Produto/arquivoSite?codProdutoArquivoSite=" . $produtoArquivo->codProdutoArquivo . "'>" . $produtoArquivo->descricao . "</a>";
            }
        }

        return $documents;
    }

    private static function uploadProductPhotos($erpProductDetails) {

        $uploadedPhotosUrl = array();

        $photosUrl = ERPClient::getProductPhotosUrl($erpProductDetails->codProduto);

        foreach ($photosUrl as $index => $photoUrl) {
            $uploadedPhotosUrl[] = SELF::uploadImageFromUrl($photoUrl, $erpProductDetails->codProduto, $index);
        }

        return $uploadedPhotosUrl;
    }

    private static function deleteProductPhotos($wooComerceProduct) {

        foreach ($wooComerceProduct->images as $image) {
            wp_delete_attachment($image->id);
        }
    }

    public static function uploadImageFromUrl($url, $sku, $codImage) {

        $uploaddir = wp_upload_dir();

        $filename = $sku . '-' . $codImage . '.jpg';
        $uploadfile = $uploaddir['path'] . '/' . $filename ;

        $contents = file_get_contents($url);
        $savefile = fopen($uploadfile, 'w');
        fwrite($savefile, $contents);
        fclose($savefile);

        $uploadedImageUrl = $uploaddir['url'] . '/' . $filename ;

        return $uploadedImageUrl;

    }

    private function escapeJsonString($value) {

        $result = substr_replace("\n","\\n",$value);
        $result = substr_replace("&nbsp;"," ",$result);
        $result = trim($result, " \t\n\r\0\x0B\xC2\xA0");



        return $result;
    }

    private static function getDimensions($string) {

        $arrDimensions = array();

        $arrMatchesAttributes = array();
        $arrMatches = array();
        $string = str_replace ("\r","",$string);
        $string = str_replace ("\n","",$string);
        preg_match('/.*?{{(.*)?}}/', $string, $arrMatchesAttributes);

        $attributes = explode(";", $arrMatchesAttributes[1]);

        foreach ($attributes as $attribute) {

            preg_match('/altura:(.*)/', $attribute, $arrMatches);

            if($arrMatches[1] != "") {
                $arrDimensions['height'] = $arrMatches[1];
            }

            preg_match('/largura:(.*)/', $attribute, $arrMatches);

            if($arrMatches[1] != "") {
                $arrDimensions['width'] = $arrMatches[1];
            }

            preg_match('/comprimento:(.*)/', $attribute, $arrMatches);

            if($arrMatches[1] != "") {
                $arrDimensions['length'] = $arrMatches[1];
            }

        }



        return $arrDimensions;

    }

}