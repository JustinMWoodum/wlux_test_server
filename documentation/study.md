# study
======

## GET
### config
Retrieves an existing study condiguration

#### Query String parameters

* **`config[studyId]`** numeric study ID (required)

*  **`config[taskId]`** numeric task ID (optional). If absent, all tasks are returned.

* **`config[conditionId]`** numeric condition ID (optional). If absent, all conditions are returned.
 
#### Remarks

Combinations of missing optional parameters will return all versions of the missing parameter. For example, specifying the study ID and the task ID will return all configurations for that study and task. Likewise, specifying the study ID and the condition ID will return all tasks with that condition for the specified study.

#### Example

```
<hostpath>/study.php?config[studyId]=1234&config[conditionId]=4&config[taskId]=1
```
Returns the following response that describes the cofiguration condition 4 for task 1 of study 1234.

```javascript
{
    "data": {
        "studyId": "1234",
        "sessionId": "0",
        "taskId": "1",
        "conditionId": "4",
        "conditionCssUrl": "css/style4.css",
        "taskBarCssUrl": "http://wlux.uw.edu/rbwatson/wluxTaskBar.css",
        "startUrl": "http://students.washington.edu/rbwatson/hearts.html",
        "returnUrl": "http://wlux.uw.edu/rbwatson/task-finish.php",
        "buttonText": "End task",
        "tabShowText": "Show",
        "tabHideText": "Hide",
        "taskText": "This is the first task to do.",
        "taskHtml": null,
        "startPageHtml": "<p>These are the instructions for Task 1.</p>",
        "finishPageHtml": "<p>You just finished Task 1.</p>",
        "startPageNextUrl": "http://students.washington.edu/rbwatson/hearts.html",
        "finishPageNextUrl": "http://wlux.uw.edu/rbwatson/task-start.php",
        "measuredTask": "1",
        "taskType": "external"
    }
}
```