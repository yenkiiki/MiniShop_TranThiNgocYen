<?php
namespace Controllers\Client;

class HomeController
{
    public function index()
    {
        $pageTitle = "Trang chủ";
        ob_start();
        require __DIR__ . "/../../views/client/home/index.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }
}