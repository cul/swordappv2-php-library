<?php

    // Load the PHP library
    include_once('../../../swordappclient.php');
    include_once('../utils.php');

    // Store the values
    session_start();
    $_SESSION['sdurl'] = $_POST['sdurl'];
    $_SESSION['u'] = $_POST['u'];
    $_SESSION['p'] = $_POST['p'];
    $_SESSION['obo'] = $_POST['obo'];

    // Try and load the service document
    $client = new SWORDAPPClient();
    $response = $client->servicedocument($_POST['sdurl'], $_POST['u'], $_POST['p'], $_POST['obo']);

    if ($response->sac_status != 200) {
        $error = 'Unable to load service document. HTTP response code: ' .
                 $response->sac_status . ' - ' . $response->sac_statusmessage;
        $_SESSION['error'] = $error;
        header('Location: ../');
        die();
    } else {
        $_SESSION['error'] = '';
    }

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>SWORD v2 exerciser</title>
        <link rel='stylesheet' type='text/css' media='all' href='../css/style.css' />
    </head>
    <body>

        <div id="header">
            <h1>SWORD v2 exerciser</h1>
        </div>

        <p>
            Select a collection and an action:
        </p>

        <div class="section">

            <?php
                foreach ($response->sac_workspaces as $workspace) {
                    $wstitle = $workspace->sac_workspacetitle;
                    echo '<h3>Workspace: '. $wstitle .'</h3>';
                    $collections = $workspace->sac_collections;
                    foreach ($collections as $collection) {
                        ?><form action="get-sd/" method="post"><?php
                        $ctitle = $collection->sac_colltitle;
                        echo '<ul>';
                        echo '<li><b>Collection: </b>' . $ctitle . ' (' . $collection->sac_href . ')<ul>';
                        if (count($collection->sac_accept) > 0) {
                            foreach ($collection->sac_accept as $accept) {
                                echo "<li>Accepts: " . $accept . "</li>";
                            }
                        }
                        if (count($collection->sac_acceptalternative) > 0) {
                            foreach ($collection->sac_acceptalternative as $accept) {
                                echo "<li>Accepts: " . $accept . " alternative='multipart-related'</li>";
                            }
                        }
                        if (count($collection->sac_acceptpackaging) > 0) {
                            foreach ($collection->sac_acceptpackaging as $acceptpackaging => $q) {
                                echo "<li>Accepted packaging format: " . $acceptpackaging . " (q=" . $q . ")</li>";
                            }
                        }
                        if (!empty($collection->sac_collpolicy)) {
                            echo "<li>Collection Policy: " . $collection->sac_collpolicy . "</li>";
                        }
                        echo "<li>Collection abstract: " . $collection->sac_abstract . "</li>";
                        $mediation = "false";
                        if ($collection->sac_mediation == true) { $mediation = "true"; }
                        echo "<li>Mediation: " . $mediation . "</li>";
                        if (!empty($collection->sac_service)) {
                            echo "<li>Service document: " . $collection->sac_service . "</li>";
                        }
                        echo '</ul></li></ul>';
                        ?></form><?php
                    }
                }
            ?>

        </div>

        <div class="section">
            <h2>Response:</h2>
            <pre>Status code: <?php echo $response->sac_status; ?></pre>
            <pre><?php echo xmlpp($response->sac_xml, true); ?></pre>
        </div>

        <div id="footer">
                Based on the <a href="http://github.com/stuartlewis/swordappv2-php-library/">swordappv2-php-library</a>
        </div>
    </body>
</html>