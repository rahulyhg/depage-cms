<?php

require_once('../Fs.php');
require_once('../FsFile.php');
require_once('../Exceptions/FsException.php');

// {{{ FsTestClass
class FsTestClass extends Depage\Fs\FsFile
{
    public function parseUrl($url) {
        return parent::parseUrl($url);
    }

    public function cleanUrl($url) {
        return parent::cleanUrl($url);
    }
}
// }}}

// {{{ getMode
function getMode($path) {
    return substr(sprintf('%o', fileperms($path)), -4);
}
// }}}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
