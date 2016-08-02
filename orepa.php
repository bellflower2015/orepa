<?php
require "vendor/autoload.php";
require "orepa.ini";

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth(
    CONSUMER_KEY,
    CONSUMER_SECRET,
    ACCESS_TOKEN,
    ACCESS_TOKEN_SECRET
);

if (!file_exists('img')) {
    @mkdir('img');
}
@chdir('img');

$id = 0;

do {
    $hash_params = ['q' => '#おれぱの日', 'lang' => 'ja', 'count' => 100, 'max_id' => $id];
    $tweets = $connection->get('search/tweets', $hash_params)->statuses;

    foreach ($tweets as $tweet) {
        if ($id == 0 || bccomp($id, $tweet->id) == 1) {
            $id = $tweet->id;
            $id = bcsub($id, 1);
        }
        if (@is_array($tweet->extended_entities->media)) {
            foreach($tweet->extended_entities->media as $key => $media) {
                if (isset($tweet->extended_entities->media[$key])) {
                    $url = $tweet->extended_entities->media[$key]->media_url;
                    if (!empty($url)) {
                        $pathInfo = pathinfo($url);
                        $filename = $pathInfo['filename'] . '.' . $pathInfo['extension'];

                        if (!file_exists($filename)) {
                            echo "GET {$url} ... ";
                            $tmp = file_get_contents($url);
                            $fp = fopen($filename, 'w');
                            fwrite($fp, $tmp);
                            fclose($fp);
                            echo "DONE\n";
                        }
                    }
                }
            }
        }
    }
    sleep(1);
} while (count($tweets) > 0);
