##Server status
The production server has moved to http://wlux.uw.edu/rbwatson as of 7/21/2013. 
The current code on http://wlux.uw.edu/rbwatson is current and in sync with the GitHub repo as of 8/24/2013.

The code running on http://staff.washington.edu/rbwatson is now out of date and will be removed in the near future.

The wlux_test_server code runs on http://wlux.uw.edu/rbwatson and is used to test server-side code while we're experimenting with WebLabUX utilities and "plumbing." I'll make sure that what is in the master repo is also on the server.

To run the demo, go to http://wlux.uw.edu/rbwatson/start.php?wlux_study=1234 

Documentation of the service is in the [documentation](/documentation/_top.md) folder.

##Release notes
*24 Aug, 2013* - Finished porting the test site to use the web-service interface, the MySQL database, and support multi-task studies. However, it's still rather brittle so please let me know if you find something that breaks.

*21 July, 2013* - Moved code to WLUX server. Started move of config functions to DB. Adopting a more consistent web-server interface for the web methods: All functions should return a json object that includes a _data_ object for sucessful calls or an _error_ object with some explanation, if not.

**THIS BUILD IS NOT READY FOR RELEASE -- IT IS FOR TESTING/DEMO ONLY **
When ready for production, the javascript needs to be compiled / minified so that it
will download and run faster on client sites. This can be done using the google closure 
compiler (compiler.jar), via the following command:

   java -jar compiler.jar --js jquery.js --js wlux_instrumentation.js --js_output_file wlux_instrumentation.min.js

This also combines jquery and wlux_instrumentation into a single file. Now test sites need 
only include a single script, `wlux_instrumentation.min.js`.

To avoid having to copy/paste or memorize this command, there are two scripts `compile.sh` and
`compile.bat` which will run the minification command on linux and windows, respectively.

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
| *buttonText* | the text to display on the task button. -- Usually something like "End Task" or "End study." |
| *returnUrl* | the URL to the return page in WebLabUX. -- This is typically the post-study questionnaire, but could be configured to point to the next task in a multi-task study. (was: *returnURL*) |
| *taskText* | The unformatted text to display in the task bar. |
| *taskHtml* | Formatted HTML to display in the task bar. If both this field and the *taskText* are defined, only this field will be used. (was: *taskHTML*) |
| *tabShowText* | Text to display in the show/hide task button when the task bar is hidden. |
| *tabHideText* | Text to display in the show/hide task button when the taks bar is visible. |

Any other fields that might appear in the configuration data object should not be used as they might disappear without notice.

### Styles used by the taskBarCSS file
The taskBarCSS file referenced in the study config data object uses the styles shown here to configure the task bar and  task/study end button.

![Task bar .css styles](./TaskBarCSS.png)

## Data logger data objects
The logger data object is defined by the structure in WLUX_Instrumentation.js and has two flavors: **open** and **transition**.
Because these structures are defined in the code, confirm the fields as they are defined in the POST request to logger.php.

The logger interface is supported by DB tables now and is access by sending the log data from the client page to the logger in a POST request. The request data determines the type of log entry to write.

### Open log entry
The Open log entry is used to identify when a new page is opened. It has no other meaning beyond the page was opened.


| Data field | Description |
|--------------|------------------------------------------------------------------| 
| *clientTimestamp* | The JavaScript formatted timestamp taken from the client browser. |
| *sessionId* | The session ID for the current session - received from WebLabUX in the study config data. |
| *taskId* | the current task in the current session (currently this always 1). |
| *conditionId* | The condition ID for the current session - received from WebLabUX in the study config data. |
| *fromUrl* |  The URL of the page from which the call was made (i.e the page that was just opened. |

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



### Transition log entry
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


