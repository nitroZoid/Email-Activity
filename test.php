<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('max_execution_time', 10000);

        require_once 'load_data.php';
        require_once 'sent_collect.php';
        require_once 'open_collect.php';
        require_once 'click_collect.php';
        require_once 'ultimate_reduce.php';
        $config = $argv[1];
        if($config){
                require_once $config.'.php';
                $a = new Load();
                $a->welcome();
                $a->delete_auto(DELETE_EXISTING_DATA);
                $a->load_sent_data_auto(INPUT_SENT_PATH, LOAD_DATA_SENT);
                $a->load_data_auto_open(INPUT_OPEN_PATH, LOAD_DATA);
                $a->load_data_auto_click(INPUT_CLICK_PATH, LOAD_DATA);

                $a->put_sort_csv_sent(OUTPUT_PATH, SORT_SENT_DATA);
                $a->put_sort_csv_open(OUTPUT_PATH, SORT_OPEN_DATA);
                $a->put_sort_csv_click(OUTPUT_PATH, SORT_CLICK_DATA);

                $d = new Load_Sent();
                $d->reduce_to_count_auto(OUTPUT_PATH);
                $b = new Load_Open();
                $b->reduce_to_count_auto(OUTPUT_PATH);
                $c = new Load_Click();
                $c->reduce_to_count_auto(OUTPUT_PATH);
                $e = new Reduce();
                $e->reduce_data_auto(OUTPUT_PATH);
        }
        else{
                $a = new Load();
                $a->welcome();
                print "\nDo you want to delete the already existing entries in the open_activity_table and click_activity_table?(Y/N)\n";
                $flag_del =  stream_get_line(STDIN, 1024, PHP_EOL);
                $a->delete($flag_del);
                print "Do you want to load new data to the existing database from new csv files for sent_table?(Y/N)\n";
                $flag_load_sent_data = stream_get_line(STDIN, 1024, PHP_EOL);
                $a->load_sent_data($flag_load_sent_data);
                print "Do you want to load new data to the existing database from new csv files for open_activiy_table and click_activity_table?(Y/N)\n";
                $flag_load_data = stream_get_line(STDIN, 1024, PHP_EOL);
                $a->load_data($flag_load_data);
                print "\nDo you want to sort the sent, open and click tables and put into a file?";
                $flag_sort = stream_get_line(STDIN, 1024, PHP_EOL);
                if($flag_sort == 'Y'||$flag_reduce_program=='y'){
                        $d2 = new Load_Sent();
                        $d2->put_sort_csv();
                        $b2 = new Load_Sent();
                        $b2->put_sort_csv();
                        $c2 = new Load_Click();
                        $c2->put_sort_csv();
                }
                print "\nDo you want to run the reduce program for sent, open, click data files? (Make sure the data files are in the same folder as the programs open_collect, sent_collect, click_collect)\n";
                $flag_reduce_program = stream_get_line(STDIN, 1024, PHP_EOL);
                if($flag_reduce_program=='Y'||$flag_reduce_program=='y'){
                        $d = new Load_Sent();
                        $d->reduce_to_count();
                        $b = new Load_Open();
                        $b->reduce_to_count();
                        $c = new Load_Click();
                        $c->reduce_to_count();
                        $e = new Reduce();
                        $e->reduce_data();
                }
                else{
                        echo "\nYou have chosen not to run the reduce functions for all the files\n Exiting...";
                }

        }
?>
