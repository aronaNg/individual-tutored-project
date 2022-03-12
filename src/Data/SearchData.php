<?php
namespace App\Data;
use App\Entity\Produit;

use App\Entity\TypeProduit;

class SearchData
{

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var TypeProduit[]
     */
    public $typeProduit = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

    /**
     * @var boolean
     */
    public $disponible = false;

}