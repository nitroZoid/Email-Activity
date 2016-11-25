<?php
//Author: Hari Eswar SM for Octane Marketing Pvt. Ltd.

error_reporting(E_ALL & ~E_NOTICE);
ini_set('max_execution_time', 10000);
date_default_timezone_set("Asia/Kolkata");
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', 10000);
ini_set('default_socket_timeout', 10000);
require_once 'config.php';

        class Reduce{
                public function log_msg($str){
                        $logfile = fopen(__DIR__."/logs/log_".date("Y-m-d").".txt", "a");
                        echo date("Y-m-d h:i:s")."| $str\n";
                        $log = date("Y-m-d h:i:s")."| $str\n";
                        fwrite($logfile, $log);
                        fclose($logfile);
                }

                public function reduce_data(){
                        $this->log_msg("Program entered reduce data function with the input choice\n");

                        $sent_data_file = fopen("sent_data_reduced.csv", "rb");
                        $open_data_file = fopen("open_data_reduced.csv", "rb");
                        $click_data_file = new SplFileObject("click_data_reduced.csv");

                        $reduce_data_file = fopen("reduce_data.csv", "wb");
                        $abnormal_file = fopen("abnormality.csv", "wb");
                        $reduce_array = array();
                        $row_sent = fgetcsv($sent_data_file);
                        $save_count = 0;
                        $click_data_file->seek(0);
                        $this->log_msg("Starting Loop for reduce!\n");
                        $start_time = time();
                        while(!feof($open_data_file) || !feof($sent_data_file)){
                                if(feof($open_data_file) || feof($sent_data_file))
                                        break;
                                $row_open = fgetcsv($open_data_file);
                                if($save_count == 0 && strcmp($row_open[0], $row_sent[0])==0){
                                        if(strcmp($row_open[0], $row_click[0])==0){
                                                $reduce_array[0] = $row_open[0];
                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                $reduce_array[3] = (float)$row_click[1]/(float)$row_sent[1];
                                                $reduce_array[4] = $row_open[3];
                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPEN_LIMIT){
                                                        fputcsv($reduce_data_file, $reduce_array);
                                                }
                                                else{
                                                        fputcsv($abnormal_file, $reduce_array);
                                                }
                                                $click_data_file->next();
                                        }
                                        else{
                                                $reduce_array[0] = $row_open[0];
                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                $reduce_array[3] = 0;
                                                $reduce_array[4] = $row_open[3];
                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPENS_LIMIT){
                                                        fputcsv($reduce_data_file, $reduce_array);
                                                }
                                                 else{
                                                        fputcsv($abnormal_file, $reduce_array);
                                                }

                                        }
                                        $save_count = $save_count + 1;
                                }

                                while(strcmp($row_open[0], $row_sent[0])!=0){
                                        $row_sent = fgetcsv($sent_data_file);
                                        if((float)$row_sent[1]!=0.0){
                                                if(strcmp($row_open[0], $row_sent[0])==0){
                                                        $row_click = str_getcsv($click_data_file->current());
                                                        if(strcmp($row_open[0], $row_click[0])==0){
                                                                $reduce_array[0] = $row_open[0];
                                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                                $reduce_array[3] = (float)$row_click[1]/(float)$row_open[2];
                                                                $reduce_array[4] = $row_open[3];
                                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPENS_LIMIT){
                                                                        fputcsv($reduce_data_file, $reduce_array);
                                                                }
                                                                 else{
                                                                        fputcsv($abnormal_file, $reduce_array);
                                                                }

                                                                $click_data_file->next();
                                                        }
                                                        else{
                                                                $reduce_array[0] = $row_open[0];
                                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                                $reduce_array[3] = 0;
                                                                $reduce_array[4] = $row_open[3];
                                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPENS_LIMIT){
                                                                        fputcsv($reduce_data_file, $reduce_array);
                                                                }
                                                                else{
                                                                        fputcsv($abnormal_file, $reduce_array);
                                                                }

                                                        }
                                                }
                                        }

                                }//end of while loop for matching sent and open emails
                        }       //end of !eof function
                        if(!feof($open_data_file)){
                                print "There seems to be a mismatch between the open_activity_data and the sent_data";
                                print "\nMismatch seems to occur at the following mail:\t";
                                print $row_open[0];
                        }

                        $this->log_msg("All done, check reduce_data.csv!\n");
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";

                }   //end of function reduce_data

                public function reduce_data_auto($file_location){
                        $this->log_msg("Program entered reduce data function with the input choice \n");
                        $sent_data_file = fopen($file_location."sent_data_reduced.csv", "rb");
                        $open_data_file = fopen($file_location."open_data_reduced.csv", "rb");
                        $click_data_file = new SplFileObject($file_location."click_data_reduced.csv");

                        $reduce_data_file = fopen($file_location."reduce_data.csv", "wb");
                        $reduce_array = array();
                        $row_sent = fgetcsv($sent_data_file);
                        $abnormal_file = fopen($file_location."abnormality.csv", "wb");
                        $save_count = 0;
                        $start_time = time();
                        $click_data_file->seek(0);
                        $this->log_msg("Starting Loop for reduce!\n");
                        while(!feof($open_data_file) || !feof($sent_data_file)){
                                if(feof($open_data_file) || feof($sent_data_file))
                                        break;
                                $row_open = fgetcsv($open_data_file);
                                if($save_count = 0 && strcmp($row_open[0], $row_sent[0])==0){
                                        if(strcmp($row_open[0], $row_click[0])==0){
                                                $reduce_array[0] = $row_open[0];
                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                $reduce_array[3] = (float)$row_click[1]/(float)$row_sent[1];
                                                $reduce_array[4] = $row_open[3];
                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPEN_LIMIT){
                                                        fputcsv($reduce_data_file, $reduce_array);
                                                }
                                                else{
                                                        fputcsv($abnormal_file, $reduce_array);
                                                }
                                                $click_data_file->next();
                                        }
                                        else{
                                                $reduce_array[0] = $row_open[0];
                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                $reduce_array[3] = 0;
                                                $reduce_array[4] = $row_open[3];
                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPEN_LIMIT){
                                                        fputcsv($reduce_data_file, $reduce_array);
                                                }
                                                else{
                                                        fputcsv($abnormal_file, $reduce_array);
                                                }
                                        }
                                        $save_count = $save_count + 1;
                                }

                                while(strcmp($row_open[0], $row_sent[0])!=0){
                                        $row_sent = fgetcsv($sent_data_file);
                                        if((float)$row_sent[1]!=0.0){
                                                if(strcmp($row_open[0], $row_sent[0])==0){
                                                        $row_click = str_getcsv($click_data_file->current());
                                                        if(strcmp($row_open[0], $row_click[0])==0){
                                                                $reduce_array[0] = $row_open[0];
                                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                                $reduce_array[3] = (float)$row_click[1]/(float)$row_open[2];
                                                                $reduce_array[4] = $row_open[3];
                                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPEN_LIMIT){
                                                                       fputcsv($reduce_data_file, $reduce_array);
                                                                }
                                                                else{
                                                                        fputcsv($abnormal_file, $reduce_array);
                                                                }
                                                                $click_data_file->next();
                                                        }
                                                        else{
                                                                $reduce_array[0] = $row_open[0];
                                                                $reduce_array[1] = (float)$row_open[2]/(float)$row_sent[1];
                                                                $reduce_array[2] = (float)$row_open[1]/(float)$row_sent[1];
                                                                $reduce_array[3] = 0;
                                                                $reduce_array[4] = $row_open[3];
                                                                if($reduce_array[4]/86400<=DAYS_LIMIT && $row_open[1]<=OPEN_LIMIT){
                                                                        fputcsv($reduce_data_file, $reduce_array);
                                                                }
                                                                else{
                                                                        fputcsv($abnormal_file, $reduce_array);
                                                                }
                                                        }
                                                }
                                        }

                                }//end of while loop for matching sent and open emails
                        }       //end of !eof function
                        $this->log_msg("All done, check reduce_data.csv!\n");
                        $time_taken = time() - $start_time;
                        print "\nLoop completed, you have made it in $time_taken seconds\n";

                }   //end of function reduce_data
        }       //end of class

?>
