<!DOCTYPE html>
<html>
<head>
    <title>Confirmation Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
     function isValidCreditCardNumber($number) {
        // The regular expression allows for an optional dash '-' after every group of 4 numbers
        return preg_match('/^(4|5)\d{3}(-?\d{4}){3}$/', $number);
    }

    // Function to check if the credit card number passes the Luhn Algorithm
    function passesLuhnAlgorithm($number) {
        $number = str_replace("-", "", $number);
        $length = strlen($number);
        $sum = 0;
        $alternate = false;

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int)$number[$i];

            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return $sum % 10 === 0;
    }   
    if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST["name"]) && isset($_POST["section"]) && isset($_POST["cardnumber"]) && isset($_POST["cardtype"]) ) {
        $name = $_POST["name"];
        $section = $_POST["section"];
        $cardNumber = $_POST["cardnumber"];
        $cardType = $_POST["cardtype"];
        if (isValidCreditCardNumber($cardNumber)) {
            // Check if it's a Visa or MasterCard based on the first digit
            if ((substr($cardNumber, 0, 1) === '4' && $cardType === 'visa') ||
                (substr($cardNumber, 0, 1) === '5' && $cardType === 'mastercard')) {
                // Check if the credit card number passes the Luhn Algorithm
                if (passesLuhnAlgorithm($cardNumber)) {
                    // Format the data
                    echo "<h1>Thanks, sucker!</h1>";
                    echo "<p><strong>Name:</strong> $name</p>";
                    echo "<p><strong>Section:</strong> $section</p>";
                    echo "<p><strong>Credit Card Number:</strong> $cardNumber</p>";
                    echo "<p><strong>Credit Card Type:</strong> $cardType</p>";
                    echo "<p><strong>Here are all the suckers who have submitted here:</strong></p>";
                    $userData = "$name;$section;$cardNumber;$cardType\n";

                    // Save the data to the suckers.txt file
                    file_put_contents("suckers.txt", $userData, FILE_APPEND);

                    // Display the updated contents of the suckers.txt file
                    $fileContents = file_get_contents("suckers.txt");
                    echo "<h1>Thank you for your purchase!</h1>";
                    echo "<pre>$fileContents</pre>";
                } else {
                    echo "<h1>Error: Invalid credit card number</h1>";
                    echo "<p>The submitted credit card number does not pass the Luhn Algorithm. Please provide a valid credit card number.</p>";
                }
            } else {
                echo "<h1>Error: Invalid card type</h1>";
                echo "<p>The card type doesn't match the first digit of the card number.</p>";
            }
        } else {
            echo "<h1>Error: Invalid credit card number</h1>";
            echo "<p>Please provide a valid 16-digit credit card number in the correct format.</p>";
        }
    } else {
        echo "<h1>Sorry</h1>";
        echo "<p>You didn't fill out the form completely. <a href='index.html'>Try again</a></p>";
    }   
    ?>