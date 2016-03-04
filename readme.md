# Reset

A tool for eve online which simply temporarily resets all of your standings to neutral. It uses CREST contacts writings to add anyone set blue or red and corp or alliance level as a neutral contact.

This app was written for the EVE API Challenge in March 2016.

It has two functions:
1. Read all of the standings set by your corp or alliance from the XML API, generate a matching contact for each with neutral standing and write that to crest. Saving a record of each in a database as it goes.
2. Remove all of these contacts, leaving your client how it was before.

No data is stored long term - the database is only used to remember what has been set on a character, so that it can be removed.

## TODO

-Much better error handling.
-Check API key has teh right access before saving to database.
-Return meaningful error messages when crest is broken.

## License

Reset is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
