## Architechture
The app is hosted using a php server. The php server processes the request and returns an appropriate HTML file (with embdedded CSS/JS)
The php server communicates with a locally hosted PostgreSQL database. 

### QUICK START: Configure dbCreds.json to your database server. Then Run Main.php to populate database with dummy patient and staff data.

Database connection credentials are in the dbCreds.json file (this file is .gitignored) following JSON formatting:
```JS
{
    "host":"localhost",
    "port" : 5432,
    "db_name" : "project",
    "db_username" : "postgres",
    "db_password" : "postgres"
}
```
Staff members are "hard-built" into the database. The database can be set/reset at anytime running the ``Main.php`` file. 
Condition severity is rated on a scale of 1-10 (1 for least urgent, 10 for most urgent). Wait time is approximated using this metric (time-to-treat (min)= 3 * condition-severity). 

### Admin view
The staff members have access to 3 different functions:
- ``registerPatient``: adding a new patient using their name (String) and condition severity (int 1-10). This returns the 3-letter code used for patient sign-in
- ``treatNextPatient``: sends the nextmost patient to an ER for treatement using their 3-letter-code
- ``viewListPatient``: shows the current queue and list of treated patient

### Patient view:
- ``getWaitTime``: calculates and returns the approximate wait time based on the position in the wait list given their 3-letter-code. 

### Usage
The app is accessed via the index.php file. This serves two HTML forms corresponding to each portal (patient/staff)
[SCREENSHOT_INDEX]
Upon successful sign-in, the patient ``patient/patient_index.php`` is shown information about their registry (wait time, condition severity, arrival time,..)
[SCREENSHOT_INDEX_PATIENT]
The staff view ``admin/admin_index.php`` presents staff membes with the three functionalities they need to perform (treatNextPatient, viewListPatient, registerPatient)
[SCREENSHOT_INDEX_ADMIN]
[SCREENSHOT_REGISTER]
[SCREENSHOT_VIEW_LIST]
[SCREENSHOT_NEXT_LIST]
