# About

This is a Naive Bayes text classification based on the Stanford University documentation:

http://nlp.stanford.edu/IR-book/html/htmledition/naive-bayes-text-classification-1.html

The following script runs the sample that is explained on the above resource:

    php sample-nb-classifier.php

# Naive Bayes text classification for doonish

doonish ([doonish.es](http://doonish.es "doonish trivial colavorativo")) is a Trivia game based on 
the famous Trivia Pursuit but with some collaborative twist. 

The users can play answering questions but they can create his own questions. In the
question creation they need to categorize his question into a set of categoris. The purpose
of this script is to suggest to the user the catregory which the questions should be 
categorized automatically. Then, they will be able to modify it anyway, but it could be handy
at the begining.

To run the doonish classifier:

1. (optional) Create the question TSV:

    `php generate-questions.php > questions.tsv`

2. Run the current doonish categorizer:

    `php doonish-classifier.php`

*Note:* You need to set up doonish application and the database in order to generate all the questions. 'questions.tsv' is normally  included in the source code so you don't need such thing.

