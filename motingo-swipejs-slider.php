<?php
/*
Plugin Name: Motingo SwipeJS Slider Wordpress Plugin
Plugin URI: http://motingo.com/swipejs-slider-wordpress-plugin
Description: This plugins provides a slider using SwipeJS.
Version: 1.0
Author: Bradley J. Spaulding
Author URI: http://motingo.com
License: MIT
*/

function motingo_swipejs_slider_init() {
  add_option('page-names');
}

add_action( 'init', 'motingo_swipejs_slider_init' );

function motingo_swipejs_slider_administration_menu() {
  add_options_page('Slider Options', 'Slider', 'manage_options', 'motingo-swipejs-slider', 'motingo_swipejs_slider_administration_menu_options');
}

// add slashes to html if magic quotes is not on
function motingo_slashit($stringvar){
  if (!get_magic_quotes_gpc()){
    $stringvar = addslashes($stringvar);
  }
  return $stringvar;
}
// remove slashes if magic quotes is on
function motingo_deslashit($stringvar){
  if (1 == get_magic_quotes_gpc()){
    $stringvar = stripslashes($stringvar);
  }
  return $stringvar;
}

function motingo_swipejs_slider_administration_menu_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  $option_name = 'num_pages';

?>
<div class="wrap">
  <h2>Slider Options</h2>
<?php
  $delay = get_option('delay');
  if ( isset($_POST['delay']) ) {
    $delay = $_POST['delay'];
    update_option('delay', $delay);
  }

  $speed = get_option('speed');
  if ( isset($_POST['speed']) ) {
    $speed = $_POST['speed'];
    update_option('speed', $speed);
  }

  if ( isset($_POST[$option_name]) ) {
    $num_pages = intval($_POST[$option_name]);
    update_option($option_name, $num_pages);
    for ( $i = 0; $i < $num_pages; $i++ ) {
      $slide_name = "slide-$i";
      update_option($slide_name, motingo_deslashit($_POST[$slide_name]));
    }
?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php
  }
  $num_pages = intval(get_option($option_name));
?>
  <form method="post">
    <p>
      <label for="delay">Delay between slide transitions (ms)</label><br/>
      <input type="text" name="delay" value="<?php echo $delay ?>" placeholder="Default: 3000 (3s)"/>
    </p>
    <p>
      <label for="speed">Transition speed (ms)</label><br/>
      <input type="text" name="speed" value="<?php echo $speed ?>" placeholder="Default: 500 (0.5s)"/>
    </p>
    <p>
      <label for="<?php echo $option_name ?>">Number of Slides:</label><br/>
      <input type="text" name="<?php echo $option_name ?>" value="<?php echo $num_pages ?>"/>
    </p>
<?php
  for ( $i = 0; $i < $num_pages; $i++ ) {
    $slide_name = "slide-$i"
?>
    <p>
      <label for="<?php echo $slide_name ?>">Slide <?php echo $i + 1; ?></label><br/>
    <textarea name="<?php echo $slide_name; ?>"><?php echo stripslashes(get_option($slide_name)); ?></textarea>
    </p>
<?php
  }
?>
    <p>
      <input type="submit" class="button-primary" value="Save Changes"/>
    </p>
  </form>
</div>
<?php
}

add_action( 'admin_menu', 'motingo_swipejs_slider_administration_menu' );

function motingo_swipejs_slider() {
  $num_pages = intval(get_option('num_pages'));

  $delay = get_option('delay');
  if ( empty($delay) ) {
    $delay = 3000;
  }

  $speed = get_option('speed');
  if ( empty($speed) ) {
    $speed = 500;
  }
?>
  <section class="slider" data-auto="<?php echo $delay ?>" data-speed="<?php echo $speed ?>">
    <ul>
<?php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  for ( $i = 0; $i < $num_pages; $i++ ) {
    if ( $i > 0 ) {
?>
      <li style="display:none;">
<?php
    } else {
?>
      <li style="display:block;">
<?php
    }
?>
        <?php echo stripslashes(get_option("slide-$i")); ?>
      </li>
<?php
  }
?>
    </ul>
  </section>
<?php
}
?>
