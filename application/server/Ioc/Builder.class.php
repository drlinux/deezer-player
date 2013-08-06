<?php

namespace Ioc;

class Builder
{
    /**
     * @var Container
     */
    private $_container;

    /**
     * @var string
     */
    private $_yuiJarFilePath;

    /**
     * @var string
     */
    private $_tmpPath;


    function __construct(Container $container, $yuiJarFilePath, $tmpPath)
    {
        if (!file_exists($yuiJarFilePath)) {
            throw new \Exception(sprintf('Impossible de trouver le binaire YUICompressor : "%s"', $yuiJarFilePath));
        }

        if (!is_dir($tmpPath) || !is_writable(($tmpPath))) {
            throw new \Exception(sprintf('Le chemin "%s" n\'est pas un dossier, ou le script le possède pas les droits d\'écriture dessus.'));
        }

        $this->_container = $container;
        $this->_yuiJarFilePath = $yuiJarFilePath;
        $this->_tmpPath = $tmpPath;
    }

    /**
     * @param $outputFilePath
     */
    public function generateClientModelCombinedJsFile($outputFilePath)
    {
        $options = array(
            'type' => YUICompressor::FILETYPE_JS,
            'linebreak' => false,
            'nomunge' => false,
            'nooptimize' => false,
            'semi' => true,
            'verbose' => false,
            'debug' => false,
        );

        $yui = new YUICompressor($this->_yuiJarFilePath, $this->_tmpPath, $options);
        $compressedJsCode = $yui->combineFiles($this->_getComponentFilePathList(), $outputFilePath);

        echo($compressedJsCode);
    }

    public function generateClientInitJsFile($outputFilePath)
    {

    }

    /**
     * @return array
     */
    private function _getComponentFilePathList()
    {
        $files = $this->_container->generateComponentsFilePathList(APPLICATION_PATH . '/client', '.js');

        return array_unique($files);
    }


}