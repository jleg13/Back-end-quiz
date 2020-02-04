<?php

class Question implements JsonSerializable {

    public $id;
    public $text;
    public $choices;
    public $answer;

    public function __construct($id, $text, $choices, $answer) {
        $this->id = $id;
        $this->text = $text;
        $this->choices = $choices;
        $this->answer = $answer;
    }

    public function checkAnswer($s) {
        return strtoupper($this->answer) === strtoupper($s);
    }

    public function jsonSerialize() {
        // This function converts a Question object into an Array to be handled by json_encode()
		return [
        	'id' => $this->id,
        	'text' => $this->text,
        	'choices' => $this->choices
		];
	}
}

?>
