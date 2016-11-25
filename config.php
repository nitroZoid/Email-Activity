<?php
define('INPUT_OPEN_PATH', '/rating_folders/input/open');
define('OUTPUT_OPEN_PATH','/rating_folders/output/open');

define('INPUT_SENT_PATH', '/rating_folders/input/sent');
define('OUTPUT_SENT_PATH','/rating_folders/output/sent');

define('INPUT_CLICK_PATH', '/rating_folders/input/click');
define('OUTPUT_CLICK_PATH','/rating_folders/output/click');

define('OUTPUT_PATH', '/rating_folders/output/');

define('SORT_SENT_DATA','Y');
define('SORT_OPEN_DATA','N');
define('SORT_CLICK_DATA','N');

define('DAYS_LIMIT', 10);
define('OPENS_LIMIT', 30);

define('DELETE_EXISTING_DATA', 'N');   //Delete the existing data in the open_activity_table and click_activity_table
define('LOAD_DATA','N');        //Load Data into open_actviity_table ane click_activity_table from the csv files
define('LOAD_DATA_SENT', 'N'); //Load Data into sent_table from the csv files


?>
