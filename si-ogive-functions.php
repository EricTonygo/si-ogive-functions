<?php
/*
  Plugin Name: Global Parcel Deal Custom functions
  Description: L'ensemble des fonctions globales du site.
  Version: 0.1
  License: GPL
  Author: Eric TONYE
  Author URI: https://gpdeal.com/
 */

use Themosis\Facades\Action;
use Themosis\Facades\User;
use Themosis\Facades\Section;
use Themosis\Facades\Field;
use Themosis\Facades\Metabox;

add_action('after_setup_theme', 'my_theme_supports');

add_action('init', 'my_custom_init');

function my_awesome_mail_content_type() {
    return "text/html";
}

add_filter("wp_mail_content_type", "my_awesome_mail_content_type");

function wpb_sender_email($original_email_address) {
    if ($original_email_address == 'wordpress@si-ogive.com') {
        return 'contact@si-ogive.com';
    } else {
        return $original_email_address;
    }
}

// Function to change sender name
function wpb_sender_name($original_name_from) {
    if (strtolower($original_name_from) == "wordpress") {
        return get_bloginfo('name');
    } else {
        return $original_name_from;
    }
}

// Hooking up our functions to WordPress filters
add_filter('wp_mail_from', 'wpb_sender_email');
add_filter('wp_mail_from_name', 'wpb_sender_name');

//function woocommerce_support() {
//    add_theme_support('woocommerce');
//}

function childtheme_formats() {
    add_theme_support('post-thumbnails');
    add_theme_support('post-formats', array('aside', 'gallery', 'link'));
}

function my_theme_supports() {
    //woocommerce_support();
    childtheme_formats();
}

//Add additional role customer for every user because we want to use it in woocommerce
//add_action('user_register', 'add_secondary_role', 10, 1);
//
//function add_secondary_role($user_id) {
//
//    $user = get_user_by('id', $user_id);
//    $user->add_role('customer');
//}

function bbx_images($html) {
    $html = preg_replace('/(width|height)="\d*"\s/', "", $html);
    return $html;
}

add_filter('post_thumbnail_html', 'bbx_images', 10);
add_filter('image_send_to_editor', 'bbx_images', 10);
add_filter('wp_get_attachment_link', 'bbx_images', 10);

//Check whether a user has a specifique role
function get_user_roles_by_user_id($user_id) {
    $user = get_userdata($user_id);
    return empty($user) ? array() : $user->roles;
}

function is_user_in_role($user_id, $role) {
    return in_array($role, get_user_roles_by_user_id($user_id));
}

