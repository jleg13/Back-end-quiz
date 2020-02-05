<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

/* AUTOLOAD CLASSES */
spl_autoload_register(function ($class_name) {
    require_once __DIR__ . "/class/" . $class_name . ".php";
});


/*
 * Get the value of a request parameter from either GET or POST
 *
 * $k = parameter name
 */
function getParameter($k)
{
    if (isset($_GET) && isset($_GET[$k])) {
        return $_GET[$k];
    } elseif (isset($_POST) && isset($_POST[$k])) {
        return $_POST[$k];
    } else {
        return NULL;
    }
}


/* Get a specific question object from a list of questions
 *
 * $question_list = Array of question objects
 * $qid = Question ID to match
 */
function getQuestionByID($question_list, $qid)
{
    for ($i = 0; $i < count($question_list); $i++) {
        if ($question_list[$i]->id == $qid) {
            return $question_list[$i];
        }
    }
    return NULL;
}


/* Initialise Quiz Data */
$questions = [
    new Question(12345, "Planning is the intelligent estimate of resources required to perform a predefined project successfully at a future date within a defined environment.", ['A' => "True", 'B' => "False", 'C' => "NA", 'D' => "NA"], "A"),
    new Question(67890, "What attributes DOES NOT make planning for software project unique?", ['A' => "Functionality is the primary output", 'B' => "Productivity and quality are dependent on humans", 'C' => "Output is physical.", 'D' => "Software engineering tools have limited predictability"], "C"),
    new Question(42365, "What is NOT a key feature of the project management plan?", ['A' => "Estimate: predict status of project", 'B' => "Resources: machines, personnel, materials, etc.", 'C' => "Dates: milestones, end date, etc.", 'D' => "Best practices: things learnt from other projects"], "D"),
    new Question(19462, "What is not a best practice in software project planning", ['A' => "Creating a work register", 'B' => "Perform variance analysis to uncover the difference between actual and planned behaviour", 'C' => "Norms for planning where knowledge repository is important", 'D' => "Following a process driven approach"], "A")
];

$question_count = rand(2, count($questions));


/* Initialise response objects */
$errors = [];
$response = [];


/* Get HTTP request parameters */
$q = getParameter("q");
$a = getParameter("a");


// No parameters given: response is a list of random question ID's
if ($q === NULL && $a === NULL) {
    shuffle($questions);
    $random_questions = array_slice($questions, 0, $question_count);
    $response = ["questions" => array_column($random_questions, "id")];
}

// Only q= provided, return question data or error is invalid question ID
elseif ($a == NULL) {
    $question_data = getQuestionByID($questions, $q);
    if ($question_data === NULL) {
        $errors[] = "Unknown Question ID: $q";
    } else {
        $response = $question_data;
    }
}

// Only a= provided, invalid parameter combination, return error
elseif ($q == NULL) {
    $errors[] = "Invalid input, question required";
}

// Both q and a provided, respond with answer correctness
else {
    $question = getQuestionByID($questions, $q);
    if ($question == NULL) {
        $errors[] = "Unknown Question ID: $q";
    } else {
        $response = ["id" => $q, "correct" => $question->checkAnswer($a)];
    }
}


// Append errors to the response if there were any
if (!empty($errors)) {
    $response["error"] = $errors;
}


/* Return JSON response to the client */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
print(json_encode($response));
