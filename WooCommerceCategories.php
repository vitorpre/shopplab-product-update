<?php
/**
 * Created by PhpStorm.
 * User: AMD
 * Date: 05/07/2019
 * Time: 00:13
 */

class WooCommerceCategories
{
    public static $categories = array();

    public static function loadCategories() {

        $wooCommerceClient = new WooCommerceClient();
        SELF::$categories = SELF::indexCategoriesBySlug($wooCommerceClient->getAllCategories());

    }

    private static function indexCategoriesBySlug($categories) {

        $indexedCategoriesArray = array();

        foreach ($categories as $category) {
            $indexedCategoriesArray[$category->slug] = $category;
        }

        return $indexedCategoriesArray;

    }

    public static function saveNewCategories($categories) {

        foreach ($categories as $category) {
            SELF::saveCategory($category);
        }

    }

    private static function saveCategory($category, $parentId = null) {

        $wooCommerceCategory = WooCommerceCategories::$categories[$category->codProdutoGrupo];

        if(!$wooCommerceCategory) {
            $newCategory = WooCommerceClient::saveCategory(SELF::formatCategoryToSave($category, $parentId));

            SELF::$categories[$newCategory->slug] = $newCategory;

            $wooCommerceCategory = SELF::$categories[$newCategory->slug];

        }

        foreach ($category->grupos as $subCategory) {
            SELF::saveCategory($subCategory, $wooCommerceCategory->id);
        }

    }

    private static function formatCategoryToSave($category, $parentId = null) {
        $categoryFormated = array();
        $categoryFormated['name'] = $category->nome;
        $categoryFormated['slug'] = (string)$category->codProdutoGrupo;
        $categoryFormated['description'] = $category->nomeCompleto;

        if($parentId) {
            $categoryFormated['parent'] = $parentId;
        }

        return $categoryFormated;
    }
}