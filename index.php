<?php
    require 'vendor/autoload.php';
    require 'configs.php';
    require 'Models/TwitterApi.php';
    require 'Models/Users.php';

    $now = new DateTime('now');
    $user = new Users();
    $twitter = new TwitterApi(CONSUMER_API_KEY, CONSUMER_API_SECRET, $access_token, $access_token_secret);

    echo "<pre>";

    foreach($user->tw_users as $tw_user) {

        // Get user from DB
        $doc = $user->collection->findOne(['screen_name' => $tw_user]);

        $tw_query = [
            "q"   => '@'.$tw_user,
            "result_type"   => 'mixed',
            'count'         => '100'
        ];

        $since_id = true;//$user->getSinceId($doc);

        // Do Search for first time or when since_id exists
        if(!$doc || ($doc && $since_id)) {

            // Set Since id for twitter query
            $tw_query['since_id'] = $since_id;

            // Search
            $search = $twitter->instance->get("search/tweets", $tw_query);

            // Get Mention users
            $mentioners = $user->getMentioners($search);

            // Check for old mentions and increment count
            if($doc && $mentioners) {

                foreach($doc->tw_stats->who_mentioned as $name => $data) {
                    if(isset($mentioners[$name])) {
                        $mentioners[$name]['count'] += $data->count;
                    } else {
                        $mentioners[$name] = [];
                        $mentioners[$name]['count'] = $data->count;
                    }
                }
            }

            // DB update query
            $mng_query = [
                'screen_name' => $tw_user,
                'tw_stats' => [
                    'last_checked' => $now->format('Y-m-d'),
                    'who_mentioned' => $mentioners,
                    'refresh_url' => $search->search_metadata->refresh_url
                ]
            ];

            // Update user data
            if($doc) {

                $mng_query['tw_stats']['first_checked'] = $doc->statuses->tw_stats->first_ckecked;

                // Replace Users Doc with new one
                // *Tried to use updateOne() but it did not work
                $result = $user->collection->replaceOne(['screen_name' => $tw_user], $mng_query);
            } else {

                $mng_query['tw_stats']['first_checked'] = $now->format('Y-m-d');

                // Insert new document
                $result = $user->collection->insertOne($mng_query);
            }
        }
    }
