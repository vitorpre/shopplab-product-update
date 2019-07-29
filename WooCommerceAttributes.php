<?php
/**
 * Created by PhpStorm.
 * User: AMD
 * Date: 30/06/2019
 * Time: 23:09
 */

class WooCommerceAttributes
{
    public static $attributes = array();

    public static function registerAttributes() {

        $attributes = array();

        $attributes[] = [
            'name' => 'Classificação Fiscal',
            'slug' => 'codClassificacaoFiscal',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Diamante Hommel',
            'slug' => 'diamanteHommel',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Numero CAS',
            'slug' => 'numeroCAS',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Nome Embalagem',
            'slug' => 'nomeEmbalagem',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Referencia',
            'slug' => 'referencia',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Formula Linear',
            'slug' => 'formulaLinear',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Numero Onu',
            'slug' => 'numeroOnu',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Densidade',
            'slug' => 'densidade',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Referencia',
            'slug' => 'referencia',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Peso Liquido',
            'slug' => 'pesoLiquido',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Peso Bruto',
            'slug' => 'pesoBruto',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Peso Molecular',
            'slug' => 'pesoMolecular',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $attributes[] = [
            'name' => 'Sigla Unidade de Medida',
            'slug' => 'siglaUnidadeMedida',
            'type' => 'select',
            'order_by' => 'menu_order',
            'has_archives' => true
        ];

        $wooCommerceClient = new WooCommerceClient();

        foreach ($attributes as $attribute) {
            $response = $wooCommerceClient->sendAttribute($attribute);
        }

    }

    public static function attributesConfigurations() {

        $attributes = array();

        $attributes['codClassificacaoFiscal'] = [
            'name' => 'Classificação Fiscal',
            'position' => 0,
            'visible' => false,
            'variation' => false,
        ];

        $attributes['diamanteHommel'] = [
            'name' => 'Diamante Hommel',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['numeroCAS'] = [
            'name' => 'Numero CAS',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['nomeEmbalagem'] = [
            'name' => 'Nome Embalagem',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['referencia'] = [
            'name' => 'Referencia',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['formulaLinear'] = [
            'name' => 'Formula Linear',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['numeroOnu'] = [
            'name' => 'Numero Onu',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['densidade'] = [
            'name' => 'Densidade',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['pesoLiquido'] = [
            'name' => 'Peso Liquido',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['pesoBruto'] = [
            'name' => 'Peso Bruto',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['pesoMolecular'] = [
            'name' => 'Peso Molecular',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        $attributes['siglaUnidadeMedida'] = [
            'name' => 'Sigla Unidade de Medida',
            'position' => 0,
            'visible' => true,
            'variation' => false,
        ];

        return $attributes;
    }

    public static function loadAttributes() {

        $wooCommerceClient = new WooCommerceClient();
        SELF::$attributes = SELF::indexAttributesBySlug($wooCommerceClient->getAttributes());

    }

    private static function indexAttributesBySlug($attributes = array()) {

        $indexedAttributes = array();

        foreach ($attributes as $attribute) {
            $indexedAttributes[$attribute->slug] = $attribute;
        }

        return $indexedAttributes;
    }
}