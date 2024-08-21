<?php
// Function to fetch LOC data from the specified repository
function fetchLocData($repoType, $repoUrl, $branchName) {
  // Base API URL
  $apiBaseUrl = "https://api.codetabs.com/v1/loc/";

  // Construct the API query string
  $apiQuery = http_build_query([
    $repoType => $repoUrl,
    'branch' => $branchName
  ]);

  // Full API URL
  $apiUrl = $apiBaseUrl . "?" . $apiQuery;

  // Fetch data from the API
  $response = file_get_contents($apiUrl);
  if ($response === FALSE) {
    return false;
  }

  // Decode JSON response
  $data = json_decode($response, true);
  return $data;
}

// Function to display LOC data in the terminal
function displayLocData($data) {
  if (empty($data)) {
    echo "No data available or error occurred.\n";
    return;
  }

  echo "Lines of Code by Language:\n\n";
  $maxLanguageLength = max(array_map('strlen', array_column($data, 'language')));
  $maxLocLength = max(array_map('strlen', array_map(function($item) {
    return (string)$item['linesOfCode'];
  }, $data)));

  foreach ($data as $item) {
    $language = str_pad($item['language'], $maxLanguageLength);
    $loc = str_pad($item['linesOfCode'], $maxLocLength, ' ', STR_PAD_LEFT);
    echo "{$language}: {$loc} lines ({$item['files']} files)\n";
  }
}

// Function to prompt the user for input
function prompt($message) {
  echo $message;
  $handle = fopen("php://stdin", "r");
  $input = trim(fgets($handle));
  return $input;
}

// Main execution
$repoType = prompt("Enter repository type (github/gitlab): ");
$repoUrl = prompt("Enter repository URL (without https://): ");
$branchName = prompt("Enter branch name (default is 'main'): ") ?: 'main';

// Fetch LOC data
$data = fetchLocData($repoType, $repoUrl, $branchName);

// Display the result
displayLocData($data);
?>