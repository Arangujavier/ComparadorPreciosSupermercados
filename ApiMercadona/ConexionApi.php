<?php
    // API MERCADONA //
    /*
        OBJETIVOS
        - Obtener la información de los productos de Mercadona
    */

    /*
        Listado de categorías: https://tienda.mercadona.es/api/categories/
        Una categoría en concreto: https://tienda.mercadona.es/api/categories/112/
        Un producto en concreto: https://tienda.mercadona.es/api/products/34180/
        Listado de productos: Los sacamos de cada una de las categorías.
    */
    $url = "https://tienda.mercadona.es/api/categories/"; // Lista las categorias de los productos

    // OBTENER CATEGORIAS //
    $categories = json_decode(file_get_contents($url), true);
    echo "Categorias: <br>";
    //print_r($categories["results"]);
    foreach($categories["results"] as $category)
    {
        echo "&emsp;" . $category["name"]. " " . $category["id"] . "<br>";
    }

    // LISTAR CATEGORIA
    
?>