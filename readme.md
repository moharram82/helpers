# Helpers

A collection of useful PHP functions for different purposes.

## Functions

- `getMySQLEnumValues`: Extracts options from MySQL's enum field definition into an array: enum('male','female').
- `validUsername`: Checks if the supplied string is a valid username that is: english only, no spaces, starts with letters only, contains only letters, numbers and underscores but does not end with an underscore.
- `isPasswordStrong`: Checks if password is strong containing at least: 1 uppercase letter, 1 lowercase letter, 1 number, 8 characters long.
- `isEnglish`: Checks if text is in english only letters.
- `validTimestamp`: Checks if a string is a valid timestamp.
- `removeHttpProtocol`: Removes protocol prefix (http://) or (https://) from a link.
- `trimExtraSpace`: Removes any extra white spaces from the beginning, end, and in between words.
- `cleanText`: Cleans text from any html and duplicate spaces or new lines.
- `validEmail`: Checks if the string is a valid email address.
- `validDate`: Checks if string is a valid date.
- `validUrl`: Checks if string is a valid URL address.
- `calculateAge`: Calculates the age for a given birth date and returns the age in years, months, days, decimal.
- `imgResize`: Resizes an image.
- `getIP`: Returns the IP address of the current internet session.
- `getUseragent`: Returns the useragent string of the current internet session.
- `randomString`: Generates random alpha-numeric string.
- `isWindows`: Checks if the current environment is Windows based.
- `removeDir`: Recursively deletes a directory and its entire contents.
- `scanDirForFiles`: Scans a dir and return an array of files based on file extension (jpg|png|gif).
