<?php

namespace AlexMasterov\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class HeadHunterResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response = [];

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->response['id'];
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->response['last_name'];
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->response['first_name'];
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->response['middle_name'];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
