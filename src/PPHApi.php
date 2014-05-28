<?php

/**
 * A PPH Guzzle Client to use to consume the PeoplePerHour.com Rest API.
 * Uses "Guzzle service descriptions" - see the docs: http://guzzle.readthedocs.org/en/latest/webservice-client/guzzle-service-descriptions.html
 */
class PPHApi extends GuzzleHttp\Command\Guzzle\GuzzleClient
{
    /**
     * @var string Default URL for the api to call
     */
    public $apiUrl = 'https://api.peopleperhour.com/';

    /**
     * @var string The ID of your app that wants to access the PPH API
     */
    public $apiId;

    /**
     * @var string Your secret key needed to access the PPH API
     */
    public $apiKey;

    /**
     * Construct this Command Client
     *
     * @param string $apiId  The ID of your app that wants to access the PPH API
     * @param string $apiKey Your secret key needed to access the PPH API
     * @param array  $config Guzzle client Configuration options - see https://github.com/guzzle/guzzle-services/blob/master/src/GuzzleClient.php
     */
    public function __construct($apiId, $apiKey, $client = null, $config = [])
    {
        $this->apiId = $apiId;
        $this->apiKey = $apiKey;

        // If no base_url was set, use our default
        if (!isset($config['base_url'])) {
            $config['base_url'] = $this->apiUrl;
        }

        if ($client === null)
            $client = new GuzzleHttp\Client();

        // Call the parent Command Client constructor.
        // See https://github.com/guzzle/guzzle-services/blob/master/src/GuzzleClient.php
        //
        // @param ClientInterface   $client      Client used to send HTTP requests
        // @param Description       $description Guzzle service description
        // @param array             $config      Configuration options
        parent::__construct($client, $this->getAPIDescription(), $config);
    }

    /**
     * @return GuzzleHttp\Command\Guzzle\Description service description for the PPH API
     **/
    public function getAPIDescription()
    {
        // All PPH operations need params set for authentication
        $auth_params = [
            'app_id' => [
                'type'     => 'string',
                'required' => true,
                'location' => 'query',
                'default'  => $this->apiId,
            ],
            'app_key' => [
                'type'     => 'string',
                'required' => true,
                'location' => 'query',
                'default'  => $this->apiKey,
            ],
        ];

        $return_attrs = [
            'a' => [
                "description" => "Optional list of attributes to return",
                'type'     => 'string',
                'required' => false,
                'location' => 'query',
            ],
        ];

        // See the docs for "Guzzle service descriptions" http://guzzle.readthedocs.org/en/latest/webservice-client/guzzle-service-descriptions.html
        return new GuzzleHttp\Command\Guzzle\Description([
            'apiVersion' => 'v1',
            'baseUrl' => $this->apiUrl,
            'operations' => [
                // User View
                'User' => [
                    'httpMethod' => 'GET',
                    'uri' => "/v1/user/{id}",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+$return_attrs+[
                        'id' => [
                            "description" => "ID of member",
                            'type'     => 'numeric',
                            'required' => false,
                            'location' => 'uri',
                        ],
                    ],
                ],
                // User List
                'UserList' => [
                    'httpMethod' => 'GET',
                    'uri' => "/v1/user/list",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+$return_attrs+[
                        'page' => [
                            "description" => "The page # to request. Defaults to 1",
                            'type'     => 'integer',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'sort' => [
                            "description" => "The attribute to use for sorting. Default is ascending. Use .desc suffix to order descending",
                            'type'     => 'string',
                            'required' => false,
                            'location' => 'query',
                        ],
                    ],
                ],
                'IsMember' => [
                    'description' => "Test whether a PeoplePerHour member exists with a particular email address.",
                    'httpMethod' => 'GET',
                    'uri' => "/v1/user/ismember",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+[
                        'email' => [
                            "description" => "Encrypted email address",
                            'type'     => 'string',
                            'required' => true,
                            'location' => 'query',
                        ],
                    ],
                ],
                // TODO: Add the whole PPH API description here
            ],
            'models' => [
                'getResponse' => [
                    'type' => 'object',
                    // Add a code property to our response object
                    'properties' => [
                        'code' => ['location' => 'statusCode']
                    ],
                    // Rather than specifying all response params individually, extract everything from the json response
                    'additionalProperties' => [
                        'location' => 'json'
                    ],
                ]
            ]
        ]);
    }
}
