##Server status

(Happy Halloween!)
On 10/31/13, the files have been moved around in the repo and on the production server as follows

|| repo folder	|| holds || prod server path	 ||  local server path ||
| www__root | Not much. Don't put files here. | http://wlux.uw.edu/rbwatson/ | http://localhost/wlux/ |
| www_common | Files used by WebLabUX and client sites being tested. | http://wlux.uw.edu/common/ | http://localhost/wlux/common/ |
| www_demo | The demo pages. | http://wlux.uw.edu/demo/ | http://localhost/wlux/demo/ |
| www_data | The WebLabUX _backend_ data service files. | http://wlux.uw.edu/data/ | http://localhost/wlux/data/ |
| build_tools | Files used to build components of this site | N/A | N/A |
| config_and_setup | Files used to initialize the backend |  N/A | N/A |
| docstudyt | Test files for research project. Don't use or change. |  N/A | N/A |
| documentation | The documentation. |  N/A | N/A |
| obsolete_files | Files we can't live with and can't live without |  N/A | N/A |
| test_files | Diagnostic files |  N/A | N/A |

The current code on http://wlux.uw.edu/demo, /data, and /common is current and in sync with the GitHub repo as of 10/31/2013.

The production server moved to http://wlux.uw.edu/rbwatson on 7/21/2013. The code running on http://staff.washington.edu/rbwatson is now out of date and will be removed in the near future.

The wlux_test_server code runs on http://wlux.uw.edu/rbwatson and is used to test server-side code while we're experimenting with WebLabUX utilities and "plumbing." I'll make sure that what is in the master repo is also on the server.

These demos should always be available:
* Demo of 3-task study of our test site: http://wlux.uw.edu/demo/start.php?wlux_study=1234 
* Demo of 1-task study with a 3rd-party study embedded in the task page: http://wlux.uw.edu/demo/start.php?wlux_study=2525
* Demo of the canonical 1-task study is http://wlux.uw.edu/demo/start.php?wlux_study=2001
* Demo of the canonical 3-task study is http://wlux.uw.edu/demo/start.php?wlux_study=1999

Documentation of the service is available in the [documentation](/documentation/_top.md) folder.

##Release notes
* *31 Oct, 2013* - refactored folder structure and names in repo and production server

* *30 Oct, 2013* - refactored the API so that each function is in its own source file and added the _study GET allIds_ function

* *1 Sep, 2013* - added support for 3rd-party surveys.

* *26 Aug, 2013* - Added the gratuity interface to enable recording gratuity information without connecting it to a participant session.

* *24 Aug, 2013* - Finished porting the test site to use the web-service interface, the MySQL database, and support multi-task studies. However, it's still rather brittle so please let me know if you find something that breaks.

* *21 July, 2013* - Moved code to WLUX server. Started move of config functions to DB. Adopting a more consistent web-server interface for the web methods: All functions should return a json object that includes a _data_ object for sucessful calls or an _error_ object with some explanation, if not.

**THIS BUILD IS NOT READY FOR RELEASE -- IT IS FOR TESTING/DEMO ONLY **
When ready for production, the javascript needs to be compiled / minified so that it
will download and run faster on client sites. This can be done using the google closure 
compiler (compiler.jar), via the following command:

   java -jar compiler.jar --js jquery.js --js wlux_instrumentation.js --js_output_file wlux_instrumentation.min.js

This also combines jquery and wlux_instrumentation into a single file. Now test sites need 
only include a single script, `wlux_instrumentation.min.js`.

To avoid having to copy/paste or memorize this command, there are two scripts `compile.sh` and
`compile.bat` which will run the minification command on linux and windows, respectively.