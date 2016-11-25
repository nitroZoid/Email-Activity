<?php
//Author: Hari Eswar SM for Octane Marketing Pvt. Ltd.

error_reporting(E_ALL & ~E_NOTICE);
ini_set('max_execution_time', 10000);
date_default_timezone_set("Asia/Kolkata");
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', 10000);
ini_set('default_socket_timeout', 10000);
require_once 'database_config.php';


        class Load_Sent{
                public function put_sort_csv($hostname = HOST, $username = ROOT, $password = PASSWORD){
                        $this->log_msg("Program entered put_sort_csv function\n");
                        print "\nSorting process beginning\n";
                        $dbh = new PDO("mysql:host=$hostname;dbname=octane", $username, $password);
                        $this->log_msg("Connection established successfully for putting in csv!\n");
                        print "Enter the directory for the sorted campaign_sent_table\n";
                        $csv_file = stream_get_line(STDIN, 1024, PHP_EOL);

                        $dbh->query("SELECT * FROM ".CAMPAIGN_SENT_TABLE." ORDER BY emailid INTO OUTFILE '$csv_file';");
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
                        $this->log_msg("Program entered sent_collect function\n");
                        print "Function warming up\n";
                        $count_file = fopen("sent_data_reduced.csv", "wb");
                        $sent_data_file = fopen("sent_data.csv", "rb");
                        $sent_file_inactivity = fopen("sent_data_inactivity.csv", "wb");
                        $sent_reduced_array = array();
                        $only_email = array();
                        $overall_count = 0;
                        $this->log_msg("Starting the count function!\n");
                        $start_time = time();
                        print "Entering the loop\n";
                        while(!feof($sent_data_file)){
                                $row = fgetcsv($sent_data_file);
                                if(feof($sent_data_file))
                                        break;;
                                if($overall_count == 0){
                                        $prev = $row[0];
                                        //$first = $row[0];
                                        $overall_count = $overall_count+1;
                                }
                                $now = $row[0];
                                if(strcmp($prev, $now) != 0){
                                        $sent_reduced_array[0] = $prev;
                                        $only_email[0] = $prev;
                                        if($overall_count==1){
                                                $sent_reduced_array[1] = $count;
                                        }
                                        else{
                                                $sent_reduced_array[1] = $count+1;
                                        }
                                        fputcsv($count_file, $sent_reduced_array);
                                        fputcsv($sent_file_inactivity, $only_email);
                                        $prev = $now;
                                        $count = 0;
                                        $overall_count = $overall_count+1;
                                }
                                else{
                                        $count = $count + 1;
                                }
                        }

                        $sent_reduced_array[0] = $prev;
                        $sent_reduced_array[1] = $count+1;
                        fputcsv($sent_file_inactivity, $only_email);
                        fputcsv($count_file, $sent_reduced_array);              //writing last record
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";
                        $this->log_msg("Count Function complete, check sent_data_reduced.csv file!\n");

                }

                public function reduce_to_count_auto($file_location){
                        $this->log_msg("Program entered reduce_to_count_auto function\n");
                        print "Function warming up\n";
                        $count_file = fopen($file_location."sent_data_reduced.csv", "wb");
                        $sent_data_file = fopen($file_location."sent_data.csv", "rb");
                        $sent_file_inactivity = fopen($file_location."sent_data_inactivity.csv", "wb");
                        $sent_reduced_array = array();
                        $only_email = array();
                        $overall_count = 0;
                        $this->log_msg("Starting the count function!\n");
                        $start_time = time();
                        print "Entering the loop\n";
                        while(!feof($sent_data_file)){
                                $row = fgetcsv($sent_data_file);
                                if(feof($sent_data_file))
                                        break;;
                                if($overall_count == 0){
                                        $prev = $row[0];
                                        //$first = $row[0];
                                        $overall_count = $overall_count+1;
                                }
                                $now = $row[0];
                                if(strcmp($prev, $now) != 0){
                                        $sent_reduced_array[0] = $prev;
                                        $only_email[0] = $prev;
                                        if($overall_count==1){
                                                $sent_reduced_array[1] = $count;
                                        }
                                        else{
                                                $sent_reduced_array[1] = $count+1;
                                        }
                                        fputcsv($sent_file_inactivity, $only_email);
                                        fputcsv($count_file, $sent_reduced_array);
                                        $prev = $now;
                                        $count = 0;
                                        $overall_count = $overall_count+1;
                                }
                                else{
                                        $count = $count + 1;
                                }
                        }

                        $sent_reduced_array[0] = $prev;
                        $sent_reduced_array[1] = $count+1;
                        fputcsv($sent_file_inactivity, $only_email);
                        fputcsv($count_file, $sent_reduced_array);
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";
                        $this->log_msg("Count Function complete, check sent_data_reduced.csv file!\n");

                }

        }
?>
