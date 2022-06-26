# Niswey

In this project I have created CRUD operations for contacts.
A simple web pages were designed in blade templates for CRUD operations.

The additional feature is added of import contacts.
Here We are feeding xml file in a request to import contacts.
XML file restrictions are that file cannot be more than 2 MB and file's mime type should be .xml.
I am dividing data fetched from xml file into chunks of 10, then im putting them in queue using Jobs.
Then I am sending email after import is done.
