<?php
/**
 * Plugin Name: Booking (Part 1)
 * Description: Calculates hotel booking price .
 * Version: 1.0
 * Author: Amritha Sabu
 */



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


add_shortcode('entire_manor', 'entire_manor_booking_function');
add_shortcode('rooms', 'rooms_booking_function');
add_shortcode('cooking_class', 'cooking_class_booking_function');
add_shortcode('enitre_manor_cooking_class', 'entire_manor_cooking_class_booking_function');
add_shortcode('display', 'booking_details');

add_action('wp_enqueue_scripts', 'enqueue_hotel_booking_styles');


function enqueue_hotel_booking_styles()
{
    wp_enqueue_style('hotel-booking-styles', plugin_dir_url(__FILE__) . 'hotel-price-calculator.css');
}

function entire_manor_booking_function()
{

    echo '<script src="' . plugin_dir_url(__FILE__) . 'form_handling.js"></script>';
    echo '<div class="heading">';
    echo '<h1>Review Your Booking</h1>';
    echo '</div>';
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="stay_details">';
    echo '<h5>Stay Details</h5>';
    echo '<div class="stay_details_form">';
    echo '<label  for="check_in_date">Check-in Date:</label>';
    echo '<input type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>';

    echo '<label  for="check_out_date">Check-out Date:</label>';
    echo '<input type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>';

    $option_adlt = array('1', '2', ' 3', '4', '5');
    echo '<label for="no-of-adults">Adults</label>';
    echo '<select class="booking_dropdown" id="no-of-adults" name="no-of-adults" value="' . htmlspecialchars($_POST['no-of-adults'] ?? '') . '">';
    foreach ($option_adlt as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';


    $option_chil = array('0', '1', '2', ' 3', '4', '5');
    echo '<label for="no-of-children">Children</label>';
    echo '<select class="booking_dropdown" id="no-of-children" name="no-of-children" value="' . htmlspecialchars($_POST['no-of-children'] ?? '') . '">';
    foreach ($option_chil as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '</div>';

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

             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>

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

        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $adults = sanitize_text_field($_POST['no-of-adults']);
        $children = sanitize_text_field($_POST['no-of-children']);
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
        $booking_details->check_in_date = $check_in_date;
        $booking_details->check_out_date = $check_out_date;
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
    echo '<h1>Review Your Booking</h1>';
    echo '</div>';
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="stay_details">';
    echo '<h5>Stay Details</h5>';
    echo '<div class="stay_details_form">';
    echo '<label  for="check_in_date">Check-in Date:</label>';
    echo '<input type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>';

    echo '<label  for="check_out_date">Check-out Date:</label>';
    echo '<input type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>';

    $option_adlt = array('1', '2', ' 3', '4', '5');
    echo '<label for="no-of-adults">Adults</label>';
    echo '<select class="booking_dropdown" id="no-of-adults" name="no-of-adults" value="' . htmlspecialchars($_POST['no-of-adults'] ?? '') . '">';
    foreach ($option_adlt as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';


    $option_chil = array('0', '1', '2', ' 3', '4', '5');
    echo '<label for="no-of-children">Children</label>';
    echo '<select class="booking_dropdown" id="no-of-children" name="no-of-children" value="' . htmlspecialchars($_POST['no-of-children'] ?? '') . '">';
    foreach ($option_chil as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '</div>';

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

             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>

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


    $price_per_night = get_option('rooms_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());



    if (isset($_POST['submit'])) {

        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $adults = sanitize_text_field($_POST['no-of-adults']);
        $children = sanitize_text_field($_POST['no-of-children']);
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
        $booking_details->check_in_date = $check_in_date;
        $booking_details->check_out_date = $check_out_date;
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
    echo '<h1>Review Your Booking</h1>';
    echo '</div>';
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="stay_details">';
    echo '<h5>Stay Details</h5>';
    echo '<div class="stay_details_form">';
    echo '<label  for="check_in_date">Check-in Date:</label>';
    echo '<input type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>';

    echo '<label  for="check_out_date">Check-out Date:</label>';
    echo '<input type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>';

    $option_adlt = array('1', '2', ' 3', '4', '5');
    echo '<label for="no-of-adults">Adults</label>';
    echo '<select class="booking_dropdown" id="no-of-adults" name="no-of-adults" value="' . htmlspecialchars($_POST['no-of-adults'] ?? '') . '">';
    foreach ($option_adlt as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';


    $option_chil = array('0', '1', '2', ' 3', '4', '5');
    echo '<label for="no-of-children">Children</label>';
    echo '<select class="booking_dropdown" id="no-of-children" name="no-of-children" value="' . htmlspecialchars($_POST['no-of-children'] ?? '') . '">';
    foreach ($option_chil as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '</div>';

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

             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>

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


    $price_per_night = get_option('cooking_class_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());



    if (isset($_POST['submit'])) {

        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $adults = sanitize_text_field($_POST['no-of-adults']);
        $children = sanitize_text_field($_POST['no-of-children']);
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
        $booking_details->check_in_date = $check_in_date;
        $booking_details->check_out_date = $check_out_date;
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
    echo '<h1>Review Your Booking</h1>';
    echo '</div>';
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<div class="stay_details">';
    echo '<h5>Stay Details</h5>';
    echo '<div class="stay_details_form">';
    echo '<label  for="check_in_date">Check-in Date:</label>';
    echo '<input type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>';

    echo '<label  for="check_out_date">Check-out Date:</label>';
    echo '<input type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>';

    $option_adlt = array('1', '2', ' 3', '4', '5');
    echo '<label for="no-of-adults">Adults</label>';
    echo '<select class="booking_dropdown" id="no-of-adults" name="no-of-adults" value="' . htmlspecialchars($_POST['no-of-adults'] ?? '') . '">';
    foreach ($option_adlt as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';


    $option_chil = array('0', '1', '2', ' 3', '4', '5');
    echo '<label for="no-of-children">Children</label>';
    echo '<select class="booking_dropdown" id="no-of-children" name="no-of-children" value="' . htmlspecialchars($_POST['no-of-children'] ?? '') . '">';
    foreach ($option_chil as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '</div>';

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

             <label  for="state">State/Province</label>
             <input type="text"  name="state" placeholder="Enter State" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>
             </p>';
    echo '<p class="booking_form_label">
             <label  for="country">Country</label>
             <input type="text"  name="country" placeholder="Enter Country" value="' . htmlspecialchars($_POST['address'] ?? '') . '" required>

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


    $price_per_night = get_option('entire_manor_cooking_class_price');
    $cleaning_fee = get_option('cleaning_fee');
    $coupons = get_option('booking_coupon_data', array());


    if (isset($_POST['submit'])) {

        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $adults = sanitize_text_field($_POST['no-of-adults']);
        $children = sanitize_text_field($_POST['no-of-children']);
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
        $booking_details->check_in_date = $check_in_date;
        $booking_details->check_out_date = $check_out_date;
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
        echo '<div class="hotel_booking_info_box">';
        echo '<div class="booking_details">';
        echo '<h3>Booking details</h3>';
        echo '<p><span class="label">Check-in Date:</span> <span class="value">' . $booking_details->check_in_date . '</span></p>';
        echo '<p><span class="label">Check-out Date:</span> <span class="value">' . $booking_details->check_out_date . '</span></p>';
        echo '<p><span class="label">Adults:</span> <span class="value">' . $booking_details->adults . '</span></p>';
        echo '<p><span class="label">Children:</span> <span class="value">' . $booking_details->children . '</span></p>';
        echo '</div>';
        //echo '<div class="booking_details">';
        //echo '<img src="wp-content\plugins\Booking\\assets\images\coop.jpg" alt="Haygood Manor">';
        //echo '<h3>Haygood Manor<h3>';
        //echo '</div>';
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
        echo '<h2><span class="label_total">Total amount:</span> <span class="value_total">$' . $booking_details->total_amount . '</span></h2>';
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
        plugin_dir_url(__FILE__) . 'assets/images/bookingicon.png'
    );
}

add_action('admin_menu', 'booking_plugin_menu');


function booking_plugin_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Booking Plugin Settings</h2>
        <form method="post" action="options.php">
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

    add_settings_section('booking_plugin_main_section', 'Main Settings', 'booking_plugin_section_callback', 'booking_plugin_settings');

    add_settings_field('entire_manor_price_field', 'Entire Manor', 'entire_manor_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('rooms_price_field', 'Rooms', 'rooms_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('cooking_class_price_field', 'Cooking Class', 'cooking_class_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('entire_manor_cooking_class_price_field', 'Entire Manor + Cooking Class', 'entire_manor_cooking_class_price_field_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('cleaning_fee_field', 'Miscellaneous Fee', 'cleaning_fee_callback', 'booking_plugin_settings', 'booking_plugin_main_section');
    add_settings_field('booking_field_coupons', 'Coupon Codes and Discount Prices', 'booking_field_coupons_callback', 'booking_plugin_settings', 'booking_plugin_main_section');


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


add_action('admin_init', 'booking_plugin_settings');