<?php
class RealMeLoginForm extends LoginForm {
	private static $allowed_actions = array(
		'redirectToRealMe'
	);

	protected $authenticator_class = 'RealMeAuthenticator';

	public function __construct($controller, $name) {
		$fields = new FieldList(array(
			new HiddenField('AuthenticationMethod', null, $this->authenticator_class)
		));

		$actions = new FieldList(array(
			new FormAction('redirectToRealMe', _t('RealMeLoginForm.LOGINBUTTON', 'Login with RealMe'))
		));

		parent::__construct($controller, $name, $fields, $actions);
	}

	public function redirectToRealMe($data, Form $form) {
		/** @var RealMeService $service */
		$service = Injector::inst()->get('RealMeService');

		// If there's no service, ensure we throw a predictable error
		if(!$service) return $this->controller->httpError(500);

		// This will either redirect to RealMe (via SimpleSAMLphp) or return true/false to indicate logged in state
		$loggedIn = $service->enforceLogin();

		if($loggedIn) {
			return $this->controller->redirect(Director::baseURL());
		} else {
			return Security::permissionFailure(
				$this->controller,
				_t(
					'RealMeLoginForm.LOGINFAILURE',
					'Unfortunately we\'re not able to log you in through RealMe right now.'
				)
			);
		}
	}
}