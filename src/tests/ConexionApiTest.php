<?php
    // ConexionApiTest.php
    require __DIR__."/../ApiMercadona/ConexionApi.php";

    class ConexionApiTest extends PHPUnit\Framework\TestCase
    {
        public function testObtainIdsCategories()
        {
            $mock = \Mockery::mock('file_get_contents');
            $json = file_get_contents(__DIR__ . '/ConsultaApiCategorias.json');
            $mock->shouldReceive('file_get_contents')->andReturn($json);

            $conexionApi = new ConexionApi();
            $categoriasObtenidas = $conexionApi->ObtainIdsCategories();

            // Asserts y verificaciones aquí
            $path = __DIR__ . "\CategoriasApi.json";
            $array = file_get_contents($path);
            $categoriasEsperadas = json_decode($array, true);

            $this->assertSame($categoriasEsperadas, $categoriasObtenidas);
        }
    }