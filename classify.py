import csv;
import numpy as np;
import scipy;
import sklearn;
from sklearn import cluster;
from scipy.cluster.vq import whiten;
import time;
outfile = 'E:/program_test/';
tol = 10000;            #tol = |superactive - active|

#declaration of all required variables
first_minus_sent = 0.0;
first_minus_sent_avg = 0.0;

def cluster_function(x, emailid, lp, max_click_pos):
        normal_label = 0; active_label = 1; superactive_label = 2;
        label_loop_count = 0;
        while True:
                a = b = c = 0.0;                #a - Normal, b - Active, c - Superactive
                print "Clustering beginning, K-Means to be done after whitening\n";
                print "Now Superactive label is ", superactive_label;
                kmeans = cluster.KMeans(n_clusters=3, n_init = 20, max_iter = 1000, tol = 0.0001);
                whitened = whiten(np.array(x));
                kmeans.fit_predict(whitened);
                labels = kmeans.labels_;
                print "Writing new labels\n";
                max_click_label = labels[max_click_pos];
                i = 0;
                write_label_array = ['']*2;
                weight_c = 0.0;
                csv_label_write = open(outfile+'labels.csv', 'w');
                labelwriter = csv.writer(csv_label_write, delimiter=',');
                for i in range(lp):
                        write_label_array[0] = emailid[i];
                        if labels[i] == normal_label:
                                write_label_array[1] = "Normal";
                                a = a +1;
                        elif labels[i] == superactive_label:
                                write_label_array[1] = "Superactive";
                                c = c +1;
                        else:
                                write_label_array[1] = "Active";
                                b = b + 1;
                        labelwriter.writerow(write_label_array);
                weight_c = (100*c)/(a + b + c);
                print "Normal emailid ",a, ", Active emailid ", b, ", Superactive emailid ", c;
                max_count = max(a, b, c);
                if max_count == a:
                        cal_diff = abs(b-c);
                        if cal_diff>tol:
                                if b>=c:
                                        return weight_c;
                                        break;
                        else:
                                if label_loop_count==0:
                                        superactive_label = max_click_label;
                                        label_loop_count = label_loop_count+1;
                                        print "Inside label_loop_count loop\n";
                                else:
                                        print "Inside the exit program\n";
                                        return weight_c;
                                        break;


if __name__ == "__main__":
        i = 0;
        lp = len(list(csv.reader(open(outfile+'reduce_data.csv'))));
        x = [[0.0]*4 for i in range(lp)];
        emailid = ['']*lp;
        print "Total array size of reduce data ", lp;
        weight_c = 0.0;
        i = 0;
        loop_count = 0;
        csv_reduce_read = open(outfile+'reduce_data.csv', 'r');
        data = csv.reader(csv_reduce_read, delimiter = ',');
        for row in data:
                emailid[i] = row[0];
                x[i][0] = float(row[1]);
                x[i][1] = float(row[2]);
                x[i][2] = float(row[3]);

                if x[i][2] == 1 and loop_count == 0:
                        max_click_pos = i;
                x[i][3] = float(row[4]);
                if x[i][3] > first_minus_sent:
                        first_minus_sent = x[i][3];
                first_minus_sent_avg += float(row[4]);
                #first_minus_sent_avg += float(row[3]);
                i = i+1;
        j = 0;
        first_minus_sent_avg = first_minus_sent_avg/i;
        #Normalized
        for j in range(lp):
                x[j][3] = (first_minus_sent - x[j][3])/first_minus_sent_avg;
        weight_c = cluster_function(x, emailid, lp, max_click_pos);
        print "Percentage of Superactive emailids = ", weight_c;
