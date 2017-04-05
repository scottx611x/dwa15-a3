<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller {


	/**
	 * Show the main app content to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('main');
	}
    /**
     * Generate a password based off the guidelines of an xkcd comic
     */
	public function generatePassword(Request $request)
    {
	    new PasswordGen($request, "data/words.txt");
    }

}

class PasswordGen {
    private $base_pass = null;
    private $words = null;
    function __construct($data, $filename) {
        /**
        Read words array from file, and validate GET data upon class instantiation
         */
        # Read words into array from file
        $this->words = file($filename, FILE_IGNORE_NEW_LINES);
        # Validate and present Errors to Client if necessary
        $data_from_validator = $this->validate($data);
        if($data_from_validator["Error"]){
            # Encode data as JSON to ineract with it easier client-side
            echo json_encode($data_from_validator);
            return;
        }
        # If we reach here data is deemed O.K. and we continue to make a pass.
        $this->make_password($data_from_validator);
    }
    private function validate($data_to_validate)
    {
        /**
         * Private method to validate incoming data
         */
        if ((int)$data_to_validate['numWords'] > 20) {
            return array(
                "Error" => "You're way too paranoid. Try with <= 20 words!",
                "input_value" => "You input: " . $data_to_validate['numWords']);
        }
        if (!is_numeric($data_to_validate['numWords']) ||
            intval($data_to_validate['numWords']) < 0 ||
            intval($data_to_validate['numWords']) == 0
        ) {
            return array(
                "Error" => "Number of words must be a positive whole number for this to work!",
                "input_value" => "You input: " . $data_to_validate['numWords']);
        }
        if ($data_to_validate['numIncludeChecked'] == "true"){
            if (!is_numeric($data_to_validate['numIncluded']) ||
                intval($data_to_validate['numIncluded']) < 0 ||
                intval($data_to_validate['numIncluded']) == 0
            ) {
                return array(
                    "Error" => "Number to include must be a positive whole number for this to work!",
                    "input_value" => "You input: " . $data_to_validate['numIncluded']);
            }
        }
        # Just return original Data if nothing bad happens
        return $data_to_validate;
    }
    public function make_password($data){
        /**
        Public method to generate an xkcd-like password
         */
        foreach (range(1, $data['numWords']) as $i)
        {
            $rand_key = array_rand($this->words, 1);
            if ($i==1) {
                $this->base_pass = $this->base_pass . $this->words[$rand_key];
            }
            else{
                $this->base_pass = $this->base_pass . "-" . $this->words[$rand_key];
            }
        }
        $password = $this->base_pass;
        if ($data['numIncludeChecked'] == "true") {
            $password = $password . $data['numIncluded'];
        }
        if ($data['symbolIncludeChecked'] == "true") {
            $password = $password . $data['symbolIncluded'];
        }
        echo $password;
    }
} # eoc