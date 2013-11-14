# session
-----
[GET](#get)
* [config](#config)
* [log](#log)
 
[POST](#post)
* [start](#start)


-----
## GET
### config
Retrieves an existing study configuration

#### Query String parameters

* **`config[sessionId]`** numeric session ID (required)

*  **`config[taskId]`** numeric task ID (required). -1 returns latest task in session.

#### Remarks

When using a taskId = -1, the task may or may not be active.

#### Example

```
<hostpath>/session.php?config[sessionId]=1377397537&config[taskId]=-1
```
Returns the conifguration used by the last task opened for session 1377397537.

```javascript
{
    "data": {
        "studyId": "1234",
        "sessionId": "1377397537",
        "taskId": "3",
        "conditionId": "1",
        "conditionCssUrl": "css/style1.css",
        "taskBarCssUrl": "http://wlux.uw.edu/rbwatson/wluxTaskBar.css",
        "startUrl": "http://students.washington.edu/rbwatson/hearts.html",
        "returnUrl": "http://wlux.uw.edu/rbwatson/task-finish.php",
        "buttonText": "End task",
        "tabShowText": "Show",
        "tabHideText": "Hide",
        "taskText": "This is the third task to do.",
        "taskHtml": null,
        "startPageHtml": "<p>These are the instructions for Task 3, Condition 1.<br/>However, we wouldn't show the condition to the participant.</p>",
        "finishPageHtml": "<p>You just finished Task 3, Condition 1.<br/>However, we wouldn't show the condition to the participant.</p>",
        "startPageNextUrl": "http://students.washington.edu/rbwatson/hearts.html",
        "finishPageNextUrl": "http://wlux.uw.edu/rbwatson/task-start.php",
        "measuredTask": "1",
        "taskType": "external",
        "autoConditionId": "0"
    }
}
```
### log
Gets the session log entry for the specified session and task.

#### Query String parameters

* **`log[sessionId]`** numeric session ID (required)

*  **`log[taskId]`** numeric task ID (required).

#### Remarks

See the log.php functions for the transaction data.

#### Example

```
<hostpath>/session.php?log[sessionId]=1377397537&log[taskId]=2
```
Returns the summary of task 2 for session 1377397537.

```javascript
{
    "data": {
        "studyId": "1234",
        "sessionId": "1377397537",
        "taskId": "2",
        "conditionId": "1",
        "startTime": "2013-08-24 19:46:07",
        "endTime": "2013-08-24 19:46:12"
    }
}
```

## POST

### start
Starts a new test session for the specified study.

#### Request buffer
```javascript
{
    "start": {
        "studyId": 1234
    }
}
```
#### Response buffer

If successful, a data buffer is returned in the response.

```javascript
{
    "data": {
        "studyId": 1234,
        "sessionId": 1377453392,
        "conditionId": 4,
        "startTime": "2013-08-25 10:56:32"
    }
}
```
