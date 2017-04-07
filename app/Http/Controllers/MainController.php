<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $v = Validator::make($request->all(),
            [
                'numWords' => 'required|integer|between:1,100',
                'numIncluded' => 'required_if:numIncludeChecked,true|integer|between:0,1000000',
                'symbolIncluded' => 'required_if:symbolIncludeChecked,true|in:!,@,#,$,%,^'
            ]
        );

        if ($v->fails()){
            echo json_encode(array("Error" => $v->errors()));
            return;
        }
        else {
            new PasswordGen($request, "data/words.txt");
        }
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

        # If we reach here data is deemed O.K. and we continue to make a pass.
        $this->make_password($data);
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