# DHT22

This project is a demonstration of the live temperature and humidity monitoring system with the help of DHT22 sensor, ESP8266, Arduino IDE and XAMPP software application.

The live temperature and humidity readings are obtained from the DHT22 sensor and sent to the PHP script where the data is received and insterted into the database table. The XAMPP application is an open source software stack which has the inbuilt versions of the apache web server and mysql. The apache web server is used for hosting the local webpage which displays the report of the live data recordings in the form a dashboard which contains the graphs and alert messages if the sensor values cross a specific threshold value. The mysql server is used for the creation of the database and the table to store the sensor values. The PHP script is used for inserting and fetching the sensor values from the database and displaying the dashboard in the web page running in the local apache web server. 
