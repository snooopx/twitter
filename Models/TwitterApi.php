<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterApi {

    public $instance = null;

    function __construct($consumer_api_key, $consumer_api_secret, $access_token, $access_token_secret) {
        $this->instance = new TwitterOAuth($consumer_api_key, $consumer_api_secret, $access_token, $access_token_secret);
    }
}