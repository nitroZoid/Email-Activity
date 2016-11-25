<?php
//Author: Hari Eswar SM for Octane Marketing Pvt. Ltd.

error_reporting(E_ALL & ~E_NOTICE);
ini_set('max_execution_time', 10000);
date_default_timezone_set("Asia/Kolkata");
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', 10000);
ini_set('default_socket_timeout', 10000);
require_once 'database_config.php';
        class Load_Click{
                public function put_sort_csv($hostname = HOST, $username = USERNAME, $password = PASSWORD){
                        $this->log_msg("Program entered put_sort_csv function\n");
                        print "\nSorting process beginning\n";
                        $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                        $this->log_msg("Connection established successfully for putting in csv!\n");
                        print "Enter the directory for the sorted click_activity_table!\n";
                        $csv_file = stream_get_line(STDIN, 1024, PHP_EOL);
                        $dbh->query("SELECT emailid, TIMESTAMPDIFF(second, campaign_sent_time, click_time) AS click_minus_sent FROM ".CLICK_ACTIVITY_TABLE." INNER JOIN ".CAMPAIGN_TABLE." ON ".CAMPAIGN_TABLE.".campaignid = ".CLICK_ACTIVITY_TABLE.".campaignid ORDER BY emailid into outfile '$csv_file' fields enclosed by '"."\"' terminated by ',' lines terminated by '\n';");
                        $this->log_msg("Ordering done\n");
                }

                public function log_msg($str){
                        $logfile = fopen(__DIR__."/logs/log_".date("Y-m-d").".txt", "a");
                        echo date("Y-m-d h:i:s")."| $str\n";
                        $log = date("Y-m-d h:i:s")."| $str\n";
                        fwrite($logfile, $log);
                        fclose($logfile);
                }

                public function reduce_to_count(){

                        $this->log_msg("Program entered click collect function\n");
                        print "\nFunction warming up\n";
                        $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                        $this->log_msg("Connection established successfully for putting count in csv!\n");

                        $count_file = fopen("click_data_reduced.csv", "wb");
                        $click_data_file = fopen("click_activity_data.csv", "rb");
                        $click_activity_array = array();
                        $overall_count = 0;

                        $this->log_msg("Starting the count function!\n");

                        $start_time = time();
                        print "\nEntering the loop, it's show time\n";
                        while(!feof($click_data_file)){
                                $row = fgetcsv($click_data_file);
                                if(feof($click_data_file))
                                        break;
                                if($overall_count == 0){
                                        $prev = $row[0];
                                        //$first = $row[0];
                                        $overall_count = $overall_count+1;
                                }
                                $now = $row[0];
                                if(strcmp($prev, $now) != 0){
                                        $click_activity_array[0] = $prev;
                                        $only_email[0] = $prev;
                                        if($overall_count==1){
                                                $click_activity_array[1] = $count;
                                        }
                                        else{
                                                $click_activity_array[1] = $count+1;
                                        }
                                        fputcsv($count_file, $click_activity_array);
                                        $prev = $now;
                                        $count = 0;
                                        $overall_count = $overall_count+1;
                                }
                                else{
                                        $count = $count + 1;
                                }
                        }

                        $click_activity_array[0] = $prev;
                        $click_activity_array[1] = $count+1;
                        fputcsv($count_file, $click_activity_array);   //writing the last record
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds";
                        $this->log_msg("Count Function complete, check click_data_reduced.csv file!\n");

                }

                public function reduce_to_count_auto($file_location){
                        $this->log_msg("Program entered click collect function automatically\n");
                        print "Function warming up\n";
                        $count_file = fopen($file_location."click_data_reduced.csv", "wb");
                        $click_data_file = fopen($file_location."click_activity_data.csv", "rb");
                        $click_activity_array = array();
                        $overall_count = 0;
                        $this->log_msg("Starting the count function!\n");
                        $start_time = time();
                        print "Entering the loop\n";
                        while(!feof($click_data_file)){
                                $row = fgetcsv($click_data_file);
                                if(feof($click_data_file))
                                        break;
                                if($overall_count == 0){
                                        $prev = $row[0];
                                        //$first = $row[0];
                                        $overall_count = $overall_count+1;
                                }
                                $now = $row[0];
                                if(strcmp($prev, $now) != 0){
                                        $click_activity_array[0] = $prev;
                                        $only_email[0] = $prev;
                                        if($overall_count==1){
                                                $click_activity_array[1] = $count;
                                        }
                                        else{
                                                $click_activity_array[1] = $count+1;
                                        }
                                        fputcsv($count_file, $click_activity_array);
                                        $prev = $now;
                                        $count = 0;
                                        $overall_count = $overall_count+1;
                                }
                                else{
                                        $count = $count + 1;
                                }
                        }
                        $click_activity_array[0] = $prev;
                        $click_activity_array[1] = $count+1;
                        fputcsv($count_file, $click_activity_array);
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";
                        $this->log_msg("Count Function complete, check click_data_reduced.csv file!\n");

                }
        }

?>
