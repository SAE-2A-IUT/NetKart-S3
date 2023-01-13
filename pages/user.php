<?php
require 'header.php';
startPage("Utilisateur",[K_STYLE."main",K_STYLE."user"],[]);
?>

    <div class="body">
        <div class="left">
            <h1>User1207</h1>
            <?php
                if (isset($_GET['success']))
                {
                    if ($_GET['success'] == "TwT"){
                        ?><h2 class="error">Une erreur est survenue et la mise a jour du mot de passe n'a pas fonctionné</h2><?php
                    }
                    else {
                        ?><h2 class="success">Mot de passe modifié</h2><?php
                    }

                }
            ?>
            <div class="new_password">
                <span>Changer son mot de passe :</span>
                <form class="new_psw_form" method="post" action="user_post_2.php">
                    <label>Nouveau mot de passe</label>
                    <input name="new_password" type="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[?!*µ$£¤=+°@_~#]).{8,72}$" >
                    <label>Confirmation</label>
                    <input name="new_password_conf" type="password">
                    <input name="new_psw_form" type="submit" value="Changer">
                </form>
            </div>
        </div>
        <div class="right">
            <img src="<?php echo K_IMAGE; ?>default_avatar.jpg">
            <button id="delete_account" class="delete">Supprimer compte</button>
            <div id="horizontal" class="horizontal">
                <form>
                    <input type="submit" value="Continuer" class="send_from" id="button_click">
                </form>
                <button id="cancel_delete_account">annuler</button>
            </div>

            <p id="alert">etes vous sûr? <br>les circuits que vous avez créer seront egalement supprimé les circuits que vous avez créer seront egalement supprimé</p>
            <script>
                document.getElementById("horizontal").style.display ="none";
                document.getElementById("alert").style.display ="none";

                function delete_account() {
                    document.getElementById("horizontal").style.display = "flex";
                    document.getElementById("alert").style.display = "flex";
                }

                function cancel_delete_account() {
                    document.getElementById("horizontal").style.display = "none";
                    document.getElementById("alert").style.display = "none";
                }

                document.getElementById('delete_account').addEventListener('click', delete_account);
                document.getElementById('cancel_delete_account').addEventListener('click', cancel_delete_account);
            </script>
        </div>
    </div>

<?php
require 'footer.php';
endPage();
?>