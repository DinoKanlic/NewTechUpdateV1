<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Abstract_Client' ) ):

/**
 * WooRechnung Abstract HTTP Client Class
 *
 * @class    WR_Abstract_Client
 * @version  1.0.0
 * @package  WooRechnung\Abstract
 * @author   Zweischneider
 */
abstract class WR_Abstract_Client
{
    /**
     * The GET HTTP method.
     *
     * @var string
     */
    const HTTP_METHOD_GET = 'GET';

    /**
     * The POST HTTP method.
     *
     * @var string
     */
    const HTTP_METHOD_POST = 'POST';

    /**
     * The PUT HTTP method.
     *
     * @var string
     */
    const HTTP_METHOD_PUT = 'PUT';

    /**
     * The DELETE HTTP method.
     *
     * @var string
     */
    const HTTP_METHOD_DELETE = 'DELETE';


    /**
     * The HTTP user agent header.
     *
     * @var string
     */
    const HTTP_HEADER_AGENT = 'User-Agent';

    /**
     * The HTTP authorization header.
     *
     * @var string
     */
    const HTTP_HEADER_AUTH = 'Authorization';

    /**
     * The HTTP content type header.
     *
     * @var string
     */
    const HTTP_HEADER_CONTENT = 'Content-Type';

    /**
     * The HTTP accept header.
     *
     * @var string
     */
    const HTTP_HEADER_ACCEPT = 'Accept';

    /**
     * The HTTP accept language header.
     *
     * @var string
     */
    const HTTP_HEADER_ACCEPT_LANG = 'Accept-Language';

    /**
     * The custom HTTP header to send to identify the base URL of this shop.
     *
     * @var string
     */
    const HTTP_HEADER_SHOP_URL = 'X-Shop-URL';

    /**
     * The custom HTTP header to identify the system of this shop.
     *
     * @var string
     */
    const HTTP_HEADER_SHOP_SYSTEM = 'X-Shop-System';

    /**
     * The custom HTTP header to identify this request and trace back errors.
     *
     * @var string
     */
    const HTTP_HEADER_TRACE_ID = 'X-Trace-Id';

    /**
     * The MIME type for JSON contents.
     *
     * @var string
     */
    const MIME_TYPE_JSON = 'application/json';

    /**
     * The shop system to be used for identification.
     *
     * @var string
     */
    const SYSTEM_WOOCOMMERCE = 'woocommerce';

    /**
     * Status codes of successful requests.
     *
     * @var array
     */
    private static $_success = array( 200, 201, 204 );

    /**
     * The plugin instance via dependency injection.
     *
     * @var WR_Plugin
     */
    private $_plugin;

    /**
     * The curl instance to use for HTTP requests.
     *
     * @var resource|bool
     */
    private $_curl;

    /**
     * The last trace ID that has been generated.
     *
     * @var string
     */
    private $_trace_id;

    /**
     * Create a new instance of this HTTP client component.
     *
     * @param  WR_Plugin $plugin
     * @return void
     */
    public function __construct(WR_Plugin $plugin)
    {
        $this->_plugin = $plugin;
        $this->_curl = false;
    }

    /**
     * Load the API token required for authorization.
     *
     * @return string|null
     */
    private function load_token()
    {
        return $this->_plugin->get_settings()->get_shop_token();
    }

    /**
     * Load the plugin user agent.
     *
     * @return string
     */
    private function load_user_agent()
    {
        return $this->_plugin->get_user_agent();
    }

    /**
     * Load the home url of this shop required for authorization.
     *
     * @return string
     */
    private static function load_home_url()
    {
        return get_home_url();
    }

    /**
     * Generate a random trace ID for this request.
     *
     * @return string
     */
    private function generate_trace_id()
    {
        $this->_trace_id = uniqid();
        return $this->_trace_id;
    }

    /**
     * Return the last trace ID this client has generated.
     *
     * @return string|null
     */
    private function get_last_trace_id()
    {
        return $this->_trace_id;
    }

    /**
     * Load the WordPress locale for language sensitive responses.
     *
     * @return string
     */
    private static function load_locale()
    {
        $locale = strval(get_locale());
        $explode = explode('_', $locale, 2);
        return array_shift($explode);
    }

    /**
     * Load the API base uri to send requests to.
     *
     * @return string
     */
    private function load_base_uri()
    {
        return $this->_plugin->get_server_uri();
    }

    /**
     * Build a HTTP header using name and value.
     *
     * @param  string $name
     * @param  string $value
     * @return string
     */
    private static function build_header($name, $value)
    {
        return $name . ': ' . $value;
    }

