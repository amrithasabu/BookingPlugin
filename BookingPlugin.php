<?php
/**
 * Plugin Name: Booking Plugin
 * Description: Calculates hotel booking price .
 * Version: 1.0
 * Author: Amritha Sabu
 */



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


add_shortcode('stay_dates', 'stay_dates_function');
add_shortcode('rates_calendar', 'hotel_rates_calendar');
add_shortcode('rates_calendar_mobile', 'hotel_rates_calendar_mobile');
add_shortcode('entire_manor', 'entire_manor_booking_function');
add_shortcode('rooms', 'rooms_booking_function');
add_shortcode('cooking_class', 'cooking_class_booking_function');
add_shortcode('enitre_manor_cooking_class', 'entire_manor_cooking_class_booking_function');
add_shortcode('display', 'booking_details');

add_action('wp_enqueue_scripts', 'enqueue_hotel_booking_styles');


function enqueue_hotel_booking_styles()
{
    wp_enqueue_style('hotel-booking-styles', plugin_dir_url(__FILE__) . 'Booking.css');
}

function stay_dates_function()
{
    echo '<div class="heading">';
    echo '<h2>Find The Best Price</h2>';
    echo '</div>';
    echo '<form method="post">';
    echo '<div class="stay_details">';
    echo '<div class="stay_details_form">';
    echo '<p class="check_in_out">
             <label  for="check_in_date">Check In </label>
             <input type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>
         </p>';
    echo '<p class="check_in_out">
             <label  for="check_in_date">Check Out</label>
             <input type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>
         </p>';
    echo '<p class="check_in_out">';
    $option_adlt = array('1', '2', ' 3', '4', '5');
    echo '<label for="no_of_adults">Adults</label>';
    echo '<select class="booking_dropdown" id="no-of-adults" name="no_of_adults" value="' . htmlspecialchars($_POST['no_of_adults'] ?? '') . '">';
    foreach ($option_adlt as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</p>';
    echo '<p class="check_in_out">';
    $option_chil = array('0', '1', '2', ' 3', '4', '5');
    echo '<label for="no_of_children">Children</label>';
    echo '<select class="booking_dropdown" id="no-of_children" name="no_of_children" value="' . htmlspecialchars($_POST['no_of_children'] ?? '') . '">';
    foreach ($option_chil as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</p>';

    echo '<p><input type="submit" name="submit" value="Submit"/></p>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
            function getToday() {
                const today = new Date();
                const month = (today.getMonth() + 1).toString().padStart(2, "0");
                const day = today.getDate().toString().padStart(2, "0");
            return `${today.getFullYear()}-${month}-${day}`;
            }
  
            document.getElementById("check_in_date").min = getToday();
            document.getElementById("check_out_date").min = getToday();
            });
         </script>';


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

    if (isset($_POST['submit'])) {
        $stay_details = new stdClass();
        $stay_details->check_in_date = $checkInDate;
        $stay_details->check_in = $check_in_date;
        $stay_details->check_out_date = $checkOutDate;
        $stay_details->check_out = $check_out_date;
        $stay_details->check_in_month = $check_in_month;
        $stay_details->no_of_adults = $no_of_adults;
        $stay_details->no_of_children = $no_of_children;
        $_SESSION['stay_details'] = $stay_details;
    }

}
function hotel_rates_calendar($selectedMonth = null)
{
    $filename = plugin_dir_url(__FILE__) . 'Booking.csv';
    $data = [];
    $f = fopen($filename, 'r');

    if ($f === false) {
        die('Cannot open the file ' . $filename);
    }
    while (($row = fgetcsv($f)) !== false) {
        $data[] = $row;
    }
    fclose($f);
    /*$csv = get_option('upload_csv');
    $data = [];
    $f = fopen($csv, 'r');
    if ($f === false) {
        die('Cannot open the file ' . $csv);
    }
    while (($row = fgetcsv($f)) !== false) {
        $data[] = $row;
    }
    fclose($f);*/

    echo '<div class="hide_on_mobile">';
    $current_date = date('Y-n-j');
    $current_year = date('Y');
    $current_month = date('n') - 1;
    $currmonth = date('F');
    $index = 0;
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
    $current_month_index = array_search($currmonth, $months);

    echo '<div class="month-buttons-container">';
    foreach ($months as $monthIndex => $month) {
        if ($monthIndex >= $current_month_index) {
            $active = $selectedMonth === $monthIndex ? 'active' : '';
            echo '<button class="month-button ' . $active . '" data-month="' . $monthIndex . '">' . $month . ' ' . $current_year . '</button>';
        }
    }
    echo '</div>';


    if ($selectedMonth === '') {
        $selectedMonth = $current_month;
    }
    echo '<div class="calendar-container">';
    foreach ($months as $mIndex => $m) {
        if ($mIndex >= $current_month_index) {
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
            session_start();

            if (isset($_SESSION['stay_details'])) {
                $stay_details = $_SESSION['stay_details'];
                $checkInDate = $stay_details->check_in_date;
                $checkOutDate = $stay_details->check_out_date;
                $checkInMonth = $stay_details->check_in_month;

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
                            } elseif (isset($_POST['submit']) && ($dayCount >= $checkInDate) && ($dayCount <= $checkOutDate) && ($m === $checkInMonth)) {
                                if (isset($data[$index])) {
                                    $row = $data[$index][1];
                                    echo '<td class="selected-day">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>$' . $row . '<br></h5>';
                                    echo '<p>avg/night for 1 night</p>';
                                    echo '</td>';
                                } else {
                                    echo '<td class="selected-day">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>NIL<br></h5>';
                                    echo '<p>avg/night for 1 night</p>';
                                    echo '</td>';
                                }
                            } else {
                                if (isset($data[$index])) {
                                    $row = $data[$index][1];
                                    echo '<td class="unselected-day">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>$' . $row . '<br></h5>';
                                    echo '<p>avg/night for 1 night</p>';
                                    echo '</td>';
                                } else {
                                    echo '<td class="unselected-day">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>NIL<br></h5>';
                                    echo '<p>avg/night for 1 night</p>';
                                    echo '</td>';
                                }
                            }
                            $dayCount++;
                            $index++;
                        }
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '<form method="post">';
                echo '<p><input type="submit" name="submit_button_2" value="Book Now"/></p>';
                echo '</form>';
                echo '</div>';
            } else {
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
                            } else {
                                if (isset($data[$index])) {
                                    $row = $data[$index][1];
                                    echo '<td class="unselected-day">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>$' . $row . '<br></h5>';
                                    echo '<p>avg/night for 1 night</p>';
                                    echo '</td>';
                                } else {
                                    echo '<td class="unselected-day">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>NIL<br></h5>';
                                    echo '<p>avg/night for 1 night</p>';
                                    echo '</td>';
                                }
                            }
                            $dayCount++;
                            $index++;
                        }
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '<form method="post">';
                echo '<p><input type="submit" name="submit_button_2" value="Book Now"/></p>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';

            echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';

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
    }
    echo '<script>
    document.addEventListener("DOMContentLoaded", function () {
        var dateBoxes = document.querySelectorAll(".calendar td");
        var selectedCheckInDate = null;
        var selectedCheckOutDate = null;

        dateBoxes.forEach(function (dateBox) {
            dateBox.addEventListener("click", function () {
                var selectedDate = this.innerText.trim();
                var selectedMonth = this.closest(".calendar").getAttribute("data-month");
                var selectedYear = ' . $current_year . ';

                var selectedDateFormatted = selectedYear + "-" + (parseInt(selectedMonth) + 1) + "-" + selectedDate;

                if (selectedCheckInDate === null || selectedCheckOutDate !== null) {
                    selectedCheckInDate = selectedDateFormatted;
                    selectedCheckOutDate = null;
                    console.log("Check In: " + selectedCheckInDate);
                    this.style.backgroundColor = "#4681a9";
                    this.style.color = "white";
                } else if (selectedCheckInDate !== null || selectedCheckOutDate === null) {
                    selectedCheckOutDate = selectedDateFormatted;
                    console.log("Check Out: " + selectedCheckOutDate);
                    this.style.backgroundColor = "#4681a9";
                    this.style.color = "white";
                }
                dateBoxes.forEach(function (box) {
                    var boxDate = box.innerText.trim();
                    var boxMonth = box.closest(".calendar").getAttribute("data-month");
                    var boxYear = ' . $current_year . ';
                    var boxDateFormatted = boxYear + "-" + (parseInt(boxMonth) + 1) + "-" + boxDate;
                    console.log(boxDateFormatted);

                    if (boxDateFormatted > selectedCheckInDate && boxDateFormatted < selectedCheckOutDate) {
                        box.style.backgroundColor = "#4681a9";
                        box.style.color = "white";
                    }
                });
                
            });
        });

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
    echo '</div>';
    if (isset($_POST['submit_button_2'])) {
        ?>
        <script>
            window.location.href = "http://localhost:10010/?page_id=132";
        </script>
        <?php

        exit();
    }
}
function hotel_rates_calendar_mobile($selectedMonth = null)
{
    $filename = plugin_dir_url(__FILE__) . 'Booking.csv';
    $data = [];
    $f = fopen($filename, 'r');

    if ($f === false) {
        die('Cannot open the file ' . $filename);
    }
    while (($row = fgetcsv($f)) !== false) {
        $data[] = $row;
    }
    fclose($f);
    /*$csv = get_option('upload_csv');
    $data = [];
    $f = fopen($csv, 'r');
    if ($f === false) {
        die('Cannot open the file ' . $csv);
    }
    while (($row = fgetcsv($f)) !== false) {
        $data[] = $row;
    }
    fclose($f);*/

    echo '<div class="hide_on_desktop">';
    $current_date = date('Y-n-j');
    $current_year = date('Y');
    $current_month = date('n') - 1;
    $currmonth = date('F');
    $index = 0;
    $months = array(
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    );
    $current_month_index = array_search($currmonth, $months);

    echo '<div class="month_buttons_container_mobile">';
    foreach ($months as $monthIndex => $month) {
        if ($monthIndex >= $current_month_index) {
            $active = $selectedMonth === $monthIndex ? 'active' : '';
            echo '<button class="month_button_mobile' . $active . '" data-month="' . $monthIndex . '">' . $month . ' ' . $current_year . '</button>';
        }
    }
    echo '</div>';


    if ($selectedMonth === '') {
        $selectedMonth = $current_month;
    }
    echo '<div class="calendar_container_mobile">';
    foreach ($months as $mIndex => $m) {
        if ($mIndex >= $current_month_index) {
            echo '<div class="calendar_mobile" data-month="' . $mIndex . '" style="display: ' . ($selectedMonth == $mIndex ? 'block' : 'none') . ';">';
            echo '<h3>' . $m . ' ' . $current_year . '</h3>';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th class="small_table_mobile">Sun</th>';
            echo '<th class="small_table_mobile">Mon</th>';
            echo '<th class="small_table_mobile">Tue</th>';
            echo '<th class="small_table_mobile">Wed</th>';
            echo '<th class="small_table_mobile">Thu</th>';
            echo '<th class="small_table_mobile">Fri</th>';
            echo '<th class="small_table_mobile">Sat</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $firstDay = date('w', strtotime("$current_year-$m-01"));
            $daysInMonth = date('t', strtotime("$current_year-$m-01"));
            $currentDay = intval(date('j', strtotime($current_date)));
            $dayCount = 1;
            session_start();

            if (isset($_SESSION['stay_details'])) {
                $stay_details = $_SESSION['stay_details'];
                $checkInDate = $stay_details->check_in_date;
                $checkOutDate = $stay_details->check_out_date;
                $checkInMonth = $stay_details->check_in_month;
                $A = substr($checkInDate, 0, 3);
                $B = substr($checkOutDate, 0, 3);
                $C = substr($checkInMonth, 0, 3);

                for ($i = 0; $i < 6; $i++) {
                    echo '<tr>';

                    for ($j = 0; $j < 7; $j++) {
                        if (($i === 0 && $j < $firstDay) || $dayCount > $daysInMonth) {
                            echo '<td></td>';
                        } else {
                            if (($m === date('M')) && ($dayCount < $currentDay)) {
                                echo '<td class="past_days_mobile">';
                                echo $dayCount . '<br>';
                                echo '</td>';
                            } elseif (isset($_POST['submit']) && ($dayCount >= $A) && ($dayCount <= $B) && ($m === $C)) {
                                if (isset($data[$index])) {
                                    $row = $data[$index][1];
                                    echo '<td class="selected_day_mobile">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>$' . $row . '</h5>';
                                    echo '</td>';
                                } else {
                                    echo '<td class="selected_day_mobile">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>NIL</h5>';
                                    echo '</td>';
                                }
                            } else {
                                if (isset($data[$index])) {
                                    $row = $data[$index][1];
                                    echo '<td class="unselected_day_mobile">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>$' . $row . '</h5>';
                                    echo '</td>';
                                } else {
                                    echo '<td class="unselected_day_mobile">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>NIL</h5>';
                                    echo '</td>';
                                }
                            }
                            $dayCount++;
                            $index++;
                        }
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '<form method="post">';
                echo '<p><input type="submit" name="submit_button_2" class="submit_button_mobile_2" value="Book Now "/></p>';
                echo '</form>';
                echo '</div>';
            } else {
                for ($i = 0; $i < 6; $i++) {
                    echo '<tr>';

                    for ($j = 0; $j < 7; $j++) {
                        if (($i === 0 && $j < $firstDay) || $dayCount > $daysInMonth) {
                            echo '<td></td>';
                        } else {
                            if (($m === date('M')) && ($dayCount < $currentDay)) {
                                echo '<td class="past_days_mobile">';
                                echo $dayCount . '<br>';
                                echo '</td>';
                            } else {
                                if (isset($data[$index])) {
                                    $row = $data[$index][1];
                                    echo '<td class="unselected_day_mobile">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>$' . $row . '</h5>';
                                    echo '</td>';
                                } else {
                                    echo '<td class="unselected_day_mobile">';
                                    echo $dayCount . '<br>';
                                    echo '<h5>NIL</h5>';
                                    echo '</td>';
                                }
                            }
                            $dayCount++;
                            $index++;
                        }
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '<form method="post">';
                echo '<p><input type="submit" name="submit_button_2" class="submit_button_mobile_2" value="Book Now "/></p>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';

            echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';

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
    }
    echo '<script>
    document.addEventListener("DOMContentLoaded", function () {
        var dateBoxes = document.querySelectorAll(".calendar td");
        var selectedCheckInDate = null;
        var selectedCheckOutDate = null;

        dateBoxes.forEach(function (dateBox) {
            dateBox.addEventListener("click", function () {
                var selectedDate = this.innerText.trim();
                var selectedMonth = this.closest(".calendar").getAttribute("data-month");
                var selectedYear = ' . $current_year . ';

                var selectedDateFormatted = selectedYear + "-" + (parseInt(selectedMonth) + 1) + "-" + selectedDate;

                if (selectedCheckInDate === null || selectedCheckOutDate !== null) {
                    selectedCheckInDate = selectedDateFormatted;
                    selectedCheckOutDate = null;
                    console.log("Check In: " + selectedCheckInDate);
                    this.style.backgroundColor = "#4681a9";
                    this.style.color = "white";
                } else if (selectedCheckInDate !== null || selectedCheckOutDate === null) {
                    selectedCheckOutDate = selectedDateFormatted;
                    console.log("Check Out: " + selectedCheckOutDate);
                    this.style.backgroundColor = "#4681a9";
                    this.style.color = "white";
                }
                dateBoxes.forEach(function (box) {
                    var boxDate = box.innerText.trim();
                    var boxMonth = box.closest(".calendar").getAttribute("data-month");
                    var boxYear = ' . $current_year . ';
                    var boxDateFormatted = boxYear + "-" + (parseInt(boxMonth) + 1) + "-" + boxDate;
                    console.log(boxDateFormatted);

                    if (boxDateFormatted > selectedCheckInDate && boxDateFormatted < selectedCheckOutDate) {
                        box.style.backgroundColor = "#4681a9";
                        box.style.color = "white";
                    }
                });
                
            });
        });

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
    echo '<script>
if (window.innerWidth >= 800) {
    document.getElementById("elementId").classList.add("hide-on_desktop");
}
</script>';
    echo '</div>';
    if (isset($_POST['submit_button_2'])) {
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=132";
        </script>
        <?php

        exit();
    }
}

function entire_manor_booking_function()
{

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset($_SESSION['stay_details'])) {
        $stay_details = $_SESSION['stay_details'];
        $checkIn = $stay_details->check_in;
        $checkOut = $stay_details->check_out;
        $no_of_adults = $stay_details->no_of_adults;
        $no_of_children = $stay_details->no_of_children;
        $nights = round(abs((strtotime($checkIn) - strtotime($checkOut)) / (24 * 60 * 60)));
        $month_checkin = date('M', strtotime($checkIn));
        $day_checkin = date('D', strtotime($checkIn));
        $date_checkin = date('d', strtotime($checkIn));
        $month_checkout = date('M', strtotime($checkOut));
        $day_checkout = date('D', strtotime($checkOut));
        $date_checkout = date('d', strtotime($checkOut));
        echo '<div class="booking">';
        echo '<div class="booking_items">';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK IN </h3><p>' . $day_checkin . ',' . $month_checkin . ' ' . $date_checkin . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK OUT</h3><p>' . $day_checkout . ',' . $month_checkout . ' ' . $date_checkout . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">NIGHTS</h3><p>' . $nights . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">ADULTS</h3><p>' . $no_of_adults . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHILDREN</h3><p>' . $no_of_children . '</p></span>';
        echo '</div>';
        echo '</div>';

        unset($_SESSION['stay']);
    }
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="booking_form">';
    echo '<div class="guest_details">';
    echo '<h5>Guest Details</h5>';
    echo '<div class="guest_details_label">';
    echo '<p class="booking_form_label">
             <label  for="first-name">Full Name</label>
             <input type="text"  name="first-name" placeholder="First Name" value="' . htmlspecialchars($_POST['first-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="last-name" class="hidden_label">Last Name</label>
             <input type="text"  name="last-name" placeholder="Last Name" value="' . htmlspecialchars($_POST['last-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="email">Email</label>
             <input type="email"  name="email" placeholder="Email" value="' . htmlspecialchars($_POST['email'] ?? '') . '" required>
          </p>';
    echo '<p class="booking_form_label">
             <label  for="phone">Phone</label>
             <input type="tel"  name="phone" placeholder="Phone" value="' . htmlspecialchars($_POST['phone'] ?? '') . '" required> 
          </p>';
    echo '<p class="booking_form_label">
             <label  for="address_line_1">Address</label>
             <input type="text"  name="address_line_1" placeholder="Address Line 1" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="name="address_line_2" class="hidden_label">Address</label>
             <input type="text"  name="address_line_2" placeholder="Address Line 2" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="city">City</label>
             <input type="text"  name="city" placeholder="Enter City" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="zipcode">Zipcode</label>
             <input type="number"  name="zip" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
          </p>';
    echo '</div>';
    echo '</div>';


    echo '<div class="coupon_box">';
    echo '<h5>Add Coupon If Any</h5>';
    echo '<p class="coupon">
             <label  for="coupon_code">Coupon Code:</label>
             <input class="text_item" type="text" name="coupon_code" value="' . htmlspecialchars($_POST['coupon_code'] ?? '') . '">
          </p>';
    echo '</div>';
    echo '</div>';
    echo '<p><input type="submit" name="submit" value="Calculate Price"></p>';
    echo '</form>';


    $price_per_night = get_option('entire_manor_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());


    if (isset($_POST['submit'])) {

        $check_in_date = $checkIn;
        $check_out_date = $checkOut;
        $adults = $no_of_adults;
        $children = $no_of_children;
        $firstname = sanitize_text_field($_POST['first-name']);
        $lastname = sanitize_text_field($_POST['last-name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $addline1 = sanitize_text_field($_POST['address_line_1']);
        $addline2 = sanitize_text_field($_POST['address_line_2']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $country = sanitize_text_field($_POST['country']);
        $zip = sanitize_text_field($_POST['zip']);


        if (!validate_dates($check_in_date, $check_out_date)) {
            return '<p class="error">Error in dates. Please enter valid dates.</p>';
        }

        $check_in_timestamp = strtotime($check_in_date);
        $check_out_timestamp = strtotime($check_out_date);

        $one_day = 24 * 60 * 60;
        $nights = round(abs(($check_in_timestamp - $check_out_timestamp) / $one_day));


        $total_price = $nights * $price_per_night;
        $price_after_added_fee = $total_price + $cleaning_fee;

        $coupon_code = sanitize_text_field($_POST['coupon_code']);

        if (!empty($coupon_code)) {
            foreach ($coupons as $cc) {
                if ($coupon_code === $cc['code']) {
                    $discount_amount = $cc['discount'] * $price_after_added_fee / 100;
                    $total_amount = $price_after_added_fee - $discount_amount;
                }
            }
        } else {
            $total_amount = $price_after_added_fee;
        }

        $booking_details = new stdClass();
        $booking_details->check_in_day = $day_checkin;
        $booking_details->check_in_date = $date_checkin;
        $booking_details->check_in_month = $month_checkin;
        $booking_details->check_out_day = $day_checkout;
        $booking_details->check_out_date = $date_checkout;
        $booking_details->check_out_month = $month_checkout;
        $booking_details->adults = $adults;
        $booking_details->children = $children;
        $booking_details->firstname = $firstname;
        $booking_details->lastname = $lastname;
        $booking_details->email = $email;
        $booking_details->phone = $phone;
        $booking_details->addline1 = $addline1;
        $booking_details->addline2 = $addline2;
        $booking_details->city = $city;
        $booking_details->state = $state;
        $booking_details->country = $country;
        $booking_details->zip = $zip;
        $booking_details->price_per_night = $price_per_night;
        $booking_details->nights = $nights;
        $booking_details->total_price = $total_price;
        $booking_details->cleaning_fee = $cleaning_fee;
        $booking_details->coupon_code = $coupon_code;
        $booking_details->discount_amount = isset($discount_amount) ? $discount_amount : 0;
        $booking_details->total_amount = $total_amount;

        $_SESSION['booking_details'] = $booking_details;
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=109";
        </script>
        <?php

        exit();
    }
}

function rooms_booking_function()
{
    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset($_SESSION['stay_details'])) {
        $stay_details = $_SESSION['stay_details'];
        $checkIn = $stay_details->check_in;
        $checkOut = $stay_details->check_out;
        $no_of_adults = $stay_details->no_of_adults;
        $no_of_children = $stay_details->no_of_children;
        $nights = round(abs((strtotime($checkIn) - strtotime($checkOut)) / (24 * 60 * 60)));
        $month_checkin = date('M', strtotime($checkIn));
        $day_checkin = date('D', strtotime($checkIn));
        $date_checkin = date('d', strtotime($checkIn));
        $month_checkout = date('M', strtotime($checkOut));
        $day_checkout = date('D', strtotime($checkOut));
        $date_checkout = date('d', strtotime($checkOut));
        echo '<div class="booking">';
        echo '<div class="booking_items">';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK IN </h3><p>' . $day_checkin . ',' . $month_checkin . ' ' . $date_checkin . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK OUT</h3><p>' . $day_checkout . ',' . $month_checkout . ' ' . $date_checkout . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">NIGHTS</h3><p>' . $nights . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">ADULTS</h3><p>' . $no_of_adults . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHILDREN</h3><p>' . $no_of_children . '</p></span>';
        echo '</div>';
        echo '</div>';

    }
    unset($_SESSION['stay']);
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="booking_form">';
    echo '<div class="guest_details">';
    echo '<h5>Guest Details</h5>';
    echo '<div class="guest_details_label">';
    echo '<p class="booking_form_label">
             <label  for="first-name">Full Name</label>
             <input type="text"  name="first-name" placeholder="First Name" value="' . htmlspecialchars($_POST['first-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="last-name" class="hidden_label">Last Name</label>
             <input type="text"  name="last-name" placeholder="Last Name" value="' . htmlspecialchars($_POST['last-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="email">Email</label>
             <input type="email"  name="email" placeholder="Email" value="' . htmlspecialchars($_POST['email'] ?? '') . '" required>
          </p>';
    echo '<p class="booking_form_label">
             <label  for="phone">Phone</label>
             <input type="tel"  name="phone" placeholder="Phone" value="' . htmlspecialchars($_POST['phone'] ?? '') . '" required> 
          </p>';
    echo '<p class="booking_form_label">
             <label  for="address_line_1">Address</label>
             <input type="text"  name="address_line_1" placeholder="Address Line 1" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="name="address_line_2" class="hidden_label">Address</label>
             <input type="text"  name="address_line_2" placeholder="Address Line 2" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="city">City</label>
             <input type="text"  name="city" placeholder="Enter City" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="zipcode">Zipcode</label>
             <input type="number"  name="zip" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
          </p>';
    echo '</div>';
    echo '</div>';


    echo '<div class="coupon_box">';
    echo '<h5>Add Coupon If Any</h5>';
    echo '<p class="coupon">
             <label  for="coupon_code">Coupon Code:</label>
             <input class="text_item" type="text" name="coupon_code" value="' . htmlspecialchars($_POST['coupon_code'] ?? '') . '">
          </p>';
    echo '</div>';
    echo '</div>';
    echo '<p><input type="submit" name="submit" value="Calculate Price"></p>';
    echo '</form>';


    $price_per_night = get_option('entire_manor_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());



    if (isset($_POST['submit'])) {

        $check_in_date = $checkIn;
        $check_out_date = $checkOut;
        $adults = $no_of_adults;
        $children = $no_of_children;
        $firstname = sanitize_text_field($_POST['first-name']);
        $lastname = sanitize_text_field($_POST['last-name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $addline1 = sanitize_text_field($_POST['address_line_1']);
        $addline2 = sanitize_text_field($_POST['address_line_2']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $country = sanitize_text_field($_POST['country']);
        $zip = sanitize_text_field($_POST['zip']);


        if (!validate_dates($check_in_date, $check_out_date)) {
            return '<p class="error">Error in dates. Please enter valid dates.</p>';
        }

        $check_in_timestamp = strtotime($check_in_date);
        $check_out_timestamp = strtotime($check_out_date);

        $one_day = 24 * 60 * 60;
        $nights = round(abs(($check_in_timestamp - $check_out_timestamp) / $one_day));


        $total_price = $nights * $price_per_night;
        $price_after_added_fee = $total_price + $cleaning_fee;

        $coupon_code = sanitize_text_field($_POST['coupon_code']);

        if (!empty($coupon_code)) {
            foreach ($coupons as $cc) {
                if ($coupon_code === $cc['code']) {
                    $discount_amount = $cc['discount'] * $price_after_added_fee / 100;
                    $total_amount = $price_after_added_fee - $discount_amount;
                }
            }
        } else {
            $total_amount = $price_after_added_fee;
        }



        $booking_details = new stdClass();
        $booking_details->check_in_day = $day_checkin;
        $booking_details->check_in_date = $date_checkin;
        $booking_details->check_in_month = $month_checkin;
        $booking_details->check_out_day = $day_checkout;
        $booking_details->check_out_date = $date_checkout;
        $booking_details->check_out_month = $month_checkout;
        $booking_details->adults = $adults;
        $booking_details->children = $children;
        $booking_details->firstname = $firstname;
        $booking_details->lastname = $lastname;
        $booking_details->email = $email;
        $booking_details->phone = $phone;
        $booking_details->addline1 = $addline1;
        $booking_details->addline2 = $addline2;
        $booking_details->city = $city;
        $booking_details->state = $state;
        $booking_details->country = $country;
        $booking_details->zip = $zip;
        $booking_details->price_per_night = $price_per_night;
        $booking_details->nights = $nights;
        $booking_details->total_price = $total_price;
        $booking_details->cleaning_fee = $cleaning_fee;
        $booking_details->coupon_code = $coupon_code;
        $booking_details->discount_amount = isset($discount_amount) ? $discount_amount : 0;
        $booking_details->total_amount = $total_amount;

        $_SESSION['booking_details'] = $booking_details;
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=109";
        </script>
        <?php

        exit();
    }
}

function cooking_class_booking_function()
{

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset($_SESSION['stay_details'])) {
        $stay_details = $_SESSION['stay_details'];
        $checkIn = $stay_details->check_in;
        $checkOut = $stay_details->check_out;
        $no_of_adults = $stay_details->no_of_adults;
        $no_of_children = $stay_details->no_of_children;
        $nights = round(abs((strtotime($checkIn) - strtotime($checkOut)) / (24 * 60 * 60)));
        $month_checkin = date('M', strtotime($checkIn));
        $day_checkin = date('D', strtotime($checkIn));
        $date_checkin = date('d', strtotime($checkIn));
        $month_checkout = date('M', strtotime($checkOut));
        $day_checkout = date('D', strtotime($checkOut));
        $date_checkout = date('d', strtotime($checkOut));
        echo '<div class="booking">';
        echo '<div class="booking_items">';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK IN </h3><p>' . $day_checkin . ',' . $month_checkin . ' ' . $date_checkin . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK OUT</h3><p>' . $day_checkout . ',' . $month_checkout . ' ' . $date_checkout . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">NIGHTS</h3><p>' . $nights . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">ADULTS</h3><p>' . $no_of_adults . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHILDREN</h3><p>' . $no_of_children . '</p></span>';
        echo '</div>';
        echo '</div>';

    }
    unset($_SESSION['stay']);
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="booking_form">';
    echo '<div class="guest_details">';
    echo '<h5>Guest Details</h5>';
    echo '<div class="guest_details_label">';
    echo '<p class="booking_form_label">
             <label  for="first-name">Full Name</label>
             <input type="text"  name="first-name" placeholder="First Name" value="' . htmlspecialchars($_POST['first-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="last-name" class="hidden_label">Last Name</label>
             <input type="text"  name="last-name" placeholder="Last Name" value="' . htmlspecialchars($_POST['last-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="email">Email</label>
             <input type="email"  name="email" placeholder="Email" value="' . htmlspecialchars($_POST['email'] ?? '') . '" required>
          </p>';
    echo '<p class="booking_form_label">
             <label  for="phone">Phone</label>
             <input type="tel"  name="phone" placeholder="Phone" value="' . htmlspecialchars($_POST['phone'] ?? '') . '" required> 
          </p>';
    echo '<p class="booking_form_label">
             <label  for="address_line_1">Address</label>
             <input type="text"  name="address_line_1" placeholder="Address Line 1" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="name="address_line_2" class="hidden_label">Address</label>
             <input type="text"  name="address_line_2" placeholder="Address Line 2" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="city">City</label>
             <input type="text"  name="city" placeholder="Enter City" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="zipcode">Zipcode</label>
             <input type="number"  name="zip" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
          </p>';
    echo '</div>';
    echo '</div>';


    echo '<div class="coupon_box">';
    echo '<h5>Add Coupon If Any</h5>';
    echo '<p class="coupon">
             <label  for="coupon_code">Coupon Code:</label>
             <input class="text_item" type="text" name="coupon_code" value="' . htmlspecialchars($_POST['coupon_code'] ?? '') . '">
          </p>';
    echo '</div>';
    echo '</div>';
    echo '<p><input type="submit" name="submit" value="Calculate Price"></p>';
    echo '</form>';


    $price_per_night = get_option('entire_manor_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());



    if (isset($_POST['submit'])) {

        $check_in_date = $checkIn;
        $check_out_date = $checkOut;
        $adults = $no_of_adults;
        $children = $no_of_children;
        $firstname = sanitize_text_field($_POST['first-name']);
        $lastname = sanitize_text_field($_POST['last-name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $addline1 = sanitize_text_field($_POST['address_line_1']);
        $addline2 = sanitize_text_field($_POST['address_line_2']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $country = sanitize_text_field($_POST['country']);
        $zip = sanitize_text_field($_POST['zip']);


        if (!validate_dates($check_in_date, $check_out_date)) {
            return '<p class="error">Error in dates. Please enter valid dates.</p>';
        }

        $check_in_timestamp = strtotime($check_in_date);
        $check_out_timestamp = strtotime($check_out_date);

        $one_day = 24 * 60 * 60;
        $nights = round(abs(($check_in_timestamp - $check_out_timestamp) / $one_day));


        $total_price = $nights * $price_per_night;
        $price_after_added_fee = $total_price + $cleaning_fee;

        $coupon_code = sanitize_text_field($_POST['coupon_code']);

        if (!empty($coupon_code)) {
            foreach ($coupons as $cc) {
                if ($coupon_code === $cc['code']) {
                    $discount_amount = $cc['discount'] * $price_after_added_fee / 100;
                    $total_amount = $price_after_added_fee - $discount_amount;
                }
            }
        } else {
            $total_amount = $price_after_added_fee;
        }



        $booking_details = new stdClass();
        $booking_details->check_in_day = $day_checkin;
        $booking_details->check_in_date = $date_checkin;
        $booking_details->check_in_month = $month_checkin;
        $booking_details->check_out_day = $day_checkout;
        $booking_details->check_out_date = $date_checkout;
        $booking_details->check_out_month = $month_checkout;
        $booking_details->adults = $adults;
        $booking_details->children = $children;
        $booking_details->firstname = $firstname;
        $booking_details->lastname = $lastname;
        $booking_details->email = $email;
        $booking_details->phone = $phone;
        $booking_details->addline1 = $addline1;
        $booking_details->addline2 = $addline2;
        $booking_details->city = $city;
        $booking_details->state = $state;
        $booking_details->country = $country;
        $booking_details->zip = $zip;
        $booking_details->price_per_night = $price_per_night;
        $booking_details->nights = $nights;
        $booking_details->total_price = $total_price;
        $booking_details->cleaning_fee = $cleaning_fee;
        $booking_details->coupon_code = $coupon_code;
        $booking_details->discount_amount = isset($discount_amount) ? $discount_amount : 0;
        $booking_details->total_amount = $total_amount;

        $_SESSION['booking_details'] = $booking_details;
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=109";
        </script>
        <?php

        exit();
    }

}

function entire_manor_cooking_class_booking_function()
{
    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset($_SESSION['stay_details'])) {
        $stay_details = $_SESSION['stay_details'];
        $checkIn = $stay_details->check_in;
        $checkOut = $stay_details->check_out;
        $no_of_adults = $stay_details->no_of_adults;
        $no_of_children = $stay_details->no_of_children;
        $nights = round(abs((strtotime($checkIn) - strtotime($checkOut)) / (24 * 60 * 60)));
        $month_checkin = date('M', strtotime($checkIn));
        $day_checkin = date('D', strtotime($checkIn));
        $date_checkin = date('d', strtotime($checkIn));
        $month_checkout = date('M', strtotime($checkOut));
        $day_checkout = date('D', strtotime($checkOut));
        $date_checkout = date('d', strtotime($checkOut));
        echo '<div class="booking">';
        echo '<div class="booking_items">';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK IN </h3><p>' . $day_checkin . ',' . $month_checkin . ' ' . $date_checkin . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHECK OUT</h3><p>' . $day_checkout . ',' . $month_checkout . ' ' . $date_checkout . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">NIGHTS</h3><p>' . $nights . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">ADULTS</h3><p>' . $no_of_adults . '</p></span>';
        echo '<span class="booking_items_label"><h3 class="booking_details_label">CHILDREN</h3><p>' . $no_of_children . '</p></span>';
        echo '</div>';
        echo '</div>';

    }
    unset($_SESSION['stay']);
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="booking_form">';
    echo '<div class="guest_details">';
    echo '<h5>Guest Details</h5>';
    echo '<div class="guest_details_label">';
    echo '<p class="booking_form_label">
             <label  for="first-name">Full Name</label>
             <input type="text"  name="first-name" placeholder="First Name" value="' . htmlspecialchars($_POST['first-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="last-name" class="hidden_label">Last Name</label>
             <input type="text"  name="last-name" placeholder="Last Name" value="' . htmlspecialchars($_POST['last-name'] ?? '') . '" required>
         </p>';
    echo '<p class="booking_form_label">
             <label  for="email">Email</label>
             <input type="email"  name="email" placeholder="Email" value="' . htmlspecialchars($_POST['email'] ?? '') . '" required>
          </p>';
    echo '<p class="booking_form_label">
             <label  for="phone">Phone</label>
             <input type="tel"  name="phone" placeholder="Phone" value="' . htmlspecialchars($_POST['phone'] ?? '') . '" required> 
          </p>';
    echo '<p class="booking_form_label">
             <label  for="address_line_1">Address</label>
             <input type="text"  name="address_line_1" placeholder="Address Line 1" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="name="address_line_2" class="hidden_label">Address</label>
             <input type="text"  name="address_line_2" placeholder="Address Line 2" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="booking_form_label">
             <label  for="city">City</label>
             <input type="text"  name="city" placeholder="Enter City" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="zipcode">Zipcode</label>
             <input type="number"  name="zip" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
          </p>';
    echo '</div>';
    echo '</div>';


    echo '<div class="coupon_box">';
    echo '<h5>Add Coupon If Any</h5>';
    echo '<p class="coupon">
             <label  for="coupon_code">Coupon Code:</label>
             <input class="text_item" type="text" name="coupon_code" value="' . htmlspecialchars($_POST['coupon_code'] ?? '') . '">
          </p>';
    echo '</div>';
    echo '</div>';
    echo '<p><input type="submit" name="submit" value="Calculate Price"></p>';
    echo '</form>';


    $price_per_night = get_option('entire_manor_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());



    if (isset($_POST['submit'])) {

        $check_in_date = $checkIn;
        $check_out_date = $checkOut;
        $adults = $no_of_adults;
        $children = $no_of_children;
        $firstname = sanitize_text_field($_POST['first-name']);
        $lastname = sanitize_text_field($_POST['last-name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $addline1 = sanitize_text_field($_POST['address_line_1']);
        $addline2 = sanitize_text_field($_POST['address_line_2']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $country = sanitize_text_field($_POST['country']);
        $zip = sanitize_text_field($_POST['zip']);


        if (!validate_dates($check_in_date, $check_out_date)) {
            return '<p class="error">Error in dates. Please enter valid dates.</p>';
        }

        $check_in_timestamp = strtotime($check_in_date);
        $check_out_timestamp = strtotime($check_out_date);

        $one_day = 24 * 60 * 60;
        $nights = round(abs(($check_in_timestamp - $check_out_timestamp) / $one_day));


        $total_price = $nights * $price_per_night;
        $price_after_added_fee = $total_price + $cleaning_fee;

        $coupon_code = sanitize_text_field($_POST['coupon_code']);

        if (!empty($coupon_code)) {
            foreach ($coupons as $cc) {
                if ($coupon_code === $cc['code']) {
                    $discount_amount = $cc['discount'] * $price_after_added_fee / 100;
                    $total_amount = $price_after_added_fee - $discount_amount;
                }
            }
        } else {
            $total_amount = $price_after_added_fee;
        }



        $booking_details = new stdClass();
        $booking_details->check_in_day = $day_checkin;
        $booking_details->check_in_date = $date_checkin;
        $booking_details->check_in_month = $month_checkin;
        $booking_details->check_out_day = $day_checkout;
        $booking_details->check_out_date = $date_checkout;
        $booking_details->check_out_month = $month_checkout;
        $booking_details->adults = $adults;
        $booking_details->children = $children;
        $booking_details->firstname = $firstname;
        $booking_details->lastname = $lastname;
        $booking_details->email = $email;
        $booking_details->phone = $phone;
        $booking_details->addline1 = $addline1;
        $booking_details->addline2 = $addline2;
        $booking_details->city = $city;
        $booking_details->state = $state;
        $booking_details->country = $country;
        $booking_details->zip = $zip;
        $booking_details->price_per_night = $price_per_night;
        $booking_details->nights = $nights;
        $booking_details->total_price = $total_price;
        $booking_details->cleaning_fee = $cleaning_fee;
        $booking_details->coupon_code = $coupon_code;
        $booking_details->discount_amount = isset($discount_amount) ? $discount_amount : 0;
        $booking_details->total_amount = $total_amount;

        $_SESSION['booking_details'] = $booking_details;
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=109";
        </script>
        <?php

        exit();
    }

}

function booking_details()
{
    session_start();

    if (isset($_SESSION['booking_details'])) {
        $booking_details = $_SESSION['booking_details'];
        echo '<div class="print_details_form">';
        echo '<div class="booking_details">';
        echo '<h3>Booking details</h3>';
        echo '<div class="booking_details_1">';
        echo '<p><span class="label">Check-in Date:</span> <span class="value">' . $booking_details->check_in_day . ', ' . $booking_details->check_in_month . ' ' . $booking_details->check_in_date . '</span></p>';
        echo '<p><span class="label">Check-out Date:</span> <span class="value">' . $booking_details->check_out_day . ', ' . $booking_details->check_out_month . ' ' . $booking_details->check_out_date . '</span></p>';
        echo '</div>';
        echo '<div class="booking_details_1">';
        echo '<p><span class="label">Adults:</span> <span class="value">' . $booking_details->adults . '</span></p>';
        echo '<p><span class="label">Children:</span> <span class="value">' . $booking_details->children . '</span></p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="booked_by_info_box">';
        echo '<h3>Booked By</h3>';
        echo '<div class="booked_by">';
        echo '<p><span class="label">Name:</span> <span class="value">' . $booking_details->firstname . ' ' . $booking_details->lastname . '</span></p>';
        echo '<p><span class="label">Email:</span> <span class="value">' . $booking_details->email . '</span></p>';
        echo '<p><span class="label">Phone:</span> <span class="value">' . $booking_details->phone . '</span></p>';
        echo '</div>';

        echo '<div class="booked_by">';
        echo '<p><span class="label">Address:</span> <span class="value">' . $booking_details->addline1 . '</span></p>';
        echo '<p><span class="label"></span> <span class="value">' . $booking_details->addline2 . '</span></p>';
        echo '<p><span class="label"></span> <span class="value">' . $booking_details->city . ' , ' . $booking_details->state . '</span></p>';
        echo '<p><span class="label"></span> <span class="value">' . $booking_details->country . ' , ' . $booking_details->zip . '</span></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="price_breakup_info_box">';
        echo '<h3 class="price_breakup">Price Breakup</h3>';
        echo '<div class="price_breakup">';
        echo '<p><span class="label">Price per night:</span> <span class="value">$' . $booking_details->price_per_night . '</span></p>';
        echo '<p><span class="label">Price for ' . $booking_details->nights . ' nights:</span> <span class="value">$' . $booking_details->total_price . '</span></p>';
        echo '<p><span class="label">Cleaning Fee:</span> <span class="value">$' . $booking_details->cleaning_fee . '</span></p>';
        if (!empty($booking_details->coupon_code)) {
            echo '<p><span class="label">Discount applied:</span> <span class="value">-$' . $booking_details->discount_amount . '</span></p>';
        }
        echo '<h4><span class="label_total">Total amount:</span> <span class="value_total">$' . $booking_details->total_amount . '</span></h4>';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        unset($_SESSION['booking_details']);

    } else {
        echo '<h5>No booking details found.</h5>';
    }

    echo '<form method="post">';
    echo '<button class="payment">Payment</button>';
    echo '</form>';
}

function validate_dates($check_in_date, $check_out_date)
{
    if (empty($check_in_date) || empty($check_out_date)) {
        return false;
    }

    return true;
}



function booking_plugin_menu()
{
    add_menu_page(
        'Booking Plugin Settings',
        'Booking Plugin',
        'manage_options',
        'booking-plugin',
        'booking_plugin_settings_page',
        plugin_dir_url(__FILE__) . 'assets/images/A.png'
    );
}

add_action('admin_menu', 'booking_plugin_menu');


function booking_plugin_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Booking Plugin Settings</h2>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields('booking_plugin_settings');
            do_settings_sections('booking_plugin_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


function booking_plugin_settings()
{
    register_setting('booking_plugin_settings', 'entire_manor_price');
    register_setting('booking_plugin_settings', 'rooms_price');
    register_setting('booking_plugin_settings', 'cooking_class_price');
    register_setting('booking_plugin_settings', 'entire_manor_cooking_class_price');
    register_setting('booking_plugin_settings', 'cleaning_fee');
    register_setting('booking_plugin_settings', 'booking_coupon_data');
    register_setting('booking_plugin_settings', 'upload_csv');

    add_settings_section('booking_plugin_main_section', 'Main Settings', 'booking_plugin_section_callback', 'booking_plugin_settings');

    add_settings_field('entire_manor_price_field', 'Entire Manor', 'entire_manor_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('rooms_price_field', 'Rooms', 'rooms_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('cooking_class_price_field', 'Cooking Class', 'cooking_class_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('entire_manor_cooking_class_price_field', 'Entire Manor + Cooking Class', 'entire_manor_cooking_class_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('cleaning_fee_field', 'Miscellaneous Fee', 'cleaning_fee_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('booking_field_coupons', 'Coupon Codes and Discount Prices', 'booking_field_coupons_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('upload_csv_field', 'Price Sheet (CSV)', 'upload_csv_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
}

function booking_plugin_section_callback()
{
    echo 'Enter your booking settings below:';
}

function entire_manor_price_field_callback()
{
    $Entire_Manor = get_option('entire_manor_price');
    echo '<input type="text" name="entire_manor_price" value="' . esc_attr($Entire_Manor) . '" />';
}

function rooms_price_field_callback()
{
    $Rooms = get_option('rooms_price');
    echo '<input type="text" name="rooms_price" value="' . esc_attr($Rooms) . '" />';
}

function cooking_class_price_field_callback()
{
    $Cooking_Class = get_option('cooking_class_price');
    echo '<input type="text" name="cooking_class_price" value="' . esc_attr($Cooking_Class) . '" />';
}

function entire_manor_cooking_class_price_field_callback()
{
    $Entire_Manor_Cooking_Class = get_option('entire_manor_cooking_class_price');
    echo '<input type="text" name="entire_manor_cooking_class_price" value="' . esc_attr($Entire_Manor_Cooking_Class) . '" />';
}

function cleaning_fee_callback()
{
    $Miscellaneous_fee = get_option('cleaning_fee');
    echo '<input type="text" name="cleaning_fee" value="' . esc_attr($Miscellaneous_fee) . '" />';
}

function booking_field_coupons_callback()
{
    $coupon_data = get_option('booking_coupon_data', array());

    echo '<table id="coupon-data-table">';
    echo '<tr><th>Coupon Code</th><th>Discount Percentage</th></tr>';

    foreach ($coupon_data as $index => $data) {
        echo '<tr>';
        echo '<td><input type="text" name="booking_coupon_data[' . $index . '][code]" value="' . esc_attr($data['code']) . '" /></td>';
        echo '<td><input type="text" name="booking_coupon_data[' . $index . '][discount]" value="' . esc_attr($data['discount']) . '" /></td>';
        echo '<td><button class="remove-coupon-button" type="button">Remove</button></td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<button class="add-coupon-button" type="button">Add Coupon</button>';

    // JavaScript for adding and removing coupon fields dynamically
    ?>
    <script>     jQuery(document).ready(function ($) { $('.add-coupon-button').click(function () { var index = $('#coupon-data-table tr').length - 1; $('#coupon-data-table').append('<tr>' + '<td><input type="text" name="booking_coupon_data[' + index + '][code]" /></td>' + '<td><input type="text" name="booking_coupon_data[' + index + '][discount]" /></td>' + '<td><button class="remove-coupon-button" type="button">Remove</button></td>' + '</tr>'); }); $(document).on('click', '.remove-coupon-button', function () { $(this).closest('tr').remove(); }); });
    </script>
    <?php
}

function upload_csv_field_callback()
{
    ?>
    <form id="csv-upload-form" method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" value="' . esc_attr($CSV_File) . '" />
        <input type="submit" name="submit_csv" value="Upload" />
        <div id="upload-message"></div>
    </form>
    <?php
    handle_file_upload();
}
function handle_file_upload()
{
    if (isset($_POST['submit_csv'])) {
        echo 'BlaaaBlee';
        $uploaded_file = $_FILES['csv_file'];
        $upload_dir = wp_upload_dir();
        $target_file = $upload_dir['path'] . '/' . basename($uploaded_file['name']);
        $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

        if (strtolower($file_type) !== 'csv') {
            echo 'Please upload a CSV file.';
            return;
        }

        if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
            echo 'File uploaded successfully.';
        } else {
            echo 'Error uploading file.';
        }
        return true;
    }
}
add_action('admin_init', 'booking_plugin_settings');