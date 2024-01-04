<?php
/**
 * Plugin Name: Booking
 * Description: Calculates hotel booking price .
 * Version: 1.0
 * Author: Amritha Sabu
 */



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


add_shortcode('hotel_booking_price', 'booking_information_function');

add_action('wp_enqueue_scripts', 'enqueue_hotel_booking_styles');


function enqueue_hotel_booking_styles()
{
    wp_enqueue_style('hotel-booking-styles', plugin_dir_url(__FILE__) . 'hotel-price-calculator.css');
}


function booking_information_function()
{


    $entire_manor_price = get_option('entire_manor_price');
    $room_price = get_option('rooms_price');
    $cooking_class_price = get_option('cooking_class_price');
    $manor_and_cooking = get_option('entire_manor_cooking_class_price');


    echo '<h2>Review your Booking</h2>';
    echo '<script src="C:\Users\H.P\Local Sites\test-site\app\public\wp-content\plugins\Booking\form_handling.js"></script>';
    echo '<div class="booking-options">';
    echo '<button class="booking-option" data-option="entire_manor">Entire Manor</button>';
    echo '<button class="booking-option" data-option="rooms">Rooms</button>';
    echo '<button class="booking-option" data-option="cooking_classes">Cooking Classes</button>';
    echo '<button class="booking-option" data-option="entire_manor_cooking_classes">Entire Manor + Cooking Classes</button>';
    echo '</div>';

    echo '<input type="hidden" id="selected_option" name="selected_option" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '">';

    echo '<div class="hotel-booking-form">';
    echo '<div class="inner-div">';
    echo '<h5></h5>';
    echo '<form method="post" onsubmit="saveFormData()">';
    echo '<p id="hotel-booking-form label">
             <label  for="check_in_date">Check-in Date:</label>
             <input class="hotel-booking-date" type="date" id="check_in_date" name="check_in_date" value="' . htmlspecialchars($_POST['check_in_date'] ?? '') . '" required>
             
             <label  for="check_out_date">Check-out Date:</label>
             <input class="hotel-booking-date" type="date" id="check_out_date" name="check_out_date" value="' . htmlspecialchars($_POST['check_out_date'] ?? '') . '" required>
          </p>';


    $options = array('1', '2', ' 3', '4', '5');
    echo '<label class="hotel-booking-dropdown" for="no-of-adults">Adults</label>';
    echo '<select id="no-of-adults" name="no-of-adults" class="dropdown-menu" value="' . htmlspecialchars($_POST['no-of-adults'] ?? '') . '">';
    foreach ($options as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';


    $options = array('0', '1', '2', ' 3', '4', '5');
    echo '<label class="hotel-booking-dropdown" for="no-of-children">Children</label>';
    echo '<select id="no-of-children" name="no-of-children" class="dropdown-menu" value="' . htmlspecialchars($_POST['no-of-children'] ?? '') . '">';
    foreach ($options as $option) {
        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }
    echo '</select>';
    echo '</div>';


    echo '<div class="inner-div">';
    echo '<h5>Guest Details</h5>';
    echo '<p class="customer-info">
             <label  for="first-last-name">Full Name</label>
             <input class="text-item" type="text"  name="first-name" placeholder="First Name" value="' . htmlspecialchars($_POST['first-name'] ?? '') . '" required>
             <input class="text-item" type="text"  name="last-name" placeholder="Last Name" value="' . htmlspecialchars($_POST['last-name'] ?? '') . '" required>
 
             <label  for="email">Email</label>
             <input class="text-item" type="email"  name="email" placeholder="Email" value="' . htmlspecialchars($_POST['email'] ?? '') . '" required>
 
             <label  for="phone">Phone</label>
             <input class="text-item" type="tel"  name="phone" placeholder="Phone" value="' . htmlspecialchars($_POST['phone'] ?? '') . '" required>
 
          </p>';
    echo '</div>';


    echo '<div class="inner-div">';
    echo '<h5>Add Coupon If Any</h5>';
    echo '<p id="hotel-booking-form label">
             <label  for="coupon_code">Coupon Code:</label>
             <input class="hotel-booking-text" type="text" id="coupon_code" name="coupon_code" value="' . htmlspecialchars($_POST['coupon_code'] ?? '') . '">
          </p>';
    echo '</div>';


    echo '<p><input type="submit" name="submit" value="Calculate Price"></p>';
    echo '</form>';
    echo '</div>';



    if (isset($_POST['submit'])) {

        $check_in_date = sanitize_text_field($_POST['check_in_date']);
        $check_out_date = sanitize_text_field($_POST['check_out_date']);
        $adults = sanitize_text_field($_POST['no-of-adults']);
        $children = sanitize_text_field($_POST['no-of-children']);
        $firstname = sanitize_text_field($_POST['first-name']);
        $lastname = sanitize_text_field($_POST['last-name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $selected_option = sanitize_text_field($_POST['selected_option']);

        $price_per_night = match ($selected_option) {
            'entire_manor' => $entire_manor_price,
            'rooms' => $room_price,
            'cooking_classes' => $cooking_class_price,
            'entire_manor_cooking_classes' => $manor_and_cooking,
            default => 0,
        };
        $cleaning_fee = get_option('cleaning_fee');
        $coupons = get_option('booking_coupon_data', array());



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
        $booking_details->price_per_night = $price_per_night;
        $booking_details->nights = $nights;
        $booking_details->total_price = $total_price;
        $booking_details->cleaning_fee = $cleaning_fee;
        $booking_details->coupon_code = $coupon_code;
        $booking_details->discount_amount = isset($discount_amount) ? $discount_amount : 0;
        $booking_details->total_amount = $total_amount;

        $_SESSION['booking_details'] = $booking_details;


        ?>
        <script>
            window.location.href = "http://localhost:10004/?page_id=160";
        </script>
        <?php

        exit();

    }

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
    <script>
        jQuery(document).ready(function ($) { $('.add-coupon-button').click(function () { var index = $('#coupon-data-table tr').length - 1; $('#coupon-data-table').append('<tr>' + '<td><input type="text" name="booking_coupon_data[' + index + '][code]" /></td>' + '<td><input type="text" name="booking_coupon_data[' + index + '][discount]" /></td>' + '<td><button class="remove-coupon-button" type="button">Remove</button></td>' + '</tr>'); }); $(document).on('click', '.remove-coupon-button', function () { $(this).closest('tr').remove(); }); });
    </script>
    <?php
}


add_action('admin_init', 'booking_plugin_settings');