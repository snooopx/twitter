<?php


class Users {

    // Users to check
    public $tw_users = [
        'realDonaldTrump',
        'ladygaga',
        'ISIS',
        'AJEnglish'
    ];

    // Keeps Mongo Db Users Collection
    public $collection = null;

    function __construct() {
        $mng_client = new MongoDB\Client("mongodb://localhost:27017");

        $this->collection = $mng_client->demo->users;
    }

    // Get Since Id for searching from last check
    public function getSinceId($doc) {

        if(!$doc) {
            return false;
        }

        $now = new DateTime('now');

        // Get interval from last checked
        $interval = date_diff($now, DateTime::createFromFormat('Y-m-d',$doc->tw_stats->last_checked));

        if(intval($interval->format('%a')) > 0) {

            $url = parse_url($doc->tw_stats->refresh_url, PHP_URL_QUERY);
            parse_str($url, $refresh_url);

            if(isset($refresh_url['since_id'])) {

                return $refresh_url['since_id'];
            }

        } else {
            return false;
        }
    }


    public function getMentioners($search) {

        $mentioners = [];
        foreach($search->statuses as $s) {

            // Add new users to array
            if(!isset($mentioners[$s->user->screen_name])) {

                $mentioners[$s->user->screen_name] = [];
                $mentioners[$s->user->screen_name]['count'] = 1;
            } else {

                $mentioners[$s->user->screen_name]['count']++;
            }
        }

        return $mentioners;
    }
}