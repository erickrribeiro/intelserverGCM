<?php
error_reporting(-1);
ini_set('display_errors', 'On');
?>

<?php
require_once __DIR__ . '/demo.php';
$demo = new Demo();
$admin_id = $demo->getDemoUser();
?>

<html xmlns="http://www.w3.org/1999/html">
    <head>
        <title>SMARTe | UFAM</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

        <link href='https://fonts.googleapis.com/css?family=Raleway:400,800,100' rel='stylesheet' type='text/css'>
        <link href='style.css' rel='stylesheet' type='text/css'>
        <link href='http://api.androidhive.info/gcm/styles/default.css' rel='stylesheet' type='text/css'>

        <script type="text/javascript">
            var user_id = '<?= $admin_id ?>';
            $(document).ready(function () {
                $('input#send_to_single_user').on('click', function () {
                    var msg = $('#send_to_single').val();
                    var to = $('.select_single').val();
                    if (msg.trim().length === 0) {
                        alert('Enter a message');
                        return;
                    }

                    $('#send_to_single').val('');
                    $('#loader_single').show();

                    $.post("v1/users/" + to + '/message',
                            {user_id: user_id, message: msg},
                    function (data) {
                        if (data.error === false) {
                            $('#loader_single').hide();
                            alert('Push notification sent successfully! You should see a Toast message on device.');
                        } else {
                            alert('Sorry! Unable to post message');
                        }
                    }).done(function () {

                    }).fail(function () {
                        alert('Sorry! Unable to send message');
                    }).always(function () {
                        $('#loader_single').hide();
                    });
                });

                $('input#send_to_multiple_users').on('click', function () {
                    var msg = $('#send_to_multiple').val();
                    var to = $('.select_multiple').val();

                    if (to === null) {
                        alert("Please select the users!");
                        return;
                    }

                    if (msg.trim().length === 0) {
                        alert('Enter a message');
                        return;
                    }

                    $('#send_to_multiple').val('');
                    $('#loader_multiple').show();

                    var selMulti = $.map($(".select_multiple option:selected"), function (el, i) {
                        return $(el).val();
                    });

                    to = selMulti.join(",");

                    $.post("v1/users/message",
                            {user_id: user_id, to: to, message: msg},
                    function (data) {
                        if (data.error === false) {
                            $('#loader_multiple').hide();
                            alert('Push notification sent successfully to multiple users');
                        } else {
                            alert('Sorry! Unable to send message');
                        }
                    }).done(function () {

                    }).fail(function () {
                        alert('Sorry! Unable to send message');
                    }).always(function () {
                        $('#loader_multiple').hide();
                    });
                });

                $('input#send_to_multiple_users_with_image').on('click', function () {

                    var msg = $('#send_to_multiple_with_image').val();
                    if (msg.trim().length === 0) {
                        alert('Enter a message');
                        return;
                    }

                    $('#send_to_multiple_with_image').val('');
                    $('#loader_multiple_with_image').show();

                    $.post("v1/users/send_to_all",
                            {user_id: user_id, message: msg},
                    function (data) {
                        if (data.error === false) {
                            $('#loader_multiple_with_image').hide();
                            alert('Push notification sent successfully to multiple users');
                        } else {
                            alert('Sorry! Unable to send message');
                        }
                    }).done(function () {

                    }).fail(function () {
                        alert('Sorry! Unable to send message');
                    }).always(function () {
                        $('#loader_topic_with_image').hide();
                    });
                });
            });
        </script>



    </head>
    <body>
        <div class="header">
            <h2>SMARTe - System for Monitoring heAlth and ReporTing Event</h2>
            <h2 class="small">Universidade Federal do Amazonas - UFAM</h2>
        </div>
        <div class="container_body">
            <div class="topics">
                <h2 class="heading">Download & Install the GCM apk</h2>
                Download & Install the Google Cloud Messaging <a href="SMARTe.apk">apk</a> before trying the demos. <br/><br/>Once installed, refresh this page
                to see your name, email in the recipients list.
            </div>

            <div class="topics">
                <br/>
                <div class="separator"></div>
                <h2 class="heading">Sending message to `Single User`</h2>
                Select your name from the below recipients and send a message<br/><br/>

                <div class="container">
                    <select class="select_single">
                        <?php
                        $users = $demo->getAllUsers();
                        foreach ($users as $key => $user) {
                            ?>
                            <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?> (<?= $user['email'] ?>)</option>
                            <?php
                        }
                        ?>
                    </select><br/>
                    <textarea id="send_to_single" class="textarea_msg" placeholder="Type a message"></textarea><br/>
                    <input id="send_to_single_user" type="button" value="Send to single user" class="btn_send"/>
                    <img src="loader.gif" id="loader_single" class="loader"/>
                </div>
                <br/>
                <div class="separator"></div>
                <h2 class="heading">Sending message to `Multiple Users`</h2>
                Select multiple recipients and send a message. You can use ctrl or shift to select multiple users<br/><br/><br/>

                <div class="container">
                    <select multiple class="select_multiple">
                        <?php
                        foreach ($users as $key => $user) {
                            ?>
                            <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?> (<?= $user['email'] ?>)</option>
                            <?php
                        }
                        ?>
                    </select>
                    <br/>
                    <textarea id="send_to_multiple" class="textarea_msg" placeholder="Type a message"></textarea><br/>
                    <input id="send_to_multiple_users" type="button" value="Send to multiple users" class="btn_send"/>
                    <img src="loader.gif" id="loader_multiple" class="loader"/>
                </div>

                <br/>
                <div class="separator"></div>
                <h2 class="heading">Cadastrando familiares</h2>
                Selecione um paciente<br/><br/>

                <div class="container">
                    <select id="paciente">
                        <?php
                            $users = $demo->getAllUsers();
                            foreach ($users as $key => $user) {
                        ?>
                            <option value="<?= $user['user_id'] ?>"><?= $user['name'] ?> (<?= $user['email'] ?>)</option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="container">

                    <p>
                        Familiares
                        <select id="family" class="js-example-basic-multiple-limit js-states form-control" multiple="multiple" style="width: 500px">
                        </select>
                        <input id="insert_family" type="button" value="Cadastrar familiares" class="btn"/>
                        <img src="loader.gif" id="loader_multiple" class="loader"/>
                    </p>
                </div>
            </div>
            <br/><br/>
            <br/><br/>
            <br/><br/>
        </div>
<!--        https://github.com/select2/select2-->

        <script type="text/javascript">
            $(".js-example-basic-multiple-limit").select2({
                maximumSelectionLength: 10
            });

            $('#paciente').on('change', function () {
                var paciente = $('#paciente').val();
                console.log(paciente);

                var x = document.getElementById("family");


                $.get("v1/users/" + paciente + '/all',
                    function (data) {
                        if (data.error === false) {
                            for(var i=0; i < data.disponiveis.length; i++){
                                console.log(data.disponiveis[i]);

                                var option = document.createElement("option");
                                option.text = data.disponiveis[i].name;
                                option.value = data.disponiveis[i].user_id;
                                x.add(option);

                            }

                            $('#loader_single').hide();

                        } else {
                            alert('Sorry! Unable to post message');
                        }
                    }).done(function () {

                }).fail(function () {

                    alert('Sorry! Unable to send message');
                }).always(function () {
                    $('#loader_single').hide();
                });




            });

            $('input#insert_family').on('click', function () {
                var familia = $('#family').val();
                var paciente = $('#paciente').val();

                console.log(paciente);
                console.log(familia);

                var save =  true;

                for(var i=0; i < familia.length; i++){
                    console.log(familia[i]);

                    $.post("v1/users/addparente",
                    {id_paciente: paciente, id_familiar: familia[i]},
                    function (data) {
                        if (data.error === false) {
                            save = true;

                        } else {
                            save = false;

                        }
                    }).done(function () {

                }).fail(function () {
                        save =  false;
                }).always(function () {
                    $('#loader_topic_with_image').hide();
                });

                }
                $('#loader_single').show();

                if(save == true){
                    alert('Familiar cadastrados com sucesso.');
                }else{
                    alert('Desculpa! Não foi possivel cadastrar o familiar.');
                }

            });
        </script>
    </body>
</html>
