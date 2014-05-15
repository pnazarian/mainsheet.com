<?php

class GoogleClient extends Singleton
{
	private static $isAuthenticated = false;
	private $client;
	
	protected function __construct()
	{
		$this->client = new Google_Client();
		$this->client->setClientId('871294568862-vtlif6oq1jafssh9ulrhu3h1u3v9s3gp.apps.googleusercontent.com');
		$this->client->setClientSecret('zYwl2zSTS-cpvsGm70fR2k-7');
		$this->client->setRedirectUri('http://localhost/mainsheet.com/?page=management');							// this will change here and on the Console
		$this->client->setScopes(array('https://www.googleapis.com/auth/userinfo.email',
									'https://www.googleapis.com/auth/userinfo.profile',
									'https://www.googleapis.com/auth/drive'));
		$this->client->setUseObjects(true);

		//TODO: If protocol is not HTTPS, redirect to same address with HTTPS.
		//TODO: Next, define 'User' cookie with HTTPS Only so it is not even sent if not on an authorized page
		
		$userinfoService = new Google_Oauth2Service($this->client);
		
		$failedAuthentication = false;
		if (isset($_GET['code']) && !isset($_COOKIE["user"]))
		{
			try
			{
				$accessToken = $this->client->authenticate($_GET['code']);
						
				setcookie("user", json_encode(array(
					'tokens' => $accessToken
					)), 0, "/", "", false, true);
			}
			catch (Exception $e)
			{
				$failedAuthentication = true;
			}
		}
		if (isset($_COOKIE["user"]))
		{
			$accessToken = json_decode($_COOKIE["user"])->tokens;
		}
		else if (!isset($_GET['code']) || $failedAuthentication)
		{
			$authUrl = $this->client->createAuthUrl();
			
			header('Location: '.$authUrl);
		}

		$this->client->setAccessToken($accessToken);
		
		if ($userinfoService->userinfo->get()->email != 'mainsheet@chadwickschool.org')
			header('Location: signout.php');																	//This will change
		
		$userCookie = json_encode(array(
					'tokens' => $accessToken
					));
					
		self::$isAuthenticated = true;
	}
	
	//this function shouldn't trigger instantiation, so it is static.
	public static function getIsAuthenticated()
	{
		return self::$isAuthenticated;
	}
	
	public static function signout()
	{
		setcookie("user", false, time()-3600, "/", "", false, true);
	}
	
	//for service functions, the view calls the services' methods. The services' constructors call this.
	protected function authenticate()
	{
		return $this->client;
	}
}

?>