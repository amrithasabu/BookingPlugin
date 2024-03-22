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


add_shortcode('sign_in', 'sign_in_function');
add_shortcode('stay_options', 'stay_options_function');
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

function sign_in_function()
{
    echo '<h1>Join HayGood Manor</h1>';
    echo '<form method="post">';
    echo '<div>';
    echo '<p class="">
             <label  for="first-name">First Name</label>
             <input type="text"  name="first-name" placeholder="First Name" value="' . htmlspecialchars($_POST['first-name'] ?? '') . '" required>
         </p>';
    echo '<p class="">
             <label  for="first-name">Last Name</label>
             <input type="text"  name="last-name" placeholder="Last Name" value="' . htmlspecialchars($_POST['last-name'] ?? '') . '" required>
         </p>';
    echo '<p class="">
             <label  for="email">Email</label>
             <input type="email"  name="email" placeholder="Email" value="' . htmlspecialchars($_POST['email'] ?? '') . '" required>
          </p>';
    echo '<p class="">
             <label  for="phone">Phone</label>
             <input type="tel"  name="phone" placeholder="Phone" value="' . htmlspecialchars($_POST['phone'] ?? '') . '" required> 
          </p>';
    echo '<p class="">
             <label  for="address_line_1">Address</label>
             <input type="text"  name="address_line_1" placeholder="Address Line 1" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="">
             <label  for="name="address_line_2" class="hidden_label">Address</label>
             <input type="text"  name="address_line_2" placeholder="Address Line 2" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
            </p>';
    echo '<p class="">
             <label  for="city">City</label>
             <input type="text"  name="city" placeholder="Enter City" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="">
             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="">
             <label  for="zipcode">Zipcode</label>
             <input type="number"  name="zip" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
          </p>';
    echo '<p class="">
             <label  for="password">Password</label>
             <input type="text" pattern="[a-zA-Z0-9\s\-_.,!@#$%^&*()+=?<>{}[\]:;|\/\\]*"  name="pass" value="' . htmlspecialchars($_POST['password'] ?? '') . '" required>
          </p>';
    echo '<p class="">
             <label  for="confirm-pass">Confirm Password</label>
             <input type="text" pattern="[a-zA-Z0-9\s\-_.,!@#$%^&*()+=?<>{}[\]:;|\/\\]*"  name="confirm-pass" value="' . htmlspecialchars($_POST['confirm-password'] ?? '') . '" required>
          </p>';
    echo '</div>';
    echo '</form>';


    $hostname = "localhost";
    $username = "root";
    $password = "root";
    $database = "local";
    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die ("Connection Failed:" . mysqli_connect_errno());
    }

    $insert_query = "INSERT INTO `users` (`Unique ID`,`Name`,`Email`,`Phone`,`Address`,`Password`) 
                VALUES (?,?,?,?,?,?)";
    $stmt = mysqli_stmt_init($conn);

    if (!$stmt) {
        die ("Statement initialization failed: " . mysqli_error($conn));
    }

    if (!mysqli_stmt_prepare($stmt, $insert_query)) {
        die ("Statement preparation failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_bind_param($stmt, "sssiss", $UID, $FullName, $email, $phone, $Address, $password);
    if (!mysqli_stmt_execute($stmt)) {
        die ("Statement execution failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

}

function stay_options_function()
{
    echo '<body style="background-color: #ffffff;">';
    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';
    $Entire_Manor = get_option('entire_manor_price');
    $Cooking_Class = get_option('cooking_class_price');
    $Entire_Manor_Cooking_Class = get_option('entire_manor_cooking_class_price');
    $entire_manor_dir = plugin_dir_url(__FILE__) . 'assets/images/manor.jpg';
    $cooking_dir = plugin_dir_url(__FILE__) . 'assets/images/cooking.jpg';
    $room_dir = plugin_dir_url(__FILE__) . 'assets/images/room.jpg';
    echo '<div class="stay-options">';
    echo '<div class="stay-options-heading">';
    echo '<h1> Stay Options <h1>';
    echo '</div>';
    echo '<br>';

    echo '<div class="entire-manor">';
    echo '<img src="' . $entire_manor_dir . '" alt="HayGood Manor Image" class="image">';
    echo '<div class="content">';
    echo '<h2 class="heading-stay"> Entire Manor <h2>';
    echo '<p class="para">HayGood Manor - Historic Victorian plantation house built - 1883.
            Access to a private and personalized experience, ideal for gatherings, events, retreats, or simply enjoying a luxurious vacation with family and friends. <p>';
    //echo '<p class="para">$' . $Entire_Manor . '<p>';
    echo '<form class="form" method="post">';
    echo '<input type="submit" name="submit_1" value="View Rates"/>';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    echo '<div class="cooking-class">';
    echo '<img src="' . $entire_manor_dir . '" alt="HayGood Manor Image" class="image">';
    echo '<div class="content">';
    echo '<h2 class="heading-stay"> Cooking Class <h2>';
    echo '<p class="para">Embark on a culinary journey with our specialized cooking classes at the HG Cooking Institute.<p>';
    echo '<br>';
    echo '<br>';
    //echo '<p class="para">$' . $Cooking_Class . '<p>';
    echo '<form class="form" method="post">';
    echo '<input type="submit" name="submit_2" value="View Rates"/>';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    echo '<div class="entiremanor-cookingclass">';
    echo '<img src="' . $entire_manor_dir . '" alt="HayGood Manor Image" class="image">';
    echo '<div class="content">';
    echo '<h2 class="heading-stay">Entire Mnaor + Cooking Class <h2>';
    echo '<p class="para">description
            <br><p>';
    echo '<br>';
    echo '<br>';
    //echo '<p class="para">$' . $Entire_Manor_Cooking_Class . '<p>';
    echo '<form class="form" method="post">';
    echo '<input type="submit" name="submit_3" value="View Rates"/>';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    echo '<div class="rooms">';
    echo '<img src="' . $room_dir . '" alt="HayGood Manor Image" class="image">';
    echo '<div class="content">';
    echo '<h2 class="heading-stay"> Rooms <h2>';
    echo '<p class="para">description<p>';
    echo '<br>';
    echo '<br>';
    //echo '<p class="para">$' . $Entire_Manor . '<p>';
    echo '<form class="form" method="post">';
    echo '<input type="submit" name="submit_4" value="View Rates"/>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</body>';

    if (isset ($_POST['submit_1'])) {
        $choice = 1;
        $redirect_url = 'http://localhost:10010/?page_id=234&value=' . urlencode($choice);
        ?>
        <script>     window.location.href = "<?php echo $redirect_url; ?>";
        </script>
        <?php
        exit();
    } else if (isset ($_POST['submit_2'])) {
        $choice = 2;
        $redirect_url = 'http://localhost:10010/?page_id=234&value=' . urlencode($choice);
        ?>
            <script>     wi    ndow.location.href = "<?php echo $redirect_url; ?>";
            </script>
            <?php
            exit();
    } else if (isset ($_POST['submit_3'])) {
        $choice = 3;
        $redirect_url = 'http://localhost:10010/?page_id=234&value=' . urlencode($choice);
        ?>
                <script>     window.location.href = "<?php echo $redirect_url; ?>";
                </script>
            <?php
            exit();
    } else if (isset ($_POST['submit_4'])) {
        $choice = 4;
        $redirect_url = 'http://localhost:10010/?page_id=234&value=' . urlencode($choice);
        ?>
                    <script>     window.location.href = "<?php echo $redirect_url; ?>";
                    </script>
            <?php
            exit();
    }
}

function stay_dates_function()
{
    $hostname = "localhost";
    $username = "root";
    $password = "root";
    $database = "local";
    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die ("Connection Failed:" . mysqli_connect_errno());
    }

    $select_query = "SELECT * FROM `booking_details`";
    $result = mysqli_query($conn, $select_query);

    if (!$result) {
        die ("Error retrieving data: " . mysqli_error($conn));
    }
    $bookings = array();
    while ($row = mysqli_fetch_assoc($result)) {
        //$bookings[] = $row;
        $booked_check_in = $row['Check In'];
        $booked_check_out = $row['Check Out'];
        $bookings[] = array(
            "check_in" => $booked_check_in,
            "check_out" => $booked_check_out
        );
        //var_dump($bookings);
    }

    $currentDate = date('Y-n-j');


    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';
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
    document.addEventListener("DOMContentLoaded", function () {
        var bookedDates = <?php echo json_encode($bookings); ?>;
        var checkInDate = document.getElementById("check_in_date");
        var checkOutDate = document.getElementById("check_out_date");
    
        checkInDate.addEventListener("change", function () {
            checkOutDate.setAttribute("min", checkInDate.value);
            disableBookedDates(checkInDate.value, checkOutDate.value);
        });
    
        checkOutDate.addEventListener("change", function () {
            disableBookedDates(checkInDate.value, checkOutDate.value);
        });
    
        function disableBookedDates(checkIn, checkOut) {
            var datesToDisable = [];
            bookedDates.forEach(function (booking) {
                var bookingStart = new Date(booking.check_in);
                var bookingEnd = new Date(booking.check_out);
    
                var currentDate = new Date(checkIn);
                while (currentDate <= bookingEnd) {
                    if (currentDate >= bookingStart && currentDate <= bookingEnd) {
                        datesToDisable.push(currentDate.toISOString().split("T")[0]);
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
            });
    
            var datePicker = document.getElementById("check_in_date");
            var disabledDates = datesToDisable.join(",");
            datePicker.setAttribute("disabledDates", disabledDates);
        }
    });
    </script>';
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


    if (isset ($_POST['submit'])) {
        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $no_of_adults = sanitize_text_field($_POST['no_of_adults']);
        $no_of_children = sanitize_text_field($_POST['no_of_children']);
        $checkInDate = intval(date('j', strtotime($check_in_date)));
        $checkOutDate = intval(date('j', strtotime($check_out_date)));
        $check_in_month = date('F', strtotime($check_in_date));
    }

    /*$hostname = "localhost";
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
     $conn->close();*/

    if (isset ($_POST['submit'])) {
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
    if (isset ($_GET['value'])) {
        $received_value = $_GET['value'];
        if ($received_value == 1) {
            $filename = plugin_dir_url(__FILE__) . '/assets/Prices/EntireManorPriceList.csv';
        } else if ($received_value == 2) {
            $filename = plugin_dir_url(__FILE__) . '/assets/Prices/CookingClassPriceList.csv';
        } else if ($received_value == 3) {
            $filename = plugin_dir_url(__FILE__) . '/assets/Prices/EntireManor+CookingClass.csv';
        } else if ($received_value == 4) {
            $filename = plugin_dir_url(__FILE__) . '/assets/Prices/Rooms.csv';
        }
    }

    $filename = plugin_dir_url(__FILE__) . 'EntireManorPriceList.csv';
    $data = [];
    $f = fopen($filename, 'r');

    if ($f === false) {
        die ('Cannot open the file ' . $filename);
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

            if (isset ($_SESSION['stay_details'])) {
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
                            } elseif (isset ($_POST['submit']) && ($dayCount >= $checkInDate) && ($dayCount <= $checkOutDate) && ($m === $checkInMonth)) {
                                if (isset ($data[$index])) {
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
                                if (isset ($data[$index])) {
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
                                if (isset ($data[$index])) {
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
    if (isset ($_POST['submit_button_2'])) {
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=132";
        </script>
        <?php

        exit();
    }
}
function hotel_rates_calendar_mobile($selectedMonth = null)
{
    $filename = plugin_dir_url(__FILE__) . 'EntireManorPriceList.csv';
    $data = [];
    $f = fopen($filename, 'r');

    if ($f === false) {
        die ('Cannot open the file ' . $filename);
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

    echo '<div class="month_buttons_container_mobile hide-on-desktop">';
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
    echo '<div class="calendar_container_mobile hide-on-desktop">';
    foreach ($months as $mIndex => $m) {
        if ($mIndex >= $current_month_index) {
            echo '<div class="calendar_mobile hide-on-desktop" data-month="' . $mIndex . '" style="display: ' . ($selectedMonth == $mIndex ? 'block' : 'none') . ';">';
            echo '<h3 class="hide-on-desktop">' . $m . ' ' . $current_year . '</h3>';
            echo '<table>';
            echo '<thead class="hide-on-desktop">';
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
            echo '<tbody class="hide-on-desktop">';

            $firstDay = date('w', strtotime("$current_year-$m-01"));
            $daysInMonth = date('t', strtotime("$current_year-$m-01"));
            $currentDay = intval(date('j', strtotime($current_date)));
            $dayCount = 1;
            session_start();

            if (isset ($_SESSION['stay_details'])) {
                $stay_details = $_SESSION['stay_details'];
                $checkInDate = $stay_details->check_in_date;
                $checkOutDate = $stay_details->check_out_date;
                $checkInMonth = $stay_details->check_in_month;
                $A = substr($checkInDate, 0, 3);
                $B = substr($checkOutDate, 0, 3);
                $C = substr($checkInMonth, 0, 3);

                for ($i = 0; $i < 6; $i++) {
                    echo '<tr class="hide-on-desktop">';

                    for ($j = 0; $j < 7; $j++) {
                        if (($i === 0 && $j < $firstDay) || $dayCount > $daysInMonth) {
                            echo '<td ></td>';
                        } else {
                            if (($m === date('M')) && ($dayCount < $currentDay)) {
                                echo '<td class="past_days_mobile">';
                                echo $dayCount . '<br>';
                                echo '</td>';
                            } elseif (isset ($_POST['submit']) && ($dayCount >= $A) && ($dayCount <= $B) && ($m === $C)) {
                                if (isset ($data[$index])) {
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
                                if (isset ($data[$index])) {
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
                echo '<p><input type="submit" name="submit_button_2" class="submit_button_mobile_2 hide-on-desktop" value="Book Now "/></p>';
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
                                if (isset ($data[$index])) {
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
                echo '<div class="hide-on-desktop">';
                echo '<form method="post" class="hide-on-desktop">';
                echo '<p><input type="submit" name="submit_button_2" class="submit_button_mobile_2" value="Book Now "/></p>';
                echo '</form>';
                echo '</div>';
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
    if (isset ($_POST['submit_button_2'])) {
        ?>
        <script>     window.location.href = "http://localhost:10010/?page_id=132";
        </script>
        <?php

        exit();
    }
}

function entire_manor_booking_function()
{

    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset ($_SESSION['stay_details'])) {
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


    if (isset ($_POST['submit'])) {

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

        if (!empty ($coupon_code)) {
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
        $booking_details->check_in = $checkIn;
        $booking_details->check_out = $checkOut;
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
        $booking_details->discount_amount = isset ($discount_amount) ? $discount_amount : 0;
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
    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset ($_SESSION['stay_details'])) {
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



    if (isset ($_POST['submit'])) {

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

        if (!empty ($coupon_code)) {
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
        $booking_details->discount_amount = isset ($discount_amount) ? $discount_amount : 0;
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
    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset ($_SESSION['stay_details'])) {
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



    if (isset ($_POST['submit'])) {

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

        if (!empty ($coupon_code)) {
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
        $booking_details->discount_amount = isset ($discount_amount) ? $discount_amount : 0;
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
    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h2>Review Your Booking</h2>';
    echo '</div>';

    session_start();

    if (isset ($_SESSION['stay_details'])) {
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



    if (isset ($_POST['submit'])) {

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

        if (!empty ($coupon_code)) {
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
        $booking_details->check_in = $checkIn;
        $booking_details->check_out = $checkOut;
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
        $booking_details->discount_amount = isset ($discount_amount) ? $discount_amount : 0;
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
    echo '<div class="haygood-manor-heading">';
    echo '<h1>HayGood Manor</h1>';
    echo '</div>';

    session_start();

    if (isset ($_SESSION['booking_details'])) {
        $booking_details = $_SESSION['booking_details'];
        $checkIn = $booking_details->check_in;
        $checkOut = $booking_details->check_out;
        $no_of_adults = $booking_details->adults;
        $no_of_children = $booking_details->children;
        $fname = $booking_details->firstname;
        $lname = $booking_details->lastname;
        $email = $booking_details->email;
        $phone = $booking_details->phone;
        $addline1 = $booking_details->addline1;
        $addline2 = $booking_details->addline2;
        $city = $booking_details->city;
        $state = $booking_details->state;
        $country = $booking_details->country;
        $zip = $booking_details->zip;
        $booking_id = uniqid();
        $FullName = $fname . " " . $lname;
        $Address = $addline1 . " " . $addline2 . " " . $city . " " . $state . " " . $country . " " . $zip;
        echo '<div class="print_details_form">';
        echo '<div class="booking_details">';
        echo '<h3>Booking details</h3>';
        echo '<div class="booking_details_1">';
        echo '<p><span class="label">Check-in Date:</span> <span class="value">' . $booking_details->check_in_day . ', ' . $booking_details->check_in_month . ' ' . $booking_details->check_in_date . '</span></p>';
        echo '<p><span class="label">Check-out Date:</span> <span class="value">' . $booking_details->check_out_day . ', ' . $booking_details->check_out_month . ' ' . $booking_details->check_out_date . '</span></p>';
        echo '</div>';
        echo '<div class="booking_details_1">';
        echo '<p><span class="label">Adults:</span> <span class="value">' . $no_of_adults . '</span></p>';
        echo '<p><span class="label">Children:</span> <span class="value">' . $no_of_children . '</span></p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="booked_by_info_box">';
        echo '<h3>Booked By</h3>';
        echo '<div class="booked_by">';
        echo '<p><span class="label">Name:</span> <span class="value">' . $fname . ' ' . $lname . '</span></p>';
        echo '<p><span class="label">Email:</span> <span class="value">' . $email . '</span></p>';
        echo '<p><span class="label">Phone:</span> <span class="value">' . $phone . '</span></p>';
        echo '</div>';

        echo '<div class="booked_by">';
        echo '<p><span class="label">Address:</span> <span class="value">' . $addline1 . '</span></p>';
        echo '<p><span class="label"></span> <span class="value">' . $addline2 . '</span></p>';
        echo '<p><span class="label"></span> <span class="value">' . $city . ' , ' . $state . '</span></p>';
        echo '<p><span class="label"></span> <span class="value">' . $country . ' , ' . $zip . '</span></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="price_breakup_info_box">';
        echo '<h3 class="price_breakup">Price Breakup</h3>';
        echo '<div class="price_breakup">';
        echo '<p><span class="label">Price per night:</span> <span class="value">$' . $booking_details->price_per_night . '</span></p>';
        echo '<p><span class="label">Price for ' . $booking_details->nights . ' nights:</span> <span class="value">$' . $booking_details->total_price . '</span></p>';
        echo '<p><span class="label">Cleaning Fee:</span> <span class="value">$' . $booking_details->cleaning_fee . '</span></p>';
        if (!empty ($booking_details->coupon_code)) {
            echo '<p><span class="label">Discount applied:</span> <span class="value">-$' . $booking_details->discount_amount . '</span></p>';
        }
        echo '<h4><span class="label_total">Total amount:</span> <span class="value_total">$' . $booking_details->total_amount . '</span></h4>';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        $hostname = "localhost";
        $username = "root";
        $password = "root";
        $database = "local";
        $conn = mysqli_connect($hostname, $username, $password, $database);
        if (mysqli_connect_errno()) {
            die ("Connection Failed:" . mysqli_connect_errno());
        }

        $insert_query = "INSERT INTO `booking_details` (`Booking ID`,`Name`,`Email`,`Phone`,`Address`,`Check In`,`Check Out`,`Adults`,`Children`) 
                VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_stmt_init($conn);

        if (!$stmt) {
            die ("Statement initialization failed: " . mysqli_error($conn));
        }

        if (!mysqli_stmt_prepare($stmt, $insert_query)) {
            die ("Statement preparation failed: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_bind_param($stmt, "sssisssss", $booking_id, $FullName, $email, $phone, $Address, $checkIn, $checkOut, $no_of_adults, $no_of_children);
        if (!mysqli_stmt_execute($stmt)) {
            die ("Statement execution failed: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);


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
    if (empty ($check_in_date) || empty ($check_out_date)) {
        return false;
    }
    return true;
}

function booking_plugin_menu()
{
    add_menu_page(
        'Booking Settings',
        'Booking Plugin',
        'manage_options',
        'booking-plugin',
        'booking_plugin_settings_page',
        'dashicons-calendar'
    );
}

add_action('admin_menu', 'booking_plugin_menu');

function booking_plugin_settings_page()
{
    ?>
    <div class="accom-title">
        <table class="styled-table">
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                settings_fields('booking_plugin_settings');
                do_settings_sections('booking_plugin_settings');
                echo '<input type="submit" name="submit_button" value="Save"/>';
                //submit_button();
                ?>
            </form>
        </table>
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

    add_settings_section(
        'booking_plugin_main_section',
        'Main Settings',
        'booking_plugin_section_callback',
        'booking_plugin_settings'
    );

    add_settings_field(
        'entire_manor_price_field',
        'Entire Manor',
        'entire_manor_price_field_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section'
    );
    add_settings_field(
        'rooms_price_field',
        'Rooms',
        'rooms_price_field_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section'
    );
    add_settings_field(
        'cooking_class_price_field',
        'Cooking Class',
        'cooking_class_price_field_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section'
    );
    add_settings_field(
        'entire_manor_cooking_class_price_field',
        'Entire Manor + Cooking Class',
        'entire_manor_cooking_class_price_field_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section'
    );
    add_settings_field(
        'cleaning_fee_field',
        'Miscellaneous Fee',
        'cleaning_fee_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section'
    );
    add_settings_field(
        'booking_field_coupons',
        'Coupon Codes and Discount Prices',
        'booking_field_coupons_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section'
    );
    /*add_settings_field(
        'upload_csv_field',
        'Price Sheet (CSV)',
        'upload_csv_field_callback',
        'booking_plugin_settings',
        'booking_plugin_main_section',
    );*/
}

function booking_plugin_section_callback()
{
    echo 'Enter your booking settings below:';
}

function entire_manor_price_field_callback()
{
    // $Entire_Manor = get_option('entire_manor_price');
    //echo '<input type="text" name="entire_manor_price" value="' . esc_attr($Entire_Manor) . '" />';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>';
    ?>
    <input type="file" id="csv-file-1" accept=".csv">
    <script>
        document.getElementById("csv-file-1").addEventListener("change", function (event) {
            var file = event.target.files[0];
            if (!file) {
                return;
            }

            Papa.parse(file, {
                header: false,
                complete: function (results) {
                    // CSV data is parsed, results.data contains the parsed data
                    console.log("Parsed CSV data:", results.data);
                }
            });
            var formData = new FormData();
            formData.append('file', file);

            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/wp-admin/admin-ajax.php?action=upload_file');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('File uploaded successfully');
                } else {
                    console.log('Error uploading file');
                }
            };
            xhr.send(formData);
        });

    </script>
    <?php
}

function rooms_price_field_callback()
{
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>';
    ?>
    <input type="file" id="csv-file-2" accept=".csv">
    <script>
        document.getElementById("csv-file-2").addEventListener("change", function (event) {
            var file = event.target.files[0];
            if (!file) {
                return;
            }

            Papa.parse(file, {
                header: false,
                complete: function (results) {
                    // CSV data is parsed, results.data contains the parsed data
                    console.log("Parsed CSV data:", results.data);
                }
            });
            var formData = new FormData();
            formData.append('file', file);

            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/wp-admin/admin-ajax.php?action=upload_file');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('File uploaded successfully');
                } else {
                    console.log('Error uploading file');
                }
            };
            xhr.send(formData);
        });

    </script>
    <?php
}

function cooking_class_price_field_callback()
{
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>';
    ?>
    <input type="file" id="csv-file-3" accept=".csv">
    <script>
        document.getElementById("csv-file-3").addEventListener("change", function (event) {
            var file = event.target.files[0];
            if (!file) {
                return;
            }

            Papa.parse(file, {
                header: false,
                complete: function (results) {
                    // CSV data is parsed, results.data contains the parsed data
                    console.log("Parsed CSV data:", results.data);
                }
            });
            var formData = new FormData();
            formData.append('file', file);

            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/wp-admin/admin-ajax.php?action=upload_file');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('File uploaded successfully');
                } else {
                    console.log('Error uploading file');
                }
            };
            xhr.send(formData);
        });

    </script>
    <?php
}

function entire_manor_cooking_class_price_field_callback()
{
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>';
    ?>
    <input type="file" id="csv-fil-4" accept=".csv">
    <script>
        document.getElementById("csv-fil-4").addEventListener("change", function (event) {
            var file = event.target.files[0];
            if (!file) {
                return;
            }

            Papa.parse(file, {
                header: false,
                complete: function (results) {
                    // CSV data is parsed, results.data contains the parsed data
                    console.log("Parsed CSV data:", results.data);
                }
            });
            var formData = new FormData();
            formData.append('file', file);

            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/wp-admin/admin-ajax.php?action=upload_file');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('File uploaded successfully');
                } else {
                    console.log('Error uploading file');
                }
            };
            xhr.send(formData);
        });

    </script>
    <?php
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
    echo '<tr>
            <th>Coupon Code</th>
            <th>Discount Percentage</th>
        </tr>';

    foreach ($coupon_data as $index => $data) {
        echo '<tr>';
        echo '<td><input type="text" name="booking_coupon_data[' . $index . '][code]"
                    value="' . esc_attr($data['code']) . '" /></td>';
        echo '<td><input type="text" name="booking_coupon_data[' . $index . '][discount]"
                    value="' . esc_attr($data['discount']) . '" /></td>';
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


/*function upload_csv_field_callback()
{
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>';
    ?>
    <input type="file" id="csv-file" accept=".csv">
    <script>
        document.getElementById("csv-file").addEventListener("change", function (event) {
            var file = event.target.files[0];
            if (!file) {
                return;
            }

            Papa.parse(file, {
                header: false,
                complete: function (results) {
                    // CSV data is parsed, results.data contains the parsed data
                    console.log("Parsed CSV data:", results.data);
                }
            });
            var formData = new FormData();
            formData.append('file', file);

            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/wp-admin/admin-ajax.php?action=upload_file');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('File uploaded successfully');
                } else {
                    console.log('Error uploading file');
                }
            };
            xhr.send(formData);
        });

    </script>
    <?php
}*/

add_action('wp_ajax_upload_file', 'upload_file_handler');
add_action('wp_ajax_nopriv_upload_file', 'upload_file_handler');

function upload_file_handler()
{
    if (!empty ($_FILES['file'])) {
        $file = $_FILES['file'];
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/Complete Booking Plugin/assets/Prices';
        $upload_path = $upload_dir . '/' . $file['name'];

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // File uploaded successfully
            echo 'File uploaded successfully';
        } else {
            // Error uploading file
            echo 'Error uploading file';
        }
    } else {
        // No file uploaded
        echo 'No file uploaded';
    }
    wp_die();
}


add_action('admin_init', 'booking_plugin_settings');
function booking_add_customer_details_submenu()
{
    add_submenu_page(
        'booking-plugin',
        'Customer Details',
        'Customer Details',
        'manage_options',
        'customer-details',
        'booking_customer_details_page'
    );
}
add_action('admin_menu', 'booking_add_customer_details_submenu');

function booking_customer_details_page($selectedMonth = NULL)
{
    echo '<body style=" background-color: rgba(255, 255, 255, 0.992); max-width: 1600px;">';
    $currentDay = intval(date('j', strtotime(date('Y-n-j'))));
    $currmonth = date('F');
    $current_month = date('n') - 1;
    $current_year = date('Y');
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
    $hostname = "localhost";
    $username = "root";
    $password = "root";
    $database = "local";
    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die ("Connection Failed:" . mysqli_connect_errno());
    }

    $select_query = "SELECT * FROM `booking_details`";
    $result = mysqli_query($conn, $select_query);

    if (!$result) {
        die ("Error retrieving data: " . mysqli_error($conn));
    }
    $bookings = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }

    echo '<head> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-...">
    </head>';
    echo '<div class="header">';
    echo '<h1 class="heading"> HayGood Manor Bookings </h1>';
    echo '<div class="month-buttons-container">';
    foreach ($months as $monthIndex => $month) {
        if ($monthIndex >= $current_month_index) {
            $active = $selectedMonth === $monthIndex ? 'active' : '';
            echo '<button class="month-button ' . $active . '" data-month="' . $monthIndex . '">' . $month . ' ' . $current_year . '</button>';
        }
    }
    echo '</div>';
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
            $colors = array(
                "#b75353",
                "#5e5ebe",
                "#6cab6c",
                "#cba55d",
                "#ca6f74",
            );
            $dayCount = 1;
            $c = 0;
            if ($c >= 5) {
                $c = 0;
            }
            for ($i = 0; $i < 6; $i++) {
                echo '<tr>';
                for ($j = 0; $j < 7; $j++) {
                    if (($i === 0 && $j < $firstDay) || $dayCount > $daysInMonth) {
                        echo '<td  class="empty"></td>';
                    } else {
                        if (($m === date('F')) && ($dayCount < $currentDay)) {
                            echo '<td class="past-days">';
                            echo $dayCount . '<br>';
                            echo '<br>';
                            echo '</td>';
                            $dayCount++;
                        } else {
                            $bookingFound = false;
                            $weekend = false;
                            foreach ($bookings as $booking) {
                                $booked_check_in = intval(date('j', strtotime($booking['Check In'])));
                                $booked_check_out = intval(date('j', strtotime($booking['Check Out'])));
                                $booked_check_in_month = date('F', strtotime($booking['Check In']));

                                if (($booked_check_in >= $currentDay) && ($dayCount >= $booked_check_in) && ($dayCount <= $booked_check_out) && ($m === $booked_check_in_month)) {
                                    $colspan = min($booked_check_out - $dayCount + 1, 7 - $j);
                                    if ((7 - $j) < ($booked_check_out - $dayCount + 1)) {
                                        $weekend = true;
                                    }

                                    echo '<td class="booked-days" colspan="' . $colspan . '">';
                                    echo $dayCount . '<br>';
                                    echo '<h1 style="background-color: ' . $colors[$c] . ';">';
                                    echo '#' . $booking['Booking ID'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $booking['Name'];
                                    echo '</h1>';
                                    echo '</td>';

                                    $dayCount += $colspan;
                                    $j += ($colspan - 1);
                                    $bookingFound = true;
                                    if (!$weekend) {
                                        $c++;
                                    }
                                }
                            }
                            if ((!$bookingFound) && ($dayCount < $daysInMonth)) {
                                echo '<td class="unselcted-days">';
                                echo $dayCount . '<br>';
                                echo '</td>';
                                $dayCount++;
                            }
                        }
                    }
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
    }

    mysqli_free_result($result);
    mysqli_close($conn);

    echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
    echo '<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                var currentMonthIndex = ' . $current_month . ';
                var calendars = document.querySelectorAll(".calendar");
                calendars.forEach(function (calendar) {
                    var calendarMonth = calendar.getAttribute("data-month");
                    if (calendarMonth == currentMonthIndex) {
                        calendar.style.display = "block";
                    } else {
                        calendar.style.display = "none";
                    }
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
    echo '<head> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-...">
</head>';

    echo '<div class="customer-details">';
    echo '<div class="booking-heading">';
    echo '<h1>Bookings</h1>';
    echo '</div>';
    echo '<div class="past-bookings">';
    echo '<h1><i class="far fa-calendar-alt custom-icon"></i>  Past Bookings</h1>';
    echo '</div>';
    $hostname = "localhost";
    $username = "root";
    $password = "root";
    $database = "local";
    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die ("Connection Failed:" . mysqli_connect_errno());
    }

    $select_query = "SELECT * FROM `booking_details`";
    $result = mysqli_query($conn, $select_query);

    if (!$result) {
        die ("Error retrieving data: " . mysqli_error($conn));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $bookingID = $row['Booking ID'];
        $one_day = 24 * 60 * 60;
        $nights = round(abs((strtotime($row['Check Out']) - strtotime($row['Check In'])) / $one_day));
        $people = $row['Adults'] + $row['Children'];
        if (($row['Check In']) <= (date('Y-m-d'))) {
            echo '<div class="booking" id="booking-' . $bookingID . '">';
            echo '<p>' . $nights . '  <i class="fas fa-moon"></i></p>';
            echo '<p>' . date('M', strtotime($row['Check In'])) . " " . date('j', strtotime($row['Check In'])) . "-" . date('M', strtotime($row['Check Out'])) . " " . date('j', strtotime($row['Check Out'])) . '</p>';
            echo '<p>' . $people . ' <i class="fas fa-users"></i></p>';
            echo '<p>' . $row['Name'] . '</p>';

            echo '<div class="additional-details" style="display: none;">';
            echo '<p>Booking ID: ' . '#' . $bookingID . '</p>';
            echo '<p>Name: ' . $row['Name'] . '</p>';
            echo '<p>Email: ' . $row['Email'] . '</p>';
            echo '<p>Phone:' . $row['Phone'] . '</p>';
            echo '<p>Address:' . $row['Address'] . '</p>';
            echo '<p>Stay:' . date('M', strtotime($row['Check In'])) . " " . date('j', strtotime($row['Check In'])) . "-" . date('M', strtotime($row['Check Out'])) . " " . date('j', strtotime($row['Check Out'])) . '</p>';
            echo '<p>Adults:' . $row['Adults'] . '</p>';
            echo '<p>Children:' . $row['Children'] . '</p>';
            echo '</div>';
            echo '</div>';
        }
    }
    mysqli_free_result($result);
    mysqli_close($conn);
    echo '<div class="upcoming-bookings">';
    echo '<h1><i class="far fa-calendar-alt custom-icon"></i>   Upcoming Bookings</h1>';
    echo '</div>';
    $hostname = "localhost";
    $username = "root";
    $password = "root";
    $database = "local";
    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (mysqli_connect_errno()) {
        die ("Connection Failed:" . mysqli_connect_errno());
    }

    $select_query = "SELECT * FROM `booking_details`";
    $result = mysqli_query($conn, $select_query);

    if (!$result) {
        die ("Error retrieving data: " . mysqli_error($conn));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $bookingID = $row['Booking ID'];
        $one_day = 24 * 60 * 60;
        $nights = round(abs((strtotime($row['Check Out']) - strtotime($row['Check In'])) / $one_day));
        $people = $row['Adults'] + $row['Children'];
        if (($row['Check In']) > (date('Y-m-d'))) {
            echo '<div class="booking" id="booking-' . $bookingID . '">';
            echo '<p>' . $nights . '  <i class="fas fa-moon"></i></p>';
            echo '<p>' . date('M', strtotime($row['Check In'])) . " " . date('j', strtotime($row['Check In'])) . "-" . date('M', strtotime($row['Check Out'])) . " " . date('j', strtotime($row['Check Out'])) . '</p>';
            echo '<p>' . $people . ' <i class="fas fa-users"></i></p>';
            echo '<p>' . $row['Name'] . '</p>';

            echo '<div class="additional-details" style="display: none;">';
            echo '<p>Booking ID: ' . '#' . $bookingID . '</p>';
            echo '<p>Name: ' . $row['Name'] . '</p>';
            echo '<p>Email: ' . $row['Email'] . '</p>';
            echo '<p>Phone:' . $row['Phone'] . '</p>';
            echo '<p>Address:' . $row['Address'] . '</p>';
            echo '<p>Stay:' . date('M', strtotime($row['Check In'])) . " " . date('j', strtotime($row['Check In'])) . "-" . date('M', strtotime($row['Check Out'])) . " " . date('j', strtotime($row['Check Out'])) . '</p>';
            echo '<p>Adults:' . $row['Adults'] . '</p>';
            echo '<p>Children:' . $row['Children'] . '</p>';
            echo '</div>';
            echo '</div>';
        }
    }
    mysqli_free_result($result);
    mysqli_close($conn);
    echo '</div>';

    echo '<style>';
    echo '.booking {';
    echo 'overflow: hidden;';
    echo 'transition: height 0.3s ease;';
    echo '}';
    echo '</style>';

    echo '<script>';
    echo 'document.addEventListener("DOMContentLoaded", function() {';

    echo 'function handleBookingClick(booking) {';
    echo 'var additionalDetails = booking.querySelector(".additional-details");';
    echo 'if (additionalDetails.style.display === "none" || additionalDetails.style.display === "") {';
    echo 'additionalDetails.style.display = "block";';
    echo 'booking.style.height = (additionalDetails.scrollHeight) + "px";';
    echo '} else {';
    echo 'additionalDetails.style.display = "none";';
    echo 'booking.style.height = "auto";';
    echo '}';
    echo '}';

    echo 'var bookings = document.querySelectorAll(".booking");';
    echo 'bookings.forEach(function(booking) {';
    echo 'booking.addEventListener("click", function() {';
    echo 'handleBookingClick(this);';
    echo '});';
    echo '});';

    echo '});';
    echo '</script>';

    echo '<style>
    .header{
        width: 1600px;
        box-shadow: 0px 2px 4px rgba(178, 214, 233, 0.757);
        margin-left:-15px;
        padding-bottom:10px;
    }
    .heading{
        font-family: "Libre Baskerville", serif ;
        background-image: linear-gradient(to right,#497fcf2f,#497fcf6d,#497fcfdd);
        -webkit-background-clip: text;
        color: transparent;
        line-height:80px;
       //display: inline-block;
        text-align: center;
        font-size: 50px;
    }
    .month-buttons-container {
        gap: 10px;
        display: flex;
        justify-content: center;
        margin: 0; 
        padding: 0;
        margin: 50px 30px 50px 30px;
        padding: 0px 30px 0px 30px;
    }
    .month-button {
        background-color: rgba(70, 129, 169, 0.703);
        color: #ffffff;
        border: solid;
        border-color:#004573; 
        padding: 0.5rem 1rem;
        margin: 0 0.2rem;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }
    .month-button:hover {
        background-color: #4681a9;
        color: #ffffff; 
    }
    .month-button.active {
        border: solid;
        border-color:#000000; 
        background-color: #4681a9;
        color: #ffffff;
    }
    .view{
        display: flex;
        width: 1600px;
        box-shadow: 0px 2px 4px rgba(178, 214, 233, 0.757);
        margin-left:-15px;
        padding-bottom:10px;
    }
    .view-heading{
        font-family: "Libre Baskerville", serif ;
        background-image: linear-gradient(to right,#497fcf2f,#497fcf6d,#497fcfdd);
        -webkit-background-clip: text;
        color: transparent;
        line-height:40px;
        display: inline-block;
        text-align: center;
        font-size: 30px;
        cursor:pointer;
    }
    .calendar h3{
        font-family: "Libre Baskerville", serif ;
        color: #333;
        font-size: 30px;
        margin-left: 500px;
    }
    .calendar-container{
        margin-left: 15px;
        display: flex;
        flex-direction: row;
    }
    .calendar table{
        width: 1050px;
        border:  2px solid #497fcfb1;
        border-radius: 15px;
    }
    .calendar th{
        font-family: "Libre Baskerville", serif ;
        width: 80px;
        height: 30px;
        border: 1px solid #cccccc;
        padding: 5px;
        text-align: center;
        font-size: 20px;
    }
    .calendar td{
        width: 80px;
        height: 80px;
        text-align: start;
        border: 1px solid #cccccc;
        padding: 5px;
    }
    .past-days{
        background-color: rgba(70, 129, 169, 0.489);
        color: #000000;
    }
    .booked-days h1{
        font-family: "Libre Baskerville", serif ;
        font-size:18px;
        line-height:30px;
        color: #ffffff;
        border-radius:10px;
        text-align:center;
    }
    .unselcted-days{
        //background-color: rgba(70, 129, 169, 0.403);
    }
    .customer-details{
        margin-left: 20px;
        width: 500px;
        border: 2px solid rgba(178, 214, 233, 0.757);
        //box-shadow: -4px 0px 2px rgba(178, 214, 233, 0.757);
    }
    .booking-heading{
        margin:0px;
        margin-top:-25px;
        width: 500px;
        background-color: rgba(178, 214, 233, 0.757);
        box-shadow: 0px 2px 2px rgba(178, 214, 233, 0.757);
        padding-bottom:10px;
    }
    .booking-heading h1{
        //margin:0px;
        padding:0px;
        width:100%;
        font-family: "Libre Baskerville", serif ;
        color: #080808;
        font-size: 40px;
        margin-left:100px;
        padding-top: 30px;
    }
    .booking{
        cursor: pointer;
        height: 50px;
        border-bottom: 5px solid #b2d6e9c1;
        //border-bottom-width: 50%;
        padding: 8px;
        padding-left: 8px;
        text-align: left;
    }
    .booking p{
        margin-right: 25px;
        display: inline-block;
        //border-right: 2px solid #b2d6e9c1;
        padding-right: 10px;
        font-family: "Libre Baskerville", serif ;
        font-size: 20px;
    }
    .additional-details{
        padding: 20px;
        background-color: rgba(178, 214, 233, 0.203);
    }
    .additional-details p{
        font-family: "Libre Baskerville", serif ;
        font-size: 20px;
    }
    .past-bookings{
        border-bottom: 5px solid #0099eb85;
    }
    .past-bookings h1{
        padding:0px;
        width:100%;
        font-family: "Libre Baskerville", serif ;
        color: #080808;
        font-size: 30px;
        margin-left:30px;
        padding-top: 20px;
    }
    .upcoming-bookings {
        border-bottom: 5px solid #0099eb85;
    }
    .upcoming-bookings h1{
        padding:0px;
        width:100%;
        font-family: "Libre Baskerville", serif ;
        color: #080808;
        font-size: 30px;
        margin-left:30px;
        padding-top: 20px;
    }
    .custom-icon{
        font-size: 24px;
        padding: 8px;
        background-color: rgba(178, 214, 233, 0.757);
        color:white;
        border: 1px solid white;
        border-radius: 20px;
    }
    </style>';
}