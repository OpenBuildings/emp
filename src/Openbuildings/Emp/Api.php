<?php 

namespace Openbuildings\Emp;

/**
 * @package    Openbuildings\Emp
 * @author     Ivan Kerin
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Api {

	/**
	 * This service allows direct payments via the Web Services API. It supports one o and managed recurring payments. For merchant managed rebilling please refer to the Merchant Managed Rebilling service definition.
	 */
	const ORDER_SUBMIT = '/service/order/submit';

	/**
	 * Merchant managed rebills allows merchant to control the rebill schedule, or to generate adhoc payments referencing an existing order.
	 * This functionality is only available for merchants who have this functionality enabled, and who have corresponding non CVV/Rebill accounts configured.
	 */
	const MERCHANT_MANAGED_REBILL = '/service/order/rebill';

	/**
	 * This method will settle an Order previously performed as an Auth. This will generate an email notification to the customer on success if customer emails have been configured in your eCommerce Configuration. Notifications to the merchant are enabled by default.
	 */
	const ORDER_SETTLE = '/service/order/settle';

	/**
	 * This method will credit the specified item/transaction. Multiple items can be credited in the one request.
	 * For Auth transactions – you must provide the Transaction ID of the Settlement transaction. If you wish to reverse the Auth you should use the Void method instead.
	 */
	const ORDER_CREDIT = '/service/order/credit';

	/**
	 * This method will perform a cardholder funds transfer for the specified Order.
	 * CFT is only available if enabled by your Account Manager. CFT is not available for all merchant categories or payment types.
	 * Successful CFT transactions will result in a pending response. Your request may require approval (based on your account setup), and will then be sent to the acquirer. You will receive a further notification once the request is approved/declined.
	 */
	const ORDER_CFT = '/service/order/cft';

	/**
	 * This method will void the specified Order. This method is only available if the original transaction was an Auth. Notification to the customer is not performed.
	 */
	const ORDER_VOID = '/service/order/void';

	/**
	 * This service allows retrieval of Order data for reconciliation.
	 */
	const ORDER_SEARCH = '/service/order/search';

	/**
	 * This service allows retrieval of transaction data for reconciliation.
	 */
	const TRANSACTION_SEARCH = '/service/transaction/search';

	/**
	 * This service allows retrieval of all TC40/SAFE data related to your accounts. This data is provided by select payment acquirers, which in turn receive this data from the card associations VISA and Mastercard.
	 */
	const FRAUD_DATA = '/service/risk/frauddata';

	/**
	 * This service allows retrieval of chargeback data for reconciliation purposes.
	 */
	const CHARGEBACK_SEARCH = '/service/chargeback/search';

	/**
	 * This service allows retrieval of previously created Customers for reconciliation purposes.
	 */
	const CUSTOMER_SEARCH = '/service/customer/search';

	/**
	 * This method will instantly upgrade an existing Rebill. Upgrade means to explicitly bring forward the rebilling date immediately, perform a transaction and start the rebilling period from now. The initial trial period for the item will no longer be valid.
	 */
	const ORDER_REBILL_INSTANT_UPGRADE = '/service/order/instantupgrade';

	/**
	 * This method will cancel an existing Rebill. No customer notification is performed.
	 * Reversal of a cancellation is not supported. Usernames and Passwords associated with this Rebill will expire when the current Rebill cycle elapses.
	 */
	const ORDER_CANCEL_REBILLING = '/service/order/cancelrebill';

	/**
	 * This service allows initiation of a VBV/3D Secure Authentication request.
	 */
	const VBVMC3D_AUTH = '/service/vbvmc3d';

	/**
	 * This service allows retrieval of the results for a VBV/3D Secure Authentication request. This allows the merchant to retrieve fields required for submission in the Order Submit API method.
	 */
	const VBVMC3D_RESULT = '/service/vbvmc3d/result';

	/**
	 * The Customer Entity allows an easy way to group Orders together for a single Customer. Once a Customer ID is created - you can pass this information through on creation of new Orders.
	 */
	const CREATE_CUSTOMER = '/service/customer/add';

	/**
	 * The Customer Entity allows an easy way to group Orders together for a single Customer. This function allows you to update an existing Customer with a new Name or Email address.
	 */
	const UPDATE_CUSTOMER = '/service/customer/update';

	/**
	 * This method allows you to retrieve previous card details used by the Customer. Only cards which have been flagged by the Customer/Merchant to remember will be retrieved. Expired cards will not be retrieved. This services does not support non-credit card payment methods.
	 */
	const GET_CUSTOMER_CARDS = '/service/customer/getcards';

	/**
	 * Phone verify provides the ability to validate a customers identity using a phone verification service. It works by sending a unique code to the users phone that is then entered by the user and submitted with your payment request.
	 */
	const PHONE_VERIFY = '/service/phoneverify';

	/**
	 * This service allows retrieval of a list of INPay supported banks based on the customers country. The resulting bank id can then be passed into the Order Submit web service when making an INPay payment.
	 */
	const INPAY_BANKS = '/service/inpay/getbanks';

	/**
	 * This service allows retrieval of the payment instructions for an existing Order. The payment instructions provide the customer with the information they need to make the bank payment.
	 */
	const INPAY_INSTRUCTIONS = '/service/inpay/getinstructions';

	/**
	 * Threatmatrix should be used only on requests with user interaction in them, like order/submit. 
	 * @var array
	 */
	protected static $_endpoints_with_threatmatrix = array(
		self::ORDER_SUBMIT,
	);

	protected static $_instance;

	/**
	 * Configure the default instance (Api::instance())
	 * 
	 * @param  string $gateway_url url
	 * @param  string $client_id   
	 * @param  string $api_key     
	 */
	public static function configure($gateway_url, $client_id, $api_key)
	{
		self::$_instance = new Api($gateway_url, $client_id, $api_key);
	}
	
	/**
	 * return the default instance, you need to run Api::configure() to set it up
	 * @return Api 
	 */
	public static function instance()
	{
		if ( ! self::$_instance) 
			throw new Exception('You need to run Api::configure to be able to use the global api instance');

		return self::$_instance;
	}

	protected $_client_id;
	protected $_api_key;
	protected $_gateway_url;
	protected $_proxy;
	
	/**
	 * Threatmatrix object, used to set thm_session_Id
	 * @var Threatmarix
	 */
	protected $_threatmatrix;

	/**
	 * Getter
	 * @return string 
	 */
	public function client_id()
	{
		return $this->_client_id;
	}

	/**
	 * Getter
	 * @return string 
	 */
	public function api_key()
	{
		return $this->_api_key;
	}

	/**
	 * Getter
	 * @return string
	 */
	public function gateway_url()
	{
		return $this->_gateway_url;
	}

	/**
	 * Getter / Setter, should be in a format user:password@host:port
	 * @param  string $proxy 
	 * @return string        
	 */
	public function proxy($proxy = NULL)
	{
		if ($proxy !== NULL)
		{
			$this->_proxy = $proxy;
			return $this;
		}
		return $this->_proxy;
	}


	/**
	 * Getter / Setter of a Threatmatrix object
	 * @param  Threatmatrix $threatmatrix 
	 * @return Threatmatrix|$this               
	 */
	public function threatmatrix($threatmatrix = NULL)
	{
		if ($threatmatrix !== NULL)
		{
			$this->_threatmatrix = $threatmatrix;
			return $this;
		}
		return $this->_threatmatrix;
	}

	function __construct($gateway_url, $client_id, $api_key)
	{
		if ( ! filter_var($gateway_url, FILTER_VALIDATE_URL)) 
			throw new Exception('Gateway url must be a proper url');
		
		$this->_client_id = $client_id;
		$this->_api_key = $api_key;
		$this->_gateway_url = $gateway_url;
	}

	/**
	 * Return the parameters required for authentication
	 * @return array 
	 */
	public function auth_params($endpoint)
	{
		$params = array(
			'client_id' => $this->client_id(), 
			'api_key' => $this->api_key(),
		);

		if (in_array($endpoint, self::$_endpoints_with_threatmatrix) AND $this->threatmatrix())
		{
			$params['thm_session_id'] = $this->threatmatrix()->session_id();
		}

		return $params;
	}

	/**
	 * Generate a url for an api request
	 * @param  string $endpoint 
	 * @param  array  $params   
	 * @return string           
	 */
	public function generate_url($endpoint, array $params)
	{
		$params = array_merge($this->auth_params($endpoint), $params);
		$url = $this->_gateway_url.$endpoint;

		return $url.'?'.http_build_query($params);
	}

	/**
	 * Perform an api request, return an array with result details
	 * 
	 * @param  string $endpoint 
	 * @param  array  $params   
	 * @throws Openbuildings\Emp\Exception If errors in request, api response or the card is declined
	 * @return array           
	 */
	public function request($endpoint, array $params)
	{
		$url = $this->generate_url($endpoint, $params);

		$options = array();

		if ($this->proxy()) 
		{
			$options[CURLOPT_PROXY] = $this->proxy();
		}

		$response = Remote::get($url, $options);

		$xml_response = new \SimpleXMLElement($response);

		$trans_id = (string) ($xml_response->transaction->trans_id ?: $xml_response->trans_id);
		$response_code = (string) ($xml_response->transaction->response ?: $xml_response->response);
		
		if ($xml_response->errors)
		{
			$errors = array();
			foreach ($xml_response->errors as $error) 
			{
				$errors[] = '('.$error->error->code.') '.$error->error->text;
			}
				
			throw new Exception('Error sendig request to gateway: :errors', array(
				':errors' => join(', ', $errors)
			));
		}
		elseif ( (string) $response_code === 'D')
		{
			throw new Exception('The transaction was declined: :errors', array(
				':errors' => (string) $xml_response->transaction->response_text
			));
		}
		
		return array(
			'order_id' => (string) $xml_response->order_id,
			'order_status' => (string) $xml_response->order_status,
			'transaction_response' => $response_code,
			'transaction_id' => $trans_id,
			'raw' => json_decode(json_encode($xml_response), TRUE),
		);
	}
}
