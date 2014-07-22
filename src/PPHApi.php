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
    public $base_url = 'https://api.peopleperhour.com/';

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
     * @param string          $apiId      The ID of your app that wants to access the PPH API
     * @param string          $apiKey     Your secret key needed to access the PPH API
     * @param ClientInterface $httpClient Client used to send HTTP requests
     * @param array           $config     Guzzle client Configuration options - see https://github.com/guzzle/guzzle-services/blob/master/src/GuzzleClient.php
     */
    public function __construct($apiId, $apiKey, $httpClient = null, $config = [])
    {
        $this->apiId = $apiId;
        $this->apiKey = $apiKey;

        // If no base_url was set, use our default
        if (isset($config['base_url'])) {
            $this->base_url = $config['base_url'];
        } else {
            $config['base_url'] = $this->base_url;
        }

        if ($httpClient === null) {
            // No HTTP Client was passed, create one to use to send requests
            $httpClient = new GuzzleHttp\Client(['defaults'=>['cookies'=>true]]); // Turn on cookies so session is maintained after login
        }

        // Call the parent Command Client constructor.
        // See https://github.com/guzzle/guzzle-services/blob/master/src/GuzzleClient.php
        //
        // @param ClientInterface   $httpClient  Client used to send HTTP requests
        // @param Description       $description Guzzle service description
        // @param array             $config      Configuration options
        parent::__construct($httpClient, $this->getAPIDescription(), $config);
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

        $attributes_wanted = [
            'a' => [
                "description" => "Optional list of attributes to return",
                'type'     => 'string',
                'required' => false,
                'location' => 'query',
            ],
        ];

        $page_param = [
            'page' => [
                "description" => "The page # to request. Defaults to 1",
                'type'     => 'integer',
                'required' => false,
                'location' => 'query',
            ],
        ];

        $sort_param = [
            'sort' => [
                "description" => "The attribute to use for sorting. Default is ascending. Use .desc suffix to order descending",
                'type'     => 'string',
                'required' => false,
                'location' => 'query',
            ],
        ];

        // See the docs for "Guzzle service descriptions" http://guzzle.readthedocs.org/en/latest/webservice-client/guzzle-service-descriptions.html
        return new GuzzleHttp\Command\Guzzle\Description([
            'apiVersion' => 'v1',
            'baseUrl' => $this->base_url,
            'operations' => [

                ////////////////////////////////////////////////////////
                //                        USER
                ////////////////////////////////////////////////////////
                // User View
                'User' => [
                    'httpMethod' => 'GET',
                    'uri' => "/v1/user/{id}",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+$attributes_wanted+[
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
                    'parameters' => $auth_params+$attributes_wanted+$page_param+$sort_param+[
                        'f[mem_id]' => [
                            "description" => "Filter results by mem_id(s)",
                            'type'     => 'array',
                            'required' => false,
                            'location' => 'query',
                        ],
                    ],
                ],
                'UserLogin' => [
                    'httpMethod' => 'POST',
                    'uri' => "/v1/user/login",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+[
                        'email' => [
                            "description" => "login email address",
                            'type'     => 'string',
                            'required' => true,
                            'location' => 'postField',
                        ],
                        'password' => [
                            "description" => "login password",
                            'type'     => 'string',
                            'required' => true,
                            'location' => 'postField',
                        ],
                    ],
                ],
                'IsGuest' => [
                    'description' => "Test whether current user is logged in.",
                    'httpMethod' => 'GET',
                    'uri' => "/v1/user/isguest",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params,
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

                ////////////////////////////////////////////////////////
                //                        Hourlie
                ////////////////////////////////////////////////////////

                // Hourlie View
                'Hourlie' => [
                    'httpMethod' => 'GET',
                    'uri' => "/v1/hourlie/{id}",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+$attributes_wanted+[
                        'id' => [
                            "description" => "ID of Hourlie",
                            'type'     => 'numeric',
                            'required' => false,
                            'location' => 'uri',
                        ],
                    ],
                ],
                // Hourlie List
                'HourlieList' => [
                    'httpMethod' => 'GET',
                    'uri' => "/v1/hourlie/list",
                    'responseModel' => 'getResponse',
                    'parameters' => $auth_params+$attributes_wanted+$page_param+$sort_param+[

                        'page_size' => [
                            'type'     => 'integer',
                            'required' => false,
                            'location' => 'query',
                        ],

                        'attachment_sizes' => [
                            "description" => "Limit what attachment sizes are returned.",
                            'type'     => 'string',
                            'required' => false,
                            'location' => 'query',
                        ],

                        'currencies' => [
                            "description" => "Use this if you want more than only the default currency within the hourlie price data",
                            'type'     => 'string',
                            'required' => false,
                            'location' => 'query',
                        ],

                        // Allow a filters array
                        'f[q]' => [
                            "description" => "Query keyword with which to filter results with",
                            'type'     => 'string',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[cat]' => [
                            "description" => "ID for Hourlie Category",
                            'type'     => 'integer',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[min_price]' => [
                            "description" => "Minimum price of Hourlies",
                            'type'     => 'numeric',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[max_price]' => [
                            "description" => "Maximum price of Hourlies",
                            'type'     => 'numeric',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[ids]' => [
                            "description" => "Filter by these hourlie IDs",
                            'type'     => 'array',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[featured]' => [
                            "description" => "Denote if featured hourlies only should be fetched separately and merged with the rest of fetched hourlies",
                            'type'     => 'boolean',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[exclude_hourlies]' => [
                            "description" => "Do not include these hourlies in the results",
                            'type'     => 'array',
                            'required' => false,
                            'location' => 'query',
                            'filters' => [
                                [
                                    'method'=> "PPHApi::compressArrayToString",
                                    'args'=> [ '@value' ]
                                ]
                            ],
                        ],
                        'f[exclude_owners]' => [
                            "description" => "Do not include hourlies from these owners in the results",
                            'type'     => 'array',
                            'required' => false,
                            'location' => 'query',
                            'filters' => [
                                [
                                    'method'=> "PPHApi::compressArrayToString",
                                    'args'=> [ '@value' ]
                                ]
                            ],
                        ],
                        'f[has_cover_image]' => [
                            "description" => "Ensure all results have a image or video",
                            'type'     => 'boolean',
                            'required' => false,
                            'location' => 'query',
                        ],
                        'f[unique_owner]' => [
                            "description" => "Ensure all results are from a different seller",
                            'type'     => 'boolean',
                            'required' => false,
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

    /**
     * Use a more compressed method of requesting multiple IDs.
     *
     * i.e. Instead of using Array format:
     *  f[myvar][0]=123&f[myvar][1]=1234&f[myvar][2]=12345
     * Use string format:
     *  f[myvar]=123,1234,12345
     * Otherwise API call gets too big when Array has lots of items.
     *
     * @param Array $value Input Array
     * @return String
     */
    public static function compressArrayToString($value)
    {
        return join(',',$value);
    }
}
