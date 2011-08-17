# About

This is a Naive Bayes text classification based on the Stanford University documentation:

http://nlp.stanford.edu/IR-book/html/htmledition/naive-bayes-text-classification-1.html

The following script runs the sample that is explained on the above resource:

    php sample-nb-classifier.php

# Naive Bayes text classification for doonish

doonish ([doonish.es](http://doonish.es "doonish trivial colavorativo")) is a trivia game based on 
the famous Trivia Pursuit but with some collaborative twist.  

The users can play answering questions but they can create his own questions. In the question creation they 
need to categorize his question into a set of categoris. The purpose of this script is to suggest to the 
user the catregory which the questions should be in. Then, they will be able to modify it anyway, but it 
could be handy at the begining to make the suggestion.

To run the doonish classifier:

    php doonish-classifier.php

Basically what this script does is get the list of questions (from questions.tsv, that basically is the
export of the question table from the database removing the stop-words and with the category) and using a 
fixed set of questions for training purposes. Then, with the rest, the script tries to predict the category
from the question, so at the end you will see something like this:

    Document:    	    [statement of the question]
    Real category:		[real category]
    Suggested category:	[suggested category]
    
At the very end of the script there is the summary with the total number of questions that the script has 
processed, the total number of hits and the accuracy.

# Some GIT tips 

I'm adding this GIT tips here for myself.

To commit the changes into my local repo. execute the following command. This will open VIM to write the commit log message.

    git commit -a -v 

To push the changes into the server:

    git push -u origin master

To restore a single file:

    git checkout -- [FILENAME]
