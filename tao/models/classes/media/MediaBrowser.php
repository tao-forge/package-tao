<?php
/**
 * Created by Antoine on 12/11/14
 * at 10:18
 */

namespace oat\tao\model\media;


interface MediaBrowser {

    /**
     * @param string $relPath
     * @param array $acceptableMime
     * @return array
     */
    public function getDirectory($relPath = '/', $acceptableMime = array(), $depth = 1);

    /**
     * @param string $relPath
     * @return array
     */
    public function getFileInfo($relPath, $acceptableMime);

    /**
     * @param string $filename
     * @return string path of the file to download
     */
    public function download($filename);

} 