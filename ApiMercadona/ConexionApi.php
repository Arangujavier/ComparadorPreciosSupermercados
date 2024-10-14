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

    class ConexionApi {
        private $url =  "https://tienda.mercadona.es/api/";

        public function __construct(){
        }

        /**
         * Avanza la capa del resultado de la consulta en la API
         * @param mixed $json Consulta realizada en la API
         * @return mixed Consulta realiza en la API en el campo "results"
         */
        private function processResults($json) {
            if (isset($json["results"])) {
                return $json["results"];
            } else {
                return $json;
            }
        }

        /**
         * Obtener ids de las categorias disponibles en la API
         * @param mixed $url Ruta de la API
         * @return array Lista de ids de las categorias
         */
        public function ObtainIdsCategories($url = "https://tienda.mercadona.es/api/categories/") {
            $categories = json_decode(file_get_contents($url), true);
            $categories = $this->processResults($categories);
            $ids = array();
            $this->recursiveObtainCategoriesIds($categories, $ids);
            return $ids;
        }
        private function recursiveObtainCategoriesIds($categories, &$ids) {
            foreach($categories as $category) {
                if (isset($category["categories"])) {
                    $this->recursiveObtainCategoriesIds($category["categories"], $ids);
                } else {
                    $ids[] = $category["id"];
                }
            }
        }

        /**
         * Obtener ids de los productos disponibles en la API
         * @param mixed $category_id Id de la categoria en la que se quieren localizar los productos
         * @return array Lista de ids de las categorias
         */
        function ObtainIdsProducts($category_id) {
            $url = "https://tienda.mercadona.es/api/categories/$category_id/";
            $categories = json_decode(file_get_contents($url), true);
            $ids = array();
            if(isset($categories["categories"])){
                $this->ObtainSubcategoryIds($categories["categories"], $ids);
            }
            return $ids;
        }

        function ObtainSubcategoryIds($subcategories, &$ids) {
            foreach($subcategories as $subcategory) {
                // Comprobar si tiene productos
                if(isset($subcategory["products"])){
                    foreach($subcategory["products"] as $product){
                        array_push($ids, $product["id"]);
                    }
                }
            }
        }

        /**
         * Obtener el objeto correspondiente al id de la categoria proporcionado
         * @param mixed $category_id Id de la categoria
         * @return Category Objeto Categoria
         */
        function ObtainCategory($category_id) {
            $url = "https://tienda.mercadona.es/api/categories/$category_id/";
            $category_data = json_decode(file_get_contents($url), true);
            return new Category($category_data["name"], $category_data["id"]);
        }

        /**
         * Obtener el objeto correspondiente al id del producto proporcioanod
         * @param mixed $product_id Id del producto
         * @return Product Objeto Producto
         */
        function ObtainProduct($product_id) {
            $url = "https://tienda.mercadona.es/api/products/$product_id/";
            $product_data = json_decode(file_get_contents($url), true);
            return new Product($product_data["slug"], $product_data["id"], $product_data["price_instructions"]["unit_price"]);
        }
    }
    
    // Ejemplo de uso
    
    $conexionApi = new ConexionApi();
    $category_ids = $conexionApi->ObtainIdsCategories();
    /*
    foreach($category_ids as $category_id) {
        $category = $conexionApi->ObtainCategory($category_id);
        echo "Categoria: " . $category->name . " (ID: " . $category->id . ")" . "<br>";
        $product_ids = $conexionApi->ObtainIdsProducts($category_id);
        foreach($product_ids as $product_id) {
            $product = $conexionApi->ObtainProduct($product_id);
            echo "&emsp;Producto: " . $product->name . " (ID: " . $product->id . ") - Precio: " . $product->price . "<br>";
        }
    }
    */
?>