<?php

/**
 * SPDX-FileCopyrightText: 2012-2020 Jared Novack and contributors
 * SPDX-License-Identifier: MIT
 * SPDX-FileCopyrightText: 2017-2020 Johannes Siipola
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
    if (class_exists('Timber')) {
        $timber = new Timber\Timber();
    }
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' .
            esc_url(admin_url('plugins.php#timber')) .
            '">' .
            esc_url(admin_url('plugins.php')) .
            '</a></p></div>';
    });

    add_filter('template_include', function ($template) {
        return get_stylesheet_directory() . '/no-timber.html';
    });
    return;
}

Timber::$dirname = ['dist/templates', 'dist'];

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site
{
    /** Add timber support. */
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'theme_supports']);
        add_filter('timber/context', [$this, 'add_to_context']);
        add_filter('timber/twig', [$this, 'add_to_twig']);
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        parent::__construct();
    }
    /** This is where you can register custom post types. */
    public function register_post_types()
    {
    }
    /** This is where you can register custom taxonomies. */
    public function register_taxonomies()
    {
    }

    /** This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context($context)
    {
        $context['foo'] = 'bar';
        $context['stuff'] = 'I am a value set in your functions.php file';
        $context['notes'] =
            'These values are available everytime you call Timber::context();';
        $context['menu'] = new Timber\Menu();
        $context['site'] = $this;


        return $context;
    }

    public function theme_supports()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', [
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ]);

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        //add_theme_support('post-formats', [
        //    'aside',
        //    'image',
        //    'video',
        //    'quote',
        //    'link',
        //    'gallery',
        //    'audio',
        //]);

        add_theme_support('menus');
    }

    /** This Would return 'foo bar!'.
     *
     * @param string $text being 'foo', then returned 'foo bar!'.
     */
    public function myfoo($text)
    {
        $text .= ' bar!';
        return $text;
    }

    /** This is where you can add your own functions to twig.
     *
     * @param string $twig get extension.
     */
    public function add_to_twig($twig)
    {
        $twig->addExtension(new Twig\Extension\StringLoaderExtension());
        $twig->addFilter(new Twig\TwigFilter('myfoo', [$this, 'myfoo']));
        return $twig;
    }



    public static function get_file_hash($file)
    {
        $hash = @md5_file(get_template_directory() . $file);
        if ($hash) {
            return $hash;
        }
        return null;
    }
}
add_action('acf/init', 'my_acf_init');

function my_acf_init()
{
    add_theme_support('align-wide');
    // Bail out if function doesn’t exist.
    if (!function_exists('acf_register_block')) {
        return;
    }

    // Register a new block.
    acf_register_block(array(
        'name'            => 'example_block',
        'title'           => __('Example Block', 'your-text-domain'),
        'description'     => __('A custom example block.', 'your-text-domain'),
        'render_callback' => 'my_acf_block_render_callback',
        'category'        => 'theme',
        'icon'            => 'admin-comments',
        'keywords'        => array('example'),
    ));

    acf_register_block(array(
        'name'            => 'header',
        'title'           => __('Header Blok', 'your-text-domain'),
        'description'     => __('Een header blok.', 'your-text-domain'),
        'render_callback' => 'my_acf_block_render_callback',
        'category'        => 'theme',
        'icon'            => 'admin-comments',
        'keywords'        => array('header'),
        'align'             => 'full',
        'mode'                 => 'edit',
        // 'supports'          => array('anchor' => true, 'mode' => false)
        'supports'        => array(
            'align'        => array('full'),
            'align'        => false,
        ),
    ));
    acf_register_block(array(
        'name'            => 'hero',
        'title'           => __('Hero Blok', 'your-text-domain'),
        'description'     => __('Een Hero blok.', 'your-text-domain'),
        'render_callback' => 'my_acf_block_render_callback',
        'category'        => 'theme',
        'icon'            => 'admin-comments',
        'keywords'        => array('hero'),
        'align'             => 'full',
        'mode'                 => 'edit',
        'supports'        => array(
            'align'        => array('full'),
            'align'        => false,
        ),
        // 'supports'          => array('anchor' => true, 'mode' => false)
    ));
    acf_register_block(array(
        'name'            => 'categories',
        'title'           => __('Categories Blok', 'your-text-domain'),
        'description'     => __('Een categories blok.', 'your-text-domain'),
        'render_callback' => 'my_acf_block_render_callback',
        'category'        => 'theme',
        'icon'            => 'admin-comments',
        'keywords'        => array('categories'),
        'align'             => 'full',
        'mode'                 => 'edit',
        'supports'        => array(
            'align'        => array('full'),
            'align'        => false,
        ),
        // 'supports'          => array('anchor' => true, 'mode' => false)
    ));
    acf_register_block(array(
        'name'            => 'productslider',
        'title'           => __('Product slider Blok', 'your-text-domain'),
        'description'     => __('Een productslider blok.', 'your-text-domain'),
        'render_callback' => 'my_acf_block_render_callback',
        'category'        => 'theme',
        'icon'            => 'admin-comments',
        'keywords'        => array('product', 'slider'),
        'align'             => 'full',
        'mode'                 => 'edit',
        'supports'        => array(
            'align'        => array('full'),
            'align'        => false,
        ),
        // 'supports'          => array('anchor' => true, 'mode' => false)
    ));
}

function my_acf_block_render_callback($block, $content = '', $is_preview = false)
{
    $context = Timber::get_context();

    // Store block values.
    $context['block'] = $block;

    // Store field values.
    $context['fields'] = get_fields();

    $slug = str_replace('acf/', '', $block['name']);
    $args = array(
        'post_type' => 'products',
        'order' => 'DESC',
    );
    $context['products'] = Timber::get_posts($args);

    Timber::render('blocks/' . $slug . '/' . $slug . '.twig', $context);
}

function my_acf_block_editor_style()
{
    wp_enqueue_style('main-css', get_template_directory_uri() . '/dist/style.css');
    wp_enqueue_script('main-js', get_template_directory_uri() . '/dist/script.js');
}

add_action('enqueue_block_assets', 'my_acf_block_editor_style');

new StarterSite();

function theme_support_options()
{
    $defaults = array(
        'height'      => 150,
        'width'       => 250,
        'flex-height' => false, // <-- setting both flex-height and flex-width to false maintains an aspect ratio
        'flex-width'  => false
    );
    add_theme_support('custom-logo', $defaults);
}
// call the function in the hook
add_action('after_setup_theme', 'theme_support_options');

function register_my_menus()
{
    register_nav_menus(
        array(
            'hoofd-menu' => __('Hoofdmenu'),
            'footer-menu' => __('Footermenu'),
        )
    );
}
add_action('init', 'register_my_menus');

add_filter('timber/context', 'add_to_context');

function add_to_context($context)
{
    // So here you are adding data to Timber's context object, i.e...
    // $context['foo'] = 'I am some other typical value set in your functions.php file, unrelated to the menu';

    // Now, in similar fashion, you add a Timber Menu and send it along to the context.
    $context['hoofd-menu'] = new \Timber\Menu('hoofd-menu');

    return $context;
}


function webp_upload_mimes($existing_mimes)
{
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
}
add_filter('mime_types', 'webp_upload_mimes');

/** * Enable preview / thumbnail for webp image files.*/
function webp_is_displayable($result, $path)
{
    if ($result === false) {
        $displayable_image_types = array(IMAGETYPE_WEBP);
        $info = @getimagesize($path);

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}
add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);

function theme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

function timber_set_product($post)
{
    global $product;

    if (is_woocommerce()) {
        $product = wc_get_product($post->ID);
    }
}
