<?php
/**
 * @todo
 * Wrap all public actions in try..catch construct
 */
class ApiController extends Controller {

	/**
	 * POST Request variables
	 *
	 * @var Request
	 */
	protected $post;

	/**
	 * GET Request variables
	 *
	 * @var Request
	 */
	protected $get;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * A conainer to hold processed request's outcome
	 *
	 * @var array
	 */
	protected $data = array(
		'statusCode' => 200,
		'statusText' => 'OK',
		'response' => '',
	);

	/**
	 * Error information
	 *
	 * @var array
	 */
	protected $error = array();

	/**
	 * @var OAuth2\Server
	 */
	protected $oauthServer;

	public function __construct(Site &$site) {
		parent::__construct($site);
		$this->initialize();
	}

	public function indexAction() {
		redirect_to('public/index');
	}

	public function oauthAction($action = null) {
		if (!$this->isValidAction('oauth', $action)) {
			$this->response->badRequest('Invalid Oauth Request');
		}

		$this->intializeOauth();
		if ($action === 'authorize') {
			$this->authorize();
		} elseif ($action === 'token') {
			$this->token();
		}

		$this->response();
	}

	public function postAction($action = null) {
		/*
		 * @todo
		 * Implement resouce access under oauth
		 */
		if (!Request::isHTTPPostRequest() || !$this->isValidAction('post', $action)) {
			$this->response->badRequest('Invalid Post Request');
		}

		$method = $this->getPrivateAction($action, '-', true);
		$this->{$method}();
		$this->response();
	}

	public function getAction($action = null) {
		/*
		 * @todo
		 * Implement resouce access under oauth
		 */
		if (!Request::isHTTPGetRequest() || !$this->isValidAction('get', $action)) {
			$this->response->badRequest('Invalid Get Request');
		}

		$method = $this->getPrivateAction($action, '-', true);
		$this->{$method}();
		$this->response();
	}

	protected function isValidAction($type, $action) {
		$actions = array(
			'oauth' => array('authorize', 'token'),
			'post' => array('create-session', 'end-last-external'),
			'get' => array(),
		);

		return isset($actions[$type]) && in_array($action, $actions[$type]);
	}

	protected function createSession() {
		$this->initializeRun();
		$i = 0;
		$run_session = new RunSession($this->fdb, $this->run->id, null, null, null);
		$code = $this->post->getParam('code');

		if (!is_array($code)) {
			$code = array($code);
		}

		foreach ($code as $session) {
			if (($created = $run_session->create($session))) {
				$i++;
			}
		}

		if ($i) {
			$this->setData(200, 'OK', array('created_sessions' => $i));
		} else {
			$this->setError(500, 'Error occured when creating session');
			$this->setData(500, 'Error Request', $this->error);
		}
	}

	protected function endLastExternal() {
		$this->initializeRun();
		if(($session_code = $this->post->getParam('session'))) {
			$run_session = new RunSession($this->fdb, $this->run->id, null, $session_code, null);

			if($run_session->session !== NULL) {
				$run_session->endLastExternal();
				$this->setData(200, 'OK', array('success' => 'external unit ended'));
			} else {
				$this->setError(403, 'Invalid session token');
				$this->setData(500, 'Error Request', $this->error);
			}
		}

	}

	protected function authorize() {
		/*
		 * @todo
		 * Implement authorization under oauth
		 */
		$this->response->setContent('Authorize person');
		$this->response->send();
	}

	protected function token() {
		/*
		 * @todo
		 * Implement access token generation under oauth
		 */
		$this->response->setContent('Get Access token');
		$this->response->send();
	}

	protected function request() {
		
	}

	protected function response() {
		$this->response->setStatusCode($this->data['statusCode'], $this->data['statusText']);
		$this->response->setContentType('application/json');
		$this->response->setJsonContent($this->data['response']);
		$this->response->send();
	}

	protected function initialize() {
		$this->post = new Request($_POST);
		$this->get = new Request($_GET);
		$this->response = new Response();
	}

	protected function intializeOauth() {
		$this->oauthServer = Site::getOauthServer();
	}

	protected function initializeRun() {
		$run_name = $this->get->getParam('run_name');
		if (!$run_name) {
			alert('<strong>Error.</strong> Required "run_name" parameter not found!.', 'alert-danger');
		}

		$run = new Run($this->fdb, $run_name);
		if (!$run->valid) {
			$this->setError(400, 'Invalid Run');
		} elseif (!$run->hasApiAccess($this->post->getParam('api_secret'))) {
			$this->setError(403, 'Unauthorized access to run');
		}

		if ($this->error) {
			$this->setData(500, 'Error Request', $this->error);
			return $this->response();
		}

		$this->run = $run;
		return true;
	}

	private function setData($statusCode = null, $statusText = null, $response = null) {
		if ($statusCode !== null) {
			$this->data['statusCode'] = $statusCode;
		}
		if ($statusText !== null) {
			$this->data['statusText'] = $statusText;
		}
		if ($response !== null) {
			$this->data['response'] = $response;
		}
	}

	private function setError($code = null, $error = null, $desc = null) {
		if ($code !== null) {
			$this->error['error_code'] = $code;
		}
		if ($error !== null) {
			$this->error['error'] = $error;
		}
		if ($desc !== null) {
			$this->error['error_description'] = $desc;
		}
	}

}
