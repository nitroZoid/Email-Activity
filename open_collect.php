<?php
//Author: Hari Eswar SM for Octane Marketing Pvt. Ltd.

error_reporting(E_ALL & ~E_NOTICE);
ini_set('max_execution_time', 10000);
date_default_timezone_set("Asia/Kolkata");
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', 10000);
ini_set('default_socket_timeout', 10000);
require_once 'database_config.php';

        class Load_Open{
                public function put_sort_csv($hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered put_sort_csv function\n");
                        print "\nSorting process beginning\n";
                        $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                        $this->log_msg("Connection established successfully for putting in csv!\n");
                        print "Enter the directory for the sorted open_table!\n";
                        $csv_file = stream_get_line(STDIN, 1024, PHP_EOL);
                        $dbh->query("SELECT emailid, TIMESTAMPDIFF(second, campaign_sent_time, first_open_time) AS first_minus_sent, total_opens FROM ".OPEN_ACTIVITY_TABLE." INNER JOIN ".CAMPAIGN_TABLE." ON ".CAMPAIGN_TABLE.".campaignid = ".OPEN_ACTIVITY_TABLE.".campaignid ORDER BY emailid into outfile '$csv_file' fields enclosed by '"."\"' terminated by ',' lines terminated by '\n';");
                        $this->log_msg("Ordering done for open_activity_table data at $csv_file location\n");
                }

                public function log_msg($str){
                        $logfile = fopen(__DIR__."/logs/log_".date("Y-m-d").".txt", "a");
                        echo date("Y-m-d h:i:s")."| $str";
                        $log = date("Y-m-d h:i:s")."| $str";
                        fwrite($logfile, $log);
                        fclose($logfile);
                }

                public function reduce_to_count(){
                        $this->log_msg("Program entered open collect function\n");
                        print "Function warming up\n";
                        $count_file = fopen("open_data_reduced.csv", "wb");
                        $open_data_file = fopen("open_activity_data.csv", "rb");
                        $open_file_inactivity = fopen("open_data_inactivity.csv", "wb");
                        $open_activity_array = array();
                        $only_email = array();
                        $overall_count = 0;
                        $this->log_msg("Starting the count function!\n");
                        $start_time = time();
                        print "Entering the loop\n";
                        while(!feof($open_data_file)){
                                $row = fgetcsv($open_data_file);
                                if(feof($open_data_file))
                                        break;
                                if($overall_count == 0){
                                        $prev = $row[0];
                                        $first_minus_sent_prev = $row[1];
                                        $open_count_prev = $row[2];
                                        //$first = $row[0];
                                        $overall_count = $overall_count+1;
                                }
                                $now = $row[0];
                                $open_count_now = $row[2];
                                $first_minus_sent_now = $row[1];
                                if(strcmp($prev, $now) != 0){
                                        $open_activity_array[0] = $prev;
                                        $only_email[0] = $prev;
                                        if($overall_count==1){
                                                $open_activity_array[1] = $count;
                                        }
                                        else{
                                                $open_activity_array[1] = $count+1;
                                        }
                                        $open_activity_array[2] = $open_count_prev;
                                        $open_activity_array[3] = $first_minus_sent_prev;
                                        fputcsv($open_file_inactivity, $only_email);
                                        fputcsv($count_file, $open_activity_array);
                                        $prev = $now;
                                        $open_count_prev = $open_count_now;
                                        $first_minus_sent_prev = $first_minus_sent_now;
                                        $count = 0;
                                        $overall_count = $overall_count+1;
                                }
                                else{
                                        if($first_minus_sent_now<$first_minus_sent_prev){
                                                $first_minus_sent_prev = $first_minus_sent_now;
                                        }
                                        $count = $count + 1;
                                        $open_count_prev+=$open_count_now;
                                }
                        }
                        $open_activity_array[0] = $prev;
                        $open_activity_array[1] = $count+1;
                        $open_activity_array[2] = $open_count_prev;
                        $open_activity_array[3] = $first_minus_sent_prev;
                        $only_email[0] = $prev;
                        fputcsv($open_file_inactivity, $only_email);
                        fputcsv($count_file, $open_activity_array);    //writing the last record
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";
                        $this->log_msg("Count Function complete, check open_data_reduced.csv file!\n");

                }

                public function reduce_to_count_auto($file_location){
                        $this->log_msg("Program entered open_collect function automatically\n");
                        print "Function warming up\n";
                        $count_file = fopen($file_location."open_data_reduced.csv", "wb");
                        $open_data_file = fopen($file_location."open_activity_data.csv", "rb");
                        $open_file_inactivity = fopen($file_location."open_data_inactivity.csv", "wb");
                        $open_activity_array = array();
                        $only_email = array();
                        $overall_count = 0;
                        $this->log_msg("Starting the count function!\n");
                        $start_time = time();
                        print "Entering the loop\n";
                        while(!feof($open_data_file)){
                                $row = fgetcsv($open_data_file);
                                if(feof($open_data_file))
                                        break;
                                if($overall_count == 0){
                                        $prev = $row[0];
                                        $first_minus_sent_prev = $row[1];
                                        $open_count_prev = $row[2];
                                        //$first = $row[0];
                                        $overall_count = $overall_count+1;
                                }
                                $now = $row[0];
                                $open_count_now = $row[2];
                                $first_minus_sent_now = $row[1];
                                if(strcmp($prev, $now) != 0){
                                        $open_activity_array[0] = $prev;
                                        $only_email[0] = $prev;
                                        if($overall_count==1){
                                                $open_activity_array[1] = $count;
                                        }
                                        else{
                                                $open_activity_array[1] = $count+1;
                                        }
                                        $open_activity_array[2] = $open_count_prev;
                                        $open_activity_array[3] = $first_minus_sent_prev;
                                        fputcsv($open_file_inactivity, $only_email);
                                        fputcsv($count_file, $open_activity_array);
                                        $prev = $now;
                                        $open_count_prev = $open_count_now;
                                        $first_minus_sent_prev = $first_minus_sent_now;
                                        $count = 0;
                                        $overall_count = $overall_count+1;
                                }
                                else{
                                        if($first_minus_sent_now<$first_minus_sent_prev){
                                                $first_minus_sent_prev = $first_minus_sent_now;
                                        }
                                        $count = $count + 1;
                                        $open_count_prev+=$open_count_now;
                                }
                        }
                        $open_activity_array[0] = $prev;
                        $open_activity_array[1] = $count+1;
                        $open_activity_array[2] = $open_count_prev;
                        $open_activity_array[3] = $first_minus_sent_prev;
                        $only_email[0] = $prev;
                        fputcsv($open_file_inactivity, $only_email);
                        fputcsv($count_file, $open_activity_array);                             //writing the last record
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";
                        $this->log_msg("Count Function complete, check open_data_reduced.csv file!\n");

                }
        }

?>
