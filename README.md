# honours-server

The purpose of this project is to monitor a smartphone.

This repository is useful only if you want to host the website and database on your own server. 
Otherwise just follow the ReadMe file of the application's repository

You can download the Android application on this repository
https://github.com/bricemeunier/Honours

## LEGAL WARNING
IT IS ILLEGAL TO USE THIS SOFTWARE TO MONITOR THE PHONE OF SOMEONE YOU ARE NOT RESPONSIBLE FOR!

Such behaviour are severely condemned in most country including the UK
http://www.legislation.gov.uk/ukpga/1990/18

## Installation
First clone this repository on your server in the LAMP folder. If your server is an AWS EC2 instance, 
follow this [tutorial](https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/ec2-lamp-amazon-linux-2.html) before cloning this project.

Once cloned, create a database named "honours". You can do it via PHPMyAdmin if you are not comfortable with command line.

Then modify the DatabaseConfig.php file with your login information.

You're ready now! Do not forget to update the URL on the Android application otherwise it won't work.

## Credit
Created for Robert Gordon University by Brice MEUNIER

Supervisor : Dr. David Corsar

