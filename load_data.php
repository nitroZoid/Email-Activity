<?php

//Author: Hari Eswar SM for Octane Marketing Pvt. Ltd.

error_reporting(E_ALL & ~E_NOTICE);
ini_set('max_execution_time', 5000);
date_default_timezone_set("Asia/Kolkata");
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', 5000);
ini_set('default_socket_timeout', 5000);
require_once 'database_config.php';
        class Load{
                public function welcome(){

                        $str = <<<EOF
Hello Octaner. Welcome to the program to find the activity rating of the various email IDs. You will be providing the file location from where to extract the input .csv files for the open activity and click activity.\n
The output of the program will be reduced values of the entries into the five features:
                1. Open Rate
                2. Click Rate
                3. Unique Open Rate
                4. First Open - Sent Time


After this step run the program test2.py to get the classification results of the database of emails. Emails classified based on activity are assigned the following rating:
                1. Superactive
                2. Active
                3. Normal


EOF;
                echo $str;
                }

                public function log_msg($str){
                        $logfile = fopen(__DIR__."/logs/log_".date("Y-m-d").".txt", "a");
                        echo date("Y-m-d h:i:s")."| $str";
                        $log = date("Y-m-d h:i:s")."| $str";
                        fwrite($logfile, $log);
                        fclose($logfile);
                }

                public function log_msg_initial($str){
                        $logfile = fopen(__DIR__."/logs/log_".date("Y-m-d").".txt", "a");
                        echo "\n".date("Y-m-d h:i:s")."| $str\n";
                        $log = "\n".date("Y-m-d h:i:s")."| $str\n";
                        fwrite($logfile, $log);
                        fclose($logfile);
                }

                public function delete($flag_del = 'N',$hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg_initial("Program entered delete function with the input choice \n");
                        if($flag_del == 'Y' || $flag_del == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for delete!\n");
                                $count = $dbh->prepare("TRUNCATE TABLE ".OPEN_ACTIVITY_TABLE);
                                $count->execute();
                                print("\nDeleted open_activity_table rows\n");
                                $this->log_msg("Deleted open_activity_table rows\n");
                                $count = $dbh->prepare("TRUNCATE TABLE ".CLICK_ACTIVITY_TABLE);
                                $count->execute();
                                print("\nDeleted click_activity_table rows\n");
                                $this->log_msg("Deleted click_activity_table rows\n");
                        }
                        else{
                                $this->log_msg("Program did not perform delete action with the negative input choice \n");
                        }
                }

                public function delete_auto($flag_del = 'N',$hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg_initial("Program entered delete function with the input choice \n");
                        if($flag_del == 'Y' || $flag_del == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for delete_automatically!\n");
                                $count = $dbh->prepare("TRUNCATE TABLE ".OPEN_ACTIVITY_TABLE);
                                $count->execute();
                                print("\nDeleted open_activity_table rows\n");
                                $count = $dbh->prepare("TRUNCATE TABLE ".CAMPAIGN_SENT_TABLE);
                                $count->execute();
                                print("\nDeleted campaign_sent_table rows\n");
                                $this->log_msg("Deleted open_activity_table rows\n");
                                $count = $dbh->prepare("TRUNCATE TABLE ".CLICK_ACTIVITY_TABLE);
                                $count->execute();
                                print("\nDeleted click_activity_table rows\n");
                                $this->log_msg("Deleted click_activity_table rows\n");
                        }
                        else{
                                $this->log_msg("Program did not perform delete action with the negative input choice \n");
                        }
                }

                public function load_sent_data($flag_append = "N", $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered load sent function with the input choice \n");
                        if($flag_append == 'Y' || $flag_append == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for loading_sent_data!\n");
                                print "Enter the directory of the files for sent_table (Make sure the files are in .csv format)!\n";
                                $csv_file = stream_get_line(STDIN, 1024, PHP_EOL);
                                $files = glob("$csv_file/*.csv");
                                foreach ($files as $file){
                                        print $file;
                                        print "\n";
                                        $sql = $dbh->prepare("LOAD DATA INFILE '$file' INTO TABLE ".CAMPAIGN_SENT_TABLE." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");
                                        $sql->execute();
                                }
                                $this->log_msg("Loaded data from directory to campaign_sent_table\n");
                        }
                        else{
                                $this->log_msg("Program did not load_sent_data with the negative input choice \n");
                        }

                }

                public function load_sent_data_auto($input_file, $flag_append = "N", $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered load sent function with the input choice \n");
                        if($flag_append == 'Y' || $flag_append == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for append!\n");
                                $files = glob("$input_file/*.csv");
                                foreach ($files as $file){
                                        print $file;
                                        print "\n";
                                        $sql = $dbh->prepare("LOAD DATA INFILE '$file' INTO TABLE ".CAMPAIGN_SENT_TABLE." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");
                                        $sql->execute();
                                }
                                $this->log_msg("Loaded data from directory $input_file into campaign_sent_table\n");
                        }
                        else{
                                $this->log_msg("Program did not load_sent_data with the negative input choice \n");
                        }

                }

                public function load_data($flag_append = "N", $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered load_data function with the input choice \n");
                        if($flag_append == 'Y' || $flag_append == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for load_data!\n");
                                print "Enter the directory of the files for open_activity_table (Make sure the files are in .csv format)!\n";
                                $csv_file = stream_get_line(STDIN, 1024, PHP_EOL);
                                $this->log_msg("Loading data in open_activity_table from directory $csv_file\n");
                                $files = glob("$csv_file/*.csv");
                                foreach ($files as $file) {
                                        print $file;
                                        print "\n";
                                        $sql = $dbh->prepare("LOAD DATA INFILE '$file' INTO TABLE ".OPEN_ACTIIVTY_TABLE." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");
                                        $sql->execute();
                                }

                                $this->log_msg("Loading data in click_activity_table from directory $csv_file\n");
                                print "\nEnter the directory of the file for click_activity_table (Make sure the file is in .csv format and to include the name of the file also)!\n";
                                $csv_file2 = stream_get_line(STDIN, 1024, PHP_EOL);

                                $files = glob("$csv_file2/*.csv");
                                foreach ($files as $file) {
                                        print $file;
                                        print "\n";
                                        $sql = $dbh->prepare("LOAD DATA INFILE '$file' INTO TABLE ".CLICK_ACTIVITY_TABLE." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");
                                        $sql->execute();
                                }

                        }
                        else{
                                $this->log_msg("Program did not load_data because of the negative input choice \n");
                        }
                }

                public function load_data_auto_open($input_file, $flag_append = "N", $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered load_data_auto function for loading into open_activity_table \n");
                        if($flag_append == 'Y' || $flag_append == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for load_data_auto_open!\n");
                                $this->log_msg("Loading data in open_activity_table from directory $input_file\n");
                                $files = glob("$input_file/*.csv");
                                foreach ($files as $file) {
                                        print $file;
                                        print "\n";
                                        $sql = $dbh->prepare("LOAD DATA INFILE '$file' INTO TABLE ".OPEN_ACTIVITY_TABLE." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");
                                        $sql->execute();
                                }

                        }
                        else{
                                $this->log_msg("Program did not load data in open_activity_table \n");
                        }
                }

                 public function load_data_auto_click($input_file, $flag_append = "N", $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered load_data_auto with the input choice \n");
                        if($flag_append == 'Y' || $flag_append == 'y'){
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for load_data_auto_click!\n");
                                $this->log_msg("Loading data in click_activity_table from directory $input_file\n");
                                $files = glob("$input_file/*.csv");
                                 foreach ($files as $file) {
                                        print $file;
                                        print "\n";
                                        $sql = $dbh->prepare("LOAD DATA INFILE '$file' INTO TABLE ".CLICK_ACTIVITY_TABLE." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");
                                        $sql->execute();
                                }
                        }
                         else{
                                $this->log_msg("Program did not load_data in click_activity_table \n");
                        }
                }

                public function put_sort_csv_open($output_file, $flag = 'N', $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered put_sort_csv function for sorting open_activity_table values\n");
                        if($flag == 'y' || $flag == 'Y'){
                                print "\nSorting process beginning\n";
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for putting in csv!");
                                $output_file = $output_file."open_activity_data.csv";
                                $dbh->query("SELECT emailid, TIMESTAMPDIFF(second, campaign_sent_time, first_open_time) AS first_minus_sent, total_opens FROM ".OPEN_ACTIVITY_TABLE." INNER JOIN ".CAMPAIGN_TABLE." ON ".CAMPAIGN_TABLE.".campaignid = ".OPEN_ACTIVITY_TABLE.".campaignid ORDER BY emailid into outfile '$output_file' fields enclosed by '"."\"' terminated by ',' lines terminated by '\n';");
                                $this->log_msg("Ordering done, file open_activity_data.csv created at $output_file location\n");
                        }
                }
                public function put_sort_csv_sent($output_file, $flag = 'N', $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered put_sort_csv function for sorting campaign_sent_table values\n");
                        if($flag == 'y' || $flag == 'Y'){
                                print "\nSorting process beginning\n";
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for putting in csv!\n");
                                $output_file = $output_file."sent_data.csv";
                                echo "SELECT * FROM ".CAMPAIGN_SENT_TABLE." ORDER BY emailid INTO OUTFILE '$output_file';";
                                $dbh->query("SELECT * FROM ".CAMPAIGN_SENT_TABLE." ORDER BY emailid INTO OUTFILE '$output_file';");
                                $this->log_msg("Ordering done, file sent_data.csv is created at $output_file location\n");
                                exit();
                        }
                }
                public function put_sort_csv_click($output_file, $flag = 'N', $hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered put_sort_csv function for sorting click_activity_table values\n");
                        if($flag == 'y' || $flag == 'Y'){
                                print "\nSorting process beginning\n";
                                $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                                $this->log_msg("Connection established successfully for putting in csv!\n");
                                $output_file = $output_file."click_activity_data.csv";
                                $dbh->query("SELECT emailid, TIMESTAMPDIFF(second, campaign_sent_time, click_time) AS click_minus_sent FROM ".CLICK_ACTIVITY_TABLE." INNER JOIN ".CAMPAIGN_TABLE." ON ".CAMPAIGN_TABLE.".campaignid = ".CLICK_ACTIVITY_TABLE.".campaignid ORDER BY emailid into outfile '$output_file' fields enclosed by '"."\"' terminated by ',' lines terminated by '\n';");
                                $this->log_msg("Ordering done, file click_activity_data.csv is created at $output_file location\n");
                        }
                }
        }


?>