# study
-----
[GET](#get)
* [config](#get_config)
* [allIds](#get_allids)

-----
<a name="get"></a>
## GET
<a name="get_config"></a>
### config
Retrieves an existing study condiguration

#### Query String parameters

* **`config[studyId]`** numeric study ID (required)

* **`config[taskId]`** numeric task ID (optional). If absent, all tasks are returned.

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

## Study config data object
The study config data object passes data from the WebLabUX server to the site/page being tested 
so the WLUX_Instrumentation.js file can configure the page layout for the study session in progress. 
The data is passed as a jsonp object and currently contains these fields. The nature of this design, however,
is to allow this object to be updated as necessary so, confirm these fields with the actual payload.

| Data field | Description |
|--------------|------------------------------------------------------------------| 
| *studyId* | the study for which this session is being run |
| *sessionId* | the current test session  |
| *taskId* | the current task |
| *conditionId* | the condition ID of the current session -- used by logger calls |
| *conditionCssUrl* | The URL of the CSS to use for the current session -- This is usually a .css file on the WebLabUX server that is associated with the study. (was: *cssURL*) |  
| *taskBarCssUrl* | The URL of the CSS to use for the taskBar -- This is usually configured so the task bar affordances don't interfere with pages on the study site. (was: *taskBarCSS*) |
| *startUrl* | The first page of an external task. **Possibly to be superceded by *startPageNextUrl*. **|
| *returnUrl* | the URL to the return page in WebLabUX. -- This is typically the post-study questionnaire, but could be configured to point to the next task in a multi-task study. (was: *returnURL*) |
| *buttonText* | the text to display on the task button. -- Usually something like "End Task" or "End study." |
| *tabShowText* | Text to display in the show/hide task button when the task bar is hidden. |
| *tabHideText* | Text to display in the show/hide task button when the taks bar is visible. |
| *taskText* | The unformatted text to display in the task bar. |
| *taskHtml* | Formatted HTML to display in the task bar. If both this field and the *taskText* are defined, only this field will be used. (was: *taskHTML*) |
| *startPageHtml* | HTML text that is embedded into the task-start page when the page is opened. |
| *finishPageHtml* | HTML text that is embedded into the task-finish page when the page is opened. |
| *startPageNextUrl* | The URL of the page to open after the task-start page. |
| *finishPageNextUrl* | The URL of the page to open after the task-finish page. |
| *measuredTask* | Whether this task should be included in task-time computations. | 
| *taskType* | Enum that describes the nature of the task: **external** - task is performed on an external site; **single** - task consists of only the task-start page and the instructions it contains (usually a survey). The task-start page ignores the *finishPageHtml* and the *startPageNextUrl* fields when showing a single-page task. |

Any other fields that might appear in the configuration data object should not be used as they might disappear without notice.

<a name="get_allids"></a>
### allIds
Retrieves a list of study configuration IDs or a list of the tasks and condition IDs for a specific study.

#### Query String parameters

* **`allIds[studyId]=[*|studyId]`** command

#### Remarks

None

#### Example
```
<hostpath>/study.php?allIds[studyId]=*
```
Returns the following response that lists the IDs of the study configurations in the database.

```javascript
{
    "data": {
        "count": 4,
        "studyIds": [
            "1234",
            "2525",
            "1999",
            "2001"
        ]
    }
}
```
```
<hostpath>/study.php?allIds[studyId]=1234
```
Returns the following response that lists the tasks and conditions defined for study ID: 1234.

```javascript
{
    "data": {
        "studyId": "1234",
        "conditionCount": 4,
        "conditionsBalanced": true,
        "count": 3,
        "tasks": {
            "1": [
                "1",
                "2",
                "3",
                "4"
            ],
            "2": [
                "1",
                "2",
                "3",
                "4"
            ],
            "3": [
                "1",
                "2",
                "3",
                "4"
            ]
        }
    }
}
```

| Data field | Description |
|--------------|------------------------------------------------------------------| 
| count | The number of study IDs or tasks returned |
| studyIds | An array of all study IDs in the configuration database |
| studyId | Tthe study for which a list of tasks are defined |
| conditionCount | The number of conditions defined for each task. This value is only valid if __conditionsBalanced__ is __true__. |
| conditionsBalanced | __true__ when all task have the same number of conditions defined; otherwise __false__. |
| tasks | an array of the tasks defined for the study. In each task is a list of the conditionIds defined for the task. |
