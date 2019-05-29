<?php

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
		$value = getenv($key);

		if ($value === false && $default !== null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        return $value;
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
		global $config;

        if (is_null($key)) {
            return $config;
		}

		if (is_array($key)) {

			foreach ($key as $arr_key => $arr_value) {
				$config->set($arr_key, $arr_value);
			}

			return;
		}

        return ($config->has($key)) ? $config[$key] : $default;
    }
}

if (! function_exists('http_response_code'))
{
    function http_response_code($newcode = NULL)
    {
		static $code = 200;

        if ($newcode !== NULL)
        {
			header('X-PHP-Response-Code: '.$newcode, true, $newcode);

            if (! headers_sent()) {
                $code = $newcode;
			}
		}

        return $code;
    }
}

if (! function_exists('redirect')) {
	/**
	 * Redirects to the specified URL
	 *
	 * @param string $url The URL to be directed to
	 */
	function redirect($url = '/', $code = 200)
	{
		$code = (int) $code;

		http_response_code($code);

		header("Location: {$url}");
		echo "<meta http-equiv='refresh' content='0;url={$url}'>";
		echo "If your browser doesn't support redirect, click <a href='{$url}'>here</a> to redirect manually.";
		exit();
	}
}

if (! function_exists('cookie')) {
    /**
     * Create a new cookie instance.
     *
     * @param  string $name
     * @param  string $value
     * @param  int $seconds
     * @param  string $path
     * @param  string $domain
     * @param  bool $secure
     * @param  bool $httpOnly
     * @return bool
     */
    function cookie($name = null, $value = null, $seconds = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        return setcookie($name, $value, time() + $seconds, $path, $domain, $secure, $httpOnly);
    }
}

if (! function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     *
     * @return \Illuminate\Support\HtmlString
     */
    function csrf_field()
    {
        if(config('csrf.enabled')) {
            return '<input type="hidden" name="' . config('csrf.field_name') . '" value="' . csrf_token() . '">';
        }

        return '';
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function csrf_token($tokenId = null)
    {
        global $csrf;

        if(null !== $tokenId) {
            return $csrf->getToken($tokenId);
        }

        return $csrf->getToken(config('csrf.token_id'));
    }
}

if (! function_exists('request')) {
    /**
     * returns the request class instance
     *
     * @return mixed
     */
    function request()
    {
        global $request;

        return $request;
    }
}

if (! function_exists('response')) {
    /**
     * returns the response class instance
     *
     * @return mixed
     */
    function response()
    {
        global $response;

        return $response;
    }
}

if (! function_exists('abort')) {
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    function abort($content = '', int $status = 200, array $headers = [])
    {
        $response = new \Illuminate\Http\Response($content, $status, $headers);
        $response->send();
        return exit();
    }
}

if (! function_exists('auth')) {
    /**
     * returns the authentication class instance
     *
     * @return \moharram82\Auth\Auth
     */
    function auth()
    {
        global $auth;

        return $auth;
    }
}

if (! function_exists('trans')) {
    /**
     * loads a given key translation from the translations source
     *
     * @param string $key the translation key to be fetched
     * @param array $replace the parameters to be replaced in the translation message
     * @param null $locale the locale to be used to fetch the translation
     * @return array|string
     */
    function trans($key, array $replace = [], $locale = null)
    {
        global $translatorFactory;

        return $translatorFactory->translator->trans($key, $replace, $locale);
    }
}

if (! function_exists('view')) {
    /**
     * loads a given blade view
     *
     * @param string $template to be loaded
     * @param array $data to be passed to the view
     * @param array $mergeData to be merged
     * @return mixed
     */
    function view($template, $data = [], $mergeData = [])
    {
        global $view;

        return $view->make($template, $data, $mergeData)->render();
    }
}

if (! function_exists('trimStrings')) {
    /**
     * trims extra white spaces from request object (post globals)
     *
     * @param array $except
     * @return void
     */
    function trimStrings($except = [])
    {
        if(request()->request->count()) {
            foreach (request()->request->all() as $key => $value) {
                if(in_array($key, $except)) {
                    continue;
                }

                if(is_string($value)) {
                    request()->request->set($key, removeExtraSpace($value));
                }
            }
        }
    }
}
