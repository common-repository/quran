<?php
/*
Plugin Name: The Holy Quran
Plugin URI: http://khaledalhourani.com/
Description: Displays random verse from the holy quran. 
Version: 0.5
Author: Khaled Al Hourani
Author URI: http://holooli.com
*/

function widget_quran_init()  {

  if (!function_exists('register_sidebar_widget')) {
    return;
  }

  /**
   * Widget config
   */
  function widget_quran_control() {
    echo '<p style="text-align:right;">الرجاء الدعاء بظهر الغيب بالمغفرة والثبات والسداد لمبرمج الإضافة.</p>';
  }

  /**
   * Widget display
   */
  function widget_quran($args) {
    extract($args);

    // Check or create the base tables
    check_or_create_tables();

    echo "<link rel=\"stylesheet\" href=\"".WP_PLUGIN_URL."/quran/css/style.css\" type=\"text/css\" media=\"screen\" />";
    echo $before_widget;
    echo $before_title ."القرآن الكريم". $after_title;

    $aya = get_verse();

    if (!empty($aya)) {
      $sura = get_sorah($aya->chapter_number);

      echo '<p id="quran_verse">'.$aya->verse_content.'</p>';
      echo '<p id="quran_verse_number">الآية رقم '.$aya->verse_number.'</p>';
      echo '<p id="quran_sura">من سورة '.$sura->sura.'</p>';
    } else {
      echo '<p id="quran_error">قاعدة البيانات غير موجودة</p>';
    }

    echo '</li>' . $after_widget;
  }

  /**
   * Get random aya
   */
  function get_verse() {
    global $wpdb;

    $vid = 1 + rand() % 6236;

    $query = "SELECT quran_verses_ar.content AS verse_content, quran_verses_ar.chapter_number AS chapter_number, quran_verses_ar.number AS verse_number FROM quran_verses_ar WHERE quran_verses_ar.id = '$vid'";

    return $wpdb->get_row($query);
  }

  /**
   * Get Sorah name of specific aya
   */
  function get_sorah($sid) {
    global $wpdb;

    $query = "SELECT quran_chapters_ar.title AS sura FROM quran_chapters_ar WHERE quran_chapters_ar.number = '$sid'";

    return $wpdb->get_row($query);
  }

  /**
   * Check if table `hijri` exist, and if not create it
   */
  function check_or_create_tables() {
    global $wpdb;

    $query = ("SELECT COUNT(*) FROM quran_chapters_ar"); 
    if ($wpdb->get_row($query) <= 0) {
      // Execute table creation query
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      // Import Sorahs table
      $sql .= file_get_contents(ABSPATH . 'wp-content/plugins/quran/data/sorahs.sql');
      dbDelta($sql);
    }

    $query = ("SELECT COUNT(*) FROM quran_verses_ar"); 
    if ($wpdb->get_row($query) <= 0) {
      // Execute table creation query
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      // Import Ayat table
      $sql .= file_get_contents(ABSPATH . 'wp-content/plugins/quran/data/ayat.sql');
      dbDelta($sql);
    }
  }

  register_widget_control(array('Quran', 'widgets'), 'widget_quran_control', 200, 200);
  register_sidebar_widget(array('Quran', 'widgets'), 'widget_quran');
}

add_action('widgets_init', 'widget_quran_init');