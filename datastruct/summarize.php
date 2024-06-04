<?php
require_once("dbfetch.php");

// Fetch all rows from the loan_table
$query = "SELECT * FROM loan_table";
$result = mysqli_query($conn, $query);

// Initialize an empty array to store application texts
$loanApplicationTexts = [];

// Loop through each row and create descriptive text
while ($row = mysqli_fetch_assoc($result)) {
    $loanApplicationTexts[] = "Loan ID " . $row['id'] . " for " . $row['name'] . " with amount " . $row['amount'] . " is due on " . $row['duedate'] . ".";
}

// Concatenate all application texts
$allText = implode(" ", $loanApplicationTexts);

// Add explicit instructions to the input text
$instructionText = "Summarize the following loan database:";
$inputText = $instructionText . " " . $allText;

// Perform text summarization using BART
$summarizedText = summarizeTextWithBart($inputText);

// Display the summarized text
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Summarized Loan Applications</title>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Loan Summary</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="display-6 text-center">Summarized Loan Applications</h3>
                    </div>
                    <div class="card-body">
                        <p>' . htmlspecialchars($summarizedText) . '</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>';

// Summarization function using BART
function summarizeTextWithBart($text) {
    // BART API endpoint
    $api_url = "https://api-inference.huggingface.co/models/facebook/bart-large-cnn";

    // Prepare data for the API request
    $data = json_encode(array("inputs" => $text, "parameters" => array("max_length" => 512)));

    // Set up HTTP headers
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer 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' // Replace with your actual Hugging Face API token
    );

    // Send a POST request to the BART API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    // Check for curl errors
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Error: Unable to connect to the API. Curl error: " . $error;
    }

    curl_close($ch);

    // Decode the response
    $result = json_decode($response, true);

    // Log the full response for debugging
    file_put_contents('api_response_log.txt', print_r($result, true));

    // Check if the response contains the summarized text
    if (isset($result[0]['summary_text'])) {
        $summarizedText = $result[0]['summary_text'];
    } else {
        $summarizedText = "Error: Summarization failed. API response: " . $response . "try again in a few seconds :)";
    }

    return $summarizedText;
}
?>


