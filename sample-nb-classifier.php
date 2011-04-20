<?php  

include 'bootstrap.php';

initialize();

$trainingSet = array(
    array(
        'id' => 1,
        'words' => 'chinese beijing chinese',
        'category' => 'china'
    ),
    array(
        'id' => 2,
        'words' => 'chinese chinese shangai',
        'category' => 'china'
    ),
    array(
        'id' => 3,
        'words' => 'chinese macao',
        'category' => 'china'
    ),
    array(
        'id' => 4,
        'words' => 'tokyo japan chinese',
        'category' => 'others'
    )
);

$testSet = array(
    array(
        'id' => 5,
        'words' => 'chinese chinese chinese tokyo japan'
    ),
    array(
        'id' => 5,
        'words' => 'chinese tokyo japan'
    )
);

$categoryCounter = array();

/*
 * Loop through the training set to figure out how many categories do we 
 * have and ONLY to initialize the category counter array.
 */

foreach ($trainingSet as $document)
{
    if (!array_key_exists($document['category'], $categoryCounter))
    {
        $categoryCounter[$document['category']] = array(
            'INCLUDED' => 0,
            'NOT_INCLUDED' => 0
        );
    }
}

/*
 * And now, for each document, update the counter of each of the 
 * categories either if it's included or if it's not.
 */

foreach ($trainingSet as $document)
{
    foreach ($categoryCounter as $category => $counters)
    {
        if ($document['category'] == $category)
        {
            $categoryCounter[$category]['INCLUDED']++;
        }
        else
        {
            $categoryCounter[$category]['NOT_INCLUDED']++;
        }
    }
}

/*
 * We need to count for each word on the documents how many times it 
 * apears in each of the categories. Hard task but we should calculate 
 * it.
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

            foreach ($categoryCounter as $category => $counters)
            {
                $wordCounter[$word][$category] = 0;
            }
        }

        // increase the counter of this word for his document category
        $wordCounter[$word][$document['category']]++;
    }
}

/*
 * For each category we will see how many terms does it contain.
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

/* var_dump($wordCounter); die; // */
/* var_dump($categoryCounter); die; // */
/* var_dump($categoryLength); die; // */

/*
 * Now that we have precalculate all the information we will try to 
 * figure out which is the category of each document from the test set.
 */

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
        foreach ($categoryCounter as $category => $counters)
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
        $score[$category] = $counter['INCLUDED'] / count($trainingSet);

        foreach ($wordSet as $word)
        {
            $score[$category] *= $wordProbability[$word][$category];
        }
    }

    echo "\nThe following document has been categorized with the following scores:\n\n";
    echo "   {$document['words']}\n\n";
    var_dump($score);
}

