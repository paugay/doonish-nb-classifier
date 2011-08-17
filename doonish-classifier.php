<?php  

/**
 * doonish categorizer
 *
 * This is a test script that suggest a categorization for a question. 
 * The script needs to be trained with some questions and categories and 
 * then you can run you own test cheking if the predictions are right or 
 * not.
 *
 * @version     $Id$
 * @package     doonish
 * @author      Pau Gay <pau.gay@gmail.com>
 */

/*
 * Step 0) Define some parameters
 */

$config = array(
    // the number of questions that we will use for training purposes
    'trainingQuestions' => 2000
);

$questionArray = parseQuestions('questions.tsv');

/*
 * Step 1) Initialize the training question set and the test question 
 * set.
 */

if ($config['trainingQuestions'] > count($questionArray))
{
    die (
        "Error: The 'trainingQuestions' is lower than the number of"
        . " questions into the TSV file'"
    );
}

/*
 * Generate the training set and the test set
 */

$trainingSet = array();
$testSet     = array();

foreach ($questionArray as $question)
{
    /*
     * Depending of how many elements we have on the training set, we 
     * will add the question into the training set or into the test set.
     */

    if (count($trainingSet) < $config['trainingQuestions'])
    {
        // add the question in to the training set
        array_push($trainingSet, $question);
    }
    else
    {
        // add the question in to the training set
        array_push($testSet, $question);
    }
}

/*
 * Step 2) We are going to create three arrays with some precalculated 
 * information that will allow us to make the categorization afterwards.
 */

/*
 * Step 2.1) Category counter
 *
 * This array contains the number of documents that each category has.
 *
 * @sample
 *
 *   array(
 *       '<CATEGORY_1>' => <NUMBER_OF_DOCUMENTS_IN_CATEGORY_1>,
 *       '<CATEGORY_2>' => <NUMBER_OF_DOCUMENTS_IN_CATEGORY_2>,
 *        ...
 *   );
 *
 */

$categoryCounter = array();

foreach ($trainingSet as $document)
{
    if (!array_key_exists($document['category'], $categoryCounter))
    {
        $categoryCounter[$document['category']] = 0;
    }

    $categoryCounter[$document['category']]++;
}

/*
 * Step 2.2) Word counter
 *
 * This array contains for each word on the all universe of documents 
 * how many times it apears on each category. This is kind of a hard 
 * task but we must do it.
 *
 * @sample
 *
 *   array(
 *       '<WORD_1>' => array(
 *           '<CATEGORY_1> => <NUM_TIMES_WORD_1_APPEAR_IN_CATEGORY_1>,
 *           '<CATEGORY_2> => <NUM_TIMES_WORD_1_APPEAR_IN_CATEGORY_2>,
 *           ...
 *       ),
 *       '<WORD_2>' => array(
 *           '<CATEGORY_1> => <NUM_TIMES_WORD_2_APPEAR_IN_CATEGORY_1>,
 *           '<CATEGORY_2> => <NUM_TIMES_WORD_2_APPEAR_IN_CATEGORY_2>,
 *           ...
 *       ),
 *       ....
 *   );
 *
 */

$wordCounter = array();

foreach ($trainingSet as $document)
{
    $wordSet = explode (' ', $document['words']);

    foreach ($wordSet as $word)
    {
        if (!array_key_exists($word, $wordCounter))
        {
            $wordCounter[$word] = array();

            foreach ($categoryCounter as $category => $counter)
            {
                $wordCounter[$word][$category] = 0;
            }
        }

        // increase the counter of this word for his document category
        $wordCounter[$word][$document['category']]++;
    }
}

/*
 * Step 2.3) Category length
 *
 * We need to calculate how many words or terms contains each of the 
 * categories.
 *
 * @sample
 *
 *   array(
 *       '<CATEGORY_1>' => <NUMBER_OF_WORDS_FROM_CATEGORY_1>,
 *       '<CATEGORY_2>' => <NUMBER_OF_WORDS_FROM_CATEGORY_2>,
 *       ...
 *   );
 *
 */

$categoryLength = array();

foreach ($wordCounter as $word => $counter)
{
    foreach ($counter as $categoryName => $numWords)
    {
        if (!array_key_exists($categoryName, $categoryLength))
        {
            $categoryLength[$categoryName] = 0;
        }

        $categoryLength[$categoryName] += $numWords;
    }
}

/*
 * Step 3) Now that we have precalculate all the information we will try to 
 * figure out which is the category of each document from the test set.
 */

$total = 0;
$hits  = 0;

foreach ($testSet as $document)
{
    /*
     * Calculate the probability of each word from the document is part 
     * of each of the categories that we have.
     */

    $wordSet = explode(' ', $document['words']);

    $wordProbability = array();

    foreach ($wordSet as $word)
    {
        foreach ($categoryCounter as $category => $counter)
        {
            $wordProbability[$word][$category] = 
                ($wordCounter[$word][$category] + 1) 
                    / 
                ($categoryLength[$category] + count($wordCounter));
        }
    }

    /*
     * Now, we will calculate the score of this document for each of the 
     * categories, multiplying the individual score of each word and 
     * adding as well a value that will weight the calculation depending 
     * of the number of the documents for each category.
     */

    $score = array();

    foreach ($categoryCounter as $category => $counter)
    {
        $score[$category] = $counter / count($trainingSet);

        foreach ($wordSet as $word)
        {
            $score[$category] *= $wordProbability[$word][$category];
        }
    }

    /*
     * On $score we have the final score of each category.
     *
     * @sample
     *
     *   array(
     *       '<CARTEGORY_1>' => <PROBABILITY_THAN_DOCUMENT_IS_FROM_CAT_1>,
     *       '<CARTEGORY_2>' => <PROBABILITY_THAN_DOCUMENT_IS_FROM_CAT_2>,
     *       '<CARTEGORY_3>' => <PROBABILITY_THAN_DOCUMENT_IS_FROM_CAT_3>,
     *       ...
     *   );
     *
     */

    $suggestedCategory  = '';
    $max                = 0;

    /*
     * Iterate through the array to figure out which is the category who 
     * has the bigger score.
     */

    foreach ($score as $category => $value)
    {
        if ($value > $max)
        {
            $suggestedCategory = $category;
            $max = $value;
        }
    }

    // output the result
    echo "Document:\t\t{$document['words']}\n";
    echo "Real category:\t\t{$document['category']}\n";
    echo "Suggested category:\t{$suggestedCategory}\n\n";


    // increment counters
    if ($document['category'] == $suggestedCategory) $hits++;
    $total++;
}

// output some statistics
echo "\n--\n\n";
echo "Total: \t\t{$total}\n";
echo "Hits: \t\t{$hits}\n";
echo "Accuracy: \t" . number_format($hits/$total, 2) . "%\n";
echo "\n(with " . count($trainingSet) . " documents in the TRAINING SET and " . count($testSet) . " documents in the TEST SET)\n\n";

/*
 * Function that parse the questions from the TSV file and put it in a 
 * array.
 *
 * @param string $tsv
 *
 * @return array
 */
function parseQuestions ($tsv)
{
    $fp = fopen($tsv, 'r');

    // initialize the question array
    $questionArray = array();

    if ($fp)
    {
        // for each question
        while ($question = fgetcsv($fp, 0, "\t"))
        {
            // check that we have three values
            if (count($question) >= 3)
            {
                // add the question into the question array
                array_push(
                    $questionArray,
                    array(
                        'id'         => $question[0],
                        'words'      => $question[1],
                        'category'   => $question[2],
                    )
                );
            }
        }
    }

    return $questionArray;
}
