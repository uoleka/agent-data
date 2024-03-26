<?php

namespace App\Services;
use App\Models\Person;

class FileService 
{
    protected $person;
    protected $titles;
    protected $wordType;
    protected $filePath;
    protected $word;
   /* public function __construct($filePath)
    {
        $this->filePath = $filePath;
    } */

    public function storeValues(string $filePath)
    {   
        $this->titles = ["Mr", "Mrs", "Ms", "Miss", "Master", "Prof", "Dr"];
        $this->person = [];
        $this->wordType = " ";
        //$titles = explode(',', env('TITLES_ARRAY'));
        //Read the csv file
        $file = new \SplFileObject($filePath, 'r');
        $file->setFlags(\SplFileObject::READ_CSV);
        foreach ($file as $row) {
           $words = explode(" ", $row[0]);
           foreach ($words as $word) {       
                htmlspecialchars($word);
                if(!isset($this->person['title'])) {
                    $this->isTitle($word);
                } 
                if (isset($this->person['title'])) {
                    $this->isConjection($word);
                    if(!isset($this->person['initials'])) {
                        $this->isInitials($word);
                    } 
                    if(!isset($this->person['first_name'])) {
                        $this->isFirstName($word);    
                    } 
                    if(!isset($this->person['last_name'])) {
                        $this->isLastName($word, $words);
                    }
                }       
           }
        }
        
    }
   
    public function isTitle(string $word)
    {   
        //checks if the word is a title
        if (in_array($word, $this->titles)) {
            $this->wordType = "title";
            $this->addWord($word);
        } 
    }

    public function isInitials(string $word) 
    {
        //checks if the word has a single letter with an optional full stop.
        $word = preg_match('/^[A-Za-z]\.?$/i', $word);
        if ($word) {
            $this->wordType = "initials";
            $this->addWord($word); 
        }           
    }
    

    public function isFirstName(string $word)
    {
        //Check if the word is First name
        if (!In_array($word, $this->titles) && !in_array($word, ["&", "and"]) && !array_key_exists("first_name", $this->person)) {
            $this->wordType = "first_name";
            $this->addWord($word);
        } 
        
    }

    public function isLastName(string $word, array $words)
    {
        //Check if the word is the Last name
        $lastWord = end($words); 
        if ($lastWord === $word && array_key_exists("title", $this->person)) {
            $this->isLastWord($word);
        } 
        if (array_key_exists("first_name", $this->person)) {
            $this->wordType = "last_name";
            $this->addWord($word); 
        } 
        if (!In_array($word, $this->titles) && !in_array($word, ["&", "and"]) && array_key_exists("title", $this->person)) {
            $this->wordType = "first_name";
            $this->addWord($word);
        } 
        return;
    }

    public function isConjection(string $word) 
    {
        //Check if the last name is not set and the word is & or and
        if (!array_key_exists("last_name", $this->person) && $word ==="&" || strtolower($word) === "and") {
            $this->isLastWord($word);            
        }
    }

    public function isLastWord(string $word)
    {
        //Calls the function to add last name to the Person Array
        $this->wordType = "last_name";
        $this->addWord($word);
        $this->addPerson();
    }

    public function addWord(string $word)
    {
        //Add the word to the person array and move to the next iteration
        $this->person[$this->wordType] = $word; 
        print_r($this->person);
        
    }

    public function addPerson()
    {
        //save the values from the person array to the database
        $personModel = new Person;
        $personModel->fill($this->person);
        if (isset($person['title'])) {
            $personModel->save();
        }
        $this->person = [];
        return;
    }
}
