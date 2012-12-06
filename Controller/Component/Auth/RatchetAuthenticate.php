<?php

App::uses('BaseAuthenticate', 'Controller/Component/Auth');

class RatchetAuthenticate extends BaseAuthenticate {

	public function authenticate(CakeRequest $request, CakeResponse $response) {
                return false;
	}
        
	public function getUser($request) {
                debug($request);die();
		return false;
	}

}