<?php

/* AUTOLOAD CLASSES */
spl_autoload_register(function($class_name){
    require_once __DIR__ . "/class/" . $class_name . ".php";
});


/*
 * Get the value of a request parameter from either GET or POST
 *
 * $k = parameter name
 */
function getParameter($k){
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
function getQuestionByID($question_list, $qid){
    for ($i=0; $i<count($question_list); $i++){
        if ($question_list[$i]->id == $qid){
            return $question_list[$i];
        }
    }
    return NULL;
}


/* Initialise Quiz Data */
$questions = [
    new Question(12345, "What is your name?", ["Larry", "Barry", "Garry", "Harry"], "D"),
    new Question(67890, "What is your favourite colour", ["Green", "No, Blue", "Orange", "Red"], "B"),
    new Question(42365, "What is x?", ["1", "2", "y", "a letter"], "D"),
    new Question(19462, "Guess the answer", ["Ay", "Bee", "Cee", "Dee"], "A"),
];



$questions = [
    new Question(12350,
        "What is the largest animal to ever exist on earth?",
        ["Woolly Mammoth","African elephant","Tyrannosaurus Rex","Sulphur bottom (blue) Whale"],
        "D"
    ),
    new Question(39546,
        "Which class has the largest number of animals?",
        ["Mammals","Fishes","Insects","Reptiles"],
        "C"
    ),
    new Question(97513,
        "Salamander belongs to which class?",
        ["Aves","Reptiles","Pisces","Amphibian"],
        "D"
    ),
    new Question(93175,
        "Vertebrates and tunicates share which features?",
        ["Jaws adapted for feeding","A high degree of cephalisation","A notochord and a dorsal, hollow nerve cord","The formation of structures from the neural crest"],
        "C"
    ),
    new Question(53497,
        "The water vascular system of echinoderms...",
        ["Functions as a circulatory system that distributes nutrients to body cells","Functions in locomotion, feeding, and gas exchange","Is bilateral in organization, even through the adult animal is not bilateral symmetrical","Moves water through the animal body during suspension feeding"],
        "B"
    ),
];


$question_count = rand(2, count($questions));


/* Initialise response objects */
$errors = [];
$response = [];


/* Get HTTP request parameters */
$q = getParameter("q");
$a = getParameter("a");


// No parameters given: response is a list of random question ID's
if ($q === NULL && $a === NULL){
    shuffle($questions);
    $random_questions = array_slice($questions, 0, $question_count);
    $response = ["questions" => array_column($random_questions, "id")];
}

// Only q= provided, return question data or error is invalid question ID
elseif ($a == NULL){
    $question_data = getQuestionByID($questions, $q);
    if ($question_data === NULL){
        $errors[] = "Unknown Question ID: $q";
    } else {
        $response = $question_data;
    }
}

// Only a= provided, invalid parameter combination, return error
elseif ($q == NULL){
    $errors[] = "Invalid input, question required";
}

// Both q and a provided, respond with answer correctness
else {
    $question = getQuestionByID($questions, $q);
    if ($question == NULL){
        $errors[] = "Unknown Question ID: $q";
    } else {
        $response = ["id" => $q, "correct"=>$question->checkAnswer($a)];
    }
}


// Append errors to the response if there were any
if (!empty($errors)){
    $response["error"] = $errors;
}


/* Return JSON response to the client */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
print(json_encode($response));

?>
