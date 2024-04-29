<?php
/*
Plugin Name: Austin Weather
Description: This plugin displays current weather conditions from https://w1.weather.gov/xml/current_obs/KATT.xml
Version: 1.0
Author: Chris Stelly
*/

// Add a shortcode to display weather conditions
add_shortcode('weather_conditions', 'display_weather_conditions');

function display_weather_conditions() {
    // Fetch XML data
    $weather_xml_url = 'https://w1.weather.gov/xml/current_obs/KATT.xml';
    $weather_data = wp_remote_get($weather_xml_url);

    // Check if data is retrieved successfully
    if (is_wp_error($weather_data) || wp_remote_retrieve_response_code($weather_data) !== 200) {
        return 'Failed to retrieve weather data.';
    }

    // Parse XML
    $weather_xml = wp_remote_retrieve_body($weather_data);
    $weather_object = simplexml_load_string($weather_xml);

    // Check if XML parsing is successful
    if (!$weather_object) {
        return 'Failed to parse weather data.';
    }

    // Extract weather information
    $weather_conditions = $weather_object->weather;
    $temperature = $weather_object->temperature_string;
    $humidity = $weather_object->relative_humidity;
    $wind_speed = $weather_object->wind_mph;
    $wind_direction = $weather_object->wind_dir;
    $observation_time = $weather_object->observation_time;

    // Build output HTML
    $output = '<div class="weather-conditions">';
    $output .= '<h3>Austin Weather</h3>';
    $output .= '<p><strong>Current Conditions:</strong> ' . $weather_conditions . '</p>';
    $output .= '<p><strong>Temperature:</strong> ' . $temperature . '</p>';
    $output .= '<p><strong>Humidity:</strong> ' . $humidity . '</p>';
    $output .= '<p><strong>Wind:</strong> ' . $wind_speed . ' mph from ' . $wind_direction . '</p>';
    $output .= '<p><strong>Observation Time:</strong> ' . $observation_time . '</p>';
    $output .= '</div>';

    return $output;
}

// Enqueue CSS stylesheet
add_action('wp_enqueue_scripts', 'enqueue_weather_conditions_styles');

function enqueue_weather_conditions_styles() {
    wp_enqueue_style('weather-conditions-style', plugins_url('css/weather-conditions-style.css', __FILE__));
}