    /**
     * Build the header to identify this plugin as user agent.
     *
     * @return string
     */
    private function header_user_agent()
    {
        return self::build_header( self::HTTP_HEADER_AGENT, $this->load_user_agent() );
    }

    /**
     * Build the HTTP header required for authorization.
     *
     * @return string
     */
    private function header_authorization()
    {
        return self::build_header( self::HTTP_HEADER_AUTH, "Bearer {$this->load_token()}" );
    }

    /**
     * Build the header for the trace ID of this request.
     *
     * @return string
     */
    private function header_trace_id()
    {
        return self::build_header( self::HTTP_HEADER_TRACE_ID, $this->generate_trace_id() );
    }

    /**
     * Build the HTTP header for a outgoing JSON request body.
     *
     * @return string
     */
    private static function header_content_json()
    {
        return self::build_header( self::HTTP_HEADER_CONTENT, self::MIME_TYPE_JSON );
    }

    /**
     * Build the HTTP header for a expected JSON response body.
     *
     * @return string
     */
    private static function header_accept_json()
    {
        return self::build_header( self::HTTP_HEADER_ACCEPT, self::MIME_TYPE_JSON );
    }

    /**
     * Build the HTTP header for an expected response language.
     *
     * @return string
     */
    private static function header_accept_lang()
    {
        return self::build_header(self::HTTP_HEADER_ACCEPT_LANG, self::load_locale() );
    }

    /**
     * Build the HTTP header for the shop system.
     *
     * @return string
     */
    private static function header_shop_system()
    {
        return self::build_header( self::HTTP_HEADER_SHOP_SYSTEM, self::SYSTEM_WOOCOMMERCE );
    }

    /**
     * Build the header for the home url of this shop.
     *
     * @return string
     */
    private static function header_shop_url()
    {
        return self::build_header( self::HTTP_HEADER_SHOP_URL, self::load_home_url() );
    }

    /**
     * Get the value for the curl connection timeout option.
     *
     * @return int
     */
    private static function option_connecttimeout()
    {
        return WOORECHNUNG_HTTP_CONNECTTIMEOUT;
    }

    /**
     * Get the value for the curl request timeout option.
     *
     * @return int
     */
    private static function option_timeout()
    {
        return WOORECHNUNG_HTTP_REQUESTTIMEOUT;
    }

    /**
     * Get the value for the curl verbose option.
     *
     * @return int
     */
    private static function option_verbose()
    {
        return WOORECHNUNG_HTTP_VERBOSE;
    }


    /**
     * Encode the request body to JSON.
     *
     * @param  array $data
     * @return string
     */
    private static function encode_body( $data )
    {
        return json_encode( $data );
    }

    /**
     * Decode the response body from JSON.
     *
     * @param  string $data
     * @return array
     */
    private static function decode_body( $data )
    {
        return json_decode( strval($data), true );
    }

    /**
     * Prepare the HTTP headers for a request.
     *
     * @return array
     */
    private function prepare_headers( )
    {
        $headers = array();
        $headers[] = $this->header_user_agent();
        $headers[] = $this->header_authorization();
        $headers[] = $this->header_trace_id();
        $headers[] = self::header_shop_system();
        $headers[] = self::header_shop_url();
        $headers[] = self::header_content_json();
        $headers[] = self::header_accept_json();
        $headers[] = self::header_accept_lang();
        return $headers;
    }

