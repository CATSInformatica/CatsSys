<?php

namespace Version\Model;

/**
 * Description of CatsSysVersion
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CatsSysVersion
{

    private $currentVersion;
    private $authors;
    private $versionDescriptions;

    public function __construct($version = null)
    {
        if ($version === null) {
            $this->currentVersion = require './data/version/current-version.php';
        } else {
            $this->currentVersion = $version;
        }

        $content = require './data/version/version-description.php';

        $this->authors = $content['authors'];
        $this->versionDescriptions = $content['versions'];
    }

    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    public function getAuthors()
    {
        return $this->authors;
    }

    public function getVersionDescriptions()
    {
        return $this->versionDescriptions;
    }

}
