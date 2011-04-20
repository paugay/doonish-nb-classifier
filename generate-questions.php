<?php  

/**
 * generate questions
 *
 * Script that generate the TSV file that contain all the questions that 
 * we currently have on the application (doonish.es).
 *
 * @version     $Id$
 * @package     doonish
 * @author      Pau Gay <pau.gay@gmail.com>
 */

// initialize zend framework
include 'bootstrap.php';

/*
 * Put the stop words in a array
 */

$stopWords = trim(file_get_contents('stop-words.txt'));
$stopWords = explode(' ', $stopWords);

/*
 * Fetch the categories to translate the category_id into the real 
 * category name.
 */

$category = new Application_Model_DbTable_Category();
$categoryNames = $category->fetchAll()->toArray();

/*
 * Fetch only 'CHECKED' questions.
 */

$question = new Application_Model_DbTable_Question();
$questionSet = $question->fetchAll("status = 'CHECKED'");

foreach ($questionSet as $questionRow)
{
    echo $questionRow['id'] . "\t" 
        . extractWords($questionRow['statement']) . "\t"
        . utf8_encode($categoryNames[$questionRow['category_id'] - 1]['name']) . "\n";
}

/*
 * Function that parse the statement and extract the meaningful words.
 *
 * @param string $statement
 *
 * @return string
 */
function extractWords ($statement)
{
    // get the stop words set
    global $stopWords;

    $words = $statement;

    // remove weird stuff
    $words = str_replace(",", " ", $words);
    $words = str_replace(".", " ", $words);
    $words = str_replace("-", " ", $words);
    $words = str_replace("?", " ", $words);
    $words = str_replace("¿", " ", $words);
    $words = str_replace("\"", " ", $words);
    $words = str_replace("\\", " ", $words);
    $words = str_replace("\n", " ", $words);
    $words = str_replace("  ", " ", $words);

    // lower case
    $words = strtolower($words);

    // trim
    $words = trim($words);

    /*
     * Remove the stop words.
     */

    // put the words in a array
    $wordsArray = explode(' ', $words);

    // this will be the right words
    $words = array(); 

    foreach ($wordsArray as $word)
    {
        // if the word is not on the stop words array, include it
        if (!in_array($word, $stopWords))
        {
            array_push($words, $word);
        }
    }

    // stringify the array of right words
    $words = implode(' ', $words);

    return $words;
}

