<?php

error_reporting(-1);
ini_set('display_errors', 'On');

require_once '../include/db_handler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->get('/', function() {
    $response["error"] = false;
    echoRespnse(200, $response);
});

// User login
$app->post('/user/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('name', 'email'));

    // reading post params
    $name = $app->request->post('name');
    $email = $app->request->post('email');

    // validating email address
    validateEmail($email);

    $db = new DbHandler();
    $response = $db->createUser($name, $email);

    // echo json response
    echoRespnse(200, $response);
});


/* * *
 * Updating user
 *  we use this url to update user's gcm registration id
 */
$app->put('/user/:id', function($user_id) use ($app) {
    global $app;

    verifyRequiredParams(array('gcm_registration_id'));

    $gcm_registration_id = $app->request->put('gcm_registration_id');

    $db = new DbHandler();
    $response = $db->updateGcmID($user_id, $gcm_registration_id);

    echoRespnse(200, $response);
});

/**
 * Sending push notification to a single user
 * We use user's gcm registration id to send the message
 * * */
$app->post('/users/:id/message', function($to_user_id) {
    global $app;
    $db = new DbHandler();

    verifyRequiredParams(array('message'));

    $from_user_id = $app->request->post('user_id');
    $message = $app->request->post('message');

    require_once __DIR__ . '/../libs/gcm/gcm.php';
    require_once __DIR__ . '/../libs/gcm/push.php';
    $gcm = new GCM();
    $push = new Push();

    $fromuser = $db->getUser($from_user_id);
    $user = $db->getUser($to_user_id);
    
    $msg = array();
    $msg['message'] = $message;
    $msg['message_id'] = '';
    $msg['chat_room_id'] = '';
    $msg['created_at'] = date('Y-m-d G:i:s');

    $data = array();
    $data['user'] = $fromuser;
    $data['message'] = $msg;
    $data['image'] = '';

    $push->setTitle("Google Cloud Messaging");
    $push->setIsBackground(FALSE);
    $push->setFlag(PUSH_FLAG_USER);
    $push->setData($data);

    // sending push message to single user
    $gcm->send($user['gcm_registration_id'], $push->getPush());

    $response['user'] = $user;
    $response['error'] = false;


    echoRespnse(200, $response);
});

$app->post('/users/addparente', function() use ($app) {

    $response = array();
    $db = new DbHandler();

    $id_paciente = $app->request->post('id_paciente');
    $id_familiar = $app->request->post('id_familiar');

    $db->addParente($id_paciente, $id_familiar);

    $response['error'] = false;
    $response['paciente'] = $id_paciente;
    $response['familiar'] = $id_familiar;

    echoRespnse(200, $response);
});

$app->get('/users/:id/all', function($id) {
    global $app;
    $db = new DbHandler();

    $users = $db->getUserPaciente($id);

    $response['error'] = false;
    $response['paciente'] = $id;
    $response['disponiveis'] = $users;

    echoRespnse(200, $response);
});


$app->post('/users/push_test', function() {
    global $app;

    verifyRequiredParams(array('message', 'api_key', 'token'));

    $message = $app->request->post('message');
    $apiKey = $app->request->post('api_key');
    $token = $app->request->post('token');
    $image = $app->request->post('include_image');

    $data = array();
    $data['title'] = 'Google Cloud Messaging';
    $data['message'] = $message;
    if ($image == 'true') {
        $data['image'] = 'http://api.androidhive.info/gcm/panda.jpg';
    } else {
        $data['image'] = '';
    }
    $data['created_at'] = date('Y-m-d G:i:s');

    $fields = array(
        'to' => $token,
        'data' => $data,
    );

    // Set POST variables
    $url = 'https://gcm-http.googleapis.com/gcm/send';

    $headers = array(
        'Authorization: key=' . $apiKey,
        'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $response = array();

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        $response['error'] = TRUE;
        $response['message'] = 'Unable to send test push notification';
        echoRespnse(200, $response);
        exit;
    }

    // Close connection
    curl_close($ch);

    $response['error'] = FALSE;
    $response['message'] = 'Test push message sent successfully!';

    echoRespnse(200, $response);
});


/**
 * Sending push notification to multiple users
 * We use gcm registration ids to send notification message
 * At max you can send message to 1000 recipients
 * * */
$app->post('/users/message', function() use ($app) {

    $response = array();
    verifyRequiredParams(array('user_id', 'to', 'message'));

    require_once __DIR__ . '/../libs/gcm/gcm.php';
    require_once __DIR__ . '/../libs/gcm/push.php';

    $db = new DbHandler();

    $user_id = $app->request->post('user_id');
    $to_user_ids = array_filter(explode(',', $app->request->post('to')));
    $message = $app->request->post('message');

    $user = $db->getUser($user_id);
    $users = $db->getUsers($to_user_ids);

    var_dump($users);
    $registration_ids = array();

    // preparing gcm registration ids array
    foreach ($users as $u) {
        array_push($registration_ids, $u['gcm_registration_id']);
    }

    // insert messages in db
    // send push to multiple users
    $gcm = new GCM();
    $push = new Push();

    // creating tmp message, skipping database insertion
    $msg = array();
    $msg['message'] = $message;
    $msg['message_id'] = '';
    $msg['chat_room_id'] = '';
    $msg['created_at'] = date('Y-m-d G:i:s');

    $data = array();
    $data['user'] = $user;
    $data['message'] = $msg;
    $data['image'] = '';

    $push->setTitle("SMARTe informa");
    $push->setIsBackground(FALSE);
    $push->setFlag(PUSH_FLAG_USER);
    $push->setData($data);

    // sending push message to multiple users
    $gcm->sendMultiple($registration_ids, $push->getPush());

    $response['error'] = false;

});

