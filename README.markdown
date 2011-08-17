# About

This is a Naive Bayes text classification based on the Stanford University documentation:

http://nlp.stanford.edu/IR-book/html/htmledition/naive-bayes-text-classification-1.html

The following script runs the sample that is explained on the above resource:

    php sample-nb-classifier.php

# Naive Bayes text classification for doonish

doonish ([doonish.es](http://doonish.es "doonish trivial colavorativo")) is a trivia game based on 
the famous Trivia Pursuit but with some collaborative twist.  

The users can play answering questions but they can create his own questions. In the
question creation they need to categorize his question into a set of categoris. The purpose
of this script is to suggest to the user the catregory which the questions should be in.
Then, they will be able to modify it anyway, but it could be handy at the begining to make the 
suggestion.

To run the doonish classifier:

    `php doonish-classifier.php`

# Some GIT tips 

I'm adding this tips here for myself.

To commit the changes into my local repo. execute the following command. This will open VIM to write the commit log message.

    git commit -a -v 

To push the changes into the server:

    git push -u origin master

To restore a single file:

    git checkout -- [FILENAME]



