<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\FileFormats;


use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Shared\ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SimpleXMLElement;
use SplFileInfo;

/**
 * Class IDML
 * @package App\FileFormats
 */
class IDML
{
    /**
     * @var false|string
     */
    protected $tempFolder = '';
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * IDML constructor.
     * @param $fileName
     * @param $prefix
     * @throws Exception
     */
    public function __construct($fileName, $prefix)
    {
        $this->tempFolder = $this->tempdir();
        $zip = new ZipArchive();
        if ($zip->open($fileName)) {
            $zip->extractTo($this->tempFolder);
            $zip->close();
        }
        $this->prefix = $prefix;
    }

    /**
     * @return false|string
     */
    protected function tempdir()
    {
        $tempfile = tempnam(sys_get_temp_dir(), 'IDML_unpacked_');
        // you might want to reconsider this line when using this snippet.
        // it "could" clash with an existing directory and this line will
        // try to delete the existing one. Handle with caution.
        if (file_exists($tempfile)) {
            unlink($tempfile);
        }
        mkdir($tempfile);
        if (is_dir($tempfile)) {
            return $tempfile;
        }
    }

    /**
     * @param $mm
     * @return float
     */
    public static function mmToPoint($mm)
    {
        return $mm * 2.845275590551;
    }

    /**
     * @param $pt
     * @return float
     */
    public static function pointToMm($pt)
    {
        return $pt / 2.845275590551;
    }

    public function build()
    {
        // Get real path for our folder
        $rootPath = realpath($this->tempFolder);
        $tempFile = tempnam(sys_get_temp_dir(), 'IDML_build_');

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $added = [];
        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $added[] = [$filePath, $relativePath];
                $zip->addFile($filePath, $relativePath);
            }
        }


        // Zip archive will be created only after closing object
        $zip->close();

        // send to browser
        $this->forceDownload(
            $tempFile,
            'application/vnd.adobe.indesign-idml-package',
            "build-ev3_" . now()->format('YmdHis') . ".idml"
        );
        unlink($tempFile);
        exit;
    }

    /**
     * Force the browser to download a file
     * @param $file
     * @param string $mimeType
     * @param string $fileName
     */
    public function forceDownload($file, $mimeType = 'application/vnd.adobe.indesign-idml-package', $fileName = '')
    {
        if ($fileName == '') {
            $fileName = pathinfo($file, PATHINFO_BASENAME);
        }
        header('Content-Type: application/vnd.adobe.indesign-idml-package');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $fileName . "\"");
        if (!file_exists($file) && file_exists($this->tempPath($file))) {
            $file = $this->tempPath($file);
        }
        readfile($file);
        exit;
    }


    /**
     * Get the full path from a relative path in the temp folder
     * @param $file
     * @return string
     */
    public function tempPath($file)
    {
        return $this->tempFolder . '/' . $file;
    }

    /**
     * @param $file
     * @return SimpleXMLElement
     */
    public function getXML($file)
    {
        return simplexml_load_file($this->tempPath($file));
    }

    /**
     * @param $file
     * @param SimpleXMLElement $xml
     */
    public function setXML($file, SimpleXMLElement $xml)
    {
        $this->renderToFile($file, $xml->__toString());
    }

    /**
     * Write text to a file in the temp directory
     * @param string $filePath a relative path in the temp folder
     * @param string $code contents
     */
    public function renderToFile($filePath, $code)
    {
        file_put_contents($this->tempPath($filePath), $code);
    }

    /**
     * Remove a file from the temp directory
     * @param $filePath
     */
    public function removeFile($filePath)
    {
        if (file_exists($this->tempPath($filePath))) {
            unlink($file);
        }
    }

    /**
     * Get a unique identifier
     * @return string
     */
    public function getUID($type = '')
    {
        return $this->prefix . '_' . ($type ?: '') . '_' . str_random(16);
    }

    /**
     * Delete temp folder upon garbage collection
     */
    public function __destruct()
    {
        $this->deleteFolder($this->tempFolder);
    }

    /**
     * Delete a folder from the disk
     * @param $dirname
     * @return bool
     */
    protected function deleteFolder($dirname)
    {
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file)) {
                    unlink($dirname . "/" . $file);
                } else {
                    $this->deleteFolder($dirname . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    /**
     * @return string
     */
    public function BR()
    {
        return chr(0xE2) . chr(0x80) . chr(0xA8);
    }


}
