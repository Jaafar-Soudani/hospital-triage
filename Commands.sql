CREATE TABLE Patients (
    id INT AUTO_INCREMENT PRIMARY KEY,      -- Unique identifier for each patient
    name VARCHAR(255) NOT NULL,             -- Patient's name
    condition_severity INT NOT NULL CHECK (condition_severity BETWEEN 1 AND 10), -- Condition severity rating
    code CHAR(3) NOT NULL UNIQUE,           -- Unique 3-letter code for patient sign-in
    arrival_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Time the patient entered the queue
    is_treated BOOLEAN NOT NULL DEFAULT FALSE -- Status flag indicating if the patient has been treated
);


CREATE TABLE Staff (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Unique identifier for each staff member
    name VARCHAR(255) NOT NULL          -- Staff member's name
);

-- Insert a new patient into the Patients table
INSERT INTO Patients (name, condition_severity, code, arrival_time, is_treated)
VALUES ('John Doe', 7, 'ABC', NOW(), FALSE);


-- Calculate wait time for the patient with a specific name and code
SELECT SUM(3 * condition_severity) AS wait_time
FROM (
    SELECT condition_severity
    FROM Patients
    WHERE is_treated = FALSE
      AND arrival_time <= (
          SELECT arrival_time
          FROM Patients
          WHERE code = 'ABC'
            AND is_treated = FALSE
      )
) AS SubQuery;


-- Insert a new patient into the Patients table
INSERT INTO Patients (name, condition_severity, code)
VALUES ('John Doe', 7, 'ABC');


-- Select the next patient to treat based on highest condition severity and earliest arrival time
SELECT code
FROM Patients
WHERE is_treated = FALSE
ORDER BY condition_severity DESC, arrival_time ASC
LIMIT 1;
-- Update the patient's is_treated status
UPDATE Patients
SET is_treated = TRUE
WHERE code = 'ABC';

-- Select all patients currently in the queue 
-- (formatting is done on clientside using array methods (filter, sort, ...))
SELECT id, name, condition_severity, code, arrival_time
FROM Patients;