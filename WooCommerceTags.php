<?php
/**
 * Created by PhpStorm.
 * User: AMD
 * Date: 05/07/2019
 * Time: 00:13
 */

class WooCommerceTags
{
    public static $tags = array();

    public static function loadTags() {

        $wooCommerceClient = new WooCommerceClient();
        SELF::$tags = SELF::indexTagsBySlug($wooCommerceClient->getAllTags());

    }

    private static function indexTagsBySlug($tags) {

        $indexedTagsArray = array();

        foreach ($tags as $tag) {
            $indexedTagsArray[$tag->slug] = $tag;
        }

        return $indexedTagsArray;

    }

    public static function saveNewTags($tags) {

        foreach ($tags as $tag) {
            SELF::saveTag($tag);
        }

    }

    private static function saveTag($tag) {

        $wooCommerceTag = WooCommerceTags::$tags[ str_replace(" ", "-", strtolower($tag)) ];

        if(!$wooCommerceTag) {
            $newTag = WooCommerceClient::saveTag(SELF::formatTagToSave($tag));

            SELF::$tags[$newTag->slug] = $newTag;

        }

    }

    private static function formatTagToSave($tag) {
        $tagFormated = array();
        $tagFormated['name'] = $tag;
        $tagFormated['slug'] = str_replace(" ", "-", strtolower($tag));

        return $tagFormated;
    }

    public static function getProductsTags($products) {

        $tags = array();

        foreach($products as $product) {
            $tags[] = $product->marca;
        }

        return $tags;

    }
}