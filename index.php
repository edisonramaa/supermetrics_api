<?php
require __DIR__ . '/vendor/autoload.php';

class App {
    public static function init()
    {
        session_start();
        $socialApiWrapper = new \Client\SocialAPIWrapper(new \Client\Guzzle(),
            new \Services\ResponseService(),
            new \Services\PostProcessorService()
        );
        $options = [];
        $options["page"] = isset($_GET["page"]) ? $_GET["page"] : 1;
        if(isset($_SESSION['sl_token']))
        {
            $options["sl_token"] = $_SESSION["sl_token"];
        } else {
            $_SESSION["sl_token"] = $socialApiWrapper->generateNewToken();
            $options["sl_token"] = $_SESSION["sl_token"];
        }

        $queryParams = [
            "period" => $_GET["period"] ?? null,
            "page" => $_GET["page"] ?? null,
            "identifier" => $_GET["identifier"] ?? null,
            "statOption" => $_GET["statOption"] ?? null,
        ];

        $socialApiWrapper->processRequest(\Config\Config::SUB_URL, $options, $queryParams);
    }
}

App::init();
