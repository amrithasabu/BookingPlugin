<?php
/**
 * Plugin Name: Calender (Part 2)
 * Description: Prints the Flexible Dates Calender
 * Version: 1.0
 * Author: Amritha Sabu
 */


add_shortcode('rates_calendar', 'hotel_rates_calendar');
add_action('wp_enqueue_scripts', 'enqueue_calendar_styles');


function enqueue_calendar_styles()
{
    wp_enqueue_style('calendar-styles', plugin_dir_url(__FILE__) . 'Calendar.css');
}

function hotel_rates_calendar($selectedMonth = null)
{
    echo '<div class="stay_container">';
    echo '<script src="' . plugin_dir_url(__FILE__) . 'SaveForm.js"></script>';
    echo '<h2>Find The Best Price</h2>';
    echo '<form method="post">';
    echo '<div class="stay">';
    echo '<div class="stay_form">';
    echo '<label  for="check_in_date">Check-in Date:</label>';
    echo '<input type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>';

    echo '<label  for="check_out_date">Check-out Date:</label>';
    echo '<input type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>';

    $options = array('1', '2', ' 3', '4', '5');
    echo '<label for="no_of_adults">Adults</label>';
    echo '<select class="booking_dropdown" id="no_of_adults" name="no_of_adults" value="' . htmlspecialchars($_POST['no_of_adults'] ?? '') . '">';
    foreach ($options as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';


    $options = array('0', '1', '2', ' 3', '4', '5');
    echo '<label for="no_of_children">Children</label>';
    echo '<select class="booking_dropdown" id="no_of_children" name="no_of_children" value="' . htmlspecialchars($_POST['no_of_children'] ?? '') . '">';
    foreach ($options as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '<p><input type="submit" name="submit" value="Submit"/></p>';
    echo '</div>';
    echo '</div>';
    echo '</form>';

    if (isset($_POST['submit'])) {
        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $no_of_adults = sanitize_text_field($_POST['no_of_adults']);
        $no_of_children = sanitize_text_field($_POST['no_of_children']);
        $checkInDate = intval(date('j', strtotime($check_in_date)));
        $checkOutDate = intval(date('j', strtotime($check_out_date)));
        $check_in_month = date('F', strtotime($check_in_date));
    }

    $hostname = "localhost";
    $username = "root";
    $password = "root";
    $database = "local";
    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die("Connection Failed:" . mysqli_connect_errno());
    }
    $insert_query = "INSERT INTO `booking` (`check_in_date`,`check_out_date`,`no_of_adults`,`no_of_children`) 
    VALUES (?,?,?,?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $insert_query)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssss", $check_in_date, $check_out_date, $no_of_adults, $no_of_children);
    mysqli_stmt_execute($stmt);
    $conn->close();

    //calendar display logic
    $current_date = date('Y-n-j');
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

    echo '<div class="month-buttons-container">';
    foreach ($months as $monthIndex => $month) {
        $active = $selectedMonth === $monthIndex ? 'active' : '';
        echo '<button class="month-button ' . $active . '" data-month="' . $monthIndex . '">' . $month . ' ' . $current_year . '</button>';
    }
    echo '</div>';


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
        $currentDay = intval(date('j', strtotime($current_date)));
        $dayCount = 1;
        $Price_per_night = get_option('price_per_night');

        for ($i = 0; $i < 6; $i++) {
            echo '<tr>';

            for ($j = 0; $j < 7; $j++) {
                if (($i === 0 && $j < $firstDay) || $dayCount > $daysInMonth) {
                    echo '<td></td>';
                } else {
                    if (($m === date('F')) && ($dayCount < $currentDay)) {
                        echo '<td class="past-days">';
                        echo $dayCount . '<br>';
                        echo '<br>' . '<br>' . '<br>';
                        echo '</td>';
                    } elseif (isset($_POST['submit']) && ($dayCount >= $checkInDate) && ($dayCount <= $checkOutDate) && ($m === $check_in_month)) {
                        echo '<td class="selected-day">';
                        echo $dayCount . '<br>';
                        echo '<h5>$' . $Price_per_night . '<br></h5>';
                        echo '<p>avg/night for 1 night</p>';
                        echo '</td>';
                    } else {
                        echo '<td class="unselected-day">';
                        echo $dayCount . '<br>';
                        echo '<h5>$' . $Price_per_night . '<br></h5>';
                        echo '<p>avg/night for 1 night</p>';
                        echo '</td>';
                    }
                    $dayCount++;
                }
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '<button>Book Now</button>';
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