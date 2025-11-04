<?php
namespace ExcelUploader\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(EXCEL_UPLOADER_PATH . '/app/Views/templates');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html'
        ]);
    }

    public function render($template, $data = []) {
        return $this->twig->render($template, $data);
    }
}