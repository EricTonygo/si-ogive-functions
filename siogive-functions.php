<?php
/*
  Plugin Name: Global Parcel Deal Custom functions
  Description: L'ensemble des fonctions globales du site.
  Version: 0.1
  License: GPL
  Author: Eric TONYE
  Author URI: https://gpdeal.com/
 */

//use Themosis\Facades\Action;
//use Themosis\Facades\User;
//use Themosis\Facades\Section;
//use Themosis\Facades\Field;
//use Themosis\Facades\Metabox;

add_action('after_setup_theme', 'my_theme_supports');

add_action('init', 'my_custom_init');

function my_awesome_mail_content_type() {
    return "text/html";
}

add_filter("wp_mail_content_type", "my_awesome_mail_content_type");

function wpb_sender_email($original_email_address) {
    if ($original_email_address == 'wordpress@siogive.com') {
        return 'contact@siogive.com';
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

//Add additional role bb_participant for every user because we use it in bb_press
add_action('user_register', 'add_secondary_role', 10, 1);

function add_secondary_role($user_id) {

    $user = get_user_by('id', $user_id);
    $user->add_role('bbp_participant');
}


//Fonction to override a default new user notification message
function siogive_new_user_notification($user_id) {
    $user = get_userdata($user_id);

    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);
    $last_name = stripslashes($user->last_name);

    $subject = "Bienvenue à " . get_option('blogname') . " " . $last_name . " !";

    ob_start();
    ?>
    <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Bienvenue spécial à vous <?php echo $last_name; ?>. Merci d'avoir rejoint <?php echo get_option('blogname'); ?> </p>
    <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Nous vous communiquons ici vos identifiants pour vous connecter à notre site web: <a href="<?php echo home_url('/') ?>">www.siogive.com</a>.</p>
    <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">
    <ul style="font-style: italic; font-size: 12.8px; list-style-type: none;">
        <li>- Login : <?php echo $user_email; ?> ou <?php echo $user_login ?></li>
        <li>- Mot de passe : <?php echo get_user_meta($user_id, 'plain-text-password', true); ?></li>
    </ul>
    </p>
    <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Ces identifiants vous permettrons d'avoir accès aux détails des appels d'offres qui vous seront envoyés par SMS et mail puis publiés sur notre site internet.</p>
    <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Vous pourriez aussi par la même occasion prendre part aux différents forums de discussion sur les marchés publics au Cameroun disponibles sur notre site internet et accessibles à partir de ce lien <a href="<?php echo get_permalink(get_page_by_path(__('forums', 'siogivedomain'))) ?>">Nos Forums</a> .</p>
    <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Pour des raisons de sécurité, nous vous conseillons de garder soignesement vos identifiants.</p>
        <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Vous remerciant de votre confiance, nous restons à votre disposition pour toute information complémentaire.</p>
        <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">Cordialement,</p>
        <p style="font-style: italic; font-size: 12.8px; margin-bottom: 1em;">L'équipe <?php echo get_option('blogname'); ?></p>
        <p><a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri() ?>/assets/img/large_logo_2.PNG" style="width: 450px;"></a></p>
        <p style=" font-size: 12px; margin-bottom: 1em; color: grey;">Siège social: Yaoundé, BP: 5253, Situé à la Nouvelle route Bastos face Ariane TV Rue N°1839</p>
        <p style=" font-size: 12px; margin-bottom: 1em; color: grey;">Email: contact@siogive.com,  Tel: +237243804388/+237243803895</p>
    <?php
    $message = ob_get_contents();
    ob_end_clean();
    return array("email"=> $user_email, "subject"=> $subject , "message"=>$message);
}


/* ----------------------------------------------------------------------- */
// Filter search results
/* ----------------------------------------------------------------------- */
add_filter('pre_get_posts', function($query) {
    if ($query->is_search && !is_admin()) {
        $query->set('post_type', array('post', 'job', 'service', 'area-expertise', 'forum', 'topic'));
    }

    return $query;
});

function childtheme_formats() {
    add_theme_support('post-thumbnails');
    add_theme_support('post-formats', array('aside', 'gallery', 'link'));
}

function my_theme_supports() {
    childtheme_formats();
}

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

function post_type_domain_init() {
    $labels = array(
        'name' => _x('Domains', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Domain', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Domains', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Domain', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'domain', 'si-ogivedomain'),
        'add_new_item' => __('Add New Domain', 'si-ogivedomain'),
        'new_item' => __('New Domain', 'si-ogivedomain'),
        'edit_item' => __('Edit Domain', 'si-ogivedomain'),
        'view_item' => __('View Domain', 'si-ogivedomain'),
        'all_items' => __('All Domains', 'si-ogivedomain'),
        'search_items' => __('Search Domains', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Domains:', 'si-ogivedomain'),
        'not_found' => __('No domains found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No domains found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the domain.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'domain'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('domain', $args);
}

function post_type_subdomain_init() {
    $labels = array(
        'name' => _x('Sub domains', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Sub domain', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Sub domains', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Sub domain', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'sub domain', 'si-ogivedomain'),
        'add_new_item' => __('Add New Sub domain', 'si-ogivedomain'),
        'new_item' => __('New Sub domain', 'si-ogivedomain'),
        'edit_item' => __('Edit Sub domain', 'si-ogivedomain'),
        'view_item' => __('View Sub domain', 'si-ogivedomain'),
        'all_items' => __('All Sub domains', 'si-ogivedomain'),
        'search_items' => __('Search Sub domains', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Sub domains:', 'si-ogivedomain'),
        'not_found' => __('No sub domains found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No sub domains found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the sub domain.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'subDomain'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('subDomain', $args);
}

function post_type_additive_init() {
    $labels = array(
        'name' => _x('Additives', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Additive', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Additives', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Additive', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'additive', 'si-ogivedomain'),
        'add_new_item' => __('Add New Additive', 'si-ogivedomain'),
        'new_item' => __('New Additive', 'si-ogivedomain'),
        'edit_item' => __('Edit Additive', 'si-ogivedomain'),
        'view_item' => __('View Additive', 'si-ogivedomain'),
        'all_items' => __('All Additives', 'si-ogivedomain'),
        'search_items' => __('Search Additives', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Additives:', 'si-ogivedomain'),
        'not_found' => __('No additives found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No additives found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the additive.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'additive'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('additive', $args);
}

function post_type_calloffer_init() {
    $labels = array(
        'name' => _x('Call offers', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Call offer', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Call offers', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Call offer', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'call-offer', 'si-ogivedomain'),
        'add_new_item' => __('Add New Call offer', 'si-ogivedomain'),
        'new_item' => __('New Call offer', 'si-ogivedomain'),
        'edit_item' => __('Edit Call offer', 'si-ogivedomain'),
        'view_item' => __('View Call offer', 'si-ogivedomain'),
        'all_items' => __('All Call offers', 'si-ogivedomain'),
        'search_items' => __('Search Call offers', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Call offers:', 'si-ogivedomain'),
        'not_found' => __('No call offer found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No call offer found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the call offer.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'call-offer'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('call-offer', $args);
}

function post_type_experessionInterest_init() {
    $labels = array(
        'name' => _x('Expressions interest', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Expression interest', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Expressions interest', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Expression interest', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'expression-interest', 'si-ogivedomain'),
        'add_new_item' => __('Add New Expression interest', 'si-ogivedomain'),
        'new_item' => __('New Expression interest', 'si-ogivedomain'),
        'edit_item' => __('Edit Expression interest', 'si-ogivedomain'),
        'view_item' => __('View Expression interest', 'si-ogivedomain'),
        'all_items' => __('All Expressions interest', 'si-ogivedomain'),
        'search_items' => __('Search Expressions interest', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Expressions interest:', 'si-ogivedomain'),
        'not_found' => __('No expressions interest found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No expressions interest found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the expression interest.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'expression-interest'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('expression-interest', $args);
}

function post_type_assignment_init() {
    $labels = array(
        'name' => _x('Assignments', 'post type general name', 'si-ogivedomain'),
        'singular_name' => _x('Assignment', 'post type singular name', 'si-ogivedomain'),
        'menu_name' => _x('Assignments', 'admin menu', 'si-ogivedomain'),
        'name_admin_bar' => _x('Assignment', 'add new on admin bar', 'si-ogivedomain'),
        'add_new' => _x('Add New', 'assignment', 'si-ogivedomain'),
        'add_new_item' => __('Add New Assignment', 'si-ogivedomain'),
        'new_item' => __('New Assignment', 'si-ogivedomain'),
        'edit_item' => __('Edit Assignment', 'si-ogivedomain'),
        'view_item' => __('View Assignment', 'si-ogivedomain'),
        'all_items' => __('All Assignments', 'si-ogivedomain'),
        'search_items' => __('Search Assignments', 'si-ogivedomain'),
        'parent_item_colon' => __('Parent Assignments:', 'si-ogivedomain'),
        'not_found' => __('No assignments found.', 'si-ogivedomain'),
        'not_found_in_trash' => __('No assignments found in Trash.', 'si-ogivedomain')
    );

    $args = array(
        'labels' => $labels,
        'description' => __('This is a post type for the assignment.', 'si-ogivedomain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'delete_with_user' => true,
        'query_var' => true,
        'taxonomies' => array('category', 'post_tag'),
        'rewrite' => array('slug' => 'assignment'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('assignment', $args);
}

function my_custom_init() {
    post_type_area_expertise_init();
    post_type_service_init();
    post_type_job_init();
    post_type_domain_init();
    post_type_subDomain_init();
    post_type_additive_init();
    post_type_assignment_init();
    post_type_calloffer_init();
    post_type_experessionInterest_init();
    //add_slider_to_home();
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
//function add_slider_to_home() {
//    $home = (int) get_option('page_on_front');
//    if (themosis_is_post($home)) {
//        //remove_post_type_support('page', 'editor');
//        Metabox::make("Image à la une pour le slider de la page d'accueil", 'page')->set(array(
//            Field::infinite('sliders', array(
//                Field::media('slider-image')
//                    ), array('title' => "Image à la une"))
//        ));
//
//        Metabox::make(__("Message de la vision de OGIVE à l'acceuil", 'si-ogivedomain'), 'page')->set(array(
//            Field::textarea('our-vision-home', ['title' => 'Notre Vision'])
//        ));
//    }
//}

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
        $headers[] = 'From: OGIVE INFOS <infos@siogive.com>';
        //$headers[] = 'Reply-To:' . Input::get('nom') . ' <' . $data['adress'] . '>';
        //$headers[] = 'Bcc:<apatchong@gmail.com>';
        $headers[] = 'Bcc:<erictonyelissouck@yahoo.fr>';

        $to = $user_email;

        $subject = "Mot de passe du compte";

        $body = $plain_text_password;
        wp_mail($to, $subject, $body, $headers);
    }
}

//This function Un-quotes a quoted string even if it is more than one
function removeslashes($string) {
    $string = implode("", explode("\\", $string));
    return stripslashes(trim($string));
}

//Function of sign in si-ogive front-end website
function signin($username, $password, $remember = null, $redirect_to = null) {
    if ($remember && $remember == 'true') {
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
//        $state = intval(get_user_meta($user->ID, 'state', true));
//        $expired_state = get_user_meta($user->ID, 'expired-state', true);
//        if (!is_null($expired_state) && $expired_state == "1") {
//            $_SESSION['signin_error'] = __("Votre abonnement a expiré");
//            wp_safe_redirect(get_permalink(get_page_by_path(__('connexion', 'gpdealdomain'))));
//            exit;
//        }elseif (!is_null($state) && $state == "0") {
//            $_SESSION['signin_error'] = __("Votre compte a été désactivé");
//            wp_safe_redirect(get_permalink(get_page_by_path(__('connexion', 'gpdealdomain'))));
//            exit;
//        } else {
        $creds = array('user_login' => $user->data->user_login, 'user_password' => $password, 'remember' => $remember);
        $secure_cookie = is_ssl() ? true : false;
        $user = wp_signon($creds, $secure_cookie);
        if ($redirect_to) {
            wp_safe_redirect($redirect_to);
        } else {
            wp_safe_redirect(home_url('/'));
        }
        exit;
//        }
    } else {
        $_SESSION['signin_error'] = __("Nom d'utilisateur ou mot de passe incorrect");
        wp_safe_redirect(get_permalink(get_page_by_path(__('connexion', 'gpdealdomain'))));
        exit;
    }
}

//Function of registration user account from siogive front-end website.
function register_user($user_data = null) {
    $new_user_data = array(
        'user_login' => $user_data['user_login'],
        'user_pass' => $user_data['user_pass'],
        'user_email' => $user_data['user_email'],
        'first_name' => $user_data['first_name'],
        'last_name' => $user_data['last_name']
    );
    $user_id = wp_insert_user($new_user_data);

    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'plain-text-password', $user_data['user_pass']);
        update_user_meta($user_id, 'state', 1);
        update_user_meta($user_id, 'expired-state', 1);
        // Set the global user object
        $current_user = get_user_by('id', $user_id);
        // set the WP login cookie
        $secure_cookie = is_ssl() ? true : false;
        wp_set_auth_cookie($user_id, true, $secure_cookie);
        $json = array("message" => "Votre compte a été créé avec succès");
        return wp_send_json_success($json);
    } else {
        $json = array("message" => "Une erreur s'est produite pendant la création du compte");
        return wp_send_json_error($json);
    }
}

//**************************************** Interaction between SI OGIVE website and Alert M.P **************************************
//Function of registration user account  from another website.
function register_user_api($user_data = null) {
    $new_user_data = array(
        'user_login' => $user_data['user_login'],
        'user_pass' => $user_data['user_pass'],
        'user_email' => $user_data['user_email'],
        'first_name' => $user_data['first_name'],
        'last_name' => $user_data['last_name']
    );
    $user_id = wp_insert_user($new_user_data);

    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'plain-text-password', $user_data['user_pass']);
        update_user_meta($user_id, 'state', intval($user_data['state']));
        update_user_meta($user_id, 'expired-state', intval($user_data['expired_state']));
        //Create a return message for user in front end
        return wp_send_json_success(siogive_new_user_notification($user_id));
    } else {
        $json = array("message" => "Une erreur s'est produite pendant la création du compte");
        return wp_send_json_error($json);
    }
}

//Function for updating user account  from another website.
function update_user_api($user_id, $user_data = null) {
    $edit_user_data = array(
        'ID' => $user_id,
        'user_login' => $user_data['user_login'],
        'user_email' => $user_data['user_email'],
        'first_name' => $user_data['first_name'],
        'last_name' => $user_data['last_name']
    );
    $user_id = wp_update_user($edit_user_data);

    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'state', intval($user_data['state']));
        update_user_meta($user_id, 'expired-state', intval($user_data['expired_state']));
        //Create a return message for user in front end
        return wp_send_json_success(siogive_new_user_notification($user_id));
    } else {
        $json = array("message" => "Une erreur s'est produite pendant la mise à jour du compte");
        return wp_send_json_error($json);
    }
}

//Function for disabling or enabling user account  from another website.
function enable_disable_user_api($user_id, $user_data = null) {
    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'state', intval($user_data['state']));
        update_user_meta($user_id, 'expired-state', intval($user_data['expired_state']));
        $json = array("message" => "Votre compte a été mis à jour avec succès");
        return wp_send_json_success($json);
    } else {
        $json = array("message" => "Une erreur s'est produite pendant la mise à jour du compte");
        return wp_send_json_error($json);
    }
}

//Fonction for saving a additive
function saveAdditive($additive_data) {
    $additive_id = null;
    if ($additive_data) {
        $reference = $additive_data['reference'];
        $subject = $additive_data['subject'];
        $domain = $additive_data['main_domain'];
        $sub_domain = $additive_data['sub_domain'];
        $additive_files_ids = $additive_data['additive_files_ids'];
        $post_args = array(
            'post_title' => $reference,
            'post_content' => $subject,
            'post_type' => 'additive',
            'post_status' => 'publish',
            'meta_input' => array(
                'reference' => $reference,
                'main-domain' => $domain,
                'sub-domain' => $sub_domain,
                'detail-files-IDs' => $additive_files_ids
            )
        );
        $additive = new WP_Query(array('post_type' => 'additive', 'post_status' => 'publish', 'post_per_page' => 1, 'meta_query' => array(array('key' => 'reference', 'value' => $reference, 'compare' => '='))));

        if ($additive->have_posts()) {
            while ($additive->have_posts()) {
                $additive->the_post();
                $post_args['ID'] = get_the_ID();
                $additive_id = wp_update_post($post_args, true);
            }
            wp_reset_postdata();
        } else {
            $additive_id = wp_insert_post($post_args, true);
        }
    }
    return $additive_id;
}

//Fonction for saving a callOffer
function saveCallOffer($callOffer_data) {
    $callOffer_id = null;
    if ($callOffer_data) {
        $reference = $callOffer_data['reference'];
        $subject = $callOffer_data['subject'];
        $domain = $callOffer_data['main_domain'];
        $sub_domain = $callOffer_data['sub_domain'];
        $callOffer_files_ids = $callOffer_data['call_offer_files_ids'];
        $post_args = array(
            'post_title' => $reference,
            'post_content' => $subject,
            'post_type' => 'call-offer',
            'post_status' => 'publish',
            'meta_input' => array(
                'reference' => $reference,
                'main-domain' => $domain,
                'sub-domain' => $sub_domain,
                'detail-files-IDs' => $callOffer_files_ids
            )
        );
        $callOffer = new WP_Query(array('post_type' => 'call-offer', 'post_status' => 'publish', 'post_per_page' => 1, 'meta_query' => array(array('key' => 'reference', 'value' => $reference, 'compare' => '='))));

        if ($callOffer->have_posts()) {
            while ($callOffer->have_posts()) {
                $callOffer->the_post();
                $post_args['ID'] = get_the_ID();
                $callOffer_id = wp_update_post($post_args, true);
            }
            wp_reset_postdata();
        } else {
            $callOffer_id = wp_insert_post($post_args, true);
        }
    }
    return $callOffer_id;
}

//Fonction for saving a assignment
function saveAssignment($assignment_data) {
    $assignment_id = null;
    if ($assignment_data) {
        $reference = $assignment_data['reference'];
        $subject = $assignment_data['subject'];
        $domain = $assignment_data['main_domain'];
        $sub_domain = $assignment_data['sub_domain'];
        $assignment_files_ids = $assignment_data['assignment_files_ids'];
        $post_args = array(
            'post_title' => $reference,
            'post_content' => $subject,
            'post_type' => 'assignment',
            'post_status' => 'publish',
            'meta_input' => array(
                'reference' => $reference,
                'main-domain' => $domain,
                'sub-domain' => $sub_domain,
                'detail-files-IDs' => $assignment_files_ids
            )
        );
        $assignment = new WP_Query(array('post_type' => 'assignment', 'post_status' => 'publish', 'post_per_page' => 1, 'meta_query' => array(array('key' => 'reference', 'value' => $reference, 'compare' => '='))));

        if ($assignment->have_posts()) {
            while ($assignment->have_posts()) {
                $assignment->the_post();
                $post_args['ID'] = get_the_ID();
                $assignment_id = wp_update_post($post_args, true);
            }
            wp_reset_postdata();
        } else {
            $assignment_id = wp_insert_post($post_args, true);
        }
    }
    return $assignment_id;
}

//Fonction for saving an expression of interest
function saveExpressionInterest($expressionInterest_data) {
    $expressionInterest_id = null;
    if ($expressionInterest_data) {
        $reference = $expressionInterest_data['reference'];
        $subject= $expressionInterest_data['subject'];
        $domain = $expressionInterest_data['main_domain'];
        $sub_domain = $expressionInterest_data['sub_domain'];
        $expressionInterest_files_ids = $expressionInterest_data['expression_interest_files_ids'];
        $post_args = array(
            'post_title' => $reference,
            'post_content' => $subject,
            'post_type' => 'expression-interest',
            'post_status' => 'publish',
            'meta_input' => array(
                'reference' => $reference,
                'main-domain' => $domain,
                'sub-domain' => $sub_domain,
                'detail-files-IDs' => $expressionInterest_files_ids
            )
        );
        $expressionInterest = new WP_Query(array('post_type' => 'expression-interest', 'post_status' => 'publish', 'post_per_page' => 1, 'meta_query' => array(array('key' => 'reference', 'value' => $reference, 'compare' => '='))));

        if ($expressionInterest->have_posts()) {
            while ($expressionInterest->have_posts()) {
                $expressionInterest->the_post();
                $post_args['ID'] = get_the_ID();
                $expressionInterest_id = wp_update_post($post_args, true);
            }
            wp_reset_postdata();
        } else {
            $expressionInterest_id = wp_insert_post($post_args, true);
        }
    }
    return $expressionInterest_id;
}

//***********************************************************************************************************************************
//Return a gender of hold name
function getGenderHoldName($gender) {
    switch ($gender) {
        case 'M':
            return 'Masculin';
        case 'F':
            return "Feminin";
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

function apply_job($job_id, $application_data) {
    $firstname = $application_data['firstname'];
    $lastname = $application_data['lastname'];
    $email = $application_data['email'];
    $phone = $application_data['phone'];
    $address = $application_data['address'];
    $country = $application_data['country'];
    $qualifications = $application_data['qualifications'];
    $lastdiploma = $application_data['lastdiploma'];
    $skills = $application_data['skills'];
    $experience = $application_data['experience'];
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
        $_SESSION['success_message'] = "Votre candidature a été envoyée avec succès. \n Vous serez contacter par mail ou appel téléphonique dès l'examination de votre candidature. Merci !";
        //$_SESSION['success_message'] = $subject;
    } else {
        //$_SESSION['error_message'] = $subject;
        $_SESSION['error_message'] = "Une erreur s'est produite lors de l'envoi de votre candidature. Verifiez vos informations puis réessayez à nouveau";
    }
}

function get_multiple_files(array $_files, $top = TRUE) {
    $files = array();
    foreach ($_files as $name => $file) {
        if ($top)
            $sub_name = $file['name'];
        else
            $sub_name = $name;

        if (is_array($sub_name)) {
            foreach (array_keys($sub_name) as $key) {
                $files[$name][$key] = array(
                    'name' => $file['name'][$key],
                    'type' => $file['type'][$key],
                    'tmp_name' => $file['tmp_name'][$key],
                    'error' => $file['error'][$key],
                    'size' => $file['size'][$key],
                );
                $files[$name] = get_multiple_files($files[$name], FALSE);
            }
        } else {
            $files[$name] = $file;
        }
    }
    return $files;
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
        return false;
    }
}

function upload_file_api($file = array(), $parent_post_id = 0) {
    require_once( ABSPATH . 'wp-admin/includes/admin.php' );
    $file_return = wp_handle_upload($file, array('test_form' => false));
    if (isset($file_return['error']) || isset($file_return['upload_error_handler'])) {
        return $file_return['error'];
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
        return false;
    }
}

function ogive_add_reply_topic($topic_id) {
    if (is_user_logged_in()) {
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
    } else {
        $_SESSION['error_message'] = "Vous devez etre connecté pour ajouter une réponse.";
    }
}

function ogive_add_topic_forum($forum_id) {
    if (is_user_logged_in()) {
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
    } else {
        $_SESSION['error_message'] = "Vous devez etre connecté pour ajouter une réponse.";
    }
}

/**
 *  Given a file, i.e. /css/base.css, replaces it with a string containing the
 *  file's mtime, i.e. /css/base.1221534296.css.
 *  
 *  @param $file  The file to be loaded.  Must be an absolute path (i.e.
 *                starting with slash).
 */
function auto_version($file) {
    if (strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
        return $file;

    $mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
    return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
}
