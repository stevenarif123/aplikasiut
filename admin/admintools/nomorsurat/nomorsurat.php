<?php
// Configuration
$letter_codes = array(
    'INV' => 'Invitation',
    'ANN' => 'Announcement',
    'OTH' => 'Other'
);

// Get the current school year
$school_year = date('Y') . '-' . (date('Y') + 1);

// Get the current month and year
$month = date('m');
$year = date('Y');

// Initialize the sequential number
$sequential_number = 1;

// Function to generate the letter number
function generate_letter_number($letter_code) {
    global $sequential_number, $school_year, $month, $year;
    $letter_number = $sequential_number . '/' . $letter_code . '/' . $school_year . '/' . $month . '-' . $year;
    $sequential_number++;
    return $letter_number;
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $letter_code = $_POST['letter_code'];
    $letter_number = generate_letter_number($letter_code);
    echo '<p>Generated letter number: ' . $letter_number . '</p>';
}

?>

<!-- HTML and Tailwind CSS -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formal Letter Number Maker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="h-screen flex justify-center items-center bg-gray-100">
    <div class="max-w-md p-4 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Formal Letter Number Maker</h2>
        <form action="" method="post">
            <label for="letter_code" class="block mb-2">Select letter code:</label>
            <select id="letter_code" name="letter_code" class="block w-full p-2 pl-10 text-sm text-gray-700">
                <?php foreach ($letter_codes as $code => $description) { ?>
                    <option value="<?php echo $code ?>"><?php echo $description ?></option>
                <?php } ?>
            </select>
            <button name="submit" type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Generate Letter Number</button>
        </form>
        <?php if (isset($letter_number)) { ?>
            <p class="text-lg font-bold mt-4">Generated letter number: <?php echo $letter_number ?></p>
        <?php } ?>
    </div>
    <!-- <script>
    const form = document.querySelector('form');
    const letterNumberInput = document.querySelector('#letter-number-input');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const letterCode = document.querySelector('#letter_code').value;
        const letterNumber = generateLetterNumber(letterCode);
        letterNumberInput.value = letterNumber;
    });

    function generateLetterNumber(letterCode) {
        // Call the PHP function using AJAX or use a JavaScript implementation
        // For simplicity, I'll just return a dummy value
        return '001/' + letterCode + '/' + '2022-2023' + '/' + '02-2022';
    }
    </script> -->

<!-- Add a text input field to display the generated letter number -->
<input type="text" id="letter-number-input" class="block w-full p-2 pl-10 text-sm text-gray-700" readonly>
</body>
</html>