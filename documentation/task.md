# task
-----

## GET
### config
Retrieves an existing task configuration

#### Query String parameters

* **`config[sessionId]`** numeric session ID (required)

*  **`config[taskId]`** numeric task ID (required). -1 returns latest task in session.

#### Remarks

When using a taskId = -1, the task may or may not be active.

This method is the same as that provided by **session**.

#### Example

```
<hostpath>/task.php?config[sessionId]=1377397537&config[taskId]=-1
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

## POST

### start
Starts a new task in the specified session.

#### Remarks
Only one task can be active at a time. If the session has an unfinished task, the current task is finished and the next task is started.

#### Request buffer
```javascript
{
    "start": {
        "sessionId": 1377453392
    }
}
```
#### Response buffer

If successful, a data buffer is returned in the response.

```javascript
{
    "data": {
        "studyId": "1234",
        "sessionId": "1377453392",
        "taskId": 1,
        "conditionId": "4",
        "startTime": "2013-08-25 11:23:47",
        "startPageHtml": "<p>These are the instructions for Task 1</p>",
        "startPageNextUrl": "http://students.washington.edu/rbwatson/hearts.html"
    }
}
```
If the session had an unfinished task, the task would be finished and the response contains an additional structure

```javascript
{
    "data": {
        "studyId": "1234",
        "sessionId": "1377453392",
        "taskId": 2,
        "conditionId": "4",
        "startTime": "2013-08-25 11:27:19",
        "startPageHtml": "<p>These are the instructions for Task 2</p>",
        "startPageNextUrl": "http://students.washington.edu/rbwatson/spades.html"
    },
    "error": {
        "openTask": {
            "finishTime": "2013-08-25 11:27:19",
            "taskId": "1",
            "message": "The previous task was not finished."
        }
    }
}
```

### finish
Finishes a task in the specified session

#### Request buffer
```javascript
{
    "finish": {
        "sessionId": 1377453392,
        "taskId": 2
    }
}
```

#### Response buffer
If the operation was successful, a data buffer is returned in the response.

```javascript
{
    "data": {
        "taskId": 2,
        "finishTime": "2013-08-25 11:32:44",
        "finishPageHtml": "<p>You just finished Task 2</p>",
        "finishPageNextUrl": "http://wlux.uw.edu/rbwatson/task-start.php"
    }
}
```
