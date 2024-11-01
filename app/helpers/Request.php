<?php

namespace Tussendoor\GmbReviews\Helpers;

class Request extends ParameterBag
{
    public static function fromGlobal()
    {
        return new static($_REQUEST);
    }

    public static function files()
    {
        return new static($_FILES);
    }

    public function old($name)
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? $this->get($name, null) : null;
    }

    public function isPostRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function isGetRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }
}
