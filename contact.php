<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
<?php
/*
Plugin Name: Contact
Plugin URI: https://github.com/pluginsWordpress/Contact/blob/main/Signal.php
Description: Plugin de Contact personnalisé pour WordPress
Version: 1.0
Author: Marouane
Author URI: https://github.com/marouane216
*/
// Fonction d'activation du plugin
function mon_plugin_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'Contact';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        fullName varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        numero varchar(13) NOT NULL,
        commentaire varchar(255) NOT NULL,
        vue TINYINT NOT NULL DEFAULT '0',
        date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'mon_plugin_activation');

// Fonction de désactivation du plugin
function mon_plugin_desactivation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'Contact';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_deactivation_hook(__FILE__, 'mon_plugin_desactivation');
function mon_plugin_shortcode_Contact()
{
    ob_start();
?>
    <style>
        .divForm {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .divForm form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            width: 100%;
        }

        .divForm form div {
            display: flex !important;
            flex-direction: row !important;
            width: 100%;
        }

        .divForm form div label {
            width: 27%;
        }

        .divForm form div input,
        .divForm form div textarea {
            width: 43%;
        }

        .divForm form div textarea {
            resize: none;
            height: 7rem;
        }

        .Submit {
            background-color: #0d6efd;
            color: black;
            font-size: 1rem;
            width: 6rem;
            display: flex;
            justify-content: center;
            border: 1px solid;
            border-radius: 7px;
            cursor: pointer;
        }

        .Submit:hover {
            color: aliceblue;
        }
    </style>
    <div class="divForm">
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <div>
                <label for="fullName">Nom Complet:</label>
                <input type="text" name="fullName" id="fullName">
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email">
            </div>
            <div>
                <label for="numero">Numero Telephone:</label>
                <input type="numero" name="numero" id="numero">
            </div>
            <div>
                <label for="commentaire">Commentaire:</label>
                <textarea name="commentaire" id="commentaire"></textarea>
            </div>
            <div>
                <input type="hidden" name="action" value="mon_plugin_register">
                <input class="Submit" type="submit" value="Envoyer">
            </div>
        </form>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('mon_plugin_form', 'mon_plugin_shortcode_Contact');
function mon_plugin_register()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'Contact';

    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $numero = $_POST['numero'];
    $commentaire = $_POST['commentaire'];
    $wpdb->insert(
        $table_name,
        array(
            'fullName' => $fullName,
            'email' => $email,
            'numero' => $numero,
            'commentaire' => $commentaire
        )
    );

    wp_redirect(home_url(''));
    exit;
}
add_action('admin_post_mon_plugin_register', 'mon_plugin_register');
function affiche_Contact_add_menu_page()
{
    add_menu_page(
        __('afficheContact', 'textdomain'),
        'Affichage Contact',
        'manage_options',
        'affiche_Contact',
        '',
        'dashicons-format-chat',
        6
    );
    add_submenu_page(
        'affiche_Contact',
        __('Books Shortcode Reference', 'textdomain'),
        __('Shortcode Reference', 'textdomain'),
        'manage_options',
        'affiche_Contact',
        'affiche_Contact_callback'
    );
}
add_action('admin_menu', 'affiche_Contact_add_menu_page');

function affiche_Contact_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'Contact';

    $results = $wpdb->get_results("SELECT * FROM $table_name");
?>
    <style>
        .actionDiv {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            width: max-content;
            margin: auto;
        }

        .action {
            width: 25.2px;
            height: 25.2px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-sizing: border-box;
            border: 1.20968px solid #000000;
            border-radius: 4.83871px;
            color: white;
        }

        .delete {
            background-color: #FF0000;
        }

        .edit {
            background-color: #80FF00;
        }

        .edit a i {
            color: white;
        }

        .Role {
            background: #00d1ff;
        }
    </style>
    <table class="table" id="myTable">
        <thead>
            <tr>
                <th scope="col">Nom Complet</th>
                <th scope="col">Email</th>
                <th scope="col">Numero Telephone</th>
                <th scope="col">Commentaire</th>
                <th scope="col">Date</th>
                <th scope="col">Vue</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result) { ?>
                <tr>
                    <td><?= $result->fullName ?></td>
                    <td><?= $result->email ?></td>
                    <td><?= $result->numero ?></td>
                    <td><?= $result->commentaire ?></td>
                    <td><?= $result->date ?></td>
                    <td>
                        <?php
                        if ($result->vue == 0) {
                            echo 'Non Lue';
                        }
                        if ($result->vue == 1) {
                            echo 'Lue';
                        }
                        ?>
                    </td>
                    <td class="actionDiv">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="delete_Contact">
                            <input type="hidden" name="id_contact" value="<?=$result->id?>">
                            <button title="delete" type="submit" class="action delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="lue_Contact">
                            <input type="hidden" name="id_contact" value="<?=$result->id?>">
                            <?php if ($result->vue == 0) : ?>
                                <button title="Non Lue / Lue?" class="action Role">
                                    <i class="fa fa-edit"></i>
                                </button>
                            <?php endif ?>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <button onclick="exportTableToExcel('myTable')">Export to Excel</button>
    <script>
        function exportTableToExcel(tableID, filename = '') {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            // Specify file name
            filename = filename ? filename + '.xls' : 'excel_data.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if (navigator.msSaveOrOpenBlob) {
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }
    </script>
<?php
}
function delete_Contact()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'Contact';

    $id = $_POST['id_contact'];

    $wpdb->get_results("DELETE FROM $table_name WHERE id = $id");

    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = wp_get_referer();
        wp_redirect($referer);
        exit;
    }
}
add_action('admin_post_delete_Contact', 'delete_Contact');

function lue_Contact()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'Contact';

    $id = $_POST['id_contact'];

    $wpdb->get_results("UPDATE $table_name SET vue = 1 WHERE id = $id");

    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = wp_get_referer();
        wp_redirect($referer);
        exit;
    }
}
add_action('admin_post_lue_Contact', 'lue_Contact');
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
