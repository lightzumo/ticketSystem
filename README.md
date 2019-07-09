# Ticket System
A Ticket System built for my Web Development/Database course in the BTSi.
This Ticket System has a built-in Translation application using an Google Translate API. It also has a Subcription System that
let users know when their ticket gets updated (Twitter Message using Twitter API and via Email).
This application only uses 3 translation languages, English, French and German.


# Installation
First you need to execute the SQLdatabaseScript.sql in a mySQL database. This will install the database itself.
The you can copy all the files to your WebServer. Make sure you have PHP Mail Pear and PDO activated.

# Extra Informations
In the ActivityDiagram folder you will find the activity diagram for each stored Procedures created.
When you first install the database you will by default create a Technician called root, its password is also root, please feel free to change the password. In order to create a technician you will need to create it manually and please don't forget to hash the technicians password.

# What I learned

- Use of Google API, Twitter API
- Use of stored Procedures in MySQL
- Use of Materialize
