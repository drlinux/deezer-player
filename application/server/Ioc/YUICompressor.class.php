<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Armand
 * Date: 11/07/13
 * Time: 18:00
 * To change this template use File | Settings | File Templates.
 */

namespace Ioc;

class YUICompressor
{
    const FILETYPE_CSS = 'css';
    const FILETYPE_JS = 'js';

    const TMP_FILENAME_PATTERN = '%s/yuicompress-%s.tmp';

    /**
     * Absolute path to YUI jar file.
     *
     * @var string
     */
    private $yuiCompressorJarFilePath;

    /**
     * @var string
     */
    private $tmpDirPath;
    /**
     * @var array
     */
    private $_options = array(
        'type' => 'js',
        'linebreak' => false,
        'nomunge' => false,
        'semi' => false,
        'nooptimize' => false,
        'memoryLimit' => '32M',
        'charset' => 'UTF-8',
        'verbose' => false,
        'debug' => false,
    );

    /**
     * Construct with a path to the YUI jar and a path to a place to put temporary files
     *
     * @param string $yuiCompressorJarFilePath
     * @param string $tmpDirPath
     * @param array $options
     */
    function __construct($yuiCompressorJarFilePath, $tmpDirPath, $options = array())
    {
        $this->yuiCompressorJarFilePath = $yuiCompressorJarFilePath;
        $this->tmpDirPath = $tmpDirPath;

        foreach ($options as $optionName => $value) {
            $this->_options[$optionName] = $value;
        }
    }

    /**
     * the meat and potatoes, executes the compression command in shell
     *
     * @param array $filePathList
     * @param null $outputFilePath
     * @param bool $returnResult
     * @return null|string
     */
    function compress(array $filePathList, $outputFilePath = null)
    {
        $uniqHash = sha1(uniqid());
        $tmpFilename = sprintf(self::TMP_FILENAME_PATTERN, $this->tmpDirPath, $uniqHash);

        $this->combineFiles($filePathList, $tmpFilename);

        // Génaration de la commande.
        $cmd = sprintf(
            'java -Xmx%s -jar %s %s -o %s --charset %s --type %s',
            $this->_options['memoryLimit'],
            escapeshellarg($this->yuiCompressorJarFilePath),
            escapeshellarg($tmpFilename),
            escapeshellarg($outputFilePath),
            $this->_options['charset'],
            (strtolower($this->_options['type']) == self::FILETYPE_CSS ? self::FILETYPE_CSS : self::FILETYPE_JS)
        );

        // Ajout des options facultatives.
        if ($this->_options['linebreak'] && intval($this->_options['linebreak']) > 0) {
            $cmd .= ' --line-break ' . intval($this->_options['linebreak']);
        }

        if ($this->_options['verbose']) {
            $cmd .= " -v";
        }

        if ($this->_options['nomunge']) {
            $cmd .= ' --nomunge';
        }

        if ($this->_options['semi']) {
            $cmd .= ' --preserve-semi';
        }

        if ($this->_options['nooptimize']) {
            $cmd .= ' --disable-optimizations';
        }

        // Création du fichier de sortie si il n'existe pas.
        if (!is_file($outputFilePath)) {
            $touchResult = touch($outputFilePath);

            if (false === $touchResult) {
                throw new \Exception(sprintf('Impossible de créer le fichier de sortie : "%s".', $outputFilePath));
            }
        }

        // execute the command
        exec($cmd, $rawOutput, $exitCode);

        // Vérification de l'exécution de la commande.
        if (0 != $exitCode) {
            $printableOutput = implode("\n", $rawOutput);
            throw new \Exception(sprintf('Une erreur est surveunus durant l\'execution de YUICompress : "%s".', $printableOutput));
        }


        $removeResult = unlink($tmpFilename);

        if (false === $removeResult) {
            throw new \Exception(sprintf('Impossible de supprimer le fichier temporaire : "%s".', $tmpFilename));
        }
    }

    /**
     * @param array $filePathList
     * @param string $outputFilePath
     * @throws \Exception
     */
    public function combineFiles(array $filePathList, $outputFilePath)
    {
        // read the input
        $tmpAllFileContent = '';
        foreach ($filePathList as $filePath) {
            if (true === $this->_options['debug']) {
                $debugString = sprintf('/* Filepath : %s */', $filePath) . PHP_EOL;
                $horizontalRule = '/' . str_pad('', (strlen($debugString) - 4), '*') . '/' . PHP_EOL;

                $tmpAllFileContent .= PHP_EOL . $horizontalRule . $debugString . $horizontalRule;
            }

            $fileHandler = fopen($filePath, 'r');

            if (false === $fileHandler) {
                throw new \Exception(sprintf('Le fichier "%s" est introuvable.', $filePath));
            }

            $tmpAllFileContent .= fread($fileHandler, filesize($filePath));
            fclose($fileHandler);

            $tmpAllFileContent .= PHP_EOL;
        }

        if (!empty($tmpAllFileContent)) {
            $tmpAllFileContent = '\'use strict\';' . PHP_EOL . $tmpAllFileContent;
        }

        // create single file from all input
        $outputFileHandler = fopen($outputFilePath, 'w');

        if (false === $outputFileHandler) {
            throw new \Exception(sprintf('Impossible d\'ouvrir en écriture le fichier de sortie : "%s".', $outputFilePath));
        }

        $result = fwrite($outputFileHandler, $tmpAllFileContent);

        if (false === $result) {
            throw new \Exception(sprintf('Une erreur est survenus lors de l\'écriture dans le fichier : "%s".', $outputFilePath));
        }

        fclose($outputFileHandler);

        unset($tmpAllFileContent);
    }
}