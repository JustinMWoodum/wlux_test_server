# log
=====
## GET

Gets a collection of interaction log records.

#### Query parameters
The query parameters identify the log records to return in the response buffer.

* ```sessionId``` - the sessionId of the session (required)
* ```taskId``` - the taskId of the task (optional)

#### Remarks

If the taskId is 0 or not specified, the interaction log records for all tasks are returned, grouped by taskId.

#### Response buffer

**TODO:** Add sample buffer

## POST

The POST cammands record transaction events in the interaction log.

### open

The Open log entry is called to record when a new page is opened.

| Data field | Description |
|--------------|------------------------------------------------------------------| 
| *clientTimestamp* | The JavaScript formatted timestamp taken from the client browser. |
| *sessionId* | The session ID for the current session - received from WebLabUX in the study config data. |
| *taskId* | the current task in the current session (currently this always 1). |
| *conditionId* | The condition ID for the current session - received from WebLabUX in the study config data. |
| *fromUrl* |  The URL of the page from which the call was made (i.e the page that was just opened. |

#### Response buffer
Sample Open request data block:
```javascript
{
  "open": {
    "clientTimestamp": 1375043148,
    "sessionId": 12345,
    "taskId": 1,
    "conditionId": 2,
    "pageUrl": "http://students.washington.edu/rbwatson/hearts.html"
  }
}
```

###	transition

A transition log entry is used to log when a link is clicked that will navigate to another location (either within the page or in another page).

| Data field | Description |
|--------------|------------------------------------------------------------------| 
| *clientTimestamp* | The JavaScript formatted timestamp taken from the client browser. |
| *sessionId* | The session ID for the current session - received from WebLabUX in the study config data. |
| *taskId* | the current task in the current session (currently this always 1). |
| *conditionId* | The condition ID for the current session - received from WebLabUX in the study config data. |
| *fromUrl* |  The URL of the page that contains the link that was clicked. |
| *toUrl* |  The URL of the link target (destination) page. |
| *linkClass* |  The value of the **class** attribute of the link, if one is defined. |
| *linkId*   |  The value of the **id** attribute of the link, if one is defined. |
| *linkTag* | Reserved. |


#### Response buffer

Sample Transition request data block:
```javascript
{
  "transition": {
    "clientTimestamp": 1375043148,
    "sessionId": 12345,
    "taskId": 1,
    "conditionId": 2,
    "fromUrl": "http://students.washington.edu/rbwatson/hearts.html",
    "toUrl": "http://students.washington.edu/rbwatson/spades.html",
    "linkClass": "a_class",
    "linkId": "a_Id",
    "linkTag": "a"
  }
}
```
