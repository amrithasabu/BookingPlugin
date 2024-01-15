<?php
/**
 * Plugin Name: Calender
 * Description: Prints the Flexible Dates Calender
 * Version: 1.0
 * Author: Amritha Sabu
 */


add_shortcode('rates_calendar', 'hotel_rates_calendar');
add_action('wp_enqueue_scripts', 'enqueue_calendar_styles');


function enqueue_calendar_styles()
{
    wp_enqueue_style('hotel-booking-styles', plugin_dir_url(__FILE__) . 'Calendar.css');
}


function hotel_rates_calendar($selectedMonth = null)
{
    $current_year = date('Y');
    $current_month = date('n') - 1;
    $months = array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    );

    echo '<section class="container">';
    echo '<div class="month-buttons">';
    foreach ($months as $monthIndex => $month) {
        $active = $selectedMonth === $monthIndex ? 'active' : '';
        echo '<button class="month-button ' . $active . '" data-month="' . $monthIndex . '">' . $month . ' ' . $current_year . '</button>';
    }
    echo '</div>';
    echo '</section>';

    if ($selectedMonth === '') {
        $selectedMonth = $current_month;
    }

    echo '<div class="calendar-container">';
    foreach ($months as $mIndex => $m) {
        echo '<div class="calendar" data-month="' . $mIndex . '" style="display: ' . ($selectedMonth == $mIndex ? 'block' : 'none') . ';">';
        echo '<h3>' . $m . ' ' . $current_year . '</h3>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Sun</th>';
        echo '<th>Mon</th>';
        echo '<th>Tue</th>';
        echo '<th>Wed</th>';
        echo '<th>Thu</th>';
        echo '<th>Fri</th>';
        echo '<th>Sat</th>';
        echo '</tr>';
        echo '</thead>';

        echo '<tbody>';
        $firstDay = date('w', strtotime("$current_year-$m-01"));
        $daysInMonth = date('t', strtotime("$current_year-$m-01"));

        $dayCount = 1;
        $Price_per_night = get_option('price_per_night');

        for ($i = 0; $i < 6; $i++) {
            echo '<tr>';

            for ($j = 0; $j < 7; $j++) {
                if (($i === 0 && $j < $firstDay) || $dayCount > $daysInMonth) {
                    echo '<td></td>';
                } else {
                    echo '<td>';
                    echo $dayCount . '<br>';
                    echo '$' . $Price_per_night;
                    echo '</td>';
                    $dayCount++;
                }
            }

            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    echo '</div>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            var buttons = document.querySelectorAll(".month-button");
            buttons.forEach(function (button) {
                button.addEventListener("click", function () {
                    var selectedMonth = this.getAttribute("data-month");
                    var calendars = document.querySelectorAll(".calendar");
                    calendars.forEach(function (calendar) {
                        var calendarMonth = calendar.getAttribute("data-month");
                        calendar.style.display = selectedMonth === calendarMonth ? "block" : "none";
                    });
                    buttons.forEach(function (btn) {
                        btn.classList.remove("active");
                    });
                    this.classList.add("active");
                });
            });
        });
    </script>';
}



function calendar_plugin_menu()
{
    add_menu_page(
        'Calendar Plugin Settings',
        'Calendar Plugin',
        'manage_options',
        'calendar-plugin',
        'calendar_plugin_settings_page',
        //plugin_dir_url(__FILE__) . 'assets/images/bookingicon.png'
    );
}

add_action('admin_menu', 'calendar_plugin_menu');

function calendar_plugin_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Calendar Plugin Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('calendar_plugin_settings');
            do_settings_sections('calendar_plugin_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
function calendar_plugin_settings()
{
    register_setting('calendar_plugin_settings', 'price_per_night');

    add_settings_section('calendar_plugin_main_section', 'Main Settings', 'calendar_plugin_section_callback', 'calendar_plugin_settings');

    add_settings_field('price_per_night_field', 'Price Per Night', 'price_per_night_field_callback', 'calendar_plugin_settings', 'calendar_plugin_main_section');
}

function calendar_plugin_section_callback()
{
    echo '<h3>Enter your settings below:</h3>';
}

function price_per_night_field_callback()
{
    $Price_per_night = get_option('price_per_night');
    echo '<input type="text" name="price_per_night" value="' . esc_attr($Price_per_night) . '" />';
}
add_action('admin_init', 'calendar_plugin_settings');