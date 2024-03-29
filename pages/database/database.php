<?php

/** @file /pages/database/database.php
 *
 * @details File to manage database
 *
 * @author SAE S3 NetKart
 */

/**
 * class to manage database
 */

class database
{
    protected $l_servername;
    protected $l_username;
    protected $l_password;
    protected $l_dbname;

    protected $l_conn;

    /**
     * constructor of database class, initialise variables to connect to database
     */
    function __construct($A_SERVERNAME = "",
                         $A_USERNAME = "",
                         $A_PASSWORD = "",
                         $A_DBNAME = ""
                        )
    {
        $this->l_servername = $A_SERVERNAME;
        $this->l_username = $A_USERNAME;
        $this->l_password = $A_PASSWORD;
        $this->l_dbname = $A_DBNAME;
    }

    /**
     * @brief this function initialises the connection with database
     */
    function connection()
    {
        $this->l_conn = new mysqli($this->l_servername, $this->l_username, $this->l_password, $this->l_dbname);
        // Check connection
        if ($this->l_conn->connect_error) {
            die("Connection failed: " . $this->l_conn->connect_error);
            exit();
        }
    }
    /**
     * @brief this function remove apostrophe from a query
     *
     * @param $A_QUERY (String) : the query
     * @return (String) : the query without apostrophe
     */
    function remove_apostrophe($A_QUERY)
    {   
        $pattern = '/[a-zA-Z0-9]\'[a-zA-Z0-9]/';
        $pattern2 = '/\'/';
        $replacement = " ";
        preg_match_all($pattern, $A_QUERY, $matches);
        foreach ($matches[0] as $match) {
            $final = preg_replace($pattern2, $replacement, $match);
            $A_QUERY = str_replace($match,$final,$A_QUERY);
        }
        return $A_QUERY;
    }

