<?php

class MustacheRenderer{
    private $mustache;
    private $viewsFolder;

    public function __construct($partialsPathLoader){
        Mustache_Autoloader::register();
        $this->mustache = new Mustache_Engine(
            array(
            'partials_loader' => new Mustache_Loader_FilesystemLoader( $partialsPathLoader )
        ));
        $this->viewsFolder = $partialsPathLoader;
    }

    public function render($contentFile , $data = array() ){
        echo  $this->generateHtml(  $this->viewsFolder . '/' . $contentFile . "Vista.mustache" , $data);
    }

    public function generateHtml($contentFile, $data = array()) {
        $headerPath = $this->viewsFolder . '/header.mustache';
        $footerPath = $this->viewsFolder . '/footer.mustache';

        $header = @file_get_contents($headerPath);
        if ($header === false) {
            error_log("MustacheRenderer: no se pudo leer $headerPath");
            $header = "";
        }

        $content = @file_get_contents($contentFile);
        if ($content === false) {
            error_log("MustacheRenderer: no se pudo leer $contentFile");
            $content = "";
        }

        $footer = @file_get_contents($footerPath);
        if ($footer === false) {
            error_log("MustacheRenderer: no se pudo leer $footerPath");
            $footer = "";
        }

        $contentAsString = $header . $content . $footer;
        // Evitar que warnings del parser (p.ej. accesos a offsets en null) rompan la salida
        try {
            // Silenciar temporalmente warnings/notices mientras parsea Mustache
            $oldLevel = error_reporting();
            error_reporting($oldLevel & ~E_WARNING & ~E_NOTICE & ~E_USER_WARNING & ~E_USER_NOTICE);
            $rendered = $this->mustache->render($contentAsString, $data);
            // Restaurar nivel de errores
            error_reporting($oldLevel);

            if ($rendered === false) {
                return "<pre>Error al renderizar la plantilla.</pre>";
            }
            return $rendered;
        } catch (Exception $e) {
            // Asegurar restauración incluso si hay excepción
            if (isset($oldLevel)) {
                error_reporting($oldLevel);
            }
            return "<pre>Error al renderizar la plantilla: " . htmlspecialchars($e->getMessage()) . "</pre>";
        }
    }
}