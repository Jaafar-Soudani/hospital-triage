# Hospital Triage Application

This application simulates a hospital triage system, allowing staff members to register and treat patients based on the severity of their conditions.

## Features

- Register new patients with their name and condition severity (1-10)
- Treat the next patient in the queue
- View the current patient queue and list of treated patients
- Calculate approximate wait time for a patient based on their position in the queue

## Getting Started

1. Configure the `dbCreds.json` file with your database server credentials. This file should follow the JSON format:

```json
{
    "host": "localhost",
    "port": 5432,
    "db_name": "project",
    "db_username": "your_username",
    "db_password": "your_password"
}
```

# Instructions
Run `Main.php` to populate the database with dummy patient and staff data.

## Database Structure
Staff members are “hard-built” into the database.

The database can be reset at any time by running `Main.php`.

Condition severity is rated on a scale of 1-10 (1 for least urgent, 10 for most urgent).

Wait time is approximated using the condition severity metric: `time-to-treat (min) = 3 * condition-severity`.

## Usage
### Admin View
Staff members have access to the following functions:

- `registerPatient`: Adds a new patient using their name (String) and condition severity (int 1-10). Returns a 3-letter code for patient sign-in.
- `treatNextPatient`: Sends the next patient in the queue to an ER for treatment using their 3-letter code.
- `viewListPatient`: Shows the current queue and list of treated patients.

### Patient View
- `getWaitTime`: Calculates and returns the approximate wait time based on the position in the wait list, given the patient’s 3-letter code.
