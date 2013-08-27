# gratuity
-----

## GET

Gets the data that participants entered for their gratuity.

**NOTE: Data that would make it possible to link a gratuity record to a participant session cannot be stored in this table.**

#### Query String parameters

* **`studyId`** numeric study ID (required)

#### Example

```
<hostpath>/gratuity.php?studyId=1234
```
Returns the following response that contains the gratuity entries submitted for study 1234.

```javascript
{
    "data": [
        {
            "studyId": "1234",
            "email": "me@you.com",
            "comments": "I hope this really works!"
        },
        {
            "studyId": "1234",
            "email": "you@me.com",
            "comments": "I hope this really works!!!!"
        }
    ]
}
```

## POST

### gratuity

Writes a gratuity record to the database

#### Example

```
<hostpath>/gratuity.php
```
With the following request buffer

```javascript
{
    "gratuity": {
        "studyId": 1234,
        "email": "you@me.com",
        "comments": "I hope this really works!!!!"
    }
}
```

Returns the following response buffer if the call was successful.

```javascript
{
    "data": {
        "studyId": 1234,
        "email": "you@me.com",
        "comments": "I hope this really works!!!!"
    }
}
```
