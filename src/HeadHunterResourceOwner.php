<?php

namespace AlexMasterov\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class HeadHunterResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    protected $response = [];

    /**
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getValueByKey($this->response, 'last_name');
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getValueByKey($this->response, 'first_name');
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->getValueByKey($this->response, 'middle_name');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