    /**
     * Prepare the default curl options for a request.
     *
     * @return array
     */
    private function prepare_options( )
    {
        $options = array();
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_CONNECTTIMEOUT] = self::option_connecttimeout();
        $options[CURLOPT_TIMEOUT] = self::option_timeout();
        $options[CURLOPT_VERBOSE] = self::option_verbose();
        return $options;
    }

    /**
     * Prepare the target uri for a request.
     *
     * @return string
     */
    private function prepare_target( $path )
    {
        return $this->load_base_uri() . $path;
    }

    /**
     * Encode the request body as JSON for transport.
     *
     * @param  mixed $data
     * @return string|null
     */
    private function prepare_body($data)
    {
        return json_encode($data);
    }

    /**
     * Prepare the result for return.
     *
     * @param  int   $code
     * @param  mixed $body
     * @return array
     */
    private function prepare_result( $code, $body )
    {
        return array('code' => $code, 'body' => $body);
    }

    /**
     * Initialize a new curl session.
     *
     * @return void
     */
    private function init_client()
    {
        $this->_curl = curl_init();
    }

    /**
     * Close the current curl session.
     *
     * @return void
     */
    private function close_client()
    {
        curl_close($this->_curl);
    }

    /**
     * Decide if the client is ready for a request.
     *
     * @return bool
     */
    private function test_client()
    {
        return $this->_curl === false;
    }

    /**
     * Set the default curl options for the next request.
     *
     * @return void
     */
    private function set_options()
    {
        curl_setopt_array($this->_curl, $this->prepare_options() );
    }

    /**
     * Set the default headers for the next HTTP request.
     *
     * @return void
     */
    private function set_headers()
    {
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, $this->prepare_headers() );
    }

    /**
     * Set the target URI for the next request.
     *
     * @param  string $uri
     * @return void
     */
    private function set_target( $uri )
    {
        curl_setopt($this->_curl, CURLOPT_URL, $this->prepare_target($uri) );
    }

    /**
     * Set the HTTP method to use for the next request.
     *
     * @param  string $method
     * @return void
     */
    private function set_method ( $method )
    {
        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $method);
    }

    /**
     * Set the body data for the next HTTP request.
     *
     * @return void
     */
    private function set_body( $data )
    {
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $data);
    }

    /**
     * Prepare the curl instance for the next request.
     *
     * @param  string $method
     * @param  string $target
     * @param  mixed  $body
     * @return void
     */
    private function prepare_request( $method, $target, $body )
    {
        $this->set_headers();
        $this->set_target($target);
        $this->set_body($body);
        $this->set_method($method);
    }

    /**
     * Execute the current curl request.
     *
     * @return mixed
     */
    private function get_response()
    {
        return curl_exec($this->_curl);
    }

    /**
     * Return curl error code request failure.
     *
     * @return int
     */
    private function get_error()
    {
        return curl_errno($this->_curl);
    }

    /**
     * Return the HTTP status code from curl after request execution.
     *
     * @return int
     */
    private function get_status()
    {
        return curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
    }

    /**
     * Decide if the last HTTP response wass not successfull.
     *
     * @return bool
     */
    private function http_error()
    {
        return ! in_array( $this->get_status(), self::$_success );
    }

    /**
     * Send a HTTP request using the GET method.
     *
     * @param  string $url
     * @return array|null
     */
    public function send_get( $url )
    {
        return $this->request( self::HTTP_METHOD_GET, $url );
    }

    /**
     * Send a HTTP request using the POST method.
     *
     * @param  string $url
     * @param  array  $data
     * @return array|null
     */
    public function send_post( $url, $data = null )
    {
        return $this->request( self::HTTP_METHOD_POST, $url, $data );
    }

    /**
     * Send a HTTP request using the PUT method.
     *
     * @param  string $url
     * @param  array  $data
     * @return array|null
     */
    public function send_put( $url, $data = null )
    {
        return $this->request( self::HTTP_METHOD_PUT, $url, $data );
    }

    /**
     * Send a HTTP request using the DELETE method.
     *
     * @param  string $url
     * @return array|null
     */
    public function send_delete( $url )
    {
        return $this->request( self::HTTP_METHOD_DELETE, $url );
    }

    /**
     * Send a request using the php curl library.
     *
     * @param  string $method
     * @param  string $path
     * @param  array  $body
     */
    private function request( $method, $path, $data = null)
    {

        // Initialize a new curl session to send this request
        // If initialization fails, throw an exception
        // Otherwise continue with processing the request

        $this->init_client();

        if ( $this->test_client() ) {
            throw new WR_Client_Error( );
        }

        // When curl initializes correctly the request can be build
        // The headers and options for the request are set by default
        // The request target (URI) and the method are set individually
        // If the request method is set to POST, the body is set

        $this->set_options();
        $this->set_headers();
        $this->set_target( $path );
        $this->set_method( $method );

        if ( isset( $data ) ) {
            $this->set_body( self::encode_body( $data ) );
        }

        // Send the request prepared and return the response
        // If there is no response to the request, a curl error occurred
        // Log the error and return an empty result indicating the error

        $response = $this->get_response();

        // Check if a client side error ocurred
        // If so, throw a client exception
        // If not, continue with the http result returned

        if ( $response === false ) {
            $code = $this->get_error();
            throw new WR_Client_Error( $code );
        }

        // Catch HTTP errors
        if ( $this->http_error() ) {
            $body = strval( $response );
            $code = $this->get_status();
            throw new WR_Server_Error( $body, $code );
        }

        // Close the current curl session after the request has been processed
        // Return the result containing the response body
        $this->close_client();
        return self::decode_body($response);
    }
}

endif;
