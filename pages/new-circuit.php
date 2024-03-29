<?php
/** @file /pages/homepage.php
 *
 * @details File to create a new circuit
 *
 * @author SAE S3 NetKart
 */

require ('header.php');
session_start();
require ("./database/database.php");

startPage("Nouveau circuit", [K_STYLE . "main", K_STYLE . "new-circuit"], ['../assets/script/new-circuit']);

$l_db = new database();

$l_db->connection();

$l_nb_max_question = K_MAX_QUESTIONS;
$l_nb_max_question_images = K_MAX_IMAGES;
 if (isset($_GET['error'])){
    $l_code_err = $_GET['error'];?>
    <div class="error">
    <?php
    if ($l_code_err == 1){
        ?>Le nouveau thème existe déjà.
    <?php }
    if ($l_code_err == 2){
        ?>Un circuit existe déjà sous ce nom.
    <?php }
    if ($l_code_err == 3){
        ?>L'enregistrement n'a pas fonctionné.
    <?php }
    if ($l_code_err == 4){
        ?>Les questions se sont enregistrées partiellement (titre, consigne ou réponse). Consulter la page de <a href="edit_circuit.php">modification de circuit</a>.
    <?php }
    if ($l_code_err == 5){
        ?>Les questions se sont enregistrées partiellement (ressources). Consulter la page de <a href="edit_circuit.php">modification de circuit</a>.
    <?php }?>
    </div>
     <?php } ?>
<?php  if (isset($_GET['success'])){?>
    <div class="success">
        Le circuit a bien été créé ! Vous pouvez le modifier <a href="edit_circuit.php" target="_blank">maintenant</a> ou plus tard.
    </div>
<?php }?>
    <form method="post" class="new_circuit_form body" action="new-circuit_post.php" enctype="multipart/form-data">
        <input type="hidden" id="image_limit" value="<?php echo $l_nb_max_question_images ?>">
        <div class="left">
            <label>Nom du circuit</label>
            <input name="circuit_name" type="text" placeholder="Nom du circuit (Limite : 100 charactères)" maxlength="100" required>
            <label>Thème du circuit</label>
            <select name="circuit_theme" required>
                <option value="">Choisir un thème</option>
                <?php
                $l_themes = $l_db->get_all_themes();
                foreach ($l_themes as $l_theme){?>
                    <label for="theme">
                <option value="<?php echo $l_theme["id_theme"]; ?>"><?php echo $l_theme["nom_theme"];
                } ?>
                <option value="5">Autre thème</option>
            </select>

            <div class="other_theme">
                <label class="hidden">Si autre, précisez</label>
                <input class="hidden" name="other_theme" type="text" placeholder="Nom du theme" maxlength="100" disabled>
                <input class="hidden" name="other_theme_desc" type="text" placeholder="Description du theme (minimum : 20 caractères)" min="20" maxlength="200" disabled>
            </div>
            <label>Nombre de points que rapporte le circuit (entre 10 et 100)</label>
            <input name="circuit_points" type="number" min="10" max="100" required>
            <label>Choisir l'image du circuit</label>
            <select name="circuit_image" id="circuit_image" required>
                <option value="">Choisir un circuit</option>
                <?php
                $l_images = $l_db->get_all_images_circuit();
                foreach ($l_images as $l_image){?>
                <option value="<?php echo $l_image['id_circuitimage']; ?>"><?php echo $l_image['image']; ?>
                    <?php
                    } ?>
            </select>
            <div class="images">
                <?php
                $l_nb_img = 1;
                foreach ($l_images as $l_image){?>
                    <div>
                        <img class="original_img" src="<?php echo K_IMAGE; ?>circuit<?php echo $l_nb_img; ?>.webp" alt="<?php echo $l_image['image']; ?> - originale">
                        <img class="hover_img" src="<?php echo K_IMAGE; ?>circuit<?php echo $l_nb_img; ?>.webp" alt="<?php echo $l_image['image']; ?>">
                        <span><?php echo $l_image['image']; ?></span>
                    </div>
                    <?php
                    ++$l_nb_img;
                } ?>
            </div>
        </div>
        <div class="right">
            <h1>Les questions</h1>

            <?php
            for ($l_nb_question = 1; $l_nb_question<=$l_nb_max_question; ++$l_nb_question){
                ?>
                <label class="question"><span>Question n°<?php echo $l_nb_question;?></span><span class="arrow close"></span></label>
                <div class="hidden question_content">
                    <input name="question[<?php echo $l_nb_question;?>][question]" type="text" placeholder="Intitulé de la question" maxlength="200" required>
                    <label>Consigne</label>
                    <textarea name="question[<?php echo $l_nb_question;?>][consigne]" placeholder="Consigne de la question" aria-atomic="true" required></textarea>
                    <label>Réponse</label>
                    <input name="question[<?php echo $l_nb_question;?>][reponse]" type="text" placeholder="Réponse de la question" maxlength="200" required>
                    <h1>Ressources</h1>
                    <h2>Il y a une limite de <?php echo K_MAX_IMAGES;?> images par question. Il faut les envoyer d'une seule traite.</h2>
                    <div class="medias">

                        <div class="left">
                            <label class="file_label" for="question_files_<?php echo $l_nb_question;?>">Images</label>
                            <input type="hidden" value="<?php echo $l_nb_question-1;?>">

                            <input class="img_store" id="question_files_<?php echo $l_nb_question;?>" name="question_files_<?php echo $l_nb_question;?>[]" type="file" accept="image/*" multiple>
                            <?php
                            for ($l_nb_question_image = 0; $l_nb_question_image<$l_nb_max_question_images; ++$l_nb_question_image){?>
                                <div>
                                    <img class="question_img original_img" alt="question_image_<?php echo $l_nb_question;?>_<?php echo $l_nb_question_image+1;?>" src="no_src">
                                    <img class="question_img hover_img" alt="question_image_<?php echo $l_nb_question;?>_<?php echo $l_nb_question_image+1;?>" src="no_src">
                                    <span class="question_img remove hidden">x</span>
                                    <input type="hidden" value="<?php echo $l_nb_question_image;?>">
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="right">
                            <?php
                            for ($l_nb_question_link = 1; $l_nb_question_link<=$l_nb_max_question_images; ++$l_nb_question_link){?>
                                <input class="question_link" type="text" name="question[<?php echo $l_nb_question;?>][lien][<?php echo $l_nb_question_link;?>]"
                                       placeholder="Lien" maxlength="255">
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <input name="new_crct_form" type="submit" value="Créer le circuit">
        </div>

    </form>

<?php
require 'footer.php';
endPage();
?>