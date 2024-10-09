<?php
    // API MERCADONA //
    /*
        OBJETIVOS:
        - Obtener la información de los productos de Mercadona
        OBJETOS:
        - Category:
            - Name
            - Id
        - Product:
            - Name
            - Id
            - Price
        FUNCIONES:
        -ObtainIdsCategories: return array(category_id): Obtiene una lista de los ids de las categorias.
        -ObtainIdsProducts(category_id): return array(product_id): Obtiene una lista de los ids de los productos de una categoria.
        -ObtainCategory(category_id): return Object(Category): Obtiene el objeto categoria corrrespondiente.
        -ObtenerProducts(product_id): return Object(Product): Obtiene el objeto categoria corrrespondiente.
    */

    /*
        Listado de categorías: https://tienda.mercadona.es/api/categories/
        Listado de productos: Los sacamos de cada una de las categorías.
        Una categoría en concreto: https://tienda.mercadona.es/api/categories/112/
        Un producto en concreto: https://tienda.mercadona.es/api/products/34180/
    */
    class Category {
        public $name;
        public $id;
    
        function __construct($name, $id) {
            $this->name = $name;
            $this->id = $id;
        }
    }

    class Product {
        public $name;
        public $id;
        public $price;
    
        function __construct($name, $id, $price) {
            $this->name = $name;
            $this->id = $id;
            $this->price = $price;
        }
    }

    function processResults($json) {
        if (isset($json["results"])) {
            return $json["results"];
        } else {
            return $json;
        }
    }

    function ObtainIdsCategories($url = "https://tienda.mercadona.es/api/categories/") {
        $categories = json_decode(file_get_contents($url), true);
        $categories = processResults($categories);
        $ids = array();
        recursiveObtainIds($categories, $ids);
        return $ids;
    }
    
    function recursiveObtainIds($categories, &$ids) {
        foreach($categories as $category) {
            if (isset($category["categories"]) && is_array($category["categories"])) {
                recursiveObtainIds($category["categories"], $ids);
            } else {
                $ids[] = $category["id"];
            }
        }
    }
    
    function ObtainIdsProducts($category_id) {
        $url = "https://tienda.mercadona.es/api/categories/$category_id/products/";
        $products = json_decode(file_get_contents($url), true);
        $ids = array();
        foreach($products["results"] as $product) {
            $ids[] = $product["id"];
        }
        return $ids;
    }
    
    function ObtainCategory($category_id) {
        $url = "https://tienda.mercadona.es/api/categories/$category_id/";
        $category_data = json_decode(file_get_contents($url), true);
        return new Category($category_data["name"], $category_data["id"]);
    }
    
    function ObtainProduct($product_id) {
        $url = "https://tienda.mercadona.es/api/products/$product_id/";
        $product_data = json_decode(file_get_contents($url), true);
        return new Product($product_data["name"], $product_data["id"], $product_data["price"]);
    }
    
    // Ejemplo de uso
    $category_ids = ObtainIdsCategories();
    foreach($category_ids as $category_id) {
        $category = ObtainCategory($category_id);
        echo "Categoria: " . $category->name . " (ID: " . $category->id . ")" . "<br>";
        $product_ids = ObtainIdsProducts($category_id);
        foreach($product_ids as $product_id) {
            $product = ObtainProduct($product_id);
            echo "&emsp;Producto: " . $product->name . " (ID: " . $product->id . ") - Precio: " . $product->price . "<br>";
        }
    }
?>