    /**
     * @brief this function executes a sql query and handle errors
     *
     * @param $A_QUERY (String) the sql query that will be run
     * @param $A_IS_INSERT (Boolean) true if query is an insert
     */
    function f_query($A_QUERY, $A_IS_INSERT=false)
    {
        $l_QUERY = self::remove_apostrophe($A_QUERY);
        if (!$this->l_conn->query($l_QUERY)) {
            echo("Error description: " . $this->l_conn->error);
            return "Error";
        }
        if($A_IS_INSERT){
            return "Success";
        }
        return $this->l_conn->query($l_QUERY)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @biref this function inserts the given data into the table
     *
     * @param $A_TABLE (String) the table to insert data
     * @param $A_KEYS (String) column where data will be added
     * @param $A_VALUES (String) data to insert
     *
     * @return (Boolean) : True if insert successful, False if an error occured
     */
    function f_insert_strings($A_TABLE, $A_KEYS, $A_VALUES)
    {
        $l_sql = "INSERT INTO %s (" . implode(",", $A_KEYS) . ") VALUES ('" . implode("','", $A_VALUES) . "')";

        $l_result = $this->l_conn->query(sprintf($l_sql,
            mysqli_real_escape_string($this->l_conn, $A_TABLE)));
        echo $l_result;
        if (!$l_result) {
            echo("Error description: " . $this->l_conn->error);
            return False;
        }
        return True;
    }

    /**
     * @brief this function will delete some rows from database
     *
     * @param $A_TABLE (String) the table of the rows to delete
     * @param $A_WHERE (String) [** Optional **] the rows that will fit this condition will be deleted
     *
     * @return (Boolean) : True if delete successful, False if an error occured
     */
    function f_delete($A_TABLE, $A_WHERE = "")
    {
        $l_sql = "DELETE FROM " . $A_TABLE . ($A_WHERE != "" ? " WHERE " . $A_WHERE : "");
        if (!$this->l_conn->query($l_sql)) {
            echo("Error description: " . $this->l_conn->error);
            return False;
        }
        return True;
    }

    /**
     * @brief this function closes the connection with the database
     */
    function close()
    {
        $this->l_conn->close();
    }

    /**
     * @brief this function will return the password of a given user
     *
     * @param $A_USERNAME (String) the username to get the password from
     *
     * @return (String) : Password of given user or empty string if an error occured
     */
    function get_password($A_USERNAME)
    {
        $l_query = "SELECT mot_de_passe FROM Joueur WHERE pseudo='" . $A_USERNAME . "';";

        $l_result = $this->l_conn->query($l_query);

        $l_fetch = $l_result->fetch_all(MYSQLI_ASSOC);
        if (!$l_result) {
            echo("Error description: " . $this->l_conn->error);
            return "";
        }
        if (sizeof($l_fetch)==0){
            return '';
        }
        return $l_fetch[0]["mot_de_passe"];
    }

    /**
     * @brief this function will update the password from a given user by replacing it with a given new password
     *
     * @param $A_USERNAME (String) user that password will be updated
     * @param $A_NEW_PASSWORD (String) new password to update in database
     *
     * @return (Boolean) : True if update successful, False if an error occured
     */
    function update_password($A_USERNAME, $A_NEW_PASSWORD)
    {
        $l_sql = "UPDATE Joueur SET mot_de_passe = '" . $A_NEW_PASSWORD . "' WHERE id_joueur = '" . $A_USERNAME."'";
        if (!$this->l_conn->query($l_sql)) {
            echo("Error description: " . $this->l_conn->error);
            return False;
        }
        return True;
    }

    /**
     * @brief this function will check if a given element from a given column exists or not
     *
     * @param $A_TABLE (String) the table where to search for the element
     * @param $A_COLUMN (String) the column of the element
     * @param $A_ELEMENT (String) the value of the column to search
     *
     * @return (Boolean) : True if element already in table, False other way
     */
    function check_if_element_already_used($A_TABLE, $A_COLUMN, $A_ELEMENT)
    {
        $typed_param = "%s";
        if(gettype($A_ELEMENT)=="string"){
            $typed_param = "'%s'";
        }
        $l_query = "SELECT * FROM %s  WHERE %s =  ".$typed_param;

        $l_result = $this->l_conn->query(sprintf($l_query,
                            mysqli_real_escape_string($this->l_conn, $A_TABLE),
                            mysqli_real_escape_string($this->l_conn, $A_COLUMN),
                            mysqli_real_escape_string($this->l_conn, $A_ELEMENT)));

        if (!$l_result) {
            echo("Error description: " . $this->l_conn->error);
            exit();
        }
        return $l_result->num_rows > 0;
    }

    /**
     * @brief this function return the circuits created by an user
     *
     * @param $A_PLAYER_ID (Integer) : id of the player to get circuits from
     *
     * @return (Array of Integers) : the id of all the circuits created by a given user
     */
    function get_circuit_created_by_user($A_PLAYER_ID)
    {
        $l_sql = "SELECT id_circuit FROM Circuit WHERE id_joueur=" . $A_PLAYER_ID;
        $l_result = $this->l_conn->query($l_sql);

        if (!$l_result) {
            echo("Error description: " . $this->l_conn->error);
            return [];
        }
        return $l_result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @brief this function return the circuits created by an user
     *
     * @param $A_CIRCUIT_ID (Integer) : id of the circuit to get informations from
     *
     * @return (Array) : informations as id, name, points and image of circuit
     */
    function get_circuit_information($A_CIRCUIT_ID)
    {
        $l_sql = "SELECT id_circuit, nom_circuit, points, id_circuitimage, id_theme FROM Circuit WHERE id_circuit=" . $A_CIRCUIT_ID;
        $l_result = $this->l_conn->query($l_sql);

        if (!$l_result) {
            echo("Error description: " . $this->l_conn->error);
            return [];
        }
        return $l_result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @biref Get all information of all the circuit in the database
     * @return (Array) : array that contains all circuits information
     */
    function get_all_circuit(){
        $l_result = self::f_query(
            "SELECT id_circuit, nom_circuit, points, image, id_theme 
                                FROM Circuit c, Circuit_Image i
                                WHERE i.id_circuitimage = c.id_circuitimage");
        if($l_result=="Error"){
            return [];
        }
        return $l_result;
    }

    /**
     * @brief this function return the image
     *
     * @param $A_IMAGE_ID (String) : id of the image
     *
     * @return (String) : name of the image (path)
     */
    function get_image_circuit($A_IMAGE_ID)
    {
        return self::f_query("SELECT image  FROM Circuit_Image WHERE id_circuitimage=" . $A_IMAGE_ID);
    }

    /**
     * @brief this function return each question of a circuit
     *
     * @param $A_CIRCUIT_ID (String) : id of the circuit
     *
     * @return (Array) : all the circuit questions information
     */
    function get_question_circuit($A_CIRCUIT_ID)
    {
        return self::f_query("SELECT id_question, consigne, question, reponse  FROM Question WHERE id_circuit=" . $A_CIRCUIT_ID);
    }

    /**
     * @brief this function return each path of image for a circuit
     *
     * @param $A_CIRCUIT_ID (String) : id of the circuit
     *
     * @return (String) : path of each image
     */
    function get_image_question($A_QUESTION_ID)
    {
        return self::f_query("SELECT image_question FROM Question_Image WHERE id_question=" . $A_QUESTION_ID);
    }

    /**
     * @brief this function return each url of question for a circuit
     *
     * @param $A_QUESTION_ID (String) : id of the question
     *
     * @return (String) : url of each question
     */
    function get_url_question($A_QUESTION_ID)
    {
        return self::f_query("SELECT lien FROM Question_Lien WHERE id_question=" . $A_QUESTION_ID);
    }

    /**
     * @brief this function delete a Circuit with a given ID and all the questions of this circuit
     *
     * @param $A_CIRCUIT_ID (String) : ID of the circuit to delete
     *
     * @return (Boolean) : True if delete of Circuit successful, False if an error occured
     */
    function delete_circuit_with_id($A_CIRCUIT_ID)
    {

        $l_all_questions = self::f_query("SELECT id_question FROM Question WHERE id_circuit=" . $A_CIRCUIT_ID);
        foreach ($l_all_questions as $l_questions) {
            if ($l_questions == NULL) {
                break;
            }
            foreach ($l_questions as $l_question) {

                $l_all_images = self::f_query("SELECT id_questionimage FROM Question_Image WHERE id_question=" . $l_question);
                if ($l_all_images != NULL) {
                    foreach ($l_all_images as $l_images) {
                        foreach ($l_images as $l_image) {
                            self::delete_image($l_image);
                            self::f_delete("Question_Image", "id_questionimage=" . $l_image);
                        }
                    }
                }

                $l_all_links = self::f_query("SELECT id_questionlien FROM Question_Lien WHERE id_question=" . $l_question);
                if ($l_all_links != NULL) {
                    foreach ($l_all_links as $l_links) {
                        foreach ($l_links as $l_link) {
                            self::f_delete("Question_Lien", "id_questionlien=" . $l_link);
                        }
                    }
                }
                // Delete question
                self::f_delete("Question", "id_question=" . $l_question);

            }
        }
        // Delete circuits in Statistiques table
        self::f_delete("Statistiques","id_circuitStatistiques=".$A_CIRCUIT_ID);
        // Delete circuit
        return self::f_delete("Circuit","id_circuit=".$A_CIRCUIT_ID);
    }

    function delete_image($l_image_id) {
        $target_dir = "../assets/image/upload/";
        $l_image = self::f_query("SELECT image_question FROM Question_Image WHERE id_questionimage =".$l_image_id)[0]["image_question"];
        echo $target_dir.$l_image;
        unlink($target_dir.$l_image);
    }

    /**
     * @brief this function insert a new theme into database
     *
     * @param $A_THEME_NAME (String) : name of the new theme
     * @param $A_THEME_DESC (String) : description of the new theme
     *
     * @return (Integer) : the id of the theme created or -1 if an error occurred
     */
    function insert_theme($A_THEME_NAME, $A_THEME_DESC){
        $l_is_insert_ok = self::f_insert_strings("Theme",["nom_theme", "description"],  [$A_THEME_NAME, $A_THEME_DESC]);
        if($l_is_insert_ok){
            $l_theme_id = self::f_query("SELECT id_theme FROM Theme WHERE nom_theme ='".$A_THEME_NAME."'");
            return $l_theme_id[0]["id_theme"];
        }
        return -1;
    }

    /**
     * @brief this function insert a new circuit into database
     *
     * @param $A_NOM_CIRCUIT (String) : name of the new circuit
     * @param $A_POINTS (Integer) : number of points the user will get after finishing circuit
     * @param $A_THEME (Integer) : id of the theme the circuit belong to
     * @param $A_JOUEUR (Integer) : id of the player who created the circuit
     * @param $A_CIRCUIT_IMAGE (Integer) : id of the image of the circuit
     *
     * @return (Integer) : the id of the circuit created or -1 if an error occurred
     */
    function insert_circuit($A_NOM_CIRCUIT, $A_POINTS, $A_THEME, $A_JOUEUR, $A_CIRCUIT_IMAGE){
        $l_is_insert_ok = self::f_query("INSERT INTO Circuit (nom_circuit, points, id_theme, id_joueur, id_circuitimage) VALUES ('$A_NOM_CIRCUIT', $A_POINTS, $A_THEME, $A_JOUEUR, $A_CIRCUIT_IMAGE)",true);
        if ($l_is_insert_ok=="Success"){
            $l_circuit_id = self::f_query("SELECT id_circuit FROM Circuit WHERE nom_circuit ='".$A_NOM_CIRCUIT."'");
            return $l_circuit_id[0]["id_circuit"];
        }
        return -1;
    }

    /**
     * @param $A_ID_CIRCUIT : id of the circuit to update
     * @param $A_NOM_CIRCUIT (String) : name of the updated circuit
     * @param $A_POINTS (Integer) : number of points the user will get after finishing circuit
     * @param $A_THEME (Integer) : id of the theme the circuit belong to
     * @param $A_JOUEUR (Integer) : id of the player who created the circuit
     * @param $A_CIRCUIT_IMAGE (Integer) : id of the image of the circuit
     *
     * @return (boolean) : True if update successful, False otherwise
     */
    function update_circuit($A_ID_CIRCUIT, $A_NOM_CIRCUIT, $A_POINTS, $A_THEME, $A_JOUEUR, $A_CIRCUIT_IMAGE){
        $l_is_update_ok = self::f_query("UPDATE Circuit SET nom_circuit='".$A_NOM_CIRCUIT."', points=".$A_POINTS.", id_theme=".$A_THEME.", id_joueur=".$A_JOUEUR.", id_circuitimage=".$A_CIRCUIT_IMAGE." WHERE id_circuit=".$A_ID_CIRCUIT,true);
        return $l_is_update_ok=="Success";
    }

    /**
     * @brief this function insert a question from a circuit into database
     *
     * @param $A_CONSIGNE (String) : detailed question
     * @param $A_QUESTION (String) : question
     * @param $A_REPONSE (String) : answer of the question
     * @param $A_CIRCUIT (Integer) : id of the circuit the question belongs to
     *
     * @return (Integer) : the id of the question created or -1 if an error occurred
     */
    function insert_question($A_CONSIGNE, $A_QUESTION, $A_REPONSE, $A_CIRCUIT){
        $l_is_insert_ok = self::f_query("INSERT INTO Question (consigne, question, reponse, id_circuit) VALUES ('".$A_CONSIGNE."','".$A_QUESTION."', '".$A_REPONSE."',".$A_CIRCUIT.")",true);
        if ($l_is_insert_ok=="Success"){
            $l_question_id = self::f_query("SELECT id_question FROM Question WHERE question ='".$A_QUESTION."' AND id_circuit=".$A_CIRCUIT);
            return $l_question_id[0]["id_question"];
        }
        return -1;
    }

    /**
     * @param $A_ID_QUESTION (Integer) : id of the question
     * @param $A_CONSIGNE (String) : detailed question
     * @param $A_QUESTION (String) : question
     * @param $A_REPONSE (String) : answer of the question
     *
     * @return (boolean) : True if update successful, False otherwise
     */
    function update_question($A_ID_QUESTION, $A_QUESTION, $A_CONSIGNE, $A_REPONSE){
        $l_is_update_ok = self::f_query("UPDATE Question SET question='".$A_QUESTION."', consigne='".$A_CONSIGNE."', reponse='".$A_REPONSE."' WHERE id_question=".$A_ID_QUESTION, true);
        return $l_is_update_ok=="Success";
    }

    /**
     * @brief this function insert the links given with a specified question
     *
     * @param $A_LINK (String) : link to help answer the question
     * @param $A_QUESTION (Integer) : id of the question the link refers to
     *
     * @return (Boolean) : True if insert successful, False otherwise
     */
    function insert_links($A_LINK, $A_QUESTION){
        $l_is_insert_ok = self::f_query("INSERT INTO Question_Lien (lien, id_question) VALUES ('".$A_LINK."', ".$A_QUESTION.")",true);
        return $l_is_insert_ok=="Success";
    }

    /**
     * @brief this function insert the image given with a specified question
     *
     * @param $A_IMAGE (String) : name of the image uploaded
     * @param $A_QUESTION (Integer) : id of the question the link refers to
     *
     * @return (Boolean) : True if insert successful, False otherwise
     */
    function insert_images_question($A_IMAGE, $A_QUESTION){
        $l_is_insert_ok = self::f_query("INSERT INTO Question_Image (image_question, id_question) VALUES ('".$A_IMAGE."', ".$A_QUESTION.")",true);
        return $l_is_insert_ok=="Success";
    }

    /**
     * @brief this function returns all the themes in database
     *
     * @return (Array) : array that contains the id and name of all themes
     */
    function get_all_themes(){
        return self::f_query("SELECT id_theme, nom_theme FROM Theme");
    }

    /**
     * @brief this function returns all the images of circuits in database
     *
     * @return (Array) : id and name of all the possible images for a circuit
     */
    function get_all_images_circuit(){
        return self::f_query("SELECT id_circuitimage,image FROM Circuit_Image");
    }

    /**
     * @brief this function insert a new multiplayer session in database
     *
     * @param $A_NOM (String) : link to help answer the question
     * @param $A_CODE (String) : code to join the session
     * @param $A_DEBUT (String) : time the session started
     * @param $A_DUREE (String) : time the session will last
     * @param $A_JOUEUR (Integer) : id of the user who created the session
     *
     * @return (Integer) : id of the created session
     */
    function insert_session($A_NOM, $A_CODE, $A_DEBUT, $A_DUREE, $A_JOUEUR, $A_THEME){
        $l_is_insert_ok = self::f_query("INSERT INTO Groupe (nom_groupe, code, debut, duree, id_joueur, id_theme) VALUES ('".$A_NOM."', '".$A_CODE."', '".$A_DEBUT."', '".$A_DUREE."',".$A_JOUEUR.",".$A_THEME.")",true);
        if ($l_is_insert_ok=="Success"){
            $l_question_id = self::f_query("SELECT id_groupe FROM Groupe WHERE nom_groupe ='".$A_NOM."' AND code='".$A_CODE."'");
            return $l_question_id[0]["id_groupe"];
        }
        return -1;
    }

    /**
     * Return a boolean to know if the user has a session.
     *
     * @param $A_ID_JOUEUR (Int) User id.
     * @return (Boolean) If the user has a session.
     */
    function verifyPlayerSession($A_ID_JOUEUR){
        return self::f_query("SELECT count(*) FROM Groupe WHERE id_joueur ='".$A_ID_JOUEUR."'")[0]['count(*)'];
    }

    /**
     * Return the session data.
     *
     * @param $A_ID_JOUEUR (Int) User id.
     * @return (Array) Session data
     */
    function get_session_by_host($A_ID_JOUEUR)
    {
        if (self::f_query("SELECT * FROM Groupe a, Groupe_Joueur b WHERE a.id_joueur =" . $A_ID_JOUEUR . " AND a.id_groupe = b.id_groupe")) {
            return self::f_query("SELECT * FROM Groupe a, Groupe_Joueur b WHERE a.id_joueur =" . $A_ID_JOUEUR . " AND a.id_groupe = b.id_groupe ORDER BY b.score DESC");
        }
        if (self::f_query("SELECT * FROM Groupe WHERE id_joueur =" . $A_ID_JOUEUR)) {
            return self::f_query("SELECT * FROM Groupe WHERE id_joueur =" . $A_ID_JOUEUR)[0];
        }
        return [];
    }

     /** @brief : this function will check if player has already won the circuit
     *
     * @param $A_ID_JOUEUR (Integer) : id of the player who won the game
     * @param $A_ID_CIRCUIT (Integer) : id of the circuit won by player
     *
     * @return (boolean) : True if victory already in database, False otherwise
     */
    function check_if_victory_already($A_ID_JOUEUR, $A_ID_CIRCUIT){
        $l_victory = self::f_query("SELECT id_joueurStatistiques, id_circuitStatistiques FROM Statistiques WHERE id_joueurStatistiques=".$A_ID_JOUEUR." AND id_circuitStatistiques=".$A_ID_CIRCUIT);
        return sizeof($l_victory) == 0;
    }

    /**
     * @brief : this function insert a victory into database for game-solo
     *
     * @param $A_ID_JOUEUR (Integer) : id of the player who won the game
     * @param $A_ID_CIRCUIT (Integer) : id of the circuit won by player
     *
     * @return (boolean) : True if insert successful, False otherwise
     */
    function insert_victory($A_ID_JOUEUR, $A_ID_CIRCUIT){
        $l_is_insert_ok = self::f_query("INSERT INTO Statistiques (id_joueurStatistiques, id_circuitStatistiques) VALUES (".$A_ID_JOUEUR.",".$A_ID_CIRCUIT.")", true);
        return $l_is_insert_ok=="Success";
    }

    /**
     * @brief : this function delete a multiplayer session with all the players
     *
     * @param $A_ID_SESSION (Integer) : id of the session to delete
     *
     * @return (boolean) : True if delete successful, False otherwise
     */
    function delete_session_multi($A_ID_SESSION){
        $l_is_delete_ok = self::f_delete("Groupe_Joueur","id_groupe=".$A_ID_SESSION);
        if(!$l_is_delete_ok){
            return false;
        }
        $l_is_delete_ok = self::f_delete("Groupe","id_groupe=".$A_ID_SESSION);
        return $l_is_delete_ok;
    }

    /**
     * Get the sum of the circuit score obtained by user by they id
     * @param $A_ID_JOUEUR (int) : user identifiant
     * @return int|mixed|string
     */
    function get_score_player_id($A_ID_JOUEUR){
        $l_score = self::f_query("SELECT SUM(points) FROM Circuit, Statistiques WHERE id_circuit = id_circuitStatistiques AND id_joueurStatistiques=".$A_ID_JOUEUR);
        if($l_score=="Error"){
            return -1;
        }
        return $l_score[0]["SUM(points)"];
    }

    /**
     * Adding player to the player list of a multiplayer session
     *
     * @param $A_PLAYER (String) : player nickname
     * @param $A_SESSION_CODE (String) : session to join code
     * @return bool|int
     */
    function insert_player_to_multiplayer_session($A_PLAYER, $A_SESSION_CODE){
        if ($l_id_groupe = self::f_query("SELECT id_groupe FROM Groupe WHERE code='".$A_SESSION_CODE."'")) {
            if (sizeof($l_id_groupe) == 1) {
                $l_is_insert_ok = self::f_query("INSERT INTO Groupe_Joueur (pseudo_groupe,score,id_groupe) VALUES ('" . $A_PLAYER . "',0," . $l_id_groupe[0]["id_groupe"] . ")", true);
                var_dump($l_is_insert_ok);

                return $l_is_insert_ok == "Success";
            }
            return 0;
        }
        return 0;
    }

    /**
     * Get a player session id from his nickname and session code
     *
     * @param $A_PLAYER (String) User nickname
     * @param $A_SESSION_CODE (String) Session code
     * @return int|mixed|string
     */
    function select_player_session_id($A_PLAYER, $A_SESSION_CODE){
        if ($l_id_groupe = self::f_query("SELECT id_groupe FROM Groupe WHERE code='".$A_SESSION_CODE."'")) {
            if (sizeof($l_id_groupe) == 1) {
                $l_player_id = self::f_query("SELECT id_groupejoueur FROM Groupe_Joueur a, Groupe b WHERE pseudo_groupe='".$A_PLAYER."' AND a.id_groupe = b.id_groupe AND code='".$A_SESSION_CODE."'");
                return $l_player_id[0]['id_groupejoueur'];
            }
            return -1;
        }
        return -1;
    }

    /**
     * Get all session information from the session code
     *
     * @param $A_SESSION_CODE (String) Session code
     * @return string
     */
    function get_session_by_code($A_SESSION_CODE){
        return self::f_query("SELECT * FROM Groupe a, Groupe_Joueur b WHERE a.code ='" . $A_SESSION_CODE . "' AND a.id_groupe = b.id_groupe ORDER BY b.score DESC");;
    }

    /**
     *
     *
     * @param $A_THEME_ID
     * @return string
     */
    function get_circuits_by_theme($A_THEME_ID){
        return self::f_query("SELECT id_circuit FROM Circuit WHERE id_theme =" . $A_THEME_ID);
    }

    /**
     * Get user name from user id
     *
     * @param $A_ID_PLAYER (Integer) : id of the player to get username
     * @return int|mixed|string
     */
    function get_username_from_id($A_ID_PLAYER){
        $l_username = self::f_query("SELECT pseudo FROM Joueur WHERE id_joueur='".$A_ID_PLAYER."'");
        if($l_username=="Error"){
            return -1;
        }
        return $l_username[0]["pseudo"];
    }

    /**
     * Edit session player score when they win a circuit
     *
     * @param $A_ID_PLAYER
     * @param $A_INDEX_CIRCUIT
     * @return bool
     */
    function set_session_player_score($A_ID_PLAYER, $A_INDEX_CIRCUIT){
        $l_circuit = (int) self::get_circuits_by_theme(self::f_query("SELECT b.id_theme FROM Groupe_Joueur a, Groupe b WHERE a.id_groupejoueur=".$A_ID_PLAYER." AND a.id_groupe=b.id_groupe")[0]['id_theme'])[$A_INDEX_CIRCUIT]['id_circuit'];
        $l_old_score = (int) self::f_query("SELECT score FROM Groupe_Joueur WHERE id_groupejoueur=".$A_ID_PLAYER)[0]['score'];
        $l_point = (int) self::f_query("SELECT points FROM Circuit WHERE id_circuit=".$l_circuit)[0]['points'];
        $l_is_update_ok = self::f_query("UPDATE Groupe_Joueur SET score=".($l_old_score+$l_point)." WHERE id_groupejoueur=".$A_ID_PLAYER,true);
        return $l_is_update_ok === "Success";
    }
}
//TODO : voir pour de la composition

//TODO : functions => get_circuit_created_by_user / get_circuit_information / insert_circuit / delete_circuit /  update ???

// TODO : voir pr utiliser le f_query partout  ??