$app->get('/users/alert/:id', function($id) use ($app) {
    $latitude = doubleval($app->request->get('lat'));
    $longitude = doubleval($app->request->get('long'));
    $response = array();

    require_once __DIR__ . '/../libs/gcm/gcm.php';
    require_once __DIR__ . '/../libs/gcm/push.php';

    $db = new DbHandler();
    $user_id = $id;

    $user = $db->getUser($user_id);
    $users = $db->getParents($user_id);

    $registration_ids = array();

    // preparing gcm registration ids array
    foreach ($users as $u) {
        array_push($registration_ids, $u['gcm_registration_id']);
    }

    // insert messages in db
    // send push to multiple users
    $gcm = new GCM();
    $push = new Push();

    // creating tmp message, skipping database insertion

    $msg = array();
    $msg['message'] = "EEEi mano o maluco caiu, corre lá";
    $msg['latitude'] = $app->request->get('lat');
    $msg['longitude'] = $app->request->get('long');
    $msg['message_id'] = '';
    $msg['chat_room_id'] = '';
    $msg['created_at'] = date('Y-m-d G:i:s');

    $data = array();
    $data['user'] = $user;
    $data['message'] = $msg;
    $data['image'] = utf8_encode("https://maps.googleapis.com/maps/api/staticmap?center=$latitude,$longitude&zoom=16&size=400x400&key=AIzaSyDw21X58Gin5dqvlEh978nyQprBvTlEhiE");

//    $data['image'] = utf8_encode("https://maps.googleapis.com/maps/api/staticmap?center=$latitude,$longitude&zoom=12&size=400x400&markers=color:blue|label:P|$latitude,$longitude&key=AIzaSyDw21X58Gin5dqvlEh978nyQprBvTlEhiE");

//    $data = array();
//    $data['user'] = $user;
//    $data['message'] = $msg;
//    $data['image'] = '';

    $push->setTitle("SMARTe informa");
    $push->setIsBackground(FALSE);
    $push->setFlag(PUSH_FLAG_USER);
    $push->setData($data);

    // sending push message to multiple users
    $gcm->sendMultiple($registration_ids, $push->getPush());


    $response['error'] = false;
    $response['payload'] = $data;



    echoRespnse(200, $response);
});

$app->post('/users/send_to_all', function() use ($app) {

    $response = array();
    verifyRequiredParams(array('user_id', 'message'));

    require_once __DIR__ . '/../libs/gcm/gcm.php';
    require_once __DIR__ . '/../libs/gcm/push.php';

    $db = new DbHandler();

    $user_id = $app->request->post('user_id');
    $message = $app->request->post('message');

    require_once __DIR__ . '/../libs/gcm/gcm.php';
    require_once __DIR__ . '/../libs/gcm/push.php';
    $gcm = new GCM();
    $push = new Push();

    // get the user using userid
    $user = $db->getUser($user_id);

    // creating tmp message, skipping database insertion
    $msg = array();
    $msg['message'] = $message;
    $msg['message_id'] = '';
    $msg['chat_room_id'] = '';
    $msg['created_at'] = date('Y-m-d G:i:s');

    $data = array();
    $data['user'] = $user;
    $data['message'] = $msg;
// key map static    AIzaSyDw21X58Gin5dqvlEh978nyQprBvTlEhiE
//    "https://maps.googleapis.com/maps/api/staticmap?center=-3.1003528890498,-59.976722449173&zoom=12&size=400x400&markers=color:blue|label:P|-3.1003528890498,-59.976722449173&key=AIzaSyDw21X58Gin5dqvlEh978nyQprBvTlEhiE"
    $data['image'] = 'https://maps.googleapis.com/maps/api/staticmap?center=40.714728,-73.998672&zoom=12&size=400x400&key=AIzaSyDw21X58Gin5dqvlEh978nyQprBvTlEhiE';

    $push->setTitle("SMARTe Informa");
    $push->setIsBackground(FALSE);
    $push->setFlag(PUSH_FLAG_USER);
    $push->setData($data);

    // sending message to topic `global`
    // On the device every user should subscribe to `global` topic
    $gcm->sendToTopic('global', $push->getPush());

    $response['user'] = $user;
    $response['error'] = false;

    echoRespnse(200, $response);
});

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

function IsNullOrEmptyString($str) {
    return (!isset($str) || trim($str) === '');
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>