function post_type_area_expertise_init() {
    $labels = array(
        'name' => _x('Areas of expertise', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Area of expertise', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Areas of expertise', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Area of expertise', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'area-expertise', 'si-ogivedomain'),
        'add_new_item' => __('Add New Area of expertise', 'si-ogivedomain'),
        'new_item' => __('New Area of expertise', 'si-ogivedomain'),
        'edit_item' => __('Edit Area of expertise', 'si-ogivedomain'),
        'view_item' => __('View Area of expertise', 'si-ogivedomain'),
        'all_items' => __('All Areas of expertise', 'si-ogivedomain'),
        'search_items' => __('Search Area of expertise', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Area of expertise:', 'si-ogivedomain'),
        'not_found' => __('No area of expertise found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No area of expertise found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the area of expertise.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'area-expertise'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt')
    );

    register_post_type('area-expertise', $args);
}

function post_type_service_init() {
    $labels = array(
        'name' => _x('Services', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Service', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Services', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Service', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'service', 'si-ogivedomain'),
        'add_new_item' => __('Add New Service', 'si-ogivedomain'),
        'new_item' => __('New Service', 'si-ogivedomain'),
        'edit_item' => __('Edit Service', 'si-ogivedomain'),
        'view_item' => __('View Service', 'si-ogivedomain'),
        'all_items' => __('All Services', 'si-ogivedomain'),
        'search_items' => __('Search Services', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Services:', 'si-ogivedomain'),
        'not_found' => __('No services found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No services found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the service.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'service'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('service', $args);
}

function post_type_job_init() {
    $labels = array(
        'name' => _x('Jobs', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Job', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Jobs', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Job', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'job', 'si-ogivedomain'),
        'add_new_item' => __('Add New Job', 'si-ogivedomain'),
        'new_item' => __('New Job', 'si-ogivedomain'),
        'edit_item' => __('Edit Job', 'si-ogivedomain'),
        'view_item' => __('View Job', 'si-ogivedomain'),
        'all_items' => __('All Jobs', 'si-ogivedomain'),
        'search_items' => __('Search Jobs', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Jobs:', 'si-ogivedomain'),
        'not_found' => __('No jobs found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No jobs found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the job.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'job'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('job', $args);
}

function my_custom_init() {
    post_type_area_expertise_init();
    post_type_service_init();
    post_type_job_init();
    add_slider_to_home();
}

function get_published_questions() {
    $posts = query_posts(array(
        'post_type' => 'question',
        'post_per_page' => -1,
        'post_status' => 'publish'
    ));

    $questions = array();
    foreach ($posts as $post) {
        $questions[$post->ID] = $post->post_title;
    }

    wp_reset_query();
    return $questions;
}

//Add user Customs fields for Home page (Slider Images)
function add_slider_to_home() {
    $home = (int) get_option('page_on_front');
    if (themosis_is_post($home)) {
        remove_post_type_support('page', 'editor');
        Metabox::make("Image à la une pour le slider de la page d'accueil", 'page')->set(array(
            Field::infinite('sliders', array(
                Field::media('slider-image')
                    ), array('title' => "Image à la une"))
        ));

        Metabox::make(__("Message de la vision de OGIVE à l'acceuil", 'si-ogivedomain'), 'page')->set(array(
            Field::textarea('our-vision-home', ['title' => 'Notre Vision'])
        ));
    }
}

//Function for leaving a message in contact form on the website
function leave_message() {
    $sender_name = esc_attr(trim($_POST['sender_name']));
    $sender_email = esc_attr(trim($_POST['sender_email']));
    $sender_subject = esc_attr(trim($_POST['sender_subject']));
    $sender_message = esc_attr(trim($_POST['sender_message']));
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $sender_name . ' <' . $sender_email . '>';
    //$headers[] = 'Reply-To:' . Input::get('nom') . ' <' . $data['adress'] . '>';
    $headers[] = 'Bcc:<erictonyelissouck@yahoo.fr>';

    $to = get_bloginfo('admin_email');

    $subject = $sender_subject;

    $body = $sender_message;

    if (wp_mail($to, $subject, $body, $headers)) {
        $json = array("message" => __("Votre message a été envoyé avec succès", 'si-ogivedomain'));
        return wp_send_json_success($json);
    } else {
        $json = array("message" => __("Un erreur s'est produite lors de l'envoi du message. Reessayez à nouveau", 'si-ogivedomain'));
        return wp_send_json_error($json);
    }
}

//Function for getting forgot password of user
function get_password() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $user_email = esc_attr(trim($_POST['email']));
        $test_question_ID = esc_attr(trim($_POST['test_question']));
        $answer_test_question = esc_attr(trim($_POST['answer_test_question']));
        $unique_user_email = get_user_by('email', $user_email);
        if ($unique_user_email != null) {
            $user_id = $unique_user_email->ID;
            $test_question_ID_user = get_user_meta($user_id, 'test-question-ID', true);
            $answer_test_question_user = get_user_meta($user_id, 'answer-test-question', true);
            if ($test_question_ID == $test_question_ID_user && $answer_test_question == $answer_test_question_user) {
                $json = array("message" => "Correct informations");
                return wp_send_json_success($json);
            } else {
                $json = array("message" => "Les informations saisies sont incorrectes (au moins une information est érronée, incomplète ou manquante). Veuillez recommencer votre saisie !!");
                return wp_send_json_error($json);
            }
        } else {
            $json = array("message" => "Utilisateur inexistant");
            return wp_send_json_error($json);
        }
    } else {
        $user_email = esc_attr(trim($_POST['email']));
        $unique_user_email = get_user_by('email', $user_email);
        $plain_text_password = get_user_meta($user_id, 'plain-text-password', true);
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: OGIVE INFOS <infos@gpdeal.com>';
        //$headers[] = 'Reply-To:' . Input::get('nom') . ' <' . $data['adress'] . '>';
        //$headers[] = 'Bcc:<apatchong@gmail.com>';
        $headers[] = 'Bcc:<erictonyelissouck@yahoo.fr>';

        $to = $user_email;

        $subject = "Mot de passe du compte";

        $body = $plain_text_password;
        wp_mail($to, $subject, $body, $headers);
    }
}

//Function of login in si-ogive front-end website
function login() {
    $username = $_POST['_username'];
    $password = $_POST['_password'];
    if (isset($_POST['_remember']) && $_POST['_remember'] == 'true') {
        $remember = true;
    } else {
        $remember = false;
    }

    if (filter_var($username, FILTER_VALIDATE_EMAIL)) { //Invalid Email
        $user = get_user_by('email', $username);
    } else {
        $user = get_user_by('login', $username);
    }

    if ($user && wp_check_password($password, $user->data->user_pass, $user->ID)) {
        $creds = array('user_login' => $user->data->user_login, 'user_password' => $password, 'remember' => $remember);
        $secure_cookie = is_ssl() ? true : false;
        $user = wp_signon($creds, $secure_cookie);
        wp_safe_redirect(get_permalink(get_page_by_path(__('mon-compte', 'gpdealdomain'))));
        exit;
    } else {
        wp_safe_redirect(home_url('/'));
        exit;
    }
}

//Function of registration user account in gpdead front-end website.
function register_user() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if (isset($_POST['testunicity']) && $_POST['testunicity'] = 'yes') {

            $user_login = esc_attr(trim($_POST['username']));
            $user_email = esc_attr(trim($_POST['email']));
            $unique_user_email = get_user_by('email', $user_email);
            $unique_user_login = get_user_by('login', $user_login);
            if ($unique_user_login) {
                $json = array("message" => "Un utilisateur avec ce pseudo existe déjà veuillez le modifier");
                return wp_send_json_error($json);
            } elseif ($unique_user_email) {
                $json = array("message" => "Un utilisateur avec cet email existe déjà veuillez le modifier");
                return wp_send_json_error($json);
            } else {
                $json = array("message" => "Ajout possible");
                return wp_send_json_success($json);
            }
        }
    } else {
        $user_login = esc_attr(trim($_POST['username']));
        $user_pass = esc_attr($_POST['password']);
        $user_email = esc_attr(trim($_POST['email']));
        $first_name = esc_attr(trim($_POST['first_name']));
        $last_name = esc_attr(trim($_POST['last_name']));

        $new_user_data = array(
            'user_login' => $user_login,
            'user_pass' => $user_pass,
            'user_email' => $user_email,
            'role' => "participant",
            'first_name' => $first_name,
            'last_name' => $last_name
        );
        $user_id = wp_insert_user($new_user_data);
        if (!is_wp_error($user_id)) {
            // Set the global user object
            $current_user = get_user_by('id', $user_id);

            // set the WP login cookie
            $secure_cookie = is_ssl() ? true : false;
            wp_set_auth_cookie($user_id, true, $secure_cookie);
            $_SESSION['register_succes'] = "Votre compte a été créé avec succès. ";
            //wp_safe_redirect(get_permalink(get_page_by_path(__('inscription', 'si-ogivedomain'))));
            wp_safe_redirect(home_url('/'));
            exit;
        } else {
            wp_safe_redirect(get_permalink(get_page_by_path(__('inscription', 'si-ogivedomain'))));
            exit;
        }
    }
}

//Return a gender of hold name
function getGenderHoldName($gender) {
    switch ($gender) {
        case 'M':
            return 'Masculin';
            break;
        case 'F':
            return "Feminin";
            break;
        default :
            return '';
    }
}

//Function use to retrieve a list of countries online
function get_country_list() {
    $service_url = 'https://restcountries.eu/rest/v2/all';
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
        $countries = array();
    }
    curl_close($curl);
    $countries = json_decode($curl_response);
    if (isset($countries->response->status) && $countries->response->status == 'ERROR') {
        die('error occured: ' . $countries->response->errormessage);
        $countries = array();
    }
    return $countries;
}

//Function use to retrieve a list of States or Regions of a specific country by countryCode
function getStatesListOfCountry($countryCode = null) {
    $states = array(['code' => 'R1', 'flag' => 'al', 'name' => 'Region 1'], ['code' => 'R2', 'flag' => 'ak', 'name' => 'Region 2'], ['code' => 'R3', 'flag' => 'az', 'name' => 'Region 3'],
        ['code' => 'R4', 'flag' => 'ar', 'name' => 'Region 4'], ['code' => 'R5', 'flag' => 'ca', 'name' => 'Region 5']
    );
    return $states;
}

//Function use to retrieve a list of cities of a specific State
function getCitiesListOfState($stateCode = null) {
    $cities = array(['code' => 'V1', 'flag' => 'al', 'name' => 'Ville 1'], ['code' => 'V2', 'flag' => 'ak', 'name' => 'Ville 2'], ['code' => 'V3', 'flag' => 'az', 'name' => 'Ville 3'],
        ['code' => 'V4', 'flag' => 'ar', 'name' => 'Ville 4'], ['code' => 'V5', 'flag' => 'ca', 'name' => 'Ville 5']
    );
    return $cities;
}

function apply_job($job_id) {
    $firstname = esc_attr(trim($_POST['firstname']));
    $lastname = esc_attr(trim($_POST['lastname']));
    $email = esc_attr(trim($_POST['email']));
    $phone = esc_attr(trim($_POST['phone']));
    $address = esc_attr(trim($_POST['address']));
    $country = esc_attr(trim($_POST['country']));
    $qualifications = esc_attr(trim($_POST['qualifications']));
    $lastdiploma = esc_attr(trim($_POST['lastdiploma']));
    $skills = esc_attr(trim($_POST['skills']));
    $experience = esc_attr(trim($_POST['experience']));
    $attachments = array();

    $job = get_post($job_id);
    if (!empty($_FILES['cv'])) {
        $cv_file = $_FILES['cv'];
        $attachment_id = upload_file($cv_file);
        $url = wp_get_attachment_url($attachment_id);
        $uploads = wp_upload_dir();
        $file_path = str_replace($uploads['baseurl'], $uploads['basedir'], $url);
        $attachments[] = $file_path;
    }

    //$headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $lastname . ' <' . $email . '>';
    $headers[] = 'Bcc:<erictonyelissouck@yahoo.fr>';

    $to = get_bloginfo('admin_email');
    //$to = "erictonyelissouck@yahoo.fr";
    $categories = get_the_category($job_id);
    $job_category = "";
    if (!empty($categories)) {
        $job_category = "(" . esc_html($categories[0]->name) . ")";
    }
    $subject = "Candidature à l'offre d'emploi : " . $job->post_title . " " . $job_category;

    ob_start();
    ?>
    <h2 style="text-decoration: underline">INFORMATIONS DU CANDIDAT</h2>
    <p>
        <strong>Prenom</strong> : <?php echo $firstname; ?><br>
        <strong>Nom</strong> : <?php echo $lastname; ?><br>
        <strong>Numero de Téléphone</strong> : <?php echo $phone; ?><br>
        <strong>Adresse</strong> : <?php echo $address; ?><br>
        <strong>Pays</strong> s: <?php echo $country; ?><br>
        <strong>Dernier diplôme</strong> : <?php echo $lastdiploma; ?><br>
    </p>
    <p>
        <strong>Qualifications</strong> : <?php echo $qualifications; ?><br>
    </p>
    <p>
        <strong>Compétences</strong> : <?php echo $skills; ?><br>
    </p>
    <p>
        <strong>Expérience</strong> : <?php echo $experience; ?><br>
    </p>

    <?php
    $body = ob_get_contents();
    ob_end_clean();
    if (wp_mail($to, $subject, $body, $headers, $attachments)) {
        $_SESSION['success_message'] = "Votre candidature a été envoyée avec succès. \n Vous serez contacter par mail ou appel téléphonique dès l'examination de votre candidature.";
        //$_SESSION['success_message'] = $subject;
    } else {
        //$_SESSION['error_message'] = $subject;
        $_SESSION['error_message'] = "Une erreur s'est produite lors de l'envoi de votre candidature. Verifiez vos informations puis réessayez à nouveau";
    }
}

function upload_file($file = array(), $parent_post_id = 0) {

    require_once( ABSPATH . 'wp-admin/includes/admin.php' );

    $file_return = wp_handle_upload($file, array('test_form' => false));

    if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
        return false;
    } else {

        $filename = $file_return['file'];

        $attachment = array(
            'post_mime_type' => $file_return['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $file_return['url']
        );

        $attachment_id = wp_insert_attachment($attachment, $file_return['url'], $parent_post_id);

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $filename);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        if (0 < intval($attachment_id)) {
            return $attachment_id;
        }
    }
    return false;
}

function ogive_add_reply_topic($topic_id) {
    $reply_message = esc_attr(trim($_POST['reply_message']));
    $reply_id = bbp_insert_reply(
        //reply_data
        array(
            "post_parent" => $topic_id,
            "post_content" => $reply_message
        ),
        //reply_meta
        array(
            "forum_id" => bbp_get_topic_forum_id($topic_id),
            "topic_id" => $topic_id
        )
    );
    if (!$reply_id) {
        $_SESSION['error_message'] = "Une erreur lors de l'ajout de votre reponses. ";
    }
}

function ogive_add_topic_forum($forum_id) {
    $topic_title = esc_attr(trim($_POST['topic_title']));
    $topic_description = esc_attr(trim($_POST['topic_description']));
    $topic_id = bbp_insert_topic(
            //topic_data
            array(
        "post_parent" => $forum_id,
        "post_title" => $topic_title,
        "post_content" => $topic_description
            ),
            //topic_meta_data
            array(
        "forum_id" => $forum_id
            )
    );
    if (!$topic_id) {
        $_SESSION['error_message'] = "Une erreur lors de l'ajout de votre sujet. ";
    }
}
