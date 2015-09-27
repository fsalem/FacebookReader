<?php

define("LOCAL_MODE", true);

if (LOCAL_MODE) {
    define("BASE_URL", 'http://localhost/trustami-final/Trustami-final/index.php');
    define("REDIRECT_URL", 'http://localhost/trustami-final/Trustami-final/index.php');

    define("DB_HOST", "localhost:27017");
    define("DB_USER", "admin");
    define("DB_PASS", "admin");
    define("DB_DATABASE", "trustami");

} else {
    define("BASE_URL", 'http://54.194.235.185/test/index.php');
    define("REDIRECT_URL", 'http://54.194.235.185/test/index.php');

    define("DB_HOST", "localhost:27017");
    define("DB_USER", "admin");
    define("DB_PASS", "admin");
    define("DB_DATABASE", "trustami");
}
//define("FB_SCOPES", serialize(array('user_activities','user_education_history','user_events','user_groups','user_interests',
define("FB_SCOPES", 'user_birthday, user_activities, user_checkins, user_education_history, user_events, user_groups, user_interests,
    user_likes, user_photos, user_status, user_videos, user_work_history, user_location, user_website, user_friends,
    friends_birthday, friends_activities, friends_checkins, friends_education_history, friends_events, friends_groups, friends_interests,
    friends_likes, friends_photos, friends_status, friends_videos, friends_work_history, friends_location, friends_website','offline_access');

define("COUNT_SCOPES", serialize(array('/activities','/checkins','?fields=education', '/events', '/groups', '/interests',
    '/likes', '/photos', '/statuses','/videos', '?fields=work','?fields=location')));


define("FB_APP_ID", "581212481908191");
define("FB_APP_SEC", "a00fe376e3b81721a7564beab7503038");

?>
