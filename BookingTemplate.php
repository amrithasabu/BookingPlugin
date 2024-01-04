<?php
/*
Template Name: Booking Template
*/


session_start();

if (isset($_SESSION['booking_details'])) {
    $booking_details = $_SESSION['booking_details'];

    echo '<style>';
    echo '.body {
        background-color: #e0e0e0;
      }';
    echo '.hotel-booking-form {     
            display: flex;
            flex-direction: column;
            align-items: center;
        
     }';
    echo '.inner-div { 
            width: 950px;
            padding: 20px;
            box-shadow: 7px 7px 10px  #484747;
    }';
    echo '.hotel-booking-info-box {     
            box-shadow: 0 0 10px  rgba(0, 0, 0, 0.1);
            font-family:Georgia;
            font-size: 20px;
            font-weight: 400 normal;
            display: inline-block;
            text-align: left;
            width: 600px;
            padding: 50px;
            margin: 10px 0;
            margin-bottom: 5px;
    }';
    echo '.h5 {
            font-family: Georgia;
            font-weight: bold;
            text-align: center
            color: #000000;
            padding-bottom: 20px;
    }';
    echo '</style>';


    echo '<div class="hotel-booking-form">';
    echo '<div class="hotel-booking-info-box">';
    echo "<h2>Booking details</h2>";
    echo "Check-in Date:" . $booking_details->check_in_date . "<br>";
    echo "Check-out Date: " . $booking_details->check_out_date . "<br>";
    echo "Adults: " . $booking_details->adults . "<br>";
    echo "Children: " . $booking_details->children . "<br>";
    echo "Name: " . $booking_details->firstname . "  " . $booking_details->lastname . "<br>";
    echo "Email: " . $booking_details->email . "<br>";
    echo "Phone: " . $booking_details->phone . "<br>";
    echo '</div>';


    echo '<div class="hotel-booking-info-box">';
    echo "<h2>Price Breakup</h2>";
    echo "Price per night:  $" . $booking_details->price_per_night . "<br>";
    echo "Price for " . $booking_details->nights . ' nights:  $' . $booking_details->total_price . "<br>";
    echo "Cleaning Fee: $" . $booking_details->cleaning_fee . "<br>";
    if (!empty($booking_details->coupon_code)) {
        echo "Discount applied:  $" . $booking_details->discount_amount . "<br>";
    }
    echo "Total amount:  $" . $booking_details->total_amount . "<br>";
    echo '</div>';
    echo '</div>';

    unset($_SESSION['booking_details']);

} else {
    echo '<h5>No booking details found.</h5>';
}

echo '<style>';
echo 'input[type="submit"] {
    font-family:Georgia;
    background-color: #000000;
    color: white;
    align-items: center;
    padding: 20px 40px;
    margin-left: 690;
    font-size: 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}';
echo '</style>';

echo '<form method="post">';
echo '<p><input type="submit" name="submit" value="Payment"></p>';
echo '</form>';
