# How to set up a database configuration
Please use provided database.default.ini file and copy it as database.ini in the same folder (application/configs).
Than change the values for "development" section (if You are developer, working on a local machine), for both:
Zend_Db and Doctrine section, the database access data should be the same.

Database.ini file will not be in Your commits, as it is ignored by Git, so You will not overwrite Your fellow developer
local settings with Your own.

Don't change values in the production section, those are reserved for live version.

Values for staging are for test server, and values for testing are for unit testing.