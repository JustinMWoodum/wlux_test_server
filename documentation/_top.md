# WebLabUX Web Service Interface
This topic describes the back-end data interface used to manage  WebLabUX study sessions.

The web service interface supports these end points and methods

* [study](study.md) - functions that have to do with study configuration
* [session](session.md) - functions that have to do with study sessions
* [task](task.md) - functions that have to do with tasks in study sessions
* [log](log.md) - functions that have to do with reading and writing interaction logs

## Normal use case of APIs in a typical study

This section describes the use case for which these APIs are designed.

### Terms

* **study** - a plan to collect user interaction data from participants. A study's _study protocol_ describes the individual steps and tasks of a study and is modeled as **tasks** by using the web service.
* **session** - an instance of a study that is initiaited by/for a participant.
* **task** - an individual step in a _study protocol_ as modeled by the web service interface. To the web service interface, a _task_ could be include activities such as reading text pages, presenting surveys to the particpant, or presenting tasks for a participant to perform while their actions are tracked by the web service.
* **participant** - the individual performs the tasks of a study and whose actions are tracked.
* **researcher** - the individual who defines, monitors, and analyzes a _study_.
* **log** - the recorded interactions measured during a session
 
### Overview

Studies have three phase: 

1. **definition** - During the _definition_ phase, the study protocol is modeled as tasks.
2. **data collection** - During the _data collection_ phase, a session is created for each new participant to perform the _tasks_ defined by the study protocol.
3. **data analysis** - After _data collection_ has completed, the researcher reviews tje interaction data recorded during the sessions.

### Calling the web service interfaces from within a web page

#### Study Welcome page.

In the first web page that a potential participant opens, a new study session is created. This code snippet creates a new study session.

```javascript
var postResult = $j.post ('<hostname>/session.php', {"start": {"studyId" : studyId}},"json");
postResult.done (function (response) {
    if (response.data !== undefined) {
        // initialize the page for this session
    }
};
```
The response data buffer, shown here, contains information about the nes session that will be used by subsequent pages in the study.

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
 NOTE: the web service methods support a JSONP interface, which might be necessary in some circumstances. In the JSONP return the response would be formatted as the folllowing, where ```callBackFunction``` is the value of the ```callback``` query string parameter.
```javascript
callBackFunction ( {
        "studyId": 1234,
        "sessionId": 1377453392,
        "conditionId": 4,
        "startTime": "2013-08-25 10:56:32"
    })
```

With the data returned by the start command, the welcom page can call the first task start page. The call must include the ```session ID``` as a query parameter or as post data so the task page has the correct session context.

#### Study task-start page

The task-start page can have two functions:  
1. Show the participant information they'll need before they start performing the task.  
2. Include the information for the entire task--for example, include a survey page.  

In the first function, the next page after the task-start page is a task-performance page, such as a page on the site under test. The participant will return from the task-performance pages to the task-finish page, described below. 

In the second function the entire task takes palce in the task-start page, such as to fill out a survey page. The page that follows this page is the task-start page of the next task.

The task-start page calls the task method with the start command, as is shown in this example.

```javascript
// sessionId is read from the query string
var postResult = $j.post ('<hostname>/task.php', {"start": {"sessionId" : sessionId}},"json");
postResult.done (function (response) {
    if (response.data !== undefined) {
        // initialize task page for this task
    }
}
```
The start command returns a data buffer with the current session and task context such as this example.

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
The ```startPageHtml``` and ```startPageNextUrl``` fields can be configured to display the current task information on this page.

If this is an introductory page that precedes the task-performance page, the **next** button can be programmed to go to the URL in ```startPageNextUrl```.

If this is a single=page task, you can use the URL in ```startPageNextUrl``` to go to the start page of the next task. When the next task page calls the task method with the start command, starting the next task will automatically finish and close the preceding task.

#### Study task-finish page

When a participant returns from the task-performance pages, they should return to a task-finish page, which will finish the task for the session and provide the opportinity to show the participant a post-task questionnaires.

To finish the task and get the end-of-task instructions, call the task method with the finish command as shown in this example.

```javascript
// sessionId is read from the query string
var postResult = $j.post ('<hostname>/task.php', {"finish": {"sessionId" : sessionId, "taskId" : -1}},"json");

postResult.done (function (response) {
    if (response.data !== undefined) {
		// initialize the task-finish page with the returned data
    }
}
```
If the task was closed successfully, the response buffer contains the information necessary to initialize the page.

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
The HTML in ```finishPageHTML``` is displayed to the participant and the **next** button is programmed to go to the URL returned in ```finishPageNextUrl```.

#### Finishing the study

When the task method is called with the start command and there are no more tasks to perform, the method returns an error.

```javascript
{
    "error": {
        "lastTask": "Task 3 is the last task in this study.",
        "openTask": {
            "finishTime": "2013-08-25 17:00:32",
            "taskId": "3",
            "message": "The previous task was not finished."
        }
    }
}
```

When the task-start page receives this response. This response indicates that the study method must be called with the finish command.

```javascript
var finishResult = $j.post ('<hostname>/session.php', {"finish": {"sessionId" : sessionID}},"json");
```
This method could be called from the task-start page, if no further instructions are necessary. It could be called from a separate study-end page, if more instructions were necessary, such as the link to register for a gratuity.