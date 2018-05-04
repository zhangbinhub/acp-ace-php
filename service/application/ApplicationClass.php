<?php
namespace service\application;

class ApplicationClass
{

    private $id = null;

    private $webroot = null;

    private $appname = null;

    private $dbno = null;

    private $language = null;

    private $copyright_owner = null;

    private $copyright_begin = null;

    private $copyright_end = null;

    private $version = null;

    private $info = null;

    private $link = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getWebroot()
    {
        return $this->webroot;
    }

    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;
        return $this;
    }

    public function getAppname()
    {
        return $this->appname;
    }

    public function setAppname($appname)
    {
        $this->appname = $appname;
        return $this;
    }

    public function getDbno()
    {
        return $this->dbno;
    }

    public function setDbno($dbno)
    {
        $this->dbno = $dbno;
    }

    public function getCopyrightOwner()
    {
        return $this->copyright_owner;
    }

    public function setCopyrightOwner($copyright_owner)
    {
        $this->copyright_owner = $copyright_owner;
        return $this;
    }

    public function getCopyrightBegin()
    {
        return $this->copyright_begin;
    }

    public function setCopyrightBegin($copyright_begin)
    {
        $this->copyright_begin = $copyright_begin;
        return $this;
    }

    public function getCopyrightEnd()
    {
        return $this->copyright_end;
    }

    public function setCopyrightEnd($copyright_end)
    {
        $this->copyright_end = $copyright_end;
        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
}

?>