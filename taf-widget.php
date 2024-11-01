<?php
/*
Plugin Name: TAF plugin
Plugin URI: http://wordpress.org/extend/plugins/taf-widget/
Description: Plugin to include the latest TAF (Terminal aerodrome forecast) information from NOAA database for any Airport
Version: 0.1
Author: Luther Blissett
Author URI: http://lutherblissett.net
License: GPL3
*/

class tafWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("taf_widget", "TAF Widget",
            array("description" => "Plugin to inlclude the latest TAF information from NOAA database for any Airport"));
    }

    public function form($instance) {
        $icao = "";
        // if instance is defined, populate the fields
        if (!empty($instance)) {
            $icao = $instance["icao"];
        }

        $tableId = $this->get_field_id("icao");
        $tableName = $this->get_field_name("icao");
        echo '<label for="' . $tableId . '">ICAO</label><br/>';
        echo '<input id="' . $tableId . '" type="text" name="' .
            $tableName . '" value="' . $icao . '"/><br/>';
    }

    public function update($newInstance, $oldInstance) {
        $values = array();
        $values["icao"] = htmlentities($newInstance["icao"]);
        return $values;
    }

    public function widget($args, $instance) {
        $icao = $instance["icao"];

        $fileName = "http://weather.noaa.gov/pub/data/forecasts/taf/stations/$icao.TXT";
        $taf = '';
        $fileData = @file($fileName) or die('TAF not available');
        if ($fileData != false) {
        	list($i, $date) = each($fileData);
        	$utc = strtotime(trim($date));
        	$time = date("D, F jS Y g:i A",$utc);

        	while (list($i, $line) = each($fileData)) {
        		$taf .= ' ' . trim($line);
            	}
        	$taf = trim(str_replace('  ', ' ', $taf));
        }

	echo '<div class="widget widget-wrapper" id="' . $args['widget_id'] . '-container">';
	echo '<div class="widget-title"><b>Current TAF for ' . $icao . '</b></div>';
	echo $taf . '</div>';
    }
}

add_action("widgets_init", register_tafwidget);
function register_tafwidget() { register_widget("tafwidget"); }
