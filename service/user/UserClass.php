<?php
namespace service\user;

class UserClass
{

    private $id;

    private $name;

    private $loginno;

    private $levels;

    private $portrait;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getLoginno()
    {
        return $this->loginno;
    }

    public function setLoginno($loginno)
    {
        $this->loginno = $loginno;
        return $this;
    }

    public function getLevels()
    {
        return $this->levels;
    }

    public function setLevels($levels)
    {
        $this->levels = $levels;
        return $this;
    }

    public function getPortrait()
    {
        return $this->portrait;
    }

    public function setPortrait($portrait)
    {
        $this->portrait = $portrait;
        return $this;
    }
}
?>