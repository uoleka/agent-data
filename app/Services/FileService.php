<?php

namespace App\Services;
use App\Models\Person;

class FileService 
{
    protected $person;
    protected $titles;
    protected $conjection;
    protected $wordType;
    protected $filePath;
    protected $words;
    protected $word;
    protected $lastWord;

    /**
     * Reads each line and extract the words.
     */
    public function storeValues(string $filePath)
    {   
        $this->titles = ["Mr", "Mrs", "Ms", "Miss", "Mister", "Prof", "Dr"];
        $this->person = [];
        $this->conjection = ["&", "and"];
        $this->wordType = " ";
        //Read the csv file
        $file = new \SplFileObject($filePath, 'r');
        $file->setFlags(\SplFileObject::READ_CSV);
        foreach ($file as $row) {
           $this->words = explode(" ", $row[0]);
           //Process the words for assigning to the array
           foreach ($this->words as $word) {       
                htmlspecialchars($word);
                $this->lastWord = end($this->words);
                //will not run unless title is not set yet.
                if (!isset($this->person['title'])) {
                    $this->isTitle($word);
                }
                //Checks that Title is excuted once and is available before Others.
                if (isset($this->person['title']) && !in_array($word, $this->titles)) {
                    $this->isConjection($word); 
                    $this->isInitials($word);
                    $this->isFirstName($word);
                    $this->isLastName($word);
                } 
            }
        }
        
    }
   
    /**
     * Determine if the word is a Title.
     */
    public function isTitle(string $word)
    {   
        if (in_array($word, $this->titles)) {
            $this->wordType = "title";
            $this->addWord($word);
        } 
    }

    /**
     * Determine if the word is an initial with or without a full stop.
     */
    public function isInitials(string $word) 
    {
        $wordIsInitials = preg_match('/^[A-Za-z]\.?$/i', $word);
        if ($wordIsInitials) {
            $this->wordType = "initials";
            $this->addWord($word); 
        }           
    }
    
    /**
     * Determine if the word is a First Name.
     */
    public function isFirstName(string $word)
    {
        if (!in_array($word, $this->titles) && !in_array($word, $this->conjection) && !array_key_exists("first_name", $this->person)) {
            if (!isset($this->person["last_name"]) && !array_key_exists("initials", $this->person) && $this->lastWord != $word) {
                $this->wordType = "first_name";
                $this->addWord($word);
            }
        } 
        
    }

    /**
     * Determine if the word is a Last name and send off for Database submittion.
     */
    public function isLastName(string $word)
    {
        if ($this->lastWord == $word) {
            $this->wordType = "last_name";
            $this->addWord($word);
            $this->addPerson();    
        } 
         
    }

    /**
     * Determine if the word is a conjection.
     */
    public function isConjection(string $word) 
    {
        if (!array_key_exists("last_name", $this->person) && $word ==="&" || strtolower($word) === "and") {
            $this->isLastName($this->lastWord);            
        }
    }

    /**
     * Add the word to the Specified array Person.
     */
    public function addWord(string $word)
    {
        $this->person[$this->wordType] = $word; 
        return;
    }

    /**
     * Add the array to the database.
     */
    public function addPerson()
    {
        $personModel = new Person;
        $personModel->fill($this->person);
        $personModel->save();
        $this->person = [];
        return;  
    }